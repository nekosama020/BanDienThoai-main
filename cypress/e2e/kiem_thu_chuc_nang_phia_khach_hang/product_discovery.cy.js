describe('Chức năng Tìm kiếm & Xem chi tiết sản phẩm', () => {

  // Chạy trước mỗi test case: Vào trang chủ
  beforeEach(() => {
    // Đảm bảo URL này đúng với localhost của bạn
    cy.visit('/index.php');
  });

  // ======================================================
  // TEST CASE 1: KIỂM TRA HIỂN THỊ TRANG CHỦ
  // ======================================================
  it('Trang chủ hiển thị danh sách sản phẩm', () => {
    // Dựa vào code index.php: class="product-card"
    cy.get('.product-card').should('have.length.at.least', 1);

    // Kiểm tra ảnh sản phẩm
    cy.get('.product-image img').each(($img) => {
      cy.wrap($img).should('be.visible');
    });
  });

  // ======================================================
  // TEST CASE 2: CHỨC NĂNG TÌM KIẾM
  // ======================================================
  it('Tìm kiếm thành công với từ khóa có thật', () => {
    const keyword = 'Red Magic'; // Tên sản phẩm thực tế

    // 1. Nhập từ khóa
    cy.get('input[name="search"]').clear().type(`${keyword}{enter}`);

    // 2. Kiểm tra URL thay đổi
    cy.url().should('include', 'search=');

    // 3. Kiểm tra kết quả
    cy.get('.product-card h6').each(($el) => {
      const productName = $el.text().toLowerCase();
      expect(productName).to.contain(keyword.toLowerCase());
    });
  });

  it('Tìm kiếm thất bại với từ khóa không tồn tại', () => {
    const invalidKeyword = 'dien_thoai_xyz_123';

    // 1. Nhập từ khóa rác
    cy.get('input[name="search"]').clear().type(`${invalidKeyword}{enter}`);

    // 2. Kiểm tra kết quả: Không có thẻ .product-card nào hiện ra
    cy.get('.product-card').should('not.exist');
  });

  // ======================================================
  // TEST CASE 3: LỌC DANH MỤC (SIDEBAR) - ĐÃ SỬA
  // ======================================================
  it('Lọc sản phẩm theo danh mục Samsung', () => {
    // SỬA: Dùng .contains('Samsung') để tìm đúng nút có chữ Samsung
    // Lưu ý: Chữ "Samsung" phải viết hoa/thường y hệt như trên web của bạn
    cy.get('.col-md-2 button').contains('Samsung').click();

    // Kiểm tra URL
    cy.url().should('include', 'category=');

    // Kiểm tra có sản phẩm hiển thị
    cy.get('.product-card').should('have.length.at.least', 1);

    // (Kiểm tra nâng cao) Đảm bảo các sản phẩm hiện ra có tên Samsung
    cy.get('.product-card h6').each(($el) => {
       // Chuyển về chữ thường để so sánh cho chắc ăn
       expect($el.text().toLowerCase()).to.contain('samsung');
    });
  });

  // ======================================================
  // TEST CASE 4: XEM CHI TIẾT SẢN PHẨM
  // ======================================================
  it('Xem chi tiết: Tên và Giá hiển thị đúng', () => {
    // --- BƯỚC 1: Ở TRANG CHỦ ---
    // Lấy tên sản phẩm đầu tiên (Lấy CHỮ) để lát nữa so sánh
    cy.get('.product-card').first().find('h6')
        .invoke('text')   // <--- CHUẨN: Lấy nội dung text
        .as('homeName');  // <--- Lưu vào biến homeName

    // Click vào nút "Chi tiết"
    cy.get('.product-card').first().find('a.btn-info').click();

    // --- BƯỚC 2: Ở TRANG CHI TIẾT ---
    cy.url().should('include', 'product.php');

    // So sánh tên sản phẩm (Dùng contain.text để tránh lỗi khoảng trắng)
    cy.get('@homeName').then((homeName) => {
      cy.get('h2').should('contain.text', homeName.trim());
    });

    // Kiểm tra giá tiền hiển thị
    cy.get('h4.text-danger').should('be.visible');
    
    // Kiểm tra mô tả có nội dung
    cy.get('.col-md-7 p').should('exist');
    
    // Kiểm tra nút "Thêm vào giỏ hàng" có hiển thị
    cy.contains('button', 'Thêm vào giỏ hàng').should('be.visible');
  });

});