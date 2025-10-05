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
        const apiUrl = `${DRUPAL_URL}/web/api/protax?tid_1=${tid}`;
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
            const projectRes = await fetch(`${DRUPAL_URL}/web/api/projekte?nid=${projectId}`);
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
                    .replace(/\s+/g, "-")
                    .replace(/[^a-z0-9\-]/g, "");

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


    const container = innerWrapper;
    const forwardBtn = document.getElementById("arrowforw");
    const backBtn = document.getElementById("arrowback");

    if (forwardBtn && backBtn && container) {
        forwardBtn.addEventListener("click", () => {
            const snapWidth = container.clientWidth;
            const currentScroll = container.scrollLeft;
            const nextSnap =
                Math.ceil((currentScroll + snapWidth) / snapWidth) * snapWidth;
            container.scrollTo({ left: nextSnap, behavior: "smooth" });
        });
        backBtn.addEventListener("click", () => {
            const snapWidth = container.clientWidth;
            const currentScroll = container.scrollLeft;
            const nextSnap =
                Math.floor((currentScroll - snapWidth) / snapWidth) * snapWidth;
            container.scrollTo({ left: nextSnap, behavior: "smooth" });
        });
    }
}