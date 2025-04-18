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

    // --- Transparent Video Background ---
    /*let videoLoadedBG = false;
    let videoElementExistsBG = true;
    const fallbackImage = document.getElementById("webpPlayer");


    function canPlayWebMWithTransparency() {
        const video = document.createElement('video');
        const canPlayVP9 = video.canPlayType('video/webm; codecs="vp9"');
        const canPlayVP8 = video.canPlayType('video/webm; codecs="vp8"');
        const canPlayHEVC = video.canPlayType('video/mp4; codecs="hvc1"') || video.canPlayType('video/mp4; codecs="hev1"');

        return canPlayVP9 || canPlayVP8 || canPlayHEVC;
    }

    if (canPlayWebMWithTransparency()) {
        console.log("Browser supports videos with transparency.");

        const videoElement = document.createElement('video');
        videoElement.id = "back_video";
        videoElement.className = "bg_video";
        videoElement.autoplay = true;
        videoElement.loop = true;
        videoElement.muted = true;
        videoElement.playsInline = true;

        const sourceMP4 = document.createElement('source');
        sourceMP4.src = "https://www.peira.space/img/seq/bg.mov";
        sourceMP4.type = "video/mp4; codecs=hvc1";

        const sourceWebM = document.createElement('source');
        sourceWebM.src = "https://www.peira.space/img/seq/bg.webm";
        sourceWebM.type = "video/webm";

        videoElement.appendChild(sourceMP4);
        videoElement.appendChild(sourceWebM);

        document.querySelector('.bg').appendChild(videoElement);

        videoElement.addEventListener('canplaythrough', () => {
            if (videoElementExistsBG) {
                videoLoadedBG = true;
                if (fallbackImage) fallbackImage.style.display = 'none';
            }
        });

        setTimeout(() => {
            if (!videoLoadedBG && videoElement.readyState < 3) {
                videoElement.remove();
                videoElementExistsBG = false;
            }
        }, 8000);
    } else {
        fallbackImage.style.display = 'block';
    }*/
});