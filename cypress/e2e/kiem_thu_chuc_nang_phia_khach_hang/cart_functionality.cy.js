describe('Chức năng Giỏ hàng (Tập trung vào iPhone 14 Pro Max)', () => {

  // TÊN SẢN PHẨM MỤC TIÊU (Sửa lại cho đúng y hệt trên web bạn)
  const targetProduct = 'iPhone 14 Pro Max'; 

  // ======================================================
  // BƯỚC 1: ĐĂNG NHẬP & DỌN DẸP
  // ======================================================
  beforeEach(() => {
    // 1. Đăng nhập
    cy.session('customerAuth', () => {
      cy.visit('/login.php');
      cy.get('#loginEmail').type('dung2004@gmail.com'); // Thay email thật
      cy.get('#loginPassword').type('10120204');             // Thay pass thật
      cy.get('button[name="login"]').click();

      cy.url().should('include', 'index.php');
    });

    // 2. Vào trang chủ
    cy.visit('/index.php');
  });

  // ======================================================
  // CASE 1: THÊM IPHONE 14 PRO MAX VÀO GIỎ
  // ======================================================
  it(`Thêm ${targetProduct} vào giỏ và kiểm tra hiển thị`, () => {
    // 1. Tìm thẻ .product-card nào có chứa tên sản phẩm thì click nút Thêm
    // Đây là lệnh quan trọng nhất để chọn đúng sản phẩm
    cy.contains('.product-card', targetProduct)
      .find('button[type="submit"]')
      .click();

    // 2. Vào giỏ hàng
    cy.visit('/pages/cart.php');

    // 3. Kiểm tra trong bảng giỏ hàng phải có tên sản phẩm đó
    cy.get('table tbody').should('contain', targetProduct);
  });

  // ======================================================
  // CASE 2: TEST CẬP NHẬT (KIỂM TRA LỖI BIẾN MẤT)
  // ======================================================
  it(`Cập nhật số lượng ${targetProduct} lên 5`, () => {
    // --- CHUẨN BỊ ---
    cy.contains('.product-card', targetProduct).find('button[type="submit"]').click();
    cy.visit('/pages/cart.php');

    // Tìm dòng chứa sản phẩm
    cy.contains('tr', targetProduct).as('targetRow');

    // Lấy tổng tiền ban đầu
    cy.get('h4 strong').invoke('text').as('initialTotal');

    // --- THỰC HIỆN CẬP NHẬT ---
    cy.get('@targetRow').find('input[name="quantity"]').clear().type('5');
    cy.get('@targetRow').contains('button', 'Cập nhật').click();

    // --- CHỐT CHẶN KIỂM TRA LỖI ---
    
    // 1. Kiểm tra xem sản phẩm có còn tồn tại không?
    // Nếu dòng này báo lỗi đỏ -> Code PHP của bạn đang xóa nhầm sản phẩm!
    cy.contains('tr', targetProduct).should('exist');

    // 2. Kiểm tra ô số lượng có hiện số 5 không?
    cy.contains('tr', targetProduct).find('input[name="quantity"]')
      .should('have.value', '5');

    // 3. Kiểm tra giá tiền thay đổi
    cy.get('h4 strong').invoke('text').then((newTotal) => {
      cy.get('@initialTotal').then((initialTotal) => {
        expect(newTotal).to.not.equal(initialTotal);
      });
    });
  });
  // ======================================================
  // CASE 3: XÓA IPHONE 14 PRO MAX
  // ======================================================
  it(`Xóa ${targetProduct} khỏi giỏ hàng`, () => {
    // --- BƯỚC CHUẨN BỊ: THÊM HÀNG ---
    cy.contains('.product-card', targetProduct).find('button[type="submit"]').click();
    cy.visit('/pages/cart.php');

    // --- BẮT ĐẦU TEST ---
    // 1. Kiểm tra chắc chắn sản phẩm đang có trong giỏ
    cy.contains('table tbody tr', targetProduct).should('be.visible');

    // 2. Tìm đúng dòng chứa iPhone 14 Pro Max và bấm nút Xóa
    cy.contains('tr', targetProduct)
      .contains('button', 'Xóa khỏi giỏ hàng')
      .click();

    // 3. Kiểm tra kết quả: Sản phẩm đó không còn tồn tại trong bảng nữa
    cy.get('body').then(($body) => {
      // Nếu giỏ hàng trống (xóa hết sạch) -> check chữ "trống"
      if ($body.text().includes('trống')) {
        cy.contains('Giỏ hàng trống').should('be.visible');
      } else {
        // Nếu vẫn còn món khác -> check xem iPhone 14 Pro Max đã mất chưa
        cy.get('table tbody').should('not.contain', targetProduct);
      }
    });
  });

});