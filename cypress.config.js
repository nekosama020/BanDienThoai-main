const { defineConfig } = require("cypress");

module.exports = defineConfig({
  e2e: {
    // Đây là địa chỉ gốc trên máy bạn (XAMPP)
    baseUrl: 'http://localhost/BanDienThoai-main',
    
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },
});
