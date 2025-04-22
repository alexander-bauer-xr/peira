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