(function(){try{
  window.KBSP_CONFIG = window.KBSP_CONFIG || {};
  window.KBSP_SEED = (KBSP_CONFIG.seed && KBSP_CONFIG.seed.profiles) ? KBSP_CONFIG.seed : {profiles:[]};
}catch(e){window.KBSP_SEED={profiles:[]};}})();


(function(){
  'use strict';

  const DIM_ORDER = ['comfort','jordig','hetta','syra','street','umami','fine','fräsch','sötma','salt'];

  const qs = (s,el)=> (el||document).querySelector(s);
  function el(tag, attrs, ...children){
    const e = document.createElement(tag);
    attrs = attrs||{};
    for(const k in attrs){
      if(k==='class') e.className = attrs[k];
      else if(k==='html') e.innerHTML = attrs[k];
      else if(k==='text') e.textContent = attrs[k];
      else e.setAttribute(k, attrs[k]);
    }
    children.flat().forEach(c=>{
      if(c==null) return;
      if(typeof c==='string' || typeof c==='number') e.appendChild(document.createTextNode(String(c)));
      else e.appendChild(c);
    });
    return e;
  }

  function clamp(v,min,max){ return Math.max(min, Math.min(max, v)); }

  let CONFIG=null, rootEl=null, step=0, answers=[], prefs={ heat_level:5, acid_level:5, salt_level:5 };
  let phase='intro'; // intro | quiz | result

  function generateHoroscope(profile, dims){
    const top = Object.entries(dims||{}).sort((a,b)=>b[1]-a[1]).slice(0,3).map(([k])=>k);
    const keys = (profile.keywords||[]).slice(0,3).join(', ');
    const parts = [];
    parts.push('Du är '+(profile.name||'unik')+'. '+(keys?('Nyckelord: '+keys+'. '):'')+'Din smak rör sig kring '+top.join(', ')+'.');
    parts.push('Du balanserar smaker och texturer med självklarhet. Vardagen får en liten twist och gästerna lämnar nöjda.');
    const bullets = [
      'Bygg smak i lager utan att tappa råvaran',
      'Jobba med kontraster – krispigt, krämigt och fräscht',
      'Justera på slutet med syra, sälta och en nypa hetta'
    ];
    return {paragraphs:parts, bullets:bullets, title:'Ditt smakhoroskop'};
  }


  function fetchJSON(url, opts){
    const req = (u)=>fetch(u, Object.assign({headers:{'Content-Type':'application/json'}}, opts||{}));
    return req(url).then(r=> r.ok ? r.json() : Promise.reject(r));
  }

  function ensureConfig(){
    // prefer embedded
    if(!CONFIG || !CONFIG.questions || !CONFIG.questions.length){
      if(window.KBSP && KBSP.embedded){ CONFIG = KBSP.embedded; }
    }
    // background REST if available
    const try1 = (window.KBSP && KBSP.rest) ? fetchJSON(KBSP.rest + 'config') : Promise.reject();
    const try2 = (window.KBSP && KBSP.rest_alt) ? fetchJSON(KBSP.rest_alt + 'config') : Promise.reject();
    return try1.catch(()=>try2).then(cfg=>{
      if(cfg && cfg.questions && cfg.questions.length){
        // Merge profiles to preserve horoscope/keywords from embedded if REST lacks them
        if(window.KBSP && KBSP.embedded && Array.isArray(KBSP.embedded.profiles) && Array.isArray(cfg.profiles)){
          const bySlug = new Map(KBSP.embedded.profiles.map(p=>[p.slug||p.name, p]));
          cfg.profiles = cfg.profiles.map(p=>{
            const key = p.slug||p.name; const seed = bySlug.get(key);
            if(seed){
              if(!p.horoscope_default && seed.horoscope_default) p.horoscope_default = seed.horoscope_default;
              if(!p.horoscope && seed.horoscope) p.horoscope = seed.horoscope;
              if((!p.keywords || !p.keywords.length) && seed.keywords) p.keywords = seed.keywords;
            }
            return p;
          });
        }
        CONFIG = cfg;
      }
      return CONFIG || {questions:[],profiles:[],settings:{dimensions:{}}};
    }).catch(()=> CONFIG || {questions:[],profiles:[],settings:{dimensions:{}}});
  }

  function preloadQuiz(){
    // kick off config + a few images
    ensureConfig().then(cfg=>{
      try{
        const qs = (cfg && cfg.questions) ? cfg.questions.slice(0,3) : [];
        qs.forEach(q=> (q.choices||[]).forEach(c=>{
          if(c.image){ const im = new Image(); im.src = c.image; }
        }));
      }catch(e){}
    });
  }


  function normalizeText(s){
    if(!s) return '';
    s = s.replace(/\\r\\n/g,'\\n').replace(/\\r/g,'\\n');
    s = s.replace(/\\\\n/g,'\\n');
    s = s.replace(/nn(?=\\s*★)/g,'\\n');
    s = s.replace(/(\\*\\*[^*]+\\*\\*)\\s*nn/g,'$1\\n');
    s = s.replace(/\\n{3,}/g,'\\n\\n');
    return s;
  }

  function render(root){
    root.innerHTML='';
    if(phase==='intro'){ return renderIntro(root); }
    if(!CONFIG || !CONFIG.questions || !CONFIG.questions.length){
      root.appendChild(el('p',{class:'kbsp-sub'},'Inga frågor hittades.'));
      return;
    }
    if(step<CONFIG.questions.length) renderQuestion(root); else renderFinish(root);
  }

  function renderIntro(root){
    const wrap = el('div',{class:'kbsp-hero'});
    wrap.appendChild(el('h1',{class:'kbsp-hero-title'}, (window.KBSP&&KBSP.ui&&KBSP.ui.hero_title)||'Upptäck din unika smakprofil'));
    wrap.appendChild(el('p',{class:'kbsp-sub'}, (window.KBSP&&KBSP.ui&&KBSP.ui.hero_subtitle)||'Ta fram din egna smakprofil med hjälp av vår smakprofilör.'));
    const btn = el('button',{class:'kbsp-btn kbsp-hero-start',type:'button'},'Starta din smakresa');
    btn.addEventListener('mouseover', preloadQuiz, {passive:true});
    btn.addEventListener('touchstart', preloadQuiz, {passive:true});
    btn.onclick = ()=>{ ensureConfig().then(()=>{ phase='quiz'; step=0; answers=[]; render(root); }); };
    wrap.appendChild(btn);
    root.appendChild(wrap);
    // start preload directly on intro
    preloadQuiz();
  }

  function renderProgress(root, i, total){
    const bar=el('div',{class:'kbsp-progress'});
    const inner=el('div',{class:'kbsp-progress-inner'});
    inner.style.width = Math.round((i/Math.max(1,total))*100)+'%';
    bar.appendChild(inner);
    root.appendChild(bar);
  }

  function renderQuestion(root){
    const q = CONFIG.questions[step];
    root.appendChild(el('div',{class:'kbsp-sub'},'Fråga '+(step+1)+' av '+CONFIG.questions.length));
    renderProgress(root, step, CONFIG.questions.length);
    root.appendChild(el('h2',{class:'kbsp-qtitle'}, q.title || ('Fråga '+(step+1))));
    root.appendChild(el('p',{class:'kbsp-sub'}, 'Klicka på den bild du föredrar mest.'));

    const grid=el('div',{class:'kbsp-grid'});
    (q.choices||[]).forEach((c,i)=>{
      const card=el('div',{class:'kbsp-card', role:'button', tabindex:'0'});
      const imgEl=el('img',{src:(c.image||''), alt:(c.title||'Alternativ'), loading:'lazy'});
      imgEl.onerror=()=>{ if(window.KBSP&&KBSP.assets){ imgEl.src=KBSP.assets+'img/placeholder.png'; } };
      const body=el('div',{class:'body'}, el('div',{class:'kbsp-caption'}, c.title||('Alternativ '+(i+1))));
      if(c.vegetarian || c.vegan){ card.appendChild(el('div',{class:'kbsp-veg'}, c.vegan?'Vegan':'Veg')); }
      card.appendChild(el('div',{class:'img'}, imgEl));
      card.appendChild(body);
      card.addEventListener('click', ()=> selectChoice(i));
      card.addEventListener('keydown', (e)=>{ if(e.key==='Enter'||e.key===' '){ e.preventDefault(); selectChoice(i);} });
      grid.appendChild(card);
    });
    root.appendChild(grid);

    const nav=el('div',{class:'kbsp-nav'});
    if(step>0){
      const back=el('button',{class:'kbsp-btn kbsp-light',type:'button'},'Tillbaka');
      back.onclick=()=>{ step=Math.max(0, step-1); answers.pop(); render(root); };
      nav.appendChild(back);
    }
    root.appendChild(nav);
  }

  function selectChoice(idx){
    const q = CONFIG.questions[step];
    answers.push({question_id:q.id || ('q'+(step+1)), choice_index:idx});
    step++; render(rootEl);
  }

  function slider(label, key, range, value){
    const wrap=el('div',{class:'kbsp-sl'});
    wrap.appendChild(el('div',{class:'kbsp-sl-lbl'}, label));
    const input=el('input',{type:'range', min:String(range[0]), max:String(range[1]), value:String(value)});
    const bubble=el('span',{class:'kbsp-sl-val'}, String(value));
    function update(){ bubble.textContent = String(input.value); prefs[key]=parseInt(input.value,10); }
    input.addEventListener('input', update); wrap.appendChild(input); wrap.appendChild(bubble);
    const marks=el('div',{class:'kbsp-sl-marks'}); for(let i=range[0]; i<=range[1]; i++){ marks.appendChild(el('span',{}, String(i))); }
    wrap.appendChild(marks); return wrap;
  }

  function renderFinish(root){
    root.appendChild(el('h2',{},'Finputsning'));
    root.appendChild(el('p',{class:'kbsp-sub'},'Välj hur du vill ha hetta, syra och sälta.'));
    root.appendChild(slider('Hettograd','heat_level',[0,10],prefs.heat_level));
    root.appendChild(slider('Syragrad','acid_level',[0,10],prefs.acid_level));
    root.appendChild(slider('Saltgrad','salt_level',[0,10],prefs.salt_level));
    const nav=el('div',{class:'kbsp-nav'});
    const back=el('button',{class:'kbsp-btn kbsp-light',type:'button'},'Tillbaka');
    back.onclick=()=>{ step=Math.max(0, CONFIG.questions.length-1); answers.pop(); render(root); };
    const go=el('button',{class:'kbsp-btn',type:'button'},'Visa mitt resultat');
    go.onclick=()=> computeResult(root);
    nav.appendChild(back); nav.appendChild(go); root.appendChild(nav);
  }

  function cosine(dims, target){
    let xy=0,x2=0,y2=0;
    Object.keys(target).forEach(k=>{ const x=Number(dims[k]||0); const y=Number(target[k]||0); xy+=x*y; x2+=x*x; y2+=y*y; });
    const den = Math.sqrt(Math.max(1e-9,x2))*Math.sqrt(Math.max(1e-9,y2));
    return den>0 ? (xy/den) : 0;
  }

  function computeResult(root){
    // Aggregate chosen weights and dynamic maxima per dimension
    const sums = {}, maxes = {};
    (CONFIG.questions||[]).forEach((q,qi)=>{
      const ans = answers.find(a=>(a.question_id===(q.id||('q'+(qi+1)))));
      // build per-question maxima across choices for each dimension
      const perMax = {};
      (q.choices||[]).forEach(ch=>{
        if(!ch || !ch.weights) return;
        Object.keys(ch.weights).forEach(dim=>{
          const w = Number(ch.weights[dim]||0);
          perMax[dim] = Math.max(perMax[dim]||0, w);
        });
      });
      Object.keys(perMax).forEach(dim=>{ maxes[dim]=(maxes[dim]||0)+perMax[dim]; });
      if(!ans) return;
      const c = (q.choices||[])[ans.choice_index];
      if(!c || !c.weights) return;
      Object.keys(c.weights).forEach(dim=>{ sums[dim]=(sums[dim]||0)+Number(c.weights[dim]||0); });
    });
    // Convert to percentage of achievable maximum
    const dims = {};
    const allDims = new Set([...Object.keys(maxes), ...Object.keys(sums)]);
    allDims.forEach(k=>{
      const denom = Math.max(0.0001, Number(maxes[k]||0));
      const val = Math.max(0, Math.min(100, Math.round( (Number(sums[k]||0)/denom) * 100 )));
      dims[k]=val;
    });
    // Preferences adjustments
    dims.hetta = clamp((dims.hetta||0) + (prefs.heat_level-5)*6, 0, 100);
    dims.syra  = clamp((dims.syra ||0) + (prefs.acid_level-5)*6, 0, 100);
    dims.salt  = clamp((dims.salt ||0) + (prefs.salt_level-5)*6, 0, 100);

    // Choose best profile by cosine similarity
    const profiles = CONFIG.profiles||[];
    let best=null, bscore=-Infinity, second=null, sscore=-Infinity;
    profiles.forEach(p=>{
      const sc = cosine(dims, p.match_dimensions||{});
      if(sc>bscore){ second=best; sscore=bscore; best=p; bscore=sc; }
      else if(sc>sscore){ second=p; sscore=sc; }
    });
    KBSP_RENDER_RESULT(root, dims, best, second);
  }

  function renderResult(root, dims, best, second){
    root.innerHTML='';
    if(!best){ root.appendChild(el('p',{},'Kunde inte beräkna resultat.')); return; }
    root.appendChild(el('h2',{}, best.name||'Din smakprofil'));

    const tags=el('div',{class:'kbsp-tags'});
    (best.keywords||[]).forEach(k=> tags.appendChild(el('span',{class:'kbsp-tag'}, k)));
    root.appendChild(tags);

    const rawText = normalizeText(best.horoscope_default||best.horoscope||'');
    const raw = rawText.split('\\n').map(p=>p.trim()).filter(Boolean);
    const bullets = raw.filter(p=>p.startsWith('★')).map(p=>p.replace(/^★\\s?/,''));
    const paras = raw.filter(p=>!p.startsWith('★') && !/^\\*\\*/.test(p));
    const titleLine = raw.find(p=>/^\\*\\*.+\\*\\*$/.test(p));
    if(titleLine){ root.appendChild(el('h3',{class:'kbsp-h3'}, titleLine.replace(/^\\*\\*|\\*\\*$/g,''))); }
    paras.forEach(p=> root.appendChild(el('p',{class:'kbsp-sub'}, p)));
    if(bullets.length){ const ul=el('ul',{class:'kbsp-bullets'}); bullets.forEach(b=> ul.appendChild(el('li',{}, b))); root.appendChild(ul); }

    // Bars with stable order
    const present = new Set(Object.keys(dims));
    const keys = DIM_ORDER.filter(k => present.has(k) || (KBSP && KBSP.embedded && KBSP.embedded.settings && KBSP.embedded.settings.dimensions && Object.prototype.hasOwnProperty.call(KBSP.embedded.settings.dimensions,k)));
    keys.forEach(k=>{
      const val = clamp(Number(dims[k]||0),0,100);
      const row=el('div',{class:'kbsp-barrow'});
      row.appendChild(el('div',{class:'kbsp-barlabel'}, k));
      const bar=el('div',{class:'kbsp-bar'});
      const inner=el('div',{class:'kbsp-bar-inner'}); inner.style.width = val+'%';
      const pct=el('div',{class:'kbsp-bar-pct'}, val+'%');
      bar.appendChild(inner); bar.appendChild(pct); row.appendChild(bar); root.appendChild(row);
    });

    const share=el('a',{class:'kbsp-btn',href:'#','aria-label':'Dela på Facebook'},'Dela denna på Facebook');
    share.onclick=(e)=>{ e.preventDefault(); const u=encodeURIComponent(location.href); const t=encodeURIComponent((best&&best.name)?best.name:''); window.open('https://www.facebook.com/sharer/sharer.php?u='+u+'&quote='+t,'_blank','noopener,width=680,height=640'); };
    const again=el('button',{class:'kbsp-btn kbsp-light',type:'button'},'Gör om testet');
    again.onclick=()=>{ phase='intro'; step=0; answers=[]; render(root); };
    const nav=el('div',{class:'kbsp-nav'}); nav.appendChild(again); nav.appendChild(share); root.appendChild(nav);
  }

  // Public boot
  window.KBSP_START = function(){
    try{
      rootEl = document.getElementById('kbsp-root') || qs('.kbsp-app');
      if(!rootEl){
        rootEl = document.createElement('div'); rootEl.id='kbsp-root'; rootEl.className='kbsp-app';
        document.body.insertBefore(rootEl, document.body.firstChild||null);
      }
      // Intro first, always
      phase='intro';
      // Use embedded config only (no REST required)
      if(window.KBSP && KBSP.embedded){
        CONFIG = KBSP.embedded;
      } else {
        CONFIG = {questions:[],profiles:[],settings:{dimensions:{}}};
      }
      render(rootEl);
    }catch(e){
      console.error('KBSP_START error', e);
      try{
        var box=document.createElement('div'); box.className='kbsp-error';
        box.textContent='Smakprofilören kunde inte starta. Ladda om sidan eller kontakta support.';
        document.body.appendChild(box);
      }catch(_){}
    }
  };

  // Auto-run
  document.addEventListener('DOMContentLoaded', function(){ if(window.KBSP_START) window.KBSP_START(); });

})();


// Improved result renderer with guaranteed horoscope (no dependency on outer 'el')

function KBSP_RENDER_RESULT(root, dims, best, second){
  try {
    function E(tag, attrs, text){
      const n = document.createElement(tag);
      if(attrs){
        if(attrs.class) n.className = attrs.class;
        for(const k in attrs){
          if(k==='class') continue;
          if(k==='style' && typeof attrs[k]==='object'){ Object.assign(n.style, attrs[k]); }
          else n.setAttribute(k, attrs[k]);
        }
      }
      if(text!==undefined && text!==null) n.textContent = text;
      return n;
    }
    function N(s){
      if(!s || typeof s!=='string') return '';
      return s.replace(/\r\n/g,'\n').replace(/\u00a0/g,' ').trim();
    }
    function clamp(v, a, b){ return Math.max(a, Math.min(b, v)); }

    root.innerHTML='';
    if(!best){ root.appendChild(E('p',{},'Kunde inte beräkna resultat.')); return; }

    root.appendChild(E('h2',{}, best.name||'Din smakprofil'));
    const tags=E('div',{class:'kbsp-tags'});
    (Array.isArray(best.keywords)?best.keywords:[]).forEach(k=> tags.appendChild(E('span',{class:'kbsp-tag'}, k)));
    root.appendChild(tags);

    const rawText = N(best.horoscope_default || best.horoscope || '');
    let content;
    if(rawText.length){
      const lines = rawText.split('\n').map(t=>t.trim()).filter(Boolean);
      const bullets = lines.filter(p=>/^★/.test(p)).map(p=>p.replace(/^★\s?/,''));
      const paras = lines.filter(p=>!/^★/.test(p) && !/^\*\*.+\*\*$/.test(p));
      const titleLine = lines.find(p=>/^\*\*.+\*\*$/.test(p));
      content = {title: titleLine ? titleLine.replace(/^\*\*|\*\*$/g,'') : 'Ditt smakhoroskop', paragraphs: paras, bullets: bullets};
    } else {
      const top = Object.entries(dims||{}).sort((a,b)=>b[1]-a[1]).slice(0,3).map(([k])=>k);
      const keys = (best.keywords||[]).slice(0,3).join(', ');
      const paras = [
        'Du är '+(best.name||'unik')+'. '+(keys?('Nyckelord: '+keys+'. '):'')+'Din smak rör sig kring '+top.join(', ')+'.',
        'Du balanserar smaker och texturer med självklarhet. Vardagen får en liten twist och gästerna lämnar nöjda.'
      ];
      const bullets = [
        'Bygg smak i lager utan att tappa råvaran',
        'Jobba med kontraster – krispigt, krämigt och fräscht',
        'Justera på slutet med syra, sälta och en nypa hetta'
      ];
      content = {title:'Ditt smakhoroskop', paragraphs:paras, bullets:bullets};
    }
    const horo = E('div',{class:'kbsp-horo'});
    if(content.title) horo.appendChild(E('h3',{class:'kbsp-h3'}, content.title));
    (content.paragraphs||[]).forEach(p=> horo.appendChild(E('p',{class:'kbsp-par'}, p)));
    if(content.bullets && content.bullets.length){
      const ul=E('ul',{class:'kbsp-bullets'});
      content.bullets.forEach(b=> ul.appendChild(E('li',{}, b)));
      horo.appendChild(ul);
    }
    root.appendChild(horo);

    const ORDER = ['comfort','jordig','hetta','syra','street','umami','fine','fräsch','sötma','salt'];
    ORDER.forEach(k=>{
      const val = clamp(Number((dims && dims[k])||0),0,100);
      const row=E('div',{class:'kbsp-barrow'});
      row.appendChild(E('div',{class:'kbsp-barlabel'}, k));
      const bar=E('div',{class:'kbsp-bar'});
      const inner=E('div',{class:'kbsp-bar-inner'}); inner.style.width = val+'%';
      const pct=E('div',{class:'kbsp-bar-pct'}, val+'%');
      bar.appendChild(inner); bar.appendChild(pct); row.appendChild(bar); root.appendChild(row);
    });

    const share=E('a',{class:'kbsp-btn kbsp-fb',href:'#','aria-label':'Dela på Facebook'},'Dela på Facebook');
    share.onclick=(e)=>{ e.preventDefault(); const u=encodeURIComponent(location.href); const t=encodeURIComponent((best&&best.name)?best.name:''); window.open('https://www.facebook.com/sharer/sharer.php?u='+u+'&quote='+t,'_blank','noopener,width=680,height=640'); };
    const again=E('button',{class:'kbsp-btn kbsp-light',type:'button'},'Gör om testet');
    again.onclick=()=>{ phase='intro'; step=0; answers=[]; render(root); };
    const nav=E('div',{class:'kbsp-nav'}); nav.appendChild(again); nav.appendChild(share); root.appendChild(nav);

  } catch(err){
    console.error('KBSP_RENDER_RESULT failed', err);
    try{ root.appendChild(document.createTextNode('Ett oväntat fel inträffade. Prova igen.')); }catch(e){}
  }
}


// KBSP boot: read config from data attribute if available
document.addEventListener('DOMContentLoaded', function(){
  var root = document.getElementById('kbsp-app');
  if(!root){ return; }
  if(!window.KBSP){
    var data = root.getAttribute('data-kbsp');
    if(data){ try{ window.KBSP = JSON.parse(data); }catch(e){ console.error('KBSP data parse failed', e); } }
  }
  try{ start(root); } catch(e){ console.error('KBSP start failed', e); }
});

function getProfileBySlug(slug){return (window.KBSP_SEED.profiles||[]).find(p=>p.slug===slug);}