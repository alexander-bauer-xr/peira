import initGallery from '../components/gallery';
import initSubinfoToggle from '../components/subinfo';

document.addEventListener('DOMContentLoaded', () => {
    if (window.galleryData) {
        initGallery(window.galleryData);
    }
    initSubinfoToggle();
});