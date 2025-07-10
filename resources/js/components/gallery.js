const fotos = window.galleryData || [];

const breakpoints = [
  { min: 1280, prefix: 'peira_desktop_xl' },
  { min: 1024, prefix: 'peira_desktop_lg' },
  { min: 768,  prefix: 'peira_desktop_md' },
  { min: 0,    prefix: 'peira_mobile_sm' }
];

function buildPicture({ alt = '', styles = {} }, idx, extra = '') {
  let html = '<picture>';
  breakpoints.forEach(({ min, prefix }) => {
    const webp = styles[`${prefix}_webp`];
    const jpeg = styles[`${prefix}_jpeg`];
    const media = min ? `(min-width:${min}px)` : '';
    if (webp) html += `<source type="image/webp" ${media && `media="${media}"`} srcset="${webp}">`;
    if (jpeg) html += `<source type="image/jpeg" ${media && `media="${media}"`} srcset="${jpeg}">`;
  });
  const fallback = styles.peira_mobile_sm_jpeg || Object.values(styles)[0] || '';
  html += `<img id="image-${idx}" class="${extra}" alt="${alt}" src="${fallback}">`;
  html += '</picture>';
  return html;
}

export default function initGallery() {
  if (!fotos.length) return;

  window.loadImg = function loadImg(dir = 'up') {
    const g = document.getElementById('gallery');
    if (!g) return;

    let cur = parseInt(g.dataset.id);
    if (isNaN(cur)) cur = -1;
    let i = dir === 'up' ? cur + 1 : cur - 1;
    if (i < 0) i = fotos.length - 1;
    if (i >= fotos.length) i = 0;
    g.dataset.id = i;

    const { alt, title, styles } = fotos[i];
    const copyright = title ? `© ${title}` : '';

    g.innerHTML = `
      <div id="copyright">${copyright}</div>
      <div id="arrowforw" onclick="loadImg('up')"><img class="arrowforw" src="/img/nav/garrow.svg" alt=""></div>
      <div id="arrowback" onclick="loadImg('down')"><img class="arrowback" src="/img/nav/garrow.svg" alt=""></div>
      <div class="gallery-holder">
        ${buildPicture({ alt, styles }, i, 'imgcover')}
        <div class="spinner" aria-hidden="true"></div>
      </div>
    `;

    const preload = new Image();
    preload.src = styles.peira_mobile_sm_jpeg || Object.values(styles)[0] || '';
    preload.onload = () => {
      const img = document.getElementById(`image-${i}`);
      if (!img) return;
      img.classList.remove('imgcover');
      img.classList.add('imagecontain');
      g.querySelector('.spinner')?.remove();
    };
  };
}
