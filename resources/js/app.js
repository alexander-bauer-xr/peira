import './bootstrap';
import setupMenu from './components/menu.js';
import { initCookiePreferences } from './helpers/cookiePreferences.js';

// Fix for mobile address bar causing layout shifts
function setViewportHeight() {
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
}

// Set on load and resize
setViewportHeight();
window.addEventListener('resize', setViewportHeight);
// Also update on orientation change for mobile devices
window.addEventListener('orientationchange', () => {
    setTimeout(setViewportHeight, 100);
});

document.addEventListener('DOMContentLoaded', () => {
    setupMenu();
    initCookiePreferences();
});