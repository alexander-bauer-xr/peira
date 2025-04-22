import { insertTransparentVideo } from '../helpers/insertTransparentVideo';

export default function setupMenu() {
    const menu = document.getElementById('menu');
    const menuSvg = document.getElementById('menusvg');
    const clickaway = document.getElementById('clickaway');
    const mobileCloseMenu = document.getElementById('menuitemclose');
    const logoContainer = document.querySelector('.logocontainer');
    const newsSection = document.getElementById('news');
    const menuToggles = [document.getElementById('mobileMenu'), document.getElementById('menu-interaction')];

    let isMenuOpen = false;

    function toggleSvgAnimation(isOpening) {
        const isBigScreen = window.innerWidth > 500;

        const classes = isBigScreen
            ? ['appear', 'disappear']
            : ['appear-small', 'disappear-small'];

        menuSvg.classList.remove(classes[isOpening ? 1 : 0]);
        menuSvg.classList.add(classes[isOpening ? 0 : 1]);
    }

    function resetSvgAnimationClasses() {
        if (menuSvg && menu) {
            menuSvg.classList.remove('appear', 'disappear', 'appear-small', 'disappear-small');
            menuSvg.classList.remove('appeardelayed');
        }
    }

    function toggleContentVisibility(hide) {
        const displayValue = hide ? 'none' : '';
        if (logoContainer) logoContainer.style.display = displayValue;
        if (newsSection) newsSection.style.display = displayValue;
    }

    function openMenu(event) {
        event.preventDefault();
        if (isMenuOpen) return closeMenu();

        isMenuOpen = true;

        menu.classList.remove('disappeardelayed');
        menu.classList.add('appeardelayed');

        toggleSvgAnimation(true);

        if (window.innerHeight < 700 && window.innerWidth < 800) {
            toggleContentVisibility(true);
        }

        if (clickaway) clickaway.style.display = 'block';
    }

    function closeMenu() {
        isMenuOpen = false;

        menu.classList.remove('appeardelayed');
        menu.classList.add('disappeardelayed');

        toggleSvgAnimation(false);

        if (clickaway) clickaway.style.display = 'none';
        toggleContentVisibility(false);
    }

    menuToggles.forEach(button => {
        if (button) button.addEventListener('click', openMenu);
    });

    if (clickaway) clickaway.addEventListener('click', closeMenu);
    if (mobileCloseMenu) mobileCloseMenu.addEventListener('click', closeMenu);

    let svgResetTimeout;

    window.addEventListener('resize', () => {
        if (menuSvg && menu) {
            menuSvg.classList.add('hidden');
            menu.classList.add('hidden');
        }

        if (isMenuOpen) closeMenu();
        resetSvgAnimationClasses();

        clearTimeout(svgResetTimeout);
        svgResetTimeout = setTimeout(() => {
            if (menuSvg && menu) {
                menuSvg.classList.remove('hidden');
                menu.classList.remove('hidden', 'disappeardelayed');
            }
        }, 1500);
    });

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