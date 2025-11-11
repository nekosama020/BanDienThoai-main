<?php
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase {
    private $conn;

    protected function setUp(): void {
        $this->conn = new mysqli("localhost", "root", "", "dbphonestore");
        if ($this->conn->connect_error) {
            $this->fail("Kết nối DB thất bại: " . $this->conn->connect_error);
        }

        // Đảm bảo categories có id=1
        $this->conn->query("INSERT IGNORE INTO categories (id, name, status) VALUES (1, 'Test Category', 'Active')");
    }

    public function testInsertProduct() {
        $sql = "INSERT INTO products (name, price, stock_quantity, category_id, description, specifications) 
                VALUES ('Test Phone', 12000000, 10, 1, 'Mô tả test', 'Thông số test')";
        $result = $this->conn->query($sql);

        if (!$result) {
            $this->fail("SQL Error: " . $this->conn->error);
        }

        $this->assertTrue($result);
        $this->conn->query("DELETE FROM products WHERE name='Test Phone'");
    }

    public function testUpdatePrice() {
        $this->conn->query("INSERT INTO products (name, price, stock_quantity, category_id, description, specifications) 
                            VALUES ('PriceTest', 10000000, 5, 1, 'desc', 'spec')");
        $this->conn->query("UPDATE products SET price=15000000 WHERE name='PriceTest'");

        $result = $this->conn->query("SELECT price FROM products WHERE name='PriceTest'");
        $row = $result->fetch_assoc();
        $this->assertEquals(15000000, $row['price']);

        $this->conn->query("DELETE FROM products WHERE name='PriceTest'");
    }

    public function testUpdateStock() {
        $this->conn->query("INSERT INTO products (name, price, stock_quantity, category_id, description, specifications) 
                            VALUES ('StockTest', 20000000, 10, 1, 'desc', 'spec')");
        $this->conn->query("UPDATE products SET stock_quantity=8 WHERE name='StockTest'");

        $result = $this->conn->query("SELECT stock_quantity FROM products WHERE name='StockTest'");
        $row = $result->fetch_assoc();
        $this->assertEquals(8, $row['stock_quantity']);

        $this->conn->query("DELETE FROM products WHERE name='StockTest'");
    }

    public function testSelectAllProducts() {
        $result = $this->conn->query("SELECT * FROM products");
        $this->assertGreaterThanOrEqual(0, $result->num_rows);
    }

    public function testSearchProductByName() {
        $this->conn->query("INSERT INTO products (name, price, stock_quantity, category_id, description, specifications) 
                            VALUES ('SearchTest', 5000000, 2, 1, 'desc', 'spec')");

        $result = $this->conn->query("SELECT * FROM products WHERE name LIKE '%SearchTest%'");
        $this->assertGreaterThan(0, $result->num_rows);

        $this->conn->query("DELETE FROM products WHERE name='SearchTest'");
    }
}
