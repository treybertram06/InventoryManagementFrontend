<?php

require_once 'include/common.php';
require_once 'models/user.php';

class Database {
    public $host = "localhost";
    public $dbName = "PhoneInventory";
    public $user = "root";
    public $pass = "password";
    public $charset = "utf8mb4";
    private $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    private $pdo;
    private $dsn;

    public function __construct($host = null, $dbName = null, $user = null, $pass = null, $charset = null) {
        $this->host = $host ?? $this->host;
        $this->dbName = $dbName ?? $this->dbName;
        $this->user = $user ?? $this->user;
        $this->pass = $pass ?? $this->pass;
        $this->charset = $charset ?? $this->charset;

        $this->dsn = "mysql:host=$this->host; dbname=$this->dbName; charset=$this->charset;";

        try {
            $this->pdo = new PDO($this->dsn, $this->user, $this->pass, $this->options);
            log_info("Connected to database successfully!");
        } catch (PDOException $e) {
            log_error("Connection failed: \n    DSN: " . $this->dsn . "\n    Error: " . $e->getMessage());
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
            $users[] = new User(
                $row['Usr_userId'],
                $row['Usr_username'],
                $row['Usr_email'],
                UserRole::from($row['Usr_role'])
            );
        }
        return $users;
    }
    public function get_user_by_id($id): User | null {
        $stmt = $this->pdo->prepare("SELECT * FROM User WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        } else {
            return new User(
                $data['Usr_userId'],
                $data['Usr_username'],
                $data['Usr_email'],
                UserRole::from($data['Usr_role'])
            );
        }
    }


}





