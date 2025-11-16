import './bootstrap';
import setupMenu from './components/menu.js';
import { initCookiePreferences } from './helpers/cookiePreferences.js';

function setViewportHeight() {
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
}

setViewportHeight();
window.addEventListener('resize', setViewportHeight);
window.addEventListener('orientationchange', () => {
    setTimeout(setViewportHeight, 100);
});

document.addEventListener('DOMContentLoaded', () => {
    setupMenu();
    initCookiePreferences();
});