import './bootstrap';

// Import Alpine.js for profile page and other interactions
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Import Flatpickr for calendars
import flatpickr from "flatpickr";
window.flatpickr = flatpickr;

// Import and initialize Bootstrap's JavaScript
import * as bootstrap from 'bootstrap';

// IMPORTANT: Expose Bootstrap to the global window object
window.bootstrap = bootstrap;