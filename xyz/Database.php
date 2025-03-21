<?php

/* 
 * Why Use This Approach?
 * ✅ Prevents multiple database connections – Saves memory and avoids conflicts.
 * ✅ Encapsulation – Protects direct access to the $conn property.
 * ✅ Code Reusability – You can access the database from anywhere with just:
 */

require_once 'config.php'; // Include the config file

class Database   // 
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
            $this->conn = new PDO($dsn, DB_USER, DB_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}

// Usage
// $db = Database::getInstance()->getConnection();
// Test Connection
// try {
    // $db = Database::getInstance()->getConnection();
    // if ($db) {
        // echo "✅ Database connection successful!";
    // }
// } catch (Exception $e) {
    // echo "❌ Connection failed: " . $e->getMessage();
// }
