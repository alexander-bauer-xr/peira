import initSimilarProjects from "../components/similar-projects";
import initGallery from '../components/gallery';
import initSubinfoToggle from '../components/subinfo';
import initToggleDates from '../components/termineToggle'; 

document.addEventListener('DOMContentLoaded', () => {
    if (window.galleryData) {
        initGallery(window.galleryData);
    }
    initSubinfoToggle();
    initSimilarProjects();
    initToggleDates();
});