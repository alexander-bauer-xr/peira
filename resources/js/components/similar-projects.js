import { DRUPAL_URL } from '../utils/env';

export default async function initSimilarProjects() {
    const wrapper = document.getElementById("similar-wrapper");
    if (!wrapper) {
        return;
    }

    const currentId = parseInt(wrapper.dataset.currentId, 10);
    let tagIds;
    try {
        tagIds = JSON.parse(wrapper.dataset.tags);
    } catch (e) {
        console.error("similar-projects: invalid data-tags:", wrapper.dataset.tags);
        tagIds = [];
    }
    if (!Array.isArray(tagIds) || tagIds.length === 0) {
        return;
    }

    let projectMatches = {};

    async function fetchProjectsByTagId(tid) {
        const apiUrl = `${DRUPAL_URL}/api/protax?tid_1=${tid}`;
        try {
            const response = await fetch(apiUrl);
            const projectsWithThisTag = await response.json();

            projectsWithThisTag.forEach((project) => {
                const projectId = project.nid[0].value;
                if (projectId === currentId) return;

                const tagCount = (project.field_tags || []).length;
                if (projectMatches[projectId]) {
                    projectMatches[projectId] += tagCount;
                } else {
                    projectMatches[projectId] = tagCount;
                }
            });
        } catch (err) {
            console.error("similar-projects: error fetching tag " + tid, err);
        }
    }

    for (const tid of tagIds) {
        await fetchProjectsByTagId(tid);
    }

    const sortedProjectEntries = Object.entries(projectMatches)
        .sort((a, b) => b[1] - a[1]);

    const innerWrapper = document.getElementById("inner-wrapper-similar");
    if (!innerWrapper) return;

    for (const [projectIdStr] of sortedProjectEntries) {
        const projectId = parseInt(projectIdStr, 10);
        if (isNaN(projectId)) continue;

        try {
            const projectRes = await fetch(`${DRUPAL_URL}/api/projekte?nid=${projectId}`);
            const projectJson = await projectRes.json();
            const rawProject = projectJson[0];
            if (
                rawProject &&
                rawProject.title &&
                rawProject.title[0] &&
                rawProject.title[0].value
            ) {
                let title = rawProject.title[0].value;
                if (
                    wrapper.dataset.locale === "en" &&
                    rawProject.field_titel_projekt_en &&
                    rawProject.field_titel_projekt_en[0] &&
                    rawProject.field_titel_projekt_en[0].value
                ) {
                    title += " " + rawProject.field_titel_projekt_en[0].value;
                }

                const imageUrl =
                    rawProject.field_titel && rawProject.field_titel[0]
                        ? rawProject.field_titel[0].url
                        : "";

                const card = document.createElement("div");
                card.classList.add("card", "item");

                const link = document.createElement("a");
                link.classList.add("rowimg");

                const slugified = title
                    .trim()
                    .toLowerCase()
                    // Remove umlauts by replacing with base letter
                    .replace(/ä/g, 'a')
                    .replace(/ö/g, 'o')
                    .replace(/ü/g, 'u')
                    .replace(/ß/g, 'ss')
                    // Remove apostrophes
                    .replace(/'/g, '')
                    // Replace any other non-alphanumeric characters with hyphen
                    .replace(/[^a-z0-9]+/g, '-')
                    // Remove leading/trailing hyphens
                    .replace(/^-+|-+$/g, '');

                const origin = window.location.origin;
                const locale = wrapper.dataset.locale; // e.g. "de" or "en"
                link.href = `${origin}/${locale}/projekte/${slugified}`;

                const titleDiv = document.createElement("div");
                titleDiv.classList.add("h3-text", "titleforimg");
                titleDiv.textContent = title;

                const image = document.createElement("img");
                image.classList.add("imagecover");
                image.src = imageUrl;
                image.alt = title;

                link.append(titleDiv, image);
                card.appendChild(link);
                innerWrapper.appendChild(card);
            }
        } catch (err) {
            console.error(`similar-projects: failed to fetch project ${projectId}`, err);
        }
    }

    // Setup improved gallery navigation
    setupSimilarProjectsNavigation(innerWrapper, wrapper);
}

function setupSimilarProjectsNavigation(list, wrapper) {
    // Find arrows within the wrapper scope
    const forwardBtn = wrapper.querySelector("#arrowforw");
    const backBtn = wrapper.querySelector("#arrowback");

    if (!forwardBtn || !backBtn || !list) {
        console.warn('Similar projects navigation: Missing elements', {
            forwardBtn: !!forwardBtn,
            backBtn: !!backBtn,
            list: !!list
        });
        return;
    }

    console.log('Similar projects navigation initialized');

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
    forwardBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const scrollAmount = getScrollAmount();
        const targetScroll = list.scrollLeft + scrollAmount;
        list.scrollTo({ left: targetScroll, behavior: 'smooth' });
        console.log('Scroll forward:', scrollAmount);
    });

    // Scroll backward (to the left)
    backBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const scrollAmount = getScrollAmount();
        const targetScroll = list.scrollLeft - scrollAmount;
        list.scrollTo({ left: targetScroll, behavior: 'smooth' });
        console.log('Scroll backward:', scrollAmount);
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
    
    // Initial check (wait a bit for cards to be rendered)
    setTimeout(updateArrowVisibility, 100);

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