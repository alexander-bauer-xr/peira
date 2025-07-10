/* global window, document */

/**
 *  galleryData is pushed into the page by Blade:
 *  [
 *    {
 *      alt   : 'file name.jpg',
 *      title : '© John Doe',
 *      styles: { peira_desktop_xl_webp : '…', peira_desktop_xl_jpeg : '…', … }
 *    },
 *    …
 *  ]
 */
const fotos = window.galleryData || [];

/** break-point map has to match what you generate in <x-responsive-image> */
const breakpoints = [
  { min: 1280, prefix: 'peira_desktop_xl' },
  { min: 1024, prefix: 'peira_desktop_lg' },
  { min: 768, prefix: 'peira_desktop_md' },
  { min: 0, prefix: 'peira_mobile_sm' }   
];

/**
 * Build the <picture> markup for one gallery entry.
 * We return a *string* so we can just drop it into innerHTML.
 */
function buildPicture({ alt = '', styles = {} }, idx, extraImgClasses = '') {
  let html = '<picture>';

  breakpoints.forEach(({ min, prefix }) => {
    const webp = styles[`${prefix}_webp`];
    const jpeg = styles[`${prefix}_jpeg`];
    const media = min > 0 ? `(min-width:${min}px)` : '';

    if (webp) html += `<source type="image/webp" ${media && `media="${media}"`} srcset="${webp}">`;
    if (jpeg) html += `<source type="image/jpeg" ${media && `media="${media}"`} srcset="${jpeg}">`;
  });

  const fallback = styles.peira_mobile_sm_jpeg || Object.values(styles)[0] || '';

  html += `<img id="image-${idx}" class="${extraImgClasses}" alt="${alt}" src="${fallback}">`;
  html += '</picture>';

  return html;
}

export default function initGallery() {
  if (!fotos.length) return;            // nothing to do

  window.loadImg = function loadImg(direction = 'up') {
    const gallery = document.getElementById('gallery');
    if (!gallery) return;

    let currentId = parseInt(gallery.dataset.id);
    if (isNaN(currentId)) currentId = -1;

    let i = direction === 'up' ? currentId + 1 : currentId - 1;
    if (i < 0) i = fotos.length - 1;
    if (i >= fotos.length) i = 0;

    gallery.dataset.id = i;

    const { alt, title, styles } = fotos[i];
    const copyright = title ? `© ${title}` : '';

    /* ---------------------------------------------------------------------
       Phase 1 – render placeholder (spinner / outline) while we preload
     --------------------------------------------------------------------- */
    gallery.innerHTML = `
      <div id="copyright">${copyright}</div>
      <div id="arrowforw"  onclick="loadImg('up')">
          <img class="arrowforw"  src="/img/nav/garrow.svg" alt="Pfeil vorwärts">
      </div>
      <div id="arrowback" onclick="loadImg('down')">
          <img class="arrowback" src="/img/nav/garrow.svg" alt="Pfeil zurück">
      </div>
      ${buildPicture({ alt, styles }, i, 'imgcover')}
    `;

    /* ---------------------------------------------------------------------
       Phase 2 – pre-load the *mobile fallback* and, when ready, swap the
       placeholder class for the real one – this preserves your fade-in /
       ratio-boxing behaviour while still giving the browser full <picture>
       markup for proper art-direction once the image is in-view.
     --------------------------------------------------------------------- */
    const preload = new Image();
    preload.src = styles.peira_mobile_sm_jpeg || Object.values(styles)[0] || '';

    preload.onload = () => {
      const imgTag = document.getElementById(`image-${i}`);
      if (!imgTag) return;

      imgTag.classList.remove('imgcover');
      imgTag.classList.add('imagecontain');
      // no need to swap src – it’s already the fallback we pre-loaded
      // sources inside <picture> take over automatically for larger screens
    };
  };
}
