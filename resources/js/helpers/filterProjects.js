let allProjects = [];
let tagIndex = new Map();
let tagMap = new Map();
let isLoading = false;
let abortController = new AbortController();

const locale = document.documentElement.lang || 'de';

document.addEventListener("DOMContentLoaded", async () => {
  try {
    const [projectsRes, tagsRes] = await Promise.all([
      fetch("https://peira.space/web/api/protax"),
      fetch("https://peira.space/web/api/tags")
    ]);

    allProjects = await projectsRes.json();
    const tags = await tagsRes.json();

    tags.forEach(tag => {
      const tid = tag.tid[0].value;
      const name = tag.name[0].value;
      const nameEn = tag.field_eng?.[0]?.value || name;
      tagMap.set(tid, { de: name, en: nameEn });
    });

    allProjects.forEach(project => {
      (project.field_tags || []).forEach(tag => {
        const tid = tag.target_id;
        if (!tagIndex.has(tid)) tagIndex.set(tid, []);
        tagIndex.get(tid).push(project);
      });
    });

    renderPlaceholder();
  } catch (err) {
    console.error("Failed to load data:", err);
  }
});

function renderPlaceholder() {
  const content = document.getElementById("projectgrid");
  if (content) {
    content.innerHTML = `
      <div class="widel card"> 
        <div class="cardtitlerow h3-text">Bitte wähle einen Filter aus</div>
        <img loading="lazy" class="image" src="/img/select.png" alt="Not found">
      </div>`;
  }
}

function readActiveButtonDataIds() {
  const buttons = document.querySelectorAll('.buttonsinfofilter.activeb:not(.notactiveb)');
  const dataIds = Array.from(buttons).map(button => button.dataset.id);
  return dataIds.slice(1);
}

function handleButtonClick(event) {
  event.target.classList.toggle('activeb');
  event.target.classList.toggle('notactiveb');

  const selectedIds = readActiveButtonDataIds();

  if (selectedIds.length === 0) {
    renderPlaceholder();
  } else {
    showLoadingCard();
    filterAndRender(selectedIds);
  }
}

function showLoadingCard() {
  const loader = `
    <div id="loading-card" class="loadercard"> 
      <div class="loader"></div>
    </div>`;
  const loading = document.getElementById("loadingcard");
  if (loading) loading.innerHTML = loader;
}

function filterAndRender(tagIds) {
  const matched = new Set();

  tagIds.forEach(id => {
    const matches = tagIndex.get(parseInt(id)) || [];
    matches.forEach(p => matched.add(p));
  });

  const filteredProjects = Array.from(matched);

  const sorted = filteredProjects.sort((a, b) => {
    const yA = new Date(a.field_jahr_der_?.[0]?.value || 0);
    const yB = new Date(b.field_jahr_der_?.[0]?.value || 0);
    return yB - yA;
  });

  renderProjects(sorted);
}

function renderProjects(projects) {
  const container = document.getElementById("projectgrid");
  if (!container) return;

  container.innerHTML = "";

  projects.forEach(project => {
    const title = project.title?.[0]?.value || "Untitled";
    const year = project.field_jahr_der_?.[0]?.value ? new Date(project.field_jahr_der_[0].value).getFullYear() : "";
    const image = project.field_titel?.[0]?.url || "/img/default.jpg";
    const tags = (project.field_tags || [])
      .map(tag => tagMap.get(tag.target_id)?.[locale] || "")
      .filter(Boolean);

    const style = project.field_projektstil?.[0]?.value || "";
    const overlay = project.field_bildoverlay?.[0]?.value === true;
    const darkText = project.field_schwarzertext?.[0]?.value === true;

    const classCard = overlay ? "card" : "cardo";
    const classText = darkText ? "blacktextcard" : "whitetextcard";
    const classTag = darkText ? "borderblack" : "borderwhite";

    const tagHtml = `
      <div class="tagcontainer">
        ${year ? `<div class="tag ${classTag}">${year}</div>` : ""}
        ${tags.map(t => `<div class="tag ${classTag}">${t}</div>`).join("")}
      </div>`;

    container.innerHTML += `
      <div class="${style} ${classCard}">
        <a class="${classText}" href="/${locale}/projekte/${title.replace(/\s+/g, "-").toLowerCase()}">
          ${tagHtml}
          <div class="cardtitle h3-text">${title}</div>
          <img loading="lazy" class="image" src="${image}" alt="${title}">
        </a>
      </div>`;
  });

  const loading = document.getElementById("loadingcard");
  if (loading) loading.innerHTML = "";
}

document.querySelectorAll('.marked').forEach(btn => {
  btn.addEventListener("click", handleButtonClick);
});
