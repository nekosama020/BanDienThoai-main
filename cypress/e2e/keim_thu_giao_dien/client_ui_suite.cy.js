describe('Kiểm thử Giao diện Khách hàng (Đã Đăng nhập)', () => {

  const homeUrl = '/index.php';
  const loginUrl = '/login.php'; // Hoặc login_register.php

  // ======================================================
  // BƯỚC CHUẨN BỊ: ĐĂNG NHẬP KHÁCH HÀNG
  // ======================================================
  beforeEach(() => {
    // 1. Thực hiện đăng nhập và lưu phiên (Session)
    cy.session('userUISession', () => {
      cy.visit(loginUrl);

      // SỬA: Thay thông tin tài khoản Khách hàng thật của bạn vào đây
      cy.get('#loginEmail').type('dung02042004@gmail.com'); 
      cy.get('#loginPassword').type('10120204A');
      
      cy.get('button[name="login"]').click();

      // Đảm bảo login thành công (quay về trang chủ)
      cy.url().should('include', 'index.php');
    });

    // 2. Sau khi login xong, luôn quay về trang chủ để bắt đầu test giao diện
    cy.visit(homeUrl);
  });

  // ======================================================
  // 1. KIỂM THỬ HEADER & NAVIGATION
  // ======================================================
  it('Header: Logo, Menu, và Thông tin tài khoản', () => {
    // 1. Kiểm tra Navbar nền tối
    cy.get('nav.navbar').should('be.visible')
      .and('have.css', 'background-color', 'rgb(33, 37, 41)');

    // 2. Kiểm tra Logo
    cy.get('.navbar-brand').should('be.visible');

    // 3. Kiểm tra Thanh tìm kiếm
    cy.get('form[action*="index.php"] input[name="search"]')
      .should('be.visible')
      .and('have.attr', 'placeholder', 'Tìm kiếm sản phẩm...');
    
    // 4. Kiểm tra hiển thị tên người dùng (Vì đã đăng nhập)
    // Dựa vào code header.php: <span class="text-white me-2">Hi, ...</span>
    cy.contains('span', 'Hi,').should('be.visible');

    // 5. Kiểm tra nút "Thoát" thay vì "Đăng nhập"
    cy.contains('a', 'Thoát').should('be.visible');
    
    // 6. Kiểm tra Nút "Giỏ hàng" (Màu xanh lá)
    cy.contains('a.btn-success', 'Giỏ Hàng')
      .should('be.visible')
      .and('have.css', 'background-color', 'rgb(25, 135, 84)');
  });

  // ======================================================
  // 2. KIỂM THỬ TRANG CHỦ (PRODUCT GRID)
  // ======================================================
  it('Trang chủ: Bố cục lưới sản phẩm', () => {
    // 1. Kiểm tra Sidebar danh mục (Màu nền sáng)
    cy.get('.col-md-2.bg-light').should('be.visible')
      .and('have.css', 'background-color', 'rgb(248, 249, 250)');

    // 2. Kiểm tra Thẻ sản phẩm (.product-card)
    cy.get('.product-card').first().should('be.visible')
      .and('have.css', 'border-style', 'solid'); // Có viền

    // 3. Kiểm tra Ảnh sản phẩm căn giữa
    cy.get('.product-image').first().should('have.css', 'display', 'flex')
      .and('have.css', 'justify-content', 'center');

    // 4. Kiểm tra Giá tiền (Màu đỏ)
    cy.get('.product-card .text-danger').first()
      .should('have.css', 'color', 'rgb(220, 53, 69)');
  });

  // ======================================================
  // 3. KIỂM THỬ TRANG CHI TIẾT
  // ======================================================
  it('Trang Chi tiết: Bố cục thông tin sản phẩm', () => {
    // Click vào sản phẩm đầu tiên
    cy.get('.product-card a.btn-info').first().click();

    // 1. Kiểm tra Ảnh (Cột 5) và Thông tin (Cột 7)
    cy.get('.col-md-5 img').should('be.visible');
    cy.get('.col-md-7 h2').should('be.visible');

    // 2. Kiểm tra Font chữ tên sản phẩm (Lớn hơn 20px)
    cy.get('h2').invoke('css', 'font-size').then((fontSize) => {
        expect(parseFloat(fontSize)).to.be.gte(20); 
    });

    // 3. Kiểm tra Giá tiền (Đỏ)
    cy.get('h4.text-danger').should('have.css', 'color', 'rgb(220, 53, 69)');

    // 4. Kiểm tra Nút Mua hàng (Xanh lá)
    cy.contains('button', 'Thêm vào giỏ hàng')
      .should('have.css', 'background-color', 'rgb(25, 135, 84)');
  });

  // ======================================================
  // 4. KIỂM THỬ TRANG GIỎ HÀNG
  // ======================================================
  it('Trang Giỏ hàng: Bảng và Nút thao tác', () => {
    // Thêm hàng để có giao diện test
    cy.get('.product-card button[type="submit"]').first().click();
    cy.visit('/pages/cart.php');

    // 1. Kiểm tra Bảng
    cy.get('table.table').should('be.visible');
    
    // 2. Tiêu đề bảng in đậm
    cy.get('thead th').first().should('have.css', 'font-weight').and('match', /700|bold/);

    // 3. Nút Cập nhật (Vàng)
    cy.contains('button', 'Cập nhật').should('be.visible')
      .and('have.css', 'background-color', 'rgb(255, 193, 7)');

    // 4. Nút Xóa (Đỏ)
    cy.contains('button', 'Xóa khỏi giỏ hàng').should('be.visible')
      .and('have.css', 'background-color', 'rgb(220, 53, 69)');
  });

  // ======================================================
  // 5. KIỂM THỬ CHATBOT (UI)
  // ======================================================
  it('Chatbot: Vị trí và Màu sắc', () => {
    // 1. Kiểm tra Nút mở chat (Xanh dương, Cố định)
    cy.get('#chat-circle')
      .should('have.css', 'position', 'fixed')
      .and('have.css', 'background-color', 'rgb(13, 110, 253)');

    // 2. Mở chat -> Header màu xanh dương
    cy.get('#chat-circle').click();
    cy.get('.c-head')
      .should('have.css', 'background-color', 'rgb(13, 110, 253)');
  });

  // ======================================================
  // 6. KIỂM THỬ RESPONSIVE (MOBILE)
  // ======================================================
  it('Giao diện Mobile: Menu thu gọn', () => {
    cy.viewport('iphone-x'); 
    cy.visit(homeUrl);

    // 1. Menu Bar ẩn
    cy.get('#navbarNav').should('not.be.visible');

    // 2. Nút Hamburger hiện
    cy.get('.navbar-toggler').should('be.visible');

    // 3. Bấm mở menu -> Thấy nút "Thoát" (vì đã login)
    cy.get('.navbar-toggler').click();
    cy.contains('a', 'Thoát').should('be.visible');
  });

});