export default function initSubinfoToggle() {
    const buttons = document.querySelectorAll('.buttonsinfo');
    const contents = document.querySelectorAll('.infosoflinks');
    console.log('initSubinfoToggle called');
    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            const id = button.id.replace('controlinh-', '');

            contents.forEach((el) => {
                el.classList.remove('disp');
                el.classList.add('nondisp');
            });

            buttons.forEach((el) => {
                el.classList.remove('activeb');
                el.classList.add('notactiveb');
            });

            document.getElementById('controledinh-' + id)?.classList.remove('nondisp');
            document.getElementById('controledinh-' + id)?.classList.add('disp');
            document.getElementById('controlinh-' + id)?.classList.remove('notactiveb');
            document.getElementById('controlinh-' + id)?.classList.add('activeb');
        });
    });
}