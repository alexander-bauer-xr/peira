const l=window.galleryData||[],p=[{min:1280,prefix:"peira_desktop_xl"},{min:1024,prefix:"peira_desktop_lg"},{min:768,prefix:"peira_desktop_md"},{min:0,prefix:"peira_mobile_sm"}];function u({alt:g="",styles:a={}},r,t=""){let e="<picture>";p.forEach(({min:n,prefix:o})=>{const c=a[`${o}_webp`],s=a[`${o}_jpeg`],i=n?`(min-width:${n}px)`:"";c&&(e+=`<source type="image/webp" ${i&&`media="${i}"`} srcset="${c}">`),s&&(e+=`<source type="image/jpeg" ${i&&`media="${i}"`} srcset="${s}">`)});const d=a.peira_mobile_sm_jpeg||Object.values(a)[0]||"";return e+=`<img id="image-${r}" class="${t}" alt="${g}" src="${d}">`,e+="</picture>",e}function v(){l.length&&(window.loadImg=function(a="up"){const r=document.getElementById("gallery");if(!r)return;let t=parseInt(r.dataset.id);isNaN(t)&&(t=-1);let e=a==="up"?t+1:t-1;e<0&&(e=l.length-1),e>=l.length&&(e=0),r.dataset.id=e;const{alt:d,title:n,styles:o}=l[e],c=n?`© ${n}`:"";r.innerHTML=`
      <div id="copyright">${c}</div>
      <div id="arrowforw" onclick="loadImg('up')"><img class="arrowforw" src="/img/nav/garrow.svg" alt=""></div>
      <div id="arrowback" onclick="loadImg('down')"><img class="arrowback" src="/img/nav/garrow.svg" alt=""></div>
      <div class="gallery-holder">
        ${u({alt:d,styles:o},e,"imgcover")}
        <div class="spinner" aria-hidden="true"></div>
      </div>
    `;const s=new Image;s.src=o.peira_mobile_sm_jpeg||Object.values(o)[0]||"",s.onload=()=>{var m;const i=document.getElementById(`image-${e}`);i&&(i.classList.remove("imgcover"),i.classList.add("imagecontain"),(m=r.querySelector(".spinner"))==null||m.remove())}})}export{v as i};
