import { insertTransparentVideo } from '../helpers/insertTransparentVideo';

export default function setupMenu() {
    const menu = document.getElementById('menu');
    const menuSvg = document.getElementById('menusvg');
    const clickaway = document.getElementById('clickaway');
    const logoContainer = document.querySelector('.logocontainer');
    const newsSection = document.getElementById('news');
    const menuToggles = [document.getElementById('mobileMenu'), document.getElementById('menu-interaction')];

    let isMenuOpen = false;

    function openMenu(event) {
        event.preventDefault();
        if (isMenuOpen) return closeMenu();

        isMenuOpen = true;

        menu.classList.remove('disappeardelayed');
        menu.classList.add('appeardelayed');

        menuSvg.classList.remove('disappear');
        menuSvg.classList.add('appear');

        if (window.innerHeight < 700 && window.innerWidth < 800) {
            if (logoContainer) logoContainer.style.display = 'none';
            if (newsSection) newsSection.style.display = 'none';
        }

        if (clickaway) clickaway.style.display = 'block';
    }

    function closeMenu() {
        isMenuOpen = false;

        menu.classList.remove('appeardelayed');
        menu.classList.add('disappeardelayed');

        menuSvg.classList.remove('appear');
        menuSvg.classList.add('disappear');

        if (clickaway) clickaway.style.display = 'none';
        if (logoContainer) logoContainer.style.display = '';
        if (newsSection) newsSection.style.display = '';
    }

    menuToggles.forEach(button => {
        if (button) button.addEventListener('click', openMenu);
    });

    if (clickaway) clickaway.addEventListener('click', closeMenu);

    insertTransparentVideo({
        containerSelector: '.close_img',
        videoId: 'menu_video',
        videoClass: 'close_img',
        fallbackImageId: 'webpPlayerMenu',
        sources: [
            { src: 'https://www.peira.space/img/seqmenu/menu.mov', type: 'video/mp4; codecs=hvc1' },
            { src: 'https://www.peira.space/img/seqmenu/menu.webm', type: 'video/webm' }
        ],
        timeout: 5000,
        onClick: openMenu,
        onTimeoutFallback: (img) => {
            img.src = 'https://peira.space/img/burger-menu.svg';
            img.style.setProperty('width', '60px', 'important');
        }
    });
}