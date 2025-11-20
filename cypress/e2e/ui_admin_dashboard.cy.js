describe('Kiểm thử Giao diện (UI) - Admin Dashboard', () => {

  // HÀM NÀY SẼ CHẠY TRƯỚC MỖI TEST CASE (mỗi 'it')
  beforeEach(() => {
    // 
    // == PHẦN QUAN TRỌNG: ĐĂNG NHẬP 1 LẦN ==
    // cy.session() sẽ lưu lại cookie sau khi đăng nhập.
    // Các test sau sẽ tự động dùng lại session này mà không cần gõ lại username/pass,
    // giúp test chạy nhanh hơn RẤT NHIỀU.
    //
    cy.session('adminLogin', () => {
      // 1. THAY THẾ 'admin/login.php' bằng URL trang login admin của bạn
      cy.visit('http://localhost/BanDienThoai-main/login.php');

      // 2. THAY THẾ các selector và thông tin đăng nhập admin
      cy.get('#loginEmail').type('admin@admin.admin'); // THAY #adminUsername
      cy.get('#loginPassword').type('10120204'); // THAY #adminPassword
      cy.get('form:has(#loginEmail) button[name="login"]').click(); // THAY nút login

      // 3. Kiểm tra xem đã login thành công và vào được dashboard chưa
      cy.url().should('include', 'dashboard.php');
    });
  });

  // ========== BỘ TEST CHO GIAO DIỆN ==========

  it('Kiểm tra các thành phần layout chính (Sidebar, Header)', () => {
    // 4. Phải visit lại trang dashboard sau khi đã có session
    cy.visit('http://localhost/BanDienThoai-main/admin/dashboard.php');

    // 5. THAY THẾ các selector cho đúng với layout của bạn
    
    // Kiểm tra Sidebar có tồn tại và hiển thị không
    cy.get('nav.sidebar').should('be.visible'); // Ví dụ: <nav class="sidebar">...</nav>

    // Kiểm tra Header (thanh điều hướng trên cùng)
    cy.get('header.top-navbar').should('be.visible'); // Ví dụ: <header class="top-navbar">...</header>

    // Kiểm tra xem có tên Admin đang đăng nhập ở đâu đó không
    cy.get('.user-info').should('contain', 'admin'); // Ví dụ: <div class="user-info">Chào, admin</div>
  });

  it('Kiểm tra các link điều hướng trong Sidebar', () => {
    cy.visit('http://localhost/BanDienThoai-main/admin/dashboard.php');

    // Dựa trên danh sách file bạn cung cấp, ta kiểm tra các link:
    // 6. THAY THẾ các selector cho đúng với menu của bạn
    
    cy.get('a[href="dashboard.php"]').should('be.visible').and('contain', 'Dashboard');
    cy.get('a[href="manage_category.php"]').should('be.visible').and('contain', 'Quản lý Danh mục');
    cy.get('a[href="manage_product.php"]').should('be.visible').and('contain', 'Quản lý Sản phẩm');
    cy.get('a[href="manage_order.php"]').should('be.visible').and('contain', 'Quản lý Đơn hàng');
    cy.get('a[href="statistic.php"]').should('be.visible').and('contain', 'Thống kê');
  });

  it('Kiểm tra hành vi (behavior) điều hướng', () => {
    cy.visit('http://localhost/BanDienThoai-main/admin/dashboard.php');

    // 7. Nhấp thử vào một link
    cy.get('a[href="manage_product.php"]').click();

    // 8. Kiểm tra xem URL đã chuyển đúng chưa
    cy.url().should('include', 'manage_product.php');

    // 9. Kiểm tra xem tiêu đề (ví dụ <h1>) của trang đó có đúng không
    cy.get('h1').should('contain', 'Quản lý Sản phẩm'); // Ví dụ: <h1>Quản lý Sản phẩm</h1>

    // 10. Quay lại trang dashboard
    cy.get('a[href="dashboard.php"]').click();
    cy.url().should('include', 'dashboard.php');
  });
});

//npx cypress open