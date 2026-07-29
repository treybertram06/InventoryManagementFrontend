<?php
namespace Core;

//require_once 'core/common.php';
//require_once 'models/user.php';
//require_once 'models/device.php';

use Models\User;
use Models\Device;
use Models\Sale;
use PDO;
use Models\UserRole;

class Database {
    public $host;
    public $dbName;
    public $user;
    public $pass;
    public $charset;
    private $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 3,
    ];
    private $pdo;
    private $dsn;

    public function __construct($host = null, $dbName = null, $user = null, $pass = null, $charset = null) {
        try {
            $config = require __DIR__ . '/config.php';
        } catch (\Throwable $e) {
            Common::log_error("Error reading config file: " . $e->getMessage() . "\n    Please check your config.php file - see config.example.php.");
            $config = require __DIR__ . '/config.example.php';
        }

        $this->host = $host ?? $config['db_host'];
        $this->dbName = $dbName ?? $config['db_name'];
        $this->user = $user ?? $config['db_user'];
        $this->pass = $pass ?? $config['db_pass'];
        $this->charset = $charset ?? $config['db_charset'];

        $this->dsn = "mysql:host=$this->host; dbname=$this->dbName; charset=$this->charset;";

        try {
            $this->pdo = new PDO($this->dsn, $this->user, $this->pass, $this->options);
            Common::log_info("Connected to database successfully!");
        } catch (\PDOException $e) {
            Common::log_error("Connection failed: \n    DSN: " . $this->dsn . "\n    Error: " . $e->getMessage());
            throw new \RuntimeException("Unable to connect to the database.", 0, $e);
        }
    }

    public function get_pdo() {
        return $this->pdo;
    }

    // provide getters that will handle db queries and return results as PHP objects

    // --- User ---
    public function get_all_users(): array | null {
        $stmt = $this->pdo->prepare("SELECT * FROM user");
        $stmt->execute();
        $data = $stmt->fetchAll();

        $users = [];
        if (!$data) return null;
        foreach ($data as $row) {
            $users[] = User::from_row($row);
        }
        return $users;
    }
    public function get_user_by_id($id): User | null {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        } else {
            return new User(
                $data['id'],
                $data['username'],
                $data['email'],
                UserRole::from($data['role'])
            );
        }
    }

    public function create_user($username, $email, $password, $role = 'technician'): bool {
        $stmt = $this->pdo->prepare("INSERT INTO user (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$username, $email, $password, $role]);
    }

    public function does_user_exist($username): bool {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->execute([$username]);
        return (bool) $stmt->fetch();
    }

    public function does_user_exist_by_email($email): bool {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        return (bool) $stmt->fetch();
    }

    public function update_user_role($id, string $role): bool {
        $stmt = $this->pdo->prepare("UPDATE user SET role = ? WHERE id = ?");
        return $stmt->execute([$role, $id]);
    }

    public function delete_user($id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM user WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function get_login_credentials($identifier, bool $isEmail = true): ?array {
        $stmt = $isEmail ?
            $this->pdo->prepare("SELECT id, password_hash FROM user WHERE email = ?") :
            $this->pdo->prepare("SELECT id, password_hash FROM user WHERE username = ?");

        $stmt->execute([$identifier]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // --- Device ---
    private const DEVICE_REPORT_SELECT = "
        SELECT
            s.id AS sale_id,
            d.serial_number,
            d.imei,
            d.product_type,
            dm.friendly_name,
            d.model_number,
            d.color,
            d.region_code,
            d.storage_gb,
            d.battery_original,
            d.screen_original,
            d.previously_repaired,
            d.known_issues,
            i.grade, i.condition_notes, i.repairs_needed_done, i.status,
            COALESCE(smi.revision_price, smi.supplier_value) AS cost_paid,
            i.repair_cost, i.b2b_floor_price, i.b2c_floor_price,
            s.sale_price, s.sale_channel, s.sold_at, s.buyer_info,
            b.batch_number, b.technician, d.intake_at, b.received_at,
            ds.battery_health_pct, ds.count_pass, ds.count_fail, ds.count_na, ds.count_pending
        FROM device d
        JOIN device_model dm      ON dm.product_type = d.product_type
        JOIN batch b               ON b.id = d.batch_id
        LEFT JOIN inventory_item i ON i.serial_number = d.serial_number
        LEFT JOIN diagnostic_session ds ON ds.id = i.canonical_session_id
        LEFT JOIN supplier_manifest_item smi ON smi.id = d.supplier_manifest_item_id
        LEFT JOIN sale s ON s.id = (
            SELECT id FROM sale
            WHERE sale.serial_number = d.serial_number AND sale.reversed_at IS NULL
            ORDER BY sold_at DESC LIMIT 1
        )
        WHERE d.deleted_at IS NULL
    ";

    public function get_all_devices(): array | null {
        $stmt = $this->pdo->prepare(self::DEVICE_REPORT_SELECT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        if (!$data) return null;
        return array_map(fn($row) => Device::from_row($row), $data);
    }

    public function get_device_by_serial($serialNumber): Device | null {
        $stmt = $this->pdo->prepare(self::DEVICE_REPORT_SELECT . " AND d.serial_number = ?");
        $stmt->execute([$serialNumber]);
        $data = $stmt->fetch();

        if (!$data) return null;
        return Device::from_row($data);
    }

    public function does_device_exist($serialNumber): bool {
        $stmt = $this->pdo->prepare("SELECT 1 FROM device WHERE serial_number = ?");
        $stmt->execute([$serialNumber]);
        return (bool) $stmt->fetch();
    }

    public function does_device_model_exist($productType): bool {
        $stmt = $this->pdo->prepare("SELECT 1 FROM device_model WHERE product_type = ?");
        $stmt->execute([$productType]);
        return (bool) $stmt->fetch();
    }

    public function create_device_model($productType, $friendlyName): bool {
        $stmt = $this->pdo->prepare("INSERT INTO device_model (product_type, friendly_name) VALUES (?, ?)");
        return $stmt->execute([$productType, $friendlyName]);
    }

    public function get_all_device_models(): array {
        $stmt = $this->pdo->prepare("SELECT product_type, friendly_name FROM device_model ORDER BY friendly_name");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function delete_device($serialNumber): bool {
        $stmt = $this->pdo->prepare("UPDATE device SET deleted_at = NOW() WHERE serial_number = ?");
        return $stmt->execute([$serialNumber]);
    }

    // --- Inventory Item ---
    public function create_inventory_item($serialNumber, $grade): bool {
        $stmt = $this->pdo->prepare("INSERT INTO inventory_item (serial_number, grade) VALUES (?, ?)");
        return $stmt->execute([$serialNumber, $grade]);
    }

    public function create_device(array $data): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO device (
                serial_number, product_type, model_number, color, region_code, storage_gb,
                imei, imei2, udid, meid, iccid, wifi_mac, bluetooth_mac, baseband_serial,
                hardware_model,
                battery_original, screen_original, previously_repaired, known_issues,
                activation_state, icloud_lock_status, find_my_enabled, passcode_protected, mdm_enrolled,
                can_test, device_password,
                batch_id, supplier_manifest_item_id
            ) VALUES (
                :serial_number, :product_type, :model_number, :color, :region_code, :storage_gb,
                :imei, :imei2, :udid, :meid, :iccid, :wifi_mac, :bluetooth_mac, :baseband_serial,
                :hardware_model,
                :battery_original, :screen_original, :previously_repaired, :known_issues,
                :activation_state, :icloud_lock_status, :find_my_enabled, :passcode_protected, :mdm_enrolled,
                :can_test, :device_password,
                :batch_id, :supplier_manifest_item_id
            )
        ");
        return $stmt->execute($data);
    }
    public function update_device(array $data): bool {
        $stmt = $this->pdo->prepare("
            UPDATE device
            SET
                product_type = :product_type,
                model_number = :model_number,
                color = :color,
                region_code = :region_code,
                storage_gb = :storage_gb,
                known_issues = :known_issues
            WHERE serial_number = :serial_number
        ");
        return $stmt->execute($data);
    }

    // --- Batch ---
    public function get_batch_id_by_number($batchNumber): ?int {
        $stmt = $this->pdo->prepare("SELECT id FROM batch WHERE batch_number = ?");
        $stmt->execute([$batchNumber]);
        $row = $stmt->fetch();
        return $row ? (int)$row['id'] : null;
    }

    public function create_batch($batchNumber): int {
        $stmt = $this->pdo->prepare("INSERT INTO batch (batch_number) VALUES (?)");
        $stmt->execute([$batchNumber]);
        return (int) $this->pdo->lastInsertId();
    }

    // --- Diagnostics ---
    public function get_device_model_capabilities($productType): ?array {
        $stmt = $this->pdo->prepare("
            SELECT has_home_button, has_face_id, has_action_button, has_camera_button, has_telephoto, has_lidar
            FROM device_model WHERE product_type = ?
        ");
        $stmt->execute([$productType]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create_diagnostic_session(array $data): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO diagnostic_session (
                serial_number, technician, ios_version, build_version, baseband_version,
                battery_pct, battery_health_pct, battery_cycles, battery_temp_c,
                battery_impedance_mohm, battery_serial, is_charging,
                data_capacity_gb, data_available_gb,
                count_pass, count_fail, count_na, count_pending,
                started_at, ended_at, elapsed_seconds
            ) VALUES (
                :serial_number, :technician, :ios_version, :build_version, :baseband_version,
                :battery_pct, :battery_health_pct, :battery_cycles, :battery_temp_c,
                :battery_impedance_mohm, :battery_serial, :is_charging,
                :data_capacity_gb, :data_available_gb,
                :count_pass, :count_fail, :count_na, :count_pending,
                :started_at, :ended_at, :elapsed_seconds
            )
        ");
        $stmt->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    public function create_test_result(int $sessionId, string $testId, ?string $testLabel, ?string $testGroup, string $status, string $source = 'manual'): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO test_result (session_id, test_id, test_label, test_group, status, source)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$sessionId, $testId, $testLabel, $testGroup, $status, $source]);
    }

    public function set_canonical_session($serialNumber, int $sessionId): bool {
        $stmt = $this->pdo->prepare("UPDATE inventory_item SET canonical_session_id = ? WHERE serial_number = ?");
        return $stmt->execute([$sessionId, $serialNumber]);
    }

    public function update_inventory_item_grade($serialNumber, $grade, ?string $conditionNotes): bool {
        $stmt = $this->pdo->prepare("UPDATE inventory_item SET grade = ?, condition_notes = ? WHERE serial_number = ?");
        return $stmt->execute([$grade, $conditionNotes, $serialNumber]);
    }

    public function update_inventory_item(array $data): bool {
        $stmt = $this->pdo->prepare("
            UPDATE inventory_item
            SET
                grade = :grade,
                condition_notes = :condition_notes,
                repairs_needed_done = :repairs_needed_done,
                repair_cost = :repair_cost,
                b2b_floor_price = :b2b_floor_price,
                b2c_floor_price = :b2c_floor_price
            WHERE serial_number = :serial_number
        ");
        return $stmt->execute($data);
    }
    // --- Sale ---
   public function create_sale(array $data): int {

        $stmt = $this->pdo->prepare("
            INSERT INTO sale (
                serial_number,
                technician_id,
                sale_price,
                sale_channel,
                buyer_info,
                sold_at,
                notes
            )
            VALUES (
                :serial_number,
                :technician_id,
                :sale_price,
                :sale_channel,
                :buyer_info,
                :sold_at,
                :notes
            )
        ");

        $stmt->execute($data);

        $saleId = (int)$this->pdo->lastInsertId();

        $update = $this->pdo->prepare("
            UPDATE inventory_item
            SET status = 'sold'
            WHERE serial_number = ?
        ");

        $update->execute([$data['serial_number']]);

        return $saleId;
    }

    public function get_all_sales(): array {
        $stmt = $this->pdo->prepare("
            SELECT
                sale.id, sale.serial_number, sale.sale_price, sale.sale_channel, sale.buyer_info,
                sale.sold_at, sale.reversed_at, sale.notes, sale.created_at,
                d.imei, d.product_type, dm.friendly_name,
                u_tech.username AS technician,
                u_rev.username AS reversed_by,
                -- The supplier's revised price if testing produced one, otherwise their asking price.
                COALESCE(smi.revision_price, smi.supplier_value) AS cost_paid,
                i.repair_cost,
                -- Separates a repair cost that is genuinely 0.00 from one unknown for want of an inventory row.
                (i.serial_number IS NOT NULL) AS has_inventory_row
            -- No d.deleted_at IS NULL filter, unlike DEVICE_REPORT_SELECT: /sales-history is an
            -- audit view, and only reversal un-counts a sale. See Core\Common::reportable_sales.
            FROM sale
            JOIN device d            ON d.serial_number = sale.serial_number
            JOIN device_model dm     ON dm.product_type = d.product_type
            JOIN user u_tech         ON u_tech.id = sale.technician_id
            LEFT JOIN user u_rev     ON u_rev.id = sale.reversed_by_id
            LEFT JOIN inventory_item i           ON i.serial_number = sale.serial_number
            LEFT JOIN supplier_manifest_item smi ON smi.id = d.supplier_manifest_item_id
            ORDER BY sale.sold_at DESC
        ");
        $stmt->execute();
        return array_map(fn($row) => Sale::from_row($row), $stmt->fetchAll());
    }

    public function get_sale_by_id(int $saleId): ?Sale {
        $stmt = $this->pdo->prepare("
            SELECT
                sale.id, sale.serial_number, sale.sale_price, sale.sale_channel, sale.buyer_info,
                sale.sold_at, sale.reversed_at, sale.notes, sale.created_at,
                d.imei, d.product_type, dm.friendly_name,
                u_tech.username AS technician,
                u_rev.username AS reversed_by,
                COALESCE(smi.revision_price, smi.supplier_value) AS cost_paid,
                i.repair_cost,
                (i.serial_number IS NOT NULL) AS has_inventory_row
            FROM sale
            JOIN device d            ON d.serial_number = sale.serial_number
            JOIN device_model dm     ON dm.product_type = d.product_type
            JOIN user u_tech         ON u_tech.id = sale.technician_id
            LEFT JOIN user u_rev     ON u_rev.id = sale.reversed_by_id
            LEFT JOIN inventory_item i           ON i.serial_number = sale.serial_number
            LEFT JOIN supplier_manifest_item smi ON smi.id = d.supplier_manifest_item_id
            WHERE sale.id = ?
        ");
        $stmt->execute([$saleId]);
        $row = $stmt->fetch();
        return $row ? Sale::from_row($row) : null;
    }

    public function update_sale(array $data): bool {
        $stmt = $this->pdo->prepare("
            UPDATE sale
            SET
                sale_price = :sale_price,
                sale_channel = :sale_channel,
                buyer_info = :buyer_info,
                sold_at = :sold_at,
                notes = :notes
            WHERE id = :id
        ");
        return $stmt->execute($data);
    }

    public function reverse_sale(int $saleId, int $reversedByUserId): bool {
        $stmt = $this->pdo->prepare("SELECT serial_number FROM sale WHERE id = ? AND reversed_at IS NULL");
        $stmt->execute([$saleId]);
        $row = $stmt->fetch();
        if (!$row) return false;

        $update = $this->pdo->prepare("UPDATE sale SET reversed_at = NOW(), reversed_by_id = ? WHERE id = ?");
        $update->execute([$reversedByUserId, $saleId]);

        $inventory = $this->pdo->prepare("UPDATE inventory_item SET status = 'in_stock' WHERE serial_number = ?");
        $inventory->execute([$row['serial_number']]);

        return true;
    }

    public function user_has_sale_records($userId): bool {
        $stmt = $this->pdo->prepare("SELECT 1 FROM sale WHERE technician_id = ? OR reversed_by_id = ? LIMIT 1");
        $stmt->execute([$userId, $userId]);
        return (bool) $stmt->fetch();
    }

    public function get_canonical_test_results($serialNumber): array {
        $stmt = $this->pdo->prepare("
            SELECT tr.test_id, tr.test_label, tr.test_group, tr.status
            FROM inventory_item i
            JOIN test_result tr ON tr.session_id = i.canonical_session_id
            WHERE i.serial_number = ?
            ORDER BY tr.test_group, tr.test_id
        ");
        $stmt->execute([$serialNumber]);
        return $stmt->fetchAll() ?: [];
    }

}





