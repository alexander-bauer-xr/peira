// resources/js/helpers/insertTransparentVideo.js

export function canPlayTransparentVideo() {
    const video = document.createElement('video');
    return (
        video.canPlayType('video/webm; codecs="vp9"') ||
        video.canPlayType('video/webm; codecs="vp8"') ||
        video.canPlayType('video/mp4; codecs="hvc1"') ||
        video.canPlayType('video/mp4; codecs="hev1"')
    );
}

export function insertTransparentVideo({
    containerSelector,
    videoId,
    videoClass = '',
    fallbackImageId,
    sources,
    timeout = 8000,
    onClick = null,
    onTimeoutFallback = null,
}) {
    let videoLoaded = false;
    let videoElementExists = true;

    if (!canPlayTransparentVideo()) {
        console.log(`[${videoId}] Transparent video not supported.`);
        const fallbackImage = document.getElementById(fallbackImageId);
        if (fallbackImage) fallbackImage.style.display = 'block';
        return;
    }

    console.log(`[${videoId}] Transparent video supported.`);
    const videoElement = document.createElement('video');
    videoElement.id = videoId;
    videoElement.className = videoClass;
    videoElement.setAttribute('autoplay', '');
    videoElement.setAttribute('muted', '');
    videoElement.setAttribute('playsinline', '');
    videoElement.setAttribute('webkit-playsinline', ''); // iOS Safari specific
    videoElement.setAttribute('loop', '');

    videoElement.autoplay = true;
    videoElement.loop = true;
    videoElement.muted = true;
    videoElement.playsInline = true;
    videoElement.style.pointerEvents = 'none';

    if (onClick) videoElement.onclick = onClick;

    sources.forEach(({ src, type }) => {
        const source = document.createElement('source');
        source.src = src;
        source.type = type;
        videoElement.appendChild(source);
    });

    const container = document.querySelector(containerSelector);
    if (!container) return;

    container.appendChild(videoElement);

    videoElement.addEventListener('canplaythrough', () => {
        if (videoElementExists) {
            videoLoaded = true;
            const fallbackImage = document.getElementById(fallbackImageId);
            if (fallbackImage) fallbackImage.style.display = 'none';
            console.log(`[${videoId}] video loaded.`);
        }
    });

    setTimeout(() => {
        if (!videoLoaded && videoElement.readyState < 3) {
            console.warn(`[${videoId}] took too long to load, removing.`);
            videoElement.remove();
            videoElementExists = false;

            const fallbackImage = document.getElementById(fallbackImageId);
            if (fallbackImage) {
                fallbackImage.style.display = 'block';
                if (onTimeoutFallback) onTimeoutFallback(fallbackImage);
            }
        }
    }, timeout);
}