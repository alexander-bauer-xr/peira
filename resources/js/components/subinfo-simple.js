// resources/js/pages/subinfo-simple.js
export default function initSubinfoSimple() {
  const buttons  = [...document.querySelectorAll('.buttonsinfo')];
  const contents = [...document.querySelectorAll('.infosoflinks')];

  const path  = h => new URL(h, location.origin).pathname;
  const slug  = h => path(h).split('/').filter(Boolean).pop();
  const index = s => buttons.find(b => slug(b.href) === s)?.id.replace('controlinh-', '') || '0';

  const show = id => {
    contents.forEach(el => {
      el.classList.toggle('disp',  el.id === `controledinh-${id}`);
      el.classList.toggle('nondisp', el.id !== `controledinh-${id}`);
    });
    buttons.forEach(btn => {
      const idx = btn.id.replace('controlinh-', '');
      btn.classList.toggle('activeb',   idx === id);
      btn.classList.toggle('notactiveb', idx !== id);
    });
  };

  window.addEventListener('popstate', () => show(index(slug(location.href))));

  buttons.forEach(btn => {
    btn.addEventListener('click', e => {
      const targetPath = path(btn.href);
      if (targetPath.startsWith(`/${location.pathname.split('/')[1]}/ueber-uns`)) {
        e.preventDefault();
        if (location.pathname !== targetPath) history.pushState(null, '', targetPath);
        show(index(slug(targetPath)));
      }
    });
  });

  show(index(slug(location.href)));
}