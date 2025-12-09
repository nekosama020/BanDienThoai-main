describe('Chức năng Quản lý Sản phẩm (Admin)', () => {

  const productName = 'iPhone Test ' + Date.now(); // Tên ngẫu nhiên
  const productNameUpdated = productName + ' (Updated)';

  // ======================================================
  // BƯỚC CHUẨN BỊ: ĐĂNG NHẬP VÀ VÀO TRANG QUẢN LÝ
  // ======================================================
  beforeEach(() => {
    // 1. Vào trang chủ trước
    cy.visit('/index.php');

    // 2. Tìm và bấm nút "Đăng Nhập" trên thanh menu
    // (Nếu bạn chưa đăng nhập thì nút này mới hiện)
    cy.get('body').then(($body) => {
      if ($body.find('a:contains("Đăng Nhập")').length > 0) {
        cy.contains('a', 'Đăng Nhập').click();

        // --- Thực hiện Đăng nhập ---
        cy.get('#loginEmail').type('admin@admin.admin'); // SỬA EMAIL ADMIN
        cy.get('#loginPassword').type('10120204');       // SỬA PASS ADMIN
        cy.get('button[name="login"]').click();
      }
    });

    // 3. Đảm bảo đã quay về trang chủ sau khi login
    cy.url().should('include', 'index.php');

    // 4. Tìm và bấm nút "Quản trị" (Nút này chỉ hiện khi là Admin)
    cy.contains('a', 'Quản trị').should('be.visible').click();

    // 5. Kiểm tra đã vào Dashboard
    cy.url().should('include', 'admin/dashboard.php');

    // 6. Bấm menu "Quản lý Sản phẩm" bên sidebar
    // Dựa vào code dashboard.php: data-page="/.../manage_product.php"
    cy.get('.menu-item[data-page*="manage_product.php"]').click();

    // 7. Chờ bảng sản phẩm load xong (Đảm bảo AJAX chạy xong)
    cy.get('#productList').should('be.visible');
  });

  // ======================================================
  // TEST CASE: QUY TRÌNH THÊM -> SỬA -> XÓA
  // ======================================================
  it('Thêm mới, Sửa và Xóa sản phẩm thành công', () => {
    
    // --- PHẦN 1: THÊM SẢN PHẨM MỚI (ADD) ---
    
    // 1. Click nút "Thêm sản phẩm"
    // Code của bạn: <button onclick="toggleProductForm()">Thêm sản phẩm</button>
    cy.contains('button', 'Thêm sản phẩm').click();

    // 2. Kiểm tra Form hiện ra
    // Code của bạn: <div id="addProductForm">
    cy.get('#addProductForm').should('be.visible');

    // 3. Nhập thông tin sản phẩm
    // Code của bạn: form id="productForm"
    cy.get('#productForm input[name="name"]').type(productName);
    cy.get('#productForm input[name="price"]').type('20000000');
    cy.get('#productForm input[name="stock_quantity"]').type('10');
    
    // Chọn danh mục (Chọn option thứ 2 vì option 1 có thể rỗng hoặc disabled)
    cy.get('#productForm select[name="category_id"]').find('option').eq(1).then(option => {
        cy.get('#productForm select[name="category_id"]').select(option.val());
    });

    // Upload ảnh (Nếu có file trong cypress/fixtures)
    // cy.get('#productForm input[name="image"]').selectFile('cypress/fixtures/test.jpg');

    // 4. Bấm Lưu
    // Code của bạn: <button type="submit">Lưu</button> trong form #productForm
    cy.get('#productForm button[type="submit"]').click();

    // 5. Xử lý Alert thông báo thành công
    cy.on('window:alert', (text) => {
      // Cypress tự động đóng alert
    });

    // QUAN TRỌNG: Chờ 1 giây để bảng sản phẩm kịp reload lại dòng mới
    cy.wait(1000);

    // 6. Kiểm tra sản phẩm mới xuất hiện trong bảng
    // Code JS: loadProducts() sẽ cập nhật #productList
    cy.contains('#productList tr', productName).should('be.visible');


    // --- PHẦN 2: SỬA SẢN PHẨM (EDIT) ---

    // 1. Tìm dòng chứa sản phẩm vừa tạo
    cy.contains('tr', productName).as('targetRow');

    // 2. Click nút Sửa trong dòng đó
    // Lưu ý: Nút sửa gọi hàm toggleEditProductForm
    cy.get('@targetRow').contains('button', 'Sửa').click();

    // 3. Kiểm tra Form Sửa hiện ra
    // Code của bạn: <div id="editProductDiv">
    cy.get('#editProductDiv').should('be.visible');
    
    // Kiểm tra tên cũ đã được điền vào form
    cy.get('#editProductForm input[name="name"]').should('have.value', productName);

    // 4. Nhập tên mới và giá mới
    cy.get('#editProductForm input[name="name"]').clear().type(productNameUpdated);
    cy.get('#editProductForm input[name="price"]').clear().type('25000000');

    // 5. Bấm Lưu
    cy.get('#editProductForm button[type="submit"]').click();

    // QUAN TRỌNG: Chờ 1 giây để code JS cập nhật lại tên mới trên giao diện
    cy.wait(1000);

    // 6. Kiểm tra tên mới đã được cập nhật trong bảng
    cy.contains('#productList tr', productNameUpdated).should('be.visible');


    // --- PHẦN 3: XÓA SẢN PHẨM (DELETE) ---

    // 1. Tìm lại dòng chứa tên MỚI
    cy.contains('tr', productNameUpdated).as('newTargetRow');

    // 2. Click nút Xóa
    // Code của bạn: <button onclick="deleteProduct(...)">Xóa</button>
    cy.get('@newTargetRow').contains('button', 'Xóa').click();

    // 3. Xử lý hộp thoại xác nhận (Confirm)
    // Code JS: confirm('Xác nhận xóa sản phẩm này?')
    cy.on('window:confirm', () => true); // Tự động bấm OK

    // QUAN TRỌNG: Chờ 1 giây để dòng đó bị xóa khỏi HTML
    cy.wait(1000);

    // 4. Kiểm tra sản phẩm đã biến mất hoàn toàn
    cy.get('#productList').should('not.contain', productNameUpdated);
  });

});