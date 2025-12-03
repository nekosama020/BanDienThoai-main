describe('Chức năng Hồ sơ cá nhân & Lịch sử đơn hàng', () => {

  // ======================================================
  // CHUẨN BỊ: ĐĂNG NHẬP KHÁCH HÀNG
  // ======================================================
  beforeEach(() => {
    cy.session('customerAuth', () => {
      cy.visit('http://localhost/BanDienThoai-main/login.php');
      cy.get('#loginEmail').type('dung2004@gmail.com'); // SỬA EMAIL CỦA BẠN
      cy.get('#loginPassword').type('10120204');             // SỬA PASS CỦA BẠN
      cy.get('button[name="login"]').click();
      cy.url().should('include', 'index.php');
    });
  });

  // ======================================================
  // CASE 1: CẬP NHẬT THÔNG TIN CÁ NHÂN (profile.php)
  // ======================================================
  it('Cập nhật Email, SĐT và Địa chỉ thành công', () => {
    // 1. Vào trang hồ sơ
    cy.visit('http://localhost/BanDienThoai-main/pages/profile.php');

    // Tạo dữ liệu ngẫu nhiên để đảm bảo mỗi lần chạy là thông tin mới
    const randomNum = Math.floor(Math.random() * 1000);
    const newPhone = '09' + Math.floor(Math.random() * 100000000); // SĐT ngẫu nhiên
    const newAddress = `Địa chỉ mới số ${randomNum}, Hà Nội`;
    // Lưu ý: Email không nên đổi lung tung nếu bạn dùng nó để đăng nhập lần sau. 
    // Nếu muốn test đổi email, hãy chắc chắn bạn nhớ email mới. 
    // Ở đây mình giữ nguyên email cũ hoặc chỉ đổi nếu cần thiết.
    
    // 2. Kiểm tra ô Username bị vô hiệu hóa (disabled)
    // Dựa vào code: <input ... disabled>
    cy.contains('label', 'Tên đăng nhập').next('input').should('be.disabled');

    // 3. Nhập thông tin mới
    // Dựa vào code: name="phone", name="address"
    cy.get('input[name="phone"]').clear().type(newPhone);
    cy.get('input[name="address"]').clear().type(newAddress);
    
    // (Tùy chọn) Đổi email nếu bạn muốn test
    // cy.get('input[name="email"]').clear().type(`khachhang${randomNum}@example.com`);

    // 4. Bấm nút "Lưu thay đổi"
    cy.contains('button', 'Lưu thay đổi').click();

    // 5. Kiểm tra thông báo thành công
    // Dựa vào code: <div class="alert alert-success">
    cy.get('.alert-success').should('be.visible')
      .and('contain', 'Cập nhật thông tin thành công');

    // 6. Kiểm tra lại giá trị trong ô input xem đã lưu đúng chưa
    cy.get('input[name="phone"]').should('have.value', newPhone);
    cy.get('input[name="address"]').should('have.value', newAddress);
  });

  it('Báo lỗi nếu nhập Email không hợp lệ', () => {
    // 1. Vào trang profile
    cy.visit('http://localhost/BanDienThoai-main/pages/profile.php');

    // 2. Tắt tính năng kiểm tra email của trình duyệt (để PHP có cơ hội bắt lỗi)
    cy.get('form').invoke('attr', 'novalidate', true);

    // 3. Nhập email sai
    cy.get('input[name="email"]').clear().type('email_khong_hop_le');

    // 4. Bấm Lưu
    cy.contains('button', 'Lưu thay đổi').click();

    // 5. Kiểm tra thông báo lỗi từ PHP
    cy.get('.alert-danger').should('be.visible')
      .and('contain', 'Email không hợp lệ');
  });

  // ======================================================
  // CASE 2: XEM LỊCH SỬ ĐƠN HÀNG (order.php)
  // ======================================================
  it('Truy cập trang Lịch sử đơn hàng từ Trang chủ', () => {
    // 1. Vào trang chủ
    cy.visit('http://localhost/BanDienThoai-main/index.php');

    // 2. Tìm và click vào nút "Đơn hàng" trên thanh menu
    // Dựa vào code index.php: <a href="...pages/order.php">Đơn hàng</a>
    cy.contains('a', 'Đơn hàng').click();

    // 3. Kiểm tra đã chuyển sang trang order.php chưa
    cy.url().should('include', 'pages/order.php');

    // 4. Kiểm tra nội dung cơ bản của trang Đơn hàng
    // Giả sử trang order.php có tiêu đề là "Lịch sử đơn hàng" hoặc có bảng <table>
    // Bạn có thể sửa 'h2' hoặc 'table' tùy theo code thực tế trong order.php
    cy.get('body').then(($body) => {
        if ($body.find('table').length > 0) {
            cy.get('table').should('be.visible'); // Nếu có bảng thì check bảng
        } else {
            cy.contains('chưa có đơn hàng').should('exist'); // Nếu không có bảng thì check thông báo trống
        }
    });
  });

});