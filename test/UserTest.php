<?php
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {
    private $conn;

    protected function setUp(): void {
        $this->conn = new mysqli("localhost", "root", "", "dbphonestore");
        if ($this->conn->connect_error) {
            die("❌ Kết nối thất bại: " . $this->conn->connect_error);
        }
        echo "====================================\n";
        echo "🔹 Kết nối CSDL thành công!\n\n";
    }

    public function testValidLogin() {
        echo "▶ Đang chạy testValidLogin()...\n";
        $result = $this->conn->query("SELECT * FROM users WHERE username='admin' LIMIT 1");
        $this->assertGreaterThan(0, $result->num_rows);
        echo "✅ Đăng nhập hợp lệ: Tài khoản 'admin' tồn tại.\n\n";
    }

    public function testRegisterUser() {
        echo "▶ Đang chạy testRegisterUser()...\n";
        $sql = "INSERT INTO users (username, password, email, role) 
                VALUES ('testuser', '123456', 'testuser@mail.com', 'customer')";
        $this->assertTrue($this->conn->query($sql));
        echo "✅ Đã thêm tài khoản 'testuser'.\n";

        $this->conn->query("DELETE FROM users WHERE username='testuser'");
        echo "🧹 Đã xóa tài khoản testuser (dữ liệu thử nghiệm).\n\n";
    }

    public function testUpdateUser() {
        echo "▶ Đang chạy testUpdateUser()...\n";
        $this->conn->query("INSERT INTO users (username, password, email, role) 
                            VALUES ('updateuser', '123456', 'update@mail.com', 'customer')");
        echo "📦 Đã tạo tài khoản 'updateuser'.\n";

        $this->conn->query("UPDATE users SET email='newmail@mail.com' WHERE username='updateuser'");
        echo "🔄 Đã cập nhật email thành 'newmail@mail.com'.\n";

        $result = $this->conn->query("SELECT email FROM users WHERE username='updateuser'");
        $row = $result->fetch_assoc();
        $this->assertEquals('newmail@mail.com', $row['email']);
        echo "✅ Cập nhật thành công: {$row['email']}.\n";

        $this->conn->query("DELETE FROM users WHERE username='updateuser'");
        echo "🧹 Đã xóa tài khoản updateuser (dữ liệu thử nghiệm).\n\n";
    }

    public function testViewAllUsers() {
        echo "▶ Đang chạy testViewAllUsers()...\n";
        $result = $this->conn->query("SELECT * FROM users");
        $count = $result->num_rows;
        echo "📄 Tổng số người dùng trong hệ thống: $count\n";
        $this->assertGreaterThan(0, $count);
        echo "✅ testViewAllUsers() hoàn tất.\n\n";
    }

    public function testDeleteUser() {
        echo "▶ Đang chạy testDeleteUser()...\n";
        $this->conn->query("INSERT INTO users (username, password, email, role) 
                            VALUES ('deleteuser', '123456', 'delete@mail.com', 'customer')");
        echo "📦 Đã tạo tài khoản 'deleteuser' để kiểm thử.\n";

        $this->conn->query("DELETE FROM users WHERE username='deleteuser'");
        echo "🗑️ Đã xóa tài khoản 'deleteuser'.\n";

        $result = $this->conn->query("SELECT * FROM users WHERE username='deleteuser'");
        $this->assertEquals(0, $result->num_rows);
        echo "✅ Tài khoản 'deleteuser' đã bị xóa hoàn toàn.\n\n";
    }

    protected function tearDown(): void {
        $this->conn->close();
        echo "🔸 Đóng kết nối CSDL.\n\n";
    }
}
