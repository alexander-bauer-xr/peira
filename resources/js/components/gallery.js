let fotos = window.galleryData || [];

export default function initGallery() {
    if (!fotos.length) return;

    window.loadImg = function (direction) {
        let gallery = document.getElementById("gallery");
        let currentId = parseInt(gallery.getAttribute("data-id"));
        if (isNaN(currentId)) return;

        let i = currentId;
        i = direction === "up" ? i + 1 : i - 1;

        if (i < 0) i = fotos.length - 1;
        if (i >= fotos.length) i = 0;

        gallery.setAttribute("data-id", i);
        gallery.innerHTML = `
            <div id="copyright">${fotos[i][2] ? '© ' + fotos[i][2] : ''}</div>
            <div id="arrowforw" onclick="loadImg('up')">
                <img alt="Pfeil" class="arrowforw" src="/img/nav/garrow.svg">
            </div>
            <div id="arrowback" onclick="loadImg('down')">
                <img alt="Pfeil" class="arrowback" src="/img/nav/garrow.svg">
            </div>
            <img class="imgcover" id="image-${i}" alt="${fotos[i][0]}" src="/img/outline/loadimg.svg">
        `;

        const preloaderImg = new Image();
        preloaderImg.src = fotos[i][1];
        preloaderImg.onload = () => {
            const imgEl = document.getElementById(`image-${i}`);
            imgEl.classList.remove("imgcover");
            imgEl.classList.add("imagecontain");
            imgEl.src = fotos[i][1];
        };
    };
}