import initNewsletterForm from '../components/newsletter';
import { insertTransparentVideo } from '../helpers/insertTransparentVideo';

window.subscribenews = initNewsletterForm;
document.addEventListener('DOMContentLoaded', () => {

    // --- News Toggle Logic ---
    document.querySelectorAll('.newsitem').forEach((item) => {
        item.addEventListener('click', () => {
            const id = item.id.replace('news-', '');
            const detail = document.getElementById(`news-detail-${id}`);
            const button = document.getElementById(`news-button-${id}`);

            detail.style.display = 'block';

            if (detail.classList.contains('out')) {
                detail.classList.remove('out');
                detail.classList.add('active');
                button.src = '/img/nav/Arrow_close_red.png';
                button.scrollIntoView();
            } else {
                detail.classList.remove('active');
                detail.classList.add('out');
                button.src = '/img/nav/Arrow_open_red.png';

                setTimeout(() => {
                    detail.style.display = 'none';
                }, 500);
            }
        });
    });

    document.getElementById('newslink').addEventListener('click', function (e) {
        const news = document.getElementById('news');
        const logo = document.querySelector('.logocontainer');
        const email = document.querySelector('.emailregister');
        const newslink = document.getElementById('newslink');

        [news, logo, email].forEach(el => {
            if (el) {
                el.style.display = (el.style.display === 'none' || getComputedStyle(el).display === 'none') ? 'block' : 'none';
            }
        });

        if (news && getComputedStyle(news).display !== 'none') {
            newslink.innerHTML = "Peira";
        } else {
            newslink.innerHTML = "News";
        }

        if (typeof menu !== "undefined" && menu === true) {
            const menuEl = document.getElementById('menu');
            const menusvg = document.getElementById('menusvg');

            if (menuEl) {
                menuEl.classList.remove('appeardelayed');
                menuEl.classList.add('disappeardelayed');
            }
            if (menusvg) {
                menusvg.classList.remove('beginright', 'appear');
                menusvg.classList.add('disappear');
            }

            menu = false;
        }
    });

    function handleResize() {
        const width = window.innerWidth;
        const height = window.innerHeight;
      
        const logo = document.querySelector('.logocontainer');
        const news = document.getElementById('news');
        const email = document.querySelector('.emailregister')
      
        if (width < 670 && height < 700) {
          if (logo) logo.style.display = 'none';
          if (news) news.style.display = 'none';
        } else {
          if (logo) logo.style.display = '';
          if (news) news.style.display = '';
          if(email) email.style.display = '';
        }
      }
      
      handleResize();
      
      window.addEventListener('resize', handleResize);      

    insertTransparentVideo({
        containerSelector: '.bg',
        videoId: 'back_video',
        videoClass: 'bg_video',
        fallbackImageId: 'webpPlayer',
        sources: [
            { src: 'https://www.peira.space/img/seq/bg.mov', type: 'video/mp4; codecs=hvc1' },
            { src: 'https://www.peira.space/img/seq/bg.webm', type: 'video/webm' },
        ],
    });
});