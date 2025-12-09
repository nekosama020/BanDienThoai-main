describe('Kiểm thử Giao diện Responsive (Chỉ Mobile)', () => {

  const homeUrl = '/index.php';
  const dashboardUrl = '/admin/dashboard.php';

  // ======================================================
  // CASE 1: KIỂM TRA GIAO DIỆN KHÁCH HÀNG (CHƯA LOGIN)
  // ======================================================
  it('Client Mobile (Chưa Login): Menu Hamburger và Lưới sản phẩm', () => {
    
    // 1. Giả lập màn hình iPhone X
    cy.viewport('iphone-x'); 
    cy.visit(homeUrl);

    // --- CHECK 1: MENU HEADER ---
    cy.get('#navbarNav').should('not.be.visible'); // Menu ngang ẩn
    cy.get('.navbar-toggler').should('be.visible'); // Nút 3 gạch hiện

    // Click mở menu
    cy.get('.navbar-toggler').click();
    cy.get('#navbarNav').should('be.visible');
    
    // Chưa login thì phải thấy nút "Đăng Nhập"
    cy.contains('a', 'Đăng Nhập').should('be.visible');


    // --- CHECK 2: DANH SÁCH SẢN PHẨM ---
    const viewportWidth = 375; 
    cy.get('.product-card').first().then(($card) => {
        const cardWidth = $card.outerWidth();
    });
  });

  // ======================================================
  // CASE 2: KIỂM TRA GIAO DIỆN ADMIN TRÊN MOBILE
  // ======================================================
  it('Admin Mobile: Sidebar phải ẩn đi', () => {
    
    // 1. Đăng nhập Admin
    cy.session('adminLogin', () => {
        cy.visit('/login.php');
        cy.get('#loginEmail').type('20222274@eaut.edu.vn'); 
        cy.get('#loginPassword').type('10120204');
        cy.get('button[name="login"]').click();
        
        // Sửa lỗi redirect
        cy.url().should('include', 'index.php'); 
        cy.visit(dashboardUrl);
    });

    // 2. Giả lập màn hình & Vào Dashboard
    cy.viewport('iphone-x');
    cy.visit(dashboardUrl);

    // --- CHECK: SIDEBAR ADMIN ---
    // Sidebar phải bị ẩn trên mobile
    cy.get('.sidebar').should('not.be.visible');
    // Nội dung chính tràn màn hình
    cy.get('main').should('be.visible');
  });

  // ======================================================
  // CASE 3: KHÁCH HÀNG ĐĂNG NHẬP TRÊN MOBILE (CASE MỚI)
  // ======================================================
  it('Client Mobile (Đã Login): Đăng nhập Khách và Kiểm tra Menu', () => {
    
    // 1. Giả lập Mobile
    cy.viewport('iphone-x');
    
    // 2. Vào trang chủ (Đảm bảo chưa đăng nhập hoặc clear session cũ)
    cy.visit(homeUrl);

    // 3. Thực hiện Đăng nhập trên giao diện Mobile
    // Bấm nút 3 gạch để mở menu
    cy.get('.navbar-toggler').click();
    // Bấm nút "Đăng Nhập" trong menu
    cy.contains('a', 'Đăng Nhập').should('be.visible').click();

    // 4. Điền thông tin Khách hàng (TK bạn cung cấp)
    cy.get('#loginEmail').type('dung02042004@gmail.com'); 
    cy.get('#loginPassword').type('10120204A');
    cy.get('button[name="login"]').click();

    // 5. Kiểm tra sau khi Login
    cy.url().should('include', 'index.php');

    // Mở lại menu để kiểm tra sự thay đổi
    cy.get('.navbar-toggler').click();

    // --- CÁC ĐIỂM CẦN CHECK ---
    
    // a. Phải hiện lời chào "Hi, ..."
    cy.contains('span', 'Hi,').should('be.visible');

    // b. Phải hiện nút "Thoát"
    cy.contains('a', 'Thoát').should('be.visible');

    // c. Nút "Đăng Nhập" phải biến mất
    cy.contains('a', 'Đăng Nhập').should('not.exist');

    // d. Khách hàng KHÔNG được thấy nút "Quản trị"
    cy.contains('a', 'Quản trị').should('not.exist');
  });

});