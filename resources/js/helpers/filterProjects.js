let allProjects = [];
let tagIndex = new Map();
let tagMap = new Map();
let isLoading = false;
let abortController = new AbortController();
let htmlContent = "";
let totalFilterNumber = 0;

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

    htmlContent = document.getElementById("projectgrid")?.innerHTML || "";
    totalFilterNumber = document.querySelectorAll('.buttonsinfofilter').length - 1;
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
    if (selectedIds.length === totalFilterNumber) {
      document.getElementById("projectgrid").innerHTML = htmlContent;
    } else {
      showLoadingCard();
      filterAndRender(selectedIds);
    }

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
  const matchedIds = new Set();

  tagIds.forEach(id => {
    const matches = tagIndex.get(parseInt(id)) || [];
    matches.forEach(p => {
      if (p.nid?.[0]?.value) {
        matchedIds.add(p.nid[0].value);
      }
    });
  });

  const filteredProjects = allProjects
    .filter(p => matchedIds.has(p.nid?.[0]?.value));

  const seen = new Set();
  const uniqueProjects = filteredProjects.filter(p => {
    const nid = p.nid?.[0]?.value;
    if (seen.has(nid)) return false;
    seen.add(nid);
    return true;
  });

  const sorted = uniqueProjects.sort((a, b) => {
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
    const title = locale === 'en'
  ? project.field_titel_projekt_en?.[0]?.value || project.title?.[0]?.value || "Untitled"
  : project.title?.[0]?.value || "Untitled";

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

function toggleFilterList() {
  const filterButton = document.getElementById('filterbtn');
  const filterList = document.getElementById('listoffilters');
  const arrowImg = document.getElementById('arrowimg');

  filterList.classList.toggle('hidden');

  if (filterList.classList.contains('hidden')) {
    arrowImg.src = '/img/rotpfeil.png';
    filterButton.classList.add('notactiveb');
    filterButton.classList.remove('activeb');
  } else {
    arrowImg.src = '/img/weisspfeil.png';
    filterButton.classList.remove('notactiveb');
    filterButton.classList.add('activeb');
  }
}

const filterBtn = document.getElementById('filterbtn');
filterBtn?.addEventListener('click', toggleFilterList);