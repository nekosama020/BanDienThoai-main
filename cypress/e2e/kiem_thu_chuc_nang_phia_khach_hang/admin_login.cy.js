describe('Kiểm thử chức năng Đăng nhập / Đăng ký (Customer)', () => {

// HÀM NÀY SẼ CHẠY TRƯỚC MỖI TEST CASE
  beforeEach(() => {
    // --- [SỬA ĐỔI QUAN TRỌNG NHẤT] ---
    // Thay vì viết full URL 'http://localhost/...', ta chỉ viết phần đuôi.
    // Cypress sẽ tự động ghép với 'baseUrl' được cấu hình trong cypress.config.js (ở máy bạn)
    // hoặc biến môi trường CYPRESS_baseUrl (trên GitHub Actions).
    cy.visit('/login.php'); 
  });

  // ========== BỘ TEST CHO CHỨC NĂNG ĐĂNG NHẬP ==========

  it('Đăng nhập thành công với tài khoản hợp lệ', () => {
    // 2. THAY THẾ email/password bằng một tài khoản Customer CÓ THẬT trong database của bạn
    const email = 'dung02042004@gmail.com';
    const password = '10120204A';

    // Dựa theo ID trong HTML của bạn
    cy.get('#loginEmail').type(email);
    cy.get('#loginPassword').type(password);

    // Dựa theo name="login" trong HTML
    cy.get('form:has(#loginEmail) button[name="login"]').click();

    // Kiểm tra kết quả: Chuyển hướng đến index.php
    cy.url().should('include', 'index.php');
  });

  it('Đăng nhập thất bại với mật khẩu sai', () => {
    const email = 'dung02042004@gmail.com'; // Dùng email đúng
    const password = 'saipassthedoc';      // Mật khẩu cố tình sai

    cy.get('#loginEmail').type(email);
    cy.get('#loginPassword').type(password);
    cy.get('form:has(#loginEmail) button[name="login"]').click();

    // Kiểm tra kết quả:
    // 1. Vẫn ở lại trang đăng nhập
    cy.url().should('include', 'login.php');

    // 2. Hiển thị thông báo lỗi (dựa theo class .alert-danger)
    cy.get('.alert-danger').should('be.visible');

    // 3. Kiểm tra nội dung lỗi (dựa theo $error trong PHP)
    cy.get('.alert-danger').should('contain', 'Mật khẩu không đúng!');
  });

  it('Đăng nhập thất bại với email không tồn tại', () => {
    cy.get('#loginEmail').type('emailkhongtontai@abc.com');
    cy.get('#loginPassword').type('123456');
    cy.get('form:has(#loginEmail) button[name="login"]').click();

    // Kiểm tra nội dung lỗi (dựa theo $error trong PHP)
    cy.get('.alert-danger').should('be.visible');
    cy.get('.alert-danger').should('contain', 'Email không tồn tại!');
  });

  // ========== BỘ TEST CHO CHỨC NĂNG ĐĂNG KÝ ==========

it('Đăng ký tài khoản mới thành công (Bypass OTP)', () => {
    cy.visit('/login.php');
    cy.get('a[href="#register"]').click();

    // Dùng email thần thánh
    const magicEmail = 'test_auto@gmail.com'; 
    const randomUsername = `UserTest_${Date.now()}`;

    cy.get('#registerUsername').type(randomUsername);
    cy.get('#registerEmail').type(magicEmail);
    cy.get('#registerPassword').type('123456');

    // Bấm Đăng ký -> PHP sẽ tự động bỏ qua gửi mail và chuyển sang trang nhập OTP
    cy.get('form:has(#registerEmail) button[name="register"]').click();

    // Kiểm tra đã sang trang nhập OTP
    cy.url().should('include', 'xac_thuc_dang_ky.php');

    // Nhập mã OTP thần thánh
    cy.get('input[name="otp"]').type('123456');
    cy.get('button[name="xac_nhan"]').click();

    // Kiểm tra kết quả thành công
    cy.on('window:alert', (text) => {
        expect(text).to.contain('thành công');
    });
    
    // Web sẽ tự chuyển về index
    cy.url().should('include', 'index.php');
  });

  it('Đăng ký thất bại khi email đã tồn tại', () => {
    // 3. Dùng lại email CÓ THẬT ở test đăng nhập
    const existingEmail = 'dung02042004@gmail.com';

    // Chuyển sang tab đăng ký
    cy.get('a[href="#register"]').click();

    cy.get('#registerUsername').type('Nguoi Khac');
    cy.get('#registerEmail').type(existingEmail); // Cố tình dùng email đã tồn tại
    cy.get('#registerPassword').type('123456');
    cy.get('form:has(#registerEmail) button[name="register"]').click();

    // Kiểm tra kết quả:
    // 1. Vẫn ở lại trang
    cy.url().should('include', 'login.php');

  });
});