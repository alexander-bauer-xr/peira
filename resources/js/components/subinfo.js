export default function initSubinfoToggle() {
  const buttons  = Array.from(document.querySelectorAll('.buttonsinfo'));
  const contents = Array.from(document.querySelectorAll('.infosoflinks'));

  function showTab(id) {
    contents.forEach(el => {
      el.classList.toggle('disp', el.id === `controledinh-${id}`);
      el.classList.toggle('nondisp', el.id !== `controledinh-${id}`);
    });
    buttons.forEach(btn => {
      const idx = btn.id.replace('controlinh-', '');
      btn.classList.toggle('activeb', idx === id);
      btn.classList.toggle('notactiveb', idx !== id);
    });
  }

  function indexForSlug(slug) {
    const btn = buttons.find(b => b.getAttribute('href').endsWith(`/${slug}`));
    return btn ? btn.id.replace('controlinh-', '') : '0';
  }

  window.addEventListener('popstate', () => {
    const parts = location.pathname.split('/').filter(Boolean);
    const slug  = parts.pop() || ''; 
    const id    = indexForSlug(slug);
    showTab(id);
  });

  buttons.forEach(btn => {
    btn.addEventListener('click', e => {
      const href = btn.getAttribute('href');
      const slug = href.split('/').pop();

      const base = window.location.pathname.split('/').slice(0, -1).join('/');

      if (href.startsWith(base)) {
        e.preventDefault();

        if (location.pathname !== href) {
          history.pushState(null, '', href);
        }

        const id = indexForSlug(slug);
        showTab(id);
      }
    });
  });

  const initialSlug = location.pathname.split('/').filter(Boolean).pop() || '';
  showTab(indexForSlug(initialSlug));
}
