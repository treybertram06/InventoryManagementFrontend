<?php
namespace Core;

//require_once 'core/common.php';
//require_once 'models/user.php';
//require_once 'models/device.php';

use Models\User;
use Models\Device;
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
        $stmt = $this->pdo->prepare("SELECT * FROM User");
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
            d.serial_number, d.imei, d.product_type, dm.friendly_name, d.model_number,
            d.color, d.region_code, d.storage_gb,
            d.battery_original, d.screen_original, d.previously_repaired, d.known_issues,
            i.grade, i.condition_notes, i.repairs_needed_done, i.status,
            COALESCE(smi.revision_price, smi.supplier_value) AS cost_paid,
            i.repair_cost, i.b2b_floor_price, i.b2c_floor_price,
            i.sale_price, i.sale_channel,
            b.batch_number, b.technician, d.intake_at, b.received_at,
            ds.battery_health_pct, ds.count_pass, ds.count_fail, ds.count_na, ds.count_pending
        FROM device d
        JOIN device_model dm      ON dm.product_type = d.product_type
        JOIN batch b               ON b.id = d.batch_id
        LEFT JOIN inventory_item i ON i.serial_number = d.serial_number
        LEFT JOIN diagnostic_session ds ON ds.id = i.canonical_session_id
        LEFT JOIN supplier_manifest_item smi ON smi.id = d.supplier_manifest_item_id
    ";

    public function get_all_devices(): array | null {
        $stmt = $this->pdo->prepare(self::DEVICE_REPORT_SELECT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        if (!$data) return null;
        return array_map(fn($row) => Device::from_row($row), $data);
    }

    public function get_device_by_serial($serialNumber): Device | null {
        $stmt = $this->pdo->prepare(self::DEVICE_REPORT_SELECT . " WHERE d.serial_number = ?");
        $stmt->execute([$serialNumber]);
        $data = $stmt->fetch();

        if (!$data) return null;
        return Device::from_row($data);
    }

}





