describe('Chức năng Quản lý Đơn hàng (Admin)', () => {

  // ======================================================
  // CHUẨN BỊ: ĐĂNG NHẬP -> DASHBOARD -> QUẢN LÝ ĐƠN HÀNG
  // ======================================================
  beforeEach(() => {
    // 1. Vào trang chủ và Login Admin
    cy.visit('/index.php');

    cy.get('body').then(($body) => {
      if ($body.find('a:contains("Đăng Nhập")').length > 0) {
        cy.contains('a', 'Đăng Nhập').click();
        cy.get('#loginEmail').type('20222274@eaut.edu.vn'); // Thay email Admin thật
        cy.get('#loginPassword').type('10120204');       // Thay pass Admin thật
        cy.get('button[name="login"]').click();
      }
    });

    // 2. Vào Dashboard
    cy.contains('a', 'Quản trị').should('be.visible').click();
    cy.url().should('include', 'admin/dashboard.php');

    // 3. Click menu "Quản lý Đơn hàng"
    cy.get('.menu-item[data-page*="manage_order.php"]').click();

    // 4. Chờ bảng đơn hàng hiện ra (ID: #orders-table)
    cy.get('#orders-table').should('be.visible');
  });

  // ======================================================
  // TEST CASE: XEM CHI TIẾT & CẬP NHẬT TRẠNG THÁI
  // ======================================================
  it('Xem chi tiết đơn hàng và Cập nhật trạng thái thành công', () => {
    
    // --- 1. KIỂM TRA DANH SÁCH ---
    // Đảm bảo có ít nhất 1 đơn hàng (Tìm các dòng có ID bắt đầu bằng order-row-)
    cy.get('tr[id^="order-row-"]').should('have.length.at.least', 1);

    // Lấy dòng đơn hàng đầu tiên để thao tác
    cy.get('tr[id^="order-row-"]').first().as('firstOrderRow');


    // --- 2. XEM CHI TIẾT ĐƠN HÀNG (TOGGLE) ---
    // Tìm nút "Xem chi tiết" trong dòng đó và click
    cy.get('@firstOrderRow').contains('button', 'Xem chi tiết').click();

    // Kiểm tra dòng chi tiết tương ứng hiện ra
    // Dòng chi tiết nằm ngay sau dòng chính, có ID bắt đầu bằng details-
    cy.get('@firstOrderRow').next('tr[id^="details-"]').should('be.visible');
    
    // Kiểm tra bảng con bên trong (danh sách sản phẩm)
    cy.get('@firstOrderRow').next().find('table').should('be.visible');

    // Click "Xem chi tiết" lần nữa để đóng lại
    cy.get('@firstOrderRow').contains('button', 'Xem chi tiết').click();
    cy.get('@firstOrderRow').next('tr[id^="details-"]').should('not.be.visible');


    // --- 3. CẬP NHẬT TRẠNG THÁI ---
    
    // Chọn Trạng thái đơn hàng: "Processing" (Đang xử lý)
    // Selector: select[id^="status-"] nằm trong dòng đầu tiên
    cy.get('@firstOrderRow').find('select[id^="status-"]').select('Processing');

    // Chọn Trạng thái thanh toán: "Paid" (Đã thanh toán)
    // Selector: select[id^="payment-"]
    cy.get('@firstOrderRow').find('select[id^="payment-"]').select('Paid');

    // Bấm nút "Lưu"
    cy.get('@firstOrderRow').contains('button', 'Lưu').click();


    // --- 4. KIỂM TRA KẾT QUẢ ---
    
    // Kiểm tra thông báo thành công (Alert)
    cy.on('window:alert', (text) => {
        expect(text).to.contains('thành công');
    });

    // Vì bạn dùng AJAX cập nhật tại chỗ (không reload trang),
    // ta kiểm tra giá trị trong ô select có giữ nguyên là "Processing" và "Paid" không
    cy.get('@firstOrderRow').find('select[id^="status-"]').should('have.value', 'Processing');
    cy.get('@firstOrderRow').find('select[id^="payment-"]').should('have.value', 'Paid');
  });

});