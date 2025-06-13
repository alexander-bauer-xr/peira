export default function initSubinfoToggle() {
  const buttons  = document.querySelectorAll('.buttonsinfo');
  const contents = document.querySelectorAll('.infosoflinks');

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

  window.addEventListener('popstate', () => {
    const parts = location.pathname.split('/');
    const id    = parts.pop() || '0';
    showTab(id);
  });

  buttons.forEach(btn => {
    btn.addEventListener('click', e => {
      const href = btn.getAttribute('href');
      const [ , , , , tab ] = href.split('/');
      if (href.startsWith(location.pathname.split('/').slice(0,-1).join('/'))) {
        e.preventDefault();
        history.pushState(null, '', href);
        showTab(tab);
      }
    });
  });
}
