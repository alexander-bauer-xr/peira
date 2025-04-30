import initGallery from '../components/gallery';

document.addEventListener('DOMContentLoaded', () => {
    if (window.galleryData) {
        initGallery(window.galleryData);
    }
});