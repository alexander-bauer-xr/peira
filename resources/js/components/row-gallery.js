// resources/js/components/row-gallery.js

export default function initRowGallery() {
  document.querySelectorAll(".list").forEach((container) => {
    const rowSlug = container.id.replace(/^list-/, "");
    const forwardBtn = document.getElementById(`arrowforw-${rowSlug}`);
    const backBtn = document.getElementById(`arrowback-${rowSlug}`);

    if (forwardBtn && backBtn) {
      forwardBtn.addEventListener("click", () => {
        const snapWidth = container.clientWidth;
        const currentScroll = container.scrollLeft;
        const nextSnap = Math.ceil((currentScroll + snapWidth) / snapWidth) * snapWidth;
        container.scrollTo({ left: nextSnap, behavior: "smooth" });
      });

      backBtn.addEventListener("click", () => {
        const snapWidth = container.clientWidth;
        const currentScroll = container.scrollLeft;
        const nextSnap = Math.floor((currentScroll - snapWidth) / snapWidth) * snapWidth;
        container.scrollTo({ left: nextSnap, behavior: "smooth" });
      });
    }
  });
}