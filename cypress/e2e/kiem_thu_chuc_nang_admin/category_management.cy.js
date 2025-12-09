describe('Quản lý Danh mục (Theo luồng người dùng thực tế)', () => {

  const categoryName = 'Test Danh Muc ' + Date.now(); // Tên ngẫu nhiên
  const categoryNameUpdated = categoryName + ' (Da Sua)';

  // ======================================================
  // CHUẨN BỊ: ĐI TỪ TRANG CHỦ -> LOGIN -> DASHBOARD -> DANH MỤC
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
        cy.get('#loginEmail').type('20222274@eaut.edu.vn'); // SỬA EMAIL ADMIN
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

    // 6. Bấm menu "Quản lý Danh mục" bên sidebar
    cy.get('.menu-item[data-page*="manage_category.php"]').click();

    // 7. Chờ bảng danh mục load xong
    cy.get('#categoryList').should('be.visible');
  });

  // ======================================================
  // TEST CASE: THÊM -> SỬA -> XÓA
  // ======================================================
  it('Thêm danh mục mới, sau đó Sửa và cuối cùng Xóa sạch', () => {
    
// --- PHẦN 1: THÊM DANH MỤC (ADD) ---
    
    
    // 1. Click nút "Thêm danh mục"
    
    cy.contains('button', 'Thêm danh mục').click();
    
    
    // 2. Form hiện ra, nhập tên danh mục
    
    cy.get('#categoryFormContainer').should('be.visible');
    
    cy.get('#category_name').type(categoryName);
    
    
    // 3. Bấm Lưu
    
    cy.get('#categoryForm button[type="submit"]').click();
    
    
    // 4. Xử lý Alert & Chờ load
    
    cy.on('window:alert', (text) => {
    
       // Cypress tự động đóng alert
    
    });
    
    
    // QUAN TRỌNG: Chờ 1 giây để bảng danh mục kịp reload lại dòng mới
    
    cy.wait(1000);
    
    
    // 5. Kiểm tra danh mục mới đã xuất hiện trong bảng
    
    cy.contains('#categoryList tr', categoryName).should('be.visible');
    
    
    
    // --- PHẦN 2: SỬA DANH MỤC (EDIT) ---
    
    
    // 1. Tìm dòng chứa danh mục vừa tạo
    
    cy.contains('tr', categoryName).as('targetRow');
    
    
    // 2. Tìm nút "Sửa" trong dòng đó và click
    
    cy.get('@targetRow').contains('button', 'Sửa').click();
    
    
    // 3. Form hiện ra, nhập tên mới
    
    cy.get('#category_name').clear().type(categoryNameUpdated);
    
    
    // 4. Bấm Lưu
    
    cy.get('#categoryForm button[type="submit"]').click();
    
    
    // QUAN TRỌNG: Chờ 1 giây để code JS cập nhật lại tên mới trên giao diện
    
    cy.wait(1000);
    
    
    // 5. Kiểm tra tên trong bảng đã đổi thành tên mới
    
    cy.contains('#categoryList tr', categoryNameUpdated).should('be.visible');
    
    
    
    // --- PHẦN 3: XÓA DANH MỤC (DELETE) ---
    
    
    // 1. Tìm lại dòng chứa tên MỚI
    
    cy.contains('tr', categoryNameUpdated).as('newTargetRow');
    
    
    // 2. Tìm nút "Xóa" trong dòng đó và click
    
    cy.get('@newTargetRow').contains('button', 'Xóa').click();
    
    
    // 3. Xử lý hộp thoại xác nhận (Confirm) - Tự động bấm OK
    
    cy.on('window:confirm', () => true); 
    
    
    // QUAN TRỌNG: Chờ 1 giây để dòng đó bị xóa khỏi HTML
    
    cy.wait(1000);
    
    
    // 4. Kiểm tra danh mục đã biến mất hoàn toàn khỏi bảng
    
    cy.get('#categoryList').should('not.contain', categoryNameUpdated);
  });

});