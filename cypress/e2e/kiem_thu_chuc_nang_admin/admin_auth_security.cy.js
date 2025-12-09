describe('Chức năng Quản trị viên (Admin Auth & Security)', () => {

  const loginUrl = '/login.php';
  const indexUrl = '/index.php';
  const dashboardUrl = '/admin/dashboard.php';

  // ======================================================
  // CASE 1: QUY TRÌNH ĐĂNG NHẬP ADMIN CHUẨN
  // ======================================================
  it('Admin đăng nhập -> Vào Index -> Bấm nút Quản trị -> Vào Dashboard', () => {
    // 1. Vào trang đăng nhập
    cy.visit(loginUrl);

    // 2. Đăng nhập tài khoản ADMIN (Thay thông tin thật của bạn)
    cy.get('#loginEmail').type('admin@admin.admin'); // Email admin
    cy.get('#loginPassword').type('10120204');       // Pass admin
    cy.get('button[name="login"]').click();

    // 3. Sau khi login, phải về trang chủ Index
    cy.url().should('include', 'index.php');

    // 4. Tìm nút "Quản trị" trên thanh menu và click
    // Dựa vào code header.php bạn gửi trước đó: <a ...>Quản trị</a>
    cy.contains('a', 'Quản trị').click();

    // 5. Kiểm tra đã vào Dashboard thành công
    // - Check URL
    cy.url().should('include', 'admin/dashboard.php');
    // - Check nội dung trong file dashboard.php bạn vừa gửi
    cy.get('h2').should('contain', 'Dashboard');
    cy.get('.sidebar').should('be.visible'); // Sidebar màu đen bên trái
  });

  // ======================================================
  // CASE 2: BẢO MẬT - KHÁCH VÃNG LAI (GUEST)
  // ======================================================
  it('Khách (Chưa login) cố tình gõ link Dashboard -> Bị đá về Login', () => {
    // 1. Xóa sạch mọi session/cookie để đảm bảo đang là khách
    cy.clearCookies();
    cy.clearLocalStorage();

    // 2. Cố tình truy cập thẳng vào Dashboard
    cy.visit(dashboardUrl);

    // 3. Kiểm tra: Phải bị redirect về trang Login
    // Logic PHP: header('Location: /BanDienThoai-main/login.php');
    cy.url().should('include', 'login.php');
    
    // (Tùy chọn) Kiểm tra không được nhìn thấy chữ "Dashboard"
    cy.contains('Dashboard').should('not.exist');
  });

  // ======================================================
  // CASE 3: BẢO MẬT - USER THƯỜNG (CUSTOMER)
  // ======================================================
  it('User thường cố tình gõ link Dashboard -> Bị đá về Login', () => {
    // 1. Đăng nhập bằng tài khoản KHÁCH HÀNG (Không phải Admin)
    cy.session('customerAuth', () => {
        cy.visit(loginUrl);
        cy.get('#loginEmail').type('dung2004@gmail.com'); // Email khách
        cy.get('#loginPassword').type('10120204');           // Pass khách
        cy.get('button[name="login"]').click();
        cy.url().should('include', 'index.php');
    });

    // 2. Sau khi login user thường, cố tình gõ link Dashboard
    cy.visit(dashboardUrl);

    // 3. Kiểm tra: Vẫn phải bị redirect về Login
    // Vì code PHP check: $_SESSION['roles'] !== 'Admin'
    cy.url().should('include', 'login.php');
  });

  // ======================================================
  // CASE 4: TỪ DASHBOARD QUAY VỀ TRANG CHỦ
  // ======================================================
  it('Từ Dashboard bấm nút Home (🏠) để quay về trang chủ', () => {
    // Đăng nhập Admin trước
    cy.session('adminAuth', () => {
        cy.visit(loginUrl);
        cy.get('#loginEmail').type('admin@admin.admin'); 
        cy.get('#loginPassword').type('10120204');
        cy.get('button[name="login"]').click();
    });

    // Vào dashboard
    cy.visit(dashboardUrl);

    // Tìm nút Home hình ngôi nhà 🏠 và click
    // Dựa vào code dashboard.php: title="Quay về trang chủ"
    cy.get('a[title="Quay về trang chủ"]').click();

    // Kiểm tra đã về index chưa
    cy.url().should('include', 'index.php');
  });

});