describe('Chức năng Chatbot AI', () => {

  // Chạy trước mỗi test
  beforeEach(() => {
    // 1. Giả lập (Mock) phản hồi từ Server CÓ ĐỘ TRỄ
    cy.intercept('POST', '**/chat_server.php', (req) => {
      req.reply({
        statusCode: 200,
        body: {
          reply: "Đây là câu trả lời tự động từ Cypress!" 
        },
        delay: 1000 // <--- QUAN TRỌNG: Giả vờ server mạng lag 1 giây
      });
    }).as('chatAPI');

    // 2. Vào trang chủ
    cy.visit('/index.php');
  });

  // ======================================================
  // CASE 1: MỞ VÀ ĐÓNG CHATBOT
  // ======================================================
  it('Mở và Đóng cửa sổ Chatbot thành công', () => {
    // 1. Kiểm tra nút tròn mở chat
    cy.get('#chat-circle').should('be.visible');

    // 2. Click mở chat
    cy.get('#chat-circle').click();

    // 3. Kiểm tra khung chat hiện ra
    cy.get('#chat-box').should('be.visible');

    // 4. Kiểm tra tin nhắn chào mừng
    cy.contains('.msg.bot', 'Chào bạn!').should('be.visible');

    // 5. Đóng chat bằng nút X
    cy.get('.c-head span').contains('✖').click();

    // 6. Kiểm tra khung chat đã ẩn
    cy.get('#chat-box').should('not.be.visible');
  });

  // ======================================================
  // CASE 2: GỬI TIN NHẮN VÀ NHẬN PHẢN HỒI
  // ======================================================
  it('Gửi tin nhắn và nhận phản hồi từ Bot', () => {
    const myMessage = 'Xin chào, tôi muốn mua điện thoại';

    // 1. Mở chat
    cy.get('#chat-circle').click();

    // 2. Nhập tin nhắn
    cy.get('#c-inp').type(myMessage);

    // 3. Nhấn nút Gửi
    // Lưu ý: Nút gửi là nút cuối cùng trong .c-foot
    cy.get('.c-foot button').last().click();

    // 4. Kiểm tra tin nhắn của mình đã hiện lên
    cy.contains('.msg.user', myMessage).should('be.visible');

    // 5. Kiểm tra trạng thái "Đang suy nghĩ..." xuất hiện
    // (Bây giờ nó sẽ hiện ra trong 1 giây nên Cypress sẽ bắt được)
    cy.contains('.msg.bot', 'Đang suy nghĩ...').should('be.visible');

    // 6. Đợi API giả lập trả về
    cy.wait('@chatAPI');

    // 7. Kiểm tra Bot đã trả lời câu giả định
    cy.contains('.msg.bot', 'Đây là câu trả lời tự động từ Cypress!').should('be.visible');
  });

  // ======================================================
  // CASE 3: TẢI ẢNH LÊN (PREVIEW)
  // ======================================================
  it('Tải ảnh lên và hiển thị xem trước (Preview)', () => {
    cy.get('#chat-circle').click();

    // Upload ảnh vào input ẩn
    // Tạo file ảnh giả
    cy.get('#img-input').selectFile({
      contents: Cypress.Buffer.from('fake image content'),
      fileName: 'test.jpg',
      mimeType: 'image/jpeg',
    }, { force: true });

    // Kiểm tra khung xem trước hiện ra
    cy.get('#preview-area').should('be.visible');

    // Kiểm tra ảnh thumb hiện ra
    cy.get('#img-preview').should('be.visible');

    // Thử nút xóa ảnh
    cy.get('.btn-close-img').click();

    // Kiểm tra khung xem trước ẩn đi
    cy.get('#preview-area').should('not.be.visible');
  });

  // ======================================================
  // CASE 4: KIỂM TRA NÚT MICRO
  // ======================================================
  it('Hiển thị nút Micro', () => {
    cy.get('#chat-circle').click();
    cy.get('#btn-mic').should('be.visible');
  });

});