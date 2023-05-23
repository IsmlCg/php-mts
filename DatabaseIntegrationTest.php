<?php
use PHPUnit\Framework\TestCase;

class DatabaseIntegrationTest extends TestCase {
    private $connect;

    protected function setUp(): void {
        $this->connect = new mysqli('localhost', 'root', '', 'mts_db');
        // Create necessary tables or prepare the database state
    }

    protected function tearDown(): void {
        // Clean up database state or delete test data
    }

    public function testDatabaseInsertion() {
        $sql = "INSERT INTO medicine_list ( user_id, name, description ) VALUES ( 2,'Aspirin', 'Aspirin-also known as acetylsalicylic acid-is sold over the counter and comes in many forms, from the familiar white tablets to chewing gum and rectal suppositories.');";
        $result = $this->connect->query($sql);
        $this->assertTrue($result);
    }
}
