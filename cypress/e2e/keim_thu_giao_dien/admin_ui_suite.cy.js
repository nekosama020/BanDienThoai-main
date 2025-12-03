describe('Kiểm thử Giao diện Quản trị viên (Admin UI Suite)', () => {

  const loginUrl = 'http://localhost/BanDienThoai-main/admin/login.php'; // Hoặc login.php thường
  const dashboardUrl = 'http://localhost/BanDienThoai-main/admin/dashboard.php';

  // ======================================================
  // BƯỚC CHUẨN BỊ: ĐĂNG NHẬP ADMIN
  // ======================================================
  beforeEach(() => {
    cy.session('adminUISession', () => {
      // 1. Vào trang login (Nếu admin có trang login riêng thì sửa url)
      cy.visit('http://localhost/BanDienThoai-main/login.php'); 

      // 2. Nhập thông tin Admin (Theo thông tin bạn cung cấp)
      cy.get('#loginEmail').type('admin@admin.admin'); 
      cy.get('#loginPassword').type('10120204');
      
      // 3. Bấm đăng nhập
      cy.get('button[name="login"]').click();

      // 4. Nếu login ở trang thường, phải bấm nút "Quản trị" để vào Dashboard
      // (Nếu code bạn tự chuyển vào Dashboard thì bỏ dòng này)
      cy.get('body').then(($body) => {
        if ($body.find('a:contains("Quản trị")').length > 0) {
           cy.contains('a', 'Quản trị').click();
        }
      });

      // 5. Đảm bảo đã vào được Dashboard
      cy.url().should('include', 'admin/dashboard.php');
    });

    // Luôn bắt đầu test tại trang Dashboard
    cy.visit(dashboardUrl);
  });

  // ======================================================
  // 1. KIỂM THỬ SIDEBAR & BỐ CỤC CHUNG
  // ======================================================
  it('Dashboard: Sidebar màu tối và Menu hiển thị đúng', () => {
    // 1. Kiểm tra Sidebar bên trái
    // Màu nền #343a40 (Bootstrap dark) tương đương rgb(52, 58, 64)
    cy.get('.sidebar').should('be.visible')
      .and('have.css', 'background-color', 'rgb(52, 58, 64)');

    // 2. Kiểm tra các mục Menu quan trọng
    // Phải có đủ các link và chữ màu trắng (rgb(255, 255, 255))
    const menus = ['Quản lý Sản phẩm', 'Quản lý Danh mục', 'Quản lý Đơn hàng', 'Thống kê'];
    
    menus.forEach(menu => {
      cy.contains('.sidebar a', menu).should('be.visible')
        .and('have.css', 'color', 'rgb(255, 255, 255)');
    });

    // 3. Kiểm tra nút "Quay về trang chủ" (Nút 🏠)
    // Phải nằm cố định góc phải (fixed) và màu xanh lá (rgb(25, 135, 84))
    cy.get('a[title="Quay về trang chủ"]')
      .should('have.css', 'position', 'fixed')
      .and('have.css', 'background-color', 'rgb(25, 135, 84)');
  });

  // ======================================================
  // 2. KIỂM THỬ GIAO DIỆN QUẢN LÝ SẢN PHẨM
  // ======================================================
  it('Trang Sản phẩm: Bảng dữ liệu và Nút chức năng', () => {
    // Click menu để load trang sản phẩm (Ajax Load)
    cy.get('.menu-item[data-page*="manage_product.php"]').click();

    // 1. Kiểm tra nút "Thêm sản phẩm"
    // Màu xanh lá (btn-success)
    cy.contains('button', 'Thêm sản phẩm').should('be.visible')
      .and('have.css', 'background-color', 'rgb(25, 135, 84)');

    // 2. Kiểm tra Bảng sản phẩm (#productList)
    cy.get('table').should('be.visible');
    
    // 3. Kiểm tra Header của bảng (Màu tối .table-dark)
    cy.get('thead.table-dark').should('have.css', 'color', 'rgb(255, 255, 255)');

    // 4. Kiểm tra Nút Sửa (Màu xanh dương - btn-primary)
    // Chờ bảng load xong (có ít nhất 1 dòng)
    cy.get('#productList tr').should('have.length.at.least', 1);
    
    cy.get('#productList button:contains("Sửa")').first()
      .should('have.css', 'background-color', 'rgb(13, 110, 253)'); // Bootstrap primary

    // 5. Kiểm tra Nút Xóa (Màu đỏ - btn-danger)
    cy.get('#productList button:contains("Xóa")').first()
      .should('have.css', 'background-color', 'rgb(220, 53, 69)'); // Bootstrap danger
  });

  // ======================================================
  // 3. KIỂM THỬ GIAO DIỆN QUẢN LÝ ĐƠN HÀNG
  // ======================================================
  it('Trang Đơn hàng: Dropdown trạng thái và Màu sắc', () => {
    // Click menu Đơn hàng
    cy.get('.menu-item[data-page*="manage_order.php"]').click();

    // 1. Kiểm tra Bảng đơn hàng (#orders-table)
    cy.get('#orders-table').should('be.visible');

    // 2. Kiểm tra các ô Select (Dropdown chọn trạng thái)
    // Phải hiển thị rõ ràng
    cy.get('#orders-table select').should('exist');

    // 3. Kiểm tra Nút "Lưu" (Màu xanh dương) và "Xem chi tiết" (Màu xanh lơ - btn-info)
    // Màu btn-info: rgb(13, 202, 240)
    cy.get('#orders-table button:contains("Xem chi tiết")').first()
      .should('have.css', 'background-color', 'rgb(13, 202, 240)');
  });

  // ======================================================
  // 4. KIỂM THỬ RESPONSIVE (MOBILE VIEW)
  // ======================================================
  it('Giao diện Mobile: Sidebar phải ẩn đi', () => {
    // Giả lập màn hình iPhone X
    cy.viewport('iphone-x'); 
    cy.visit(dashboardUrl);

    // Dựa vào code dashboard.php của bạn: 
    // <nav class="col-md-2 d-none d-md-block sidebar ...">
    // Class 'd-none' nghĩa là ẩn trên mọi màn hình
    // Class 'd-md-block' nghĩa là chỉ hiện từ màn hình Medium (Tablet/PC) trở lên
    
    // => Trên Mobile, Sidebar phải KHÔNG hiển thị
    cy.get('.sidebar').should('not.be.visible');

    // Nội dung chính (col-md-10) phải tràn ra full màn hình
    cy.get('main').should('be.visible');
  });

});