export default function initToggleDates() {
  function toggleDates() {
    const extraDates = document.getElementById("extraDates");
    const button = document.getElementById("toggleButton");
    const isHidden = extraDates.classList.toggle("d-none");
    button.textContent = isHidden ? "Show More" : "Show Less";
    button.setAttribute("aria-expanded", (!isHidden).toString());
  }

  const extraDates = document.getElementById("extraDates");
  const button = document.getElementById("toggleButton");

  if (!button || !extraDates) {
    return;
  }

  const extraItems = extraDates.querySelectorAll(".termin");
  if (!extraItems.length) {
    button.style.display = "none";
    return;
  }

  button.textContent = "Show More";
  button.setAttribute("aria-expanded", "false");

  button.addEventListener("click", toggleDates);
}
