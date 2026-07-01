<?php

require_once 'include/common.php';
require_once 'models/user.php';

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
    ];
    private $pdo;
    private $dsn;

    public function __construct($host = null, $dbName = null, $user = null, $pass = null, $charset = null) {
        $config = require __DIR__ . '/config.php';

        $this->host = $host ?? $config['db_host'];
        $this->dbName = $dbName ?? $config['db_name'];
        $this->user = $user ?? $config['db_user'];
        $this->pass = $pass ?? $config['db_pass'];
        $this->charset = $charset ?? $config['db_charset'];

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





