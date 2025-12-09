describe('Quy trình Thanh toán & Đặt hàng (Checkout Flow)', () => {

  const targetProduct = 'Red Magic'; // Tên sản phẩm đại diện để thêm vào giỏ

  // ======================================================
  // CHUẨN BỊ: ĐĂNG NHẬP & THÊM HÀNG VÀO GIỎ
  // ======================================================
  beforeEach(() => {
    // 1. Đăng nhập
    cy.session('customerAuth', () => {
      cy.visit('/login.php');
      cy.get('#loginEmail').type('dung2004@gmail.com'); // SỬA EMAIL CỦA BẠN
      cy.get('#loginPassword').type('10120204');             // SỬA PASS CỦA BẠN
      cy.get('button[name="login"]').click();
      cy.url().should('include', 'index.php');
    });

    // 2. Đảm bảo giỏ hàng có hàng (Nếu không Checkout sẽ lỗi)
    cy.visit('/index.php');
    // Tìm sản phẩm và bấm thêm vào giỏ
    cy.contains('.product-card', targetProduct).find('button[type="submit"]').click();
  });

  // ======================================================
  // TEST CASE: THANH TOÁN CHUYỂN KHOẢN (BANK TRANSFER)
  // ======================================================
  it('Thực hiện thanh toán Chuyển khoản và giả lập thành công', () => {
    
    // --- BƯỚC 1: VÀO GIỎ HÀNG -> SANG CHECKOUT ---
    cy.visit('/pages/cart.php');
    
    // Bấm nút "Thanh toán" ở trang giỏ hàng
    cy.contains('a', 'Thanh toán').click();

    // Kiểm tra đã vào đúng trang Checkout chưa
    cy.url().should('include', 'checkout.php');
    cy.contains('h2', 'Thanh toán').should('be.visible');

    // --- BƯỚC 2: CHỌN SẢN PHẨM ĐỂ MUA ---
    // Dựa vào code của bạn: checkbox có class="product-checkbox"
    // Chúng ta sẽ tick vào sản phẩm đầu tiên
    cy.get('.product-checkbox').first().check();

    // CÁCH 2: Lấy text ra, cắt khoảng trắng thừa, rồi so sánh
    cy.get('#totalPrice').invoke('text').then((text) => {
    expect(text.trim()).to.not.equal('0 đ');
    });


    // --- BƯỚC 3: CHỌN PHƯƠNG THỨC THANH TOÁN ---
    // Bạn muốn test "Chuyển khoản ngân hàng" -> value="Bank Transfer"
    cy.get('input[name="payment_method"][value="Bank Transfer"]').check();

    // --- BƯỚC 4: XÁC NHẬN THANH TOÁN ---
    cy.contains('button', 'Xác nhận thanh toán').click();

    // --- BƯỚC 5: MÀN HÌNH QUÉT QR (SAU KHI RELOAD) ---
    // Lúc này code PHP chạy lại và hiện phần QR Code
    cy.contains('h5', 'Quét QR để thanh toán').should('be.visible');
    
    // Kiểm tra ảnh QR có hiện không
    cy.get('img[alt="QR Code"]').should('be.visible');

    // --- BƯỚC 6: GIẢ LẬP THANH TOÁN THÀNH CÔNG ---
    // Click vào link giả lập mà bạn đã tạo
    // Link chứa href="fake_payment_success.php"
    cy.get('a[href*="fake_payment_success.php"]').click();
    cy.wait(3000);
    // --- BƯỚC 7: KIỂM TRA QUAY VỀ TRANG CHỦ ---
    // Bạn nói trang fake sẽ tự đẩy về trang chính
    // Ta kiểm tra xem URL có quay về index.php hay không
    cy.url().should('include', 'index.php');

    // (Tùy chọn) Kiểm tra thông báo thành công nếu có
    // cy.contains('thành công').should('be.visible');
  });

});