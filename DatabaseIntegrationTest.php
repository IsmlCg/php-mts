<?php
use PHPUnit\Framework\TestCase;

class DatabaseIntegrationTest extends TestCase {
    private $conn;

    protected function setUp(): void {
        $this->conn = new mysqli('localhost', 'root', '', 'mts_db');
        // Create necessary tables or prepare the database state
    }

    protected function tearDown(): void {
        // Clean up database state or delete test data
    }

    public function testDatabaseInsertion() {
        $sql = "INSERT INTO `medicine_list`( `user_id`, `name`, `description` ) VALUES ( 2, 'prod 123','testing');";
        $result = $this->conn->query($sql);
        $this->assertTrue($result);
    }

}
