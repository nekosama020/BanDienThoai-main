// ***********************************************************
// This example support/e2e.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import commands.js using ES2015 syntax:
import './commands'
// cypress/support/e2e.js

// Import thư viện đúng tên
import { slowCypressDown } from 'cypress-slow-down';

// Kích hoạt làm chậm (1 giây cho mỗi hành động)
slowCypressDown(200);