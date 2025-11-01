// resources/js/components/row-gallery.js

export default function initRowGallery() {
  // Handle both old structure (with row slugs) and new structure (single gallery)
  
  // New structure: single row gallery on row detail pages
  const singleGallery = document.querySelector(".rowwrapper .list");
  const arrowForw = document.getElementById("arrowforw");
  const arrowBack = document.getElementById("arrowback");
  
  if (singleGallery && arrowForw && arrowBack) {
    setupGalleryNavigation(singleGallery, arrowForw, arrowBack);
  }
  
  // Old structure: multiple galleries with slugs
  document.querySelectorAll(".list").forEach((container) => {
    const rowSlug = container.id.replace(/^list-/, "");
    const forwardBtn = document.getElementById(`arrowforw-${rowSlug}`);
    const backBtn = document.getElementById(`arrowback-${rowSlug}`);

    if (forwardBtn && backBtn) {
      setupGalleryNavigation(container, forwardBtn, backBtn);
    }
  });
}

function setupGalleryNavigation(list, forwardBtn, backBtn) {
  // Calculate scroll amount based on card width + gap
  const getScrollAmount = () => {
    const card = list.querySelector('.card.item');
    if (!card) {
      // Fallback: scroll by container width
      return list.clientWidth;
    }
    
    const cardWidth = card.offsetWidth;
    const cardStyle = window.getComputedStyle(card);
    const marginRight = parseInt(cardStyle.marginRight) || 0;
    
    return cardWidth + marginRight;
  };

  // Scroll forward (to the right)
  forwardBtn.addEventListener('click', () => {
    const scrollAmount = getScrollAmount();
    const targetScroll = list.scrollLeft + scrollAmount;
    list.scrollTo({ left: targetScroll, behavior: 'smooth' });
  });

  // Scroll backward (to the left)
  backBtn.addEventListener('click', () => {
    const scrollAmount = getScrollAmount();
    const targetScroll = list.scrollLeft - scrollAmount;
    list.scrollTo({ left: targetScroll, behavior: 'smooth' });
  });

  // Update arrow visibility based on scroll position
  const updateArrowVisibility = () => {
    const maxScroll = list.scrollWidth - list.clientWidth;
    
    // Hide back arrow if at start
    if (list.scrollLeft <= 0) {
      backBtn.style.opacity = '0.3';
      backBtn.style.pointerEvents = 'none';
    } else {
      backBtn.style.opacity = '1';
      backBtn.style.pointerEvents = 'auto';
    }
    
    // Hide forward arrow if at end
    if (list.scrollLeft >= maxScroll - 1) {
      forwardBtn.style.opacity = '0.3';
      forwardBtn.style.pointerEvents = 'none';
    } else {
      forwardBtn.style.opacity = '1';
      forwardBtn.style.pointerEvents = 'auto';
    }
  };

  // Listen for scroll events to update arrow visibility
  list.addEventListener('scroll', updateArrowVisibility);
  
  // Initial check
  updateArrowVisibility();

  // Optional: Add keyboard navigation when list is focused
  list.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') {
      e.preventDefault();
      backBtn.click();
    } else if (e.key === 'ArrowRight') {
      e.preventDefault();
      forwardBtn.click();
    }
  });

  // Make list focusable for keyboard navigation
  if (!list.hasAttribute('tabindex')) {
    list.setAttribute('tabindex', '0');
  }
}
