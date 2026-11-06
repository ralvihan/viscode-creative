(function () {
  var WA_NUMBER = '62XXXXXXXXXX';
  var WA_TEXT = 'Halo Viscode Creative, saya ingin konsultasi.';

  var waLinks = Array.prototype.slice.call(document.querySelectorAll('[data-wa]'));
  waLinks.forEach(function (a) {
    a.setAttribute('href', 'https://wa.me/' + WA_NUMBER + '?text=' + encodeURIComponent(WA_TEXT));
    a.setAttribute('target', '_blank');
    a.setAttribute('rel', 'noopener');
  });

  var G = {
    code: '<path d="M8.5 7L4 12l4.5 5M15.5 7L20 12l-4.5 5M13.5 5l-3 14"/>',
    term: '<rect x="3" y="4.5" width="18" height="15" rx="3"/><path d="M7.5 10l3 2.5-3 2.5M13 15h4"/>',
    pen: '<path d="M12 3l5 8-5 10-5-10z"/><circle cx="12" cy="11" r="1.4"/><path d="M12 12.4V21"/>',
    layers: '<path d="M12 3l9 5-9 5-9-5z"/><path d="M3 13l9 5 9-5"/>',
    palette: '<path d="M12 3a9 9 0 1 0 0 18c1.4 0 2-1 1.6-2.1-.5-1.3.3-2.4 1.6-2.4H17a4 4 0 0 0 4-4c0-5-4-9.5-9-9.5z"/><circle cx="7.5" cy="11" r="1"/><circle cx="10.5" cy="7" r="1"/><circle cx="15" cy="7.5" r="1"/>',
    cursor: '<path d="M5 3l14 7-6 2-2 6z"/>',
    frame: '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 9h16M9 9v11"/>',
    browser: '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 9.5h18"/><circle cx="6.5" cy="7.3" r=".4"/><circle cx="9" cy="7.3" r=".4"/>',
    braces: '<path d="M9 4c-2 0-3 1-3 3v2c0 1.5-1 2.5-2.5 3 1.5.5 2.5 1.5 2.5 3v2c0 2 1 3 3 3M15 4c2 0 3 1 3 3v2c0 1.5 1 2.5 2.5 3-1.5.5-2.5 1.5-2.5 3v2c0 2-1 3-3 3"/>',
    image: '<rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="9" cy="10" r="1.6"/><path d="M4 18l5-5 4 4 3-3 4 4"/>',
    type: '<path d="M4 19l5-13 5 13M6 14h6M16 19l3.2-8L22 19M17 16.5h4"/>',
    curve: '<path d="M4 18C8 4 16 20 20 6"/><circle cx="4" cy="18" r="1.8"/><circle cx="20" cy="6" r="1.8"/>',
    branch: '<circle cx="6" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="9" r="2"/><path d="M6 8v8M18 11c0 3-4 3-10 5"/>',
    db: '<ellipse cx="12" cy="6" rx="7" ry="3"/><path d="M5 6v6c0 1.7 3.1 3 7 3s7-1.3 7-3V6M5 12v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"/>',
    play: '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="M10 9.5v5l4.5-2.5z"/>',
    spark: '<path d="M12 3c.6 5 4 8.4 9 9-5 .6-8.4 4-9 9-.6-5-4-8.4-9-9 5-.6 8.4-4 9-9z"/>'
  };
  var BG = {
    navy: 'linear-gradient(145deg,#2a3f8f,#0b1534)',
    blue: 'linear-gradient(145deg,#8cb8ff,#3b6fe0)',
    lav: 'linear-gradient(145deg,#e2d8fa,#b9a4ee)',
    vio: 'linear-gradient(145deg,#a58cf0,#6a46d0)',
    white: '#fff',
    sky: 'linear-gradient(145deg,#c3d9ff,#6aa4ff)'
  };
  var DEVICON = 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/';
  var ICONS = [
    { d: 'vscode/vscode-original.svg',             g: 'code',    c: 'blue',  x: 22, y: 20, s: 54, b: 0, r: -10 },
    { d: 'illustrator/illustrator-plain.svg',      g: 'pen',     c: 'vio',   x: 78, y: 17, s: 52, b: 0, r: 8 },
    { d: 'photoshop/photoshop-plain.svg',          g: 'layers',  c: 'navy',  x: 10, y: 52, s: 64, b: 3, r: -8 },
    { d: 'figma/figma-original.svg',               g: 'palette', c: 'white', x: 88, y: 48, s: 66, b: 3, r: 10 },
    { d: 'github/github-original.svg',             g: 'term',    c: 'navy',  x: 31, y: 78, s: 56, b: 0, r: 6 },
    { d: 'canva/canva-original.svg',               g: 'cursor',  c: 'white', x: 68, y: 80, s: 50, b: 0, r: -6 },
    { d: 'indesign/indesign-plain.svg',            g: 'frame',   c: 'sky',   x: 40, y: 14, s: 38, b: 1, r: 8 },
    { d: 'react/react-original.svg',               g: 'braces',  c: 'lav',   x: 60, y: 24, s: 34, b: 1, r: -8 },
    { d: 'premierepro/premierepro-plain.svg',      g: 'image',   c: 'blue',  x: 18, y: 36, s: 36, b: 2, r: 10 },
    { d: 'aftereffects/aftereffects-plain.svg',    g: 'type',    c: 'white', x: 82, y: 70, s: 40, b: 2, r: -10 },
    { d: 'coreldraw/coreldraw-plain.svg',          g: 'curve',   c: 'vio',   x: 50, y: 88, s: 34, b: 1, r: 12 },
    { d: 'nodejs/nodejs-original.svg',             g: 'browser', c: 'navy',  x: 4,  y: 82, s: 46, b: 6, r: -12 },
    { d: 'vercel/vercel-original.svg',             g: 'play',    c: 'lav',   x: 95, y: 20, s: 44, b: 6, r: 12 },
    { d: 'tailwindcss/tailwindcss-original.svg',   g: 'branch',  c: 'sky',   x: 8,  y: 14, s: 30, b: 5, r: 8 },
    { d: 'wordpress/wordpress-plain.svg',          g: 'db',      c: 'blue',  x: 93, y: 88, s: 34, b: 5, r: -8 },
    { g: 'spark', c: 'white', x: 72, y: 36, s: 24, b: 2, r: 0 },
    { g: 'spark', c: 'lav',   x: 26, y: 62, s: 22, b: 2, r: 0 }
  ];
  function glyph(it) {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' + G[it.g] + '</svg>';
  }
  var orbit = document.getElementById('orbit');
  if (orbit) {
    ICONS.forEach(function (it, i) {
      var el = document.createElement('div');
      el.className = 'oi' + (it.c === 'white' ? ' light' : '');
      el.style.cssText = 'left:' + it.x + '%;top:' + it.y + '%;--s:' + it.s + 'px;--b:' + it.b + 'px;--r:' + it.r +
        'deg;--bg:' + BG[it.c] + ';--d:' + (6 + (i % 5)) + 's;--dl:-' + (i * 0.9).toFixed(1) + 's';
      var tile = document.createElement('div');
      tile.className = 'oi-t';
      if (it.d) {
        tile.innerHTML = glyph(it);
        var img = new Image();
        img.alt = '';
        img.onload = function () {
          tile.innerHTML = '';
          tile.appendChild(img);
          el.classList.add('brand');
        };
        img.src = DEVICON + it.d;
      } else {
        tile.innerHTML = glyph(it);
      }
      el.appendChild(tile);
      orbit.appendChild(el);
    });
  }

  var Rotator = (function () {
    var WORDS = {
      id: ['website', 'desain grafis', 'branding', 'ID card', 'event'],
      en: ['websites', 'graphic design', 'branding', 'ID cards', 'events']
    };
    var ENTER = ['type', 'flip', 'blur'];
    var EDITS = ['mark', 'circle', 'b', 'i', 'u'];
    var CANCEL = {};
    var rw = document.getElementById('rw');
    var wordsEl = document.getElementById('rwWords');
    if (!rw || !wordsEl) return { setLang: function () {} };
    var sel = document.getElementById('rwSel');
    var hs = document.getElementById('rwHs');
    var circ = document.getElementById('rwCirc');
    var circPath = document.getElementById('rwCircPath');
    var tb = document.getElementById('rwTb');
    var cur = document.getElementById('rwCur');
    var pen = document.getElementById('rwPen');
    var pill = document.getElementById('hpill');
    var icons = pill ? Array.prototype.slice.call(pill.querySelectorAll('.hp-i')) : [];
    var hero = document.querySelector('.hero');
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var lang = 'id', idx = 0, step = 0, els = [], runId = 0;

    function sleep(ms, id) {
      return new Promise(function (res, rej) {
        setTimeout(function () { if (id === runId) res(); else rej(CANCEL); }, ms);
      });
    }
    function build() {
      wordsEl.innerHTML = '';
      els = WORDS[lang].map(function (w) {
        var s = document.createElement('span');
        s.className = 'rw-w';
        s.setAttribute('aria-hidden', 'true');
        s.style.setProperty('--n', w.length);
        w.split('').forEach(function (ch, i) {
          var c = document.createElement('span');
          c.className = 'ch';
          c.style.setProperty('--i', i);
          c.textContent = ch === ' ' ? '\u00a0' : ch;
          s.appendChild(c);
        });
        wordsEl.appendChild(s);
        return s;
      });
      els[idx].className = 'rw-w is-on';
      els[idx].removeAttribute('aria-hidden');
    }
    function fit() {
      if (els[idx]) wordsEl.style.width = els[idx].offsetWidth + 'px';
    }
    function paint() {
      icons.forEach(function (ic, i) { ic.classList.toggle('on', i === idx % icons.length); });
      if (pill) pill.style.setProperty('--k', idx);
    }
    function hardReset() {
      [sel, hs, circ, tb, cur, pen].forEach(function (el) { el.classList.remove('on', 'click'); });
      Array.prototype.forEach.call(tb.querySelectorAll('.act'), function (b) { b.classList.remove('act'); });
      circPath.style.strokeDashoffset = 1;
      rw.classList.remove('fmt-b', 'fmt-i', 'fmt-u');
    }
    function place(el, x, y, dur) {
      el.style.setProperty('--cd', dur + 's');
      el.style.left = x;
      el.style.top = y;
    }

    /* ---- efek edit ---- */
    async function doMark(S) {
      place(cur, '0px', '100%', 0);
      void cur.offsetWidth;
      cur.classList.add('on');
      await S(450);
      place(cur, '100%', '0%', 0.9);
      sel.classList.add('on');
      await S(950);
      hs.classList.add('on');
      cur.classList.add('click');
      await S(350);
      cur.classList.remove('click');
      await S(900);
    }
    function circGeo() {
      var rr = rw.getBoundingClientRect(), cr = circ.getBoundingClientRect();
      return { ox: cr.left - rr.left, oy: cr.top - rr.top, sx: cr.width / 200, sy: cr.height / 80 };
    }
    function drawCircle(id, dur, g) {
      var fs = parseFloat(getComputedStyle(rw).fontSize) || 60;
      circPath.style.strokeWidth = Math.max(3, Math.min(6, fs * 0.05)) / ((g.sx + g.sy) / 2);
      var L = circPath.getTotalLength();
      return new Promise(function (res, rej) {
        var t0 = null;
        function f(ts) {
          if (id !== runId) return rej(CANCEL);
          if (t0 === null) t0 = ts;
          var t = Math.min(1, (ts - t0) / dur);
          var e = t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
          var p = circPath.getPointAtLength(L * e);
          circPath.style.strokeDashoffset = 1 - e;
          pen.style.left = (g.ox + p.x * g.sx) + 'px';
          pen.style.top = (g.oy + p.y * g.sy) + 'px';
          if (t < 1) requestAnimationFrame(f); else res();
        }
        requestAnimationFrame(f);
      });
    }
    async function doCircle(S, id) {
      var g = circGeo();
      circ.classList.add('on');
      place(pen, (g.ox + 32 * g.sx) + 'px', (g.oy + 15 * g.sy) + 'px', 0);
      void pen.offsetWidth;
      pen.classList.add('on');
      await S(250);
      await drawCircle(id, 1100, g);
      await S(150);
      pen.classList.remove('on');
      await S(1400);
    }
    async function doFormat(S, kind) {
      tb.classList.add('on');
      await S(500);
      var btn = tb.querySelector('[data-k="' + kind + '"]');
      var r = rw.getBoundingClientRect(), b = btn.getBoundingClientRect();
      place(cur, (r.width * 1.08) + 'px', (r.height * 1.5) + 'px', 0);
      void cur.offsetWidth;
      cur.classList.add('on');
      await S(200);
      place(cur, (b.left - r.left + b.width / 2) + 'px', (b.top - r.top + b.height / 2) + 'px', 0.85);
      await S(950);
      cur.classList.add('click');
      btn.classList.add('act', 'press');
      await S(180);
      btn.classList.remove('press');
      rw.classList.add('fmt-' + kind);
      fit();
      await S(750);
      cur.classList.remove('click');
    }
    async function clearFx(S) {
      [sel, hs, circ, tb, cur, pen].forEach(function (el) { el.classList.remove('on'); });
      await S(450);
      circPath.style.strokeDashoffset = 1;
      Array.prototype.forEach.call(tb.querySelectorAll('.act'), function (b) { b.classList.remove('act', 'press'); });
    }

    async function typeIn(S, w) {
      var chs = w.querySelectorAll('.ch');
      w.className = 'rw-w is-on typing';
      for (var i = 0; i < chs.length; i++) {
        await S(75);
        if (i) chs[i - 1].classList.remove('cur');
        chs[i].classList.add('t', 'cur');
      }
      await S(450);
      w.className = 'rw-w is-on';
      Array.prototype.forEach.call(chs, function (c) { c.classList.remove('t', 'cur'); });
    }
    async function typeOut(S, w) {
      var chs = w.querySelectorAll('.ch');
      Array.prototype.forEach.call(chs, function (c) { c.classList.add('t'); });
      w.className = 'rw-w is-on typing';
      for (var i = chs.length - 1; i >= 0; i--) {
        chs[i].classList.add('cur');
        await S(55);
        chs[i].classList.remove('t', 'cur');
      }
      await S(80);
    }

    async function run(id) {
      var S = function (ms) { return sleep(ms, id); };
      var first = true;
      try {
        while (id === runId) {
          while (document.hidden || (hero && hero.classList.contains('is-offscreen'))) await S(400);
          var w = els[idx];
          w.removeAttribute('aria-hidden');
          fit();
          paint();
          if (reduce) {
            w.className = 'rw-w is-on';
            await S(3200);
            w.className = 'rw-w';
            w.setAttribute('aria-hidden', 'true');
            idx = (idx + 1) % els.length;
            continue;
          }
          var enter = ENTER[(step * 2 + 1) % 3];
          var edit = EDITS[step % EDITS.length];
          if (first) {
            await S(1100);
            first = false;
          } else {
            if (enter === 'type') {
              await typeIn(S, w);
            } else {
              w.className = 'rw-w is-on in-' + enter;
              await S(750);
              w.className = 'rw-w is-on';
            }
            await S(250);
          }
          if (edit === 'mark') await doMark(S);
          else if (edit === 'circle') await doCircle(S, id);
          else await doFormat(S, edit);
          await S(900);
          await clearFx(S);
          if (enter === 'type') {
            await typeOut(S, w);
          } else {
            w.className = 'rw-w is-on out-' + enter;
            await S(560);
          }
          w.className = 'rw-w';
          w.setAttribute('aria-hidden', 'true');
          rw.classList.remove('fmt-b', 'fmt-i', 'fmt-u');
          step++;
          idx = (idx + 1) % els.length;
          await S(120);
        }
      } catch (e) {
        if (e !== CANCEL) throw e;
      }
    }
    function setLang(l) {
      runId++;
      lang = l;
      idx = 0;
      hardReset();
      build();
      fit();
      paint();
      run(runId);
    }

    build();
    fit();
    paint();
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(fit);
    window.addEventListener('load', fit);
    window.addEventListener('resize', fit);
    run(++runId);
    return { setLang: setLang };
  })();

  var EN = {
    'nav.features': 'Services',
    'nav.process': 'Process',
    'nav.portfolio': 'Portfolio',
    'nav.pricing': 'Pricing',
    'nav.contact': 'Contact',
    'nav.cta': 'Consult',
    'hero.l1': '<span class="tw g">Turn</span> your ideas into',
    'hero.l3': '<span class="tw g">that</span> people remember',
    'hero.s1': 'Websites',
    'hero.s2': 'Graphic Design',
    'hero.s3': 'ID Card & Event',
    'ph.h': 'Your business, looking more professional',
    'ph.p': 'A fast, clean website that is ready for customers anytime.',
    'ph.btn': 'View products',
    'about.text': 'We help individuals, small businesses, organizations, and event organizers <span class="scribble"><span class="scr-word">look their best</span><svg class="scr-svg" viewBox="0 0 200 80" preserveAspectRatio="none" aria-hidden="true"><path pathLength="1" d="M32 15C84 1 172 5 191 33C206 62 122 77 60 70C13 65 -3 40 22 22C42 8 92 5 136 8"/></svg></span>. Website development and graphic design live in one place, tailored to your needs and budget.',
    'about.s1': 'Core creative services',
    'about.s2': 'A complete digital solution',
    'about.s3': 'All your creative needs',
    'about.v3': '1 place',
    'vm.visi': 'Vision',
    'vm.visi.t': 'To become a trusted digital partner that combines technology and art.',
    'vm.misi': 'Mission',
    'vm.misi.t': 'To deliver innovative, flexible, and high-quality web and creative design services.',
    'feat.title': 'Everything you need to stand out online',
    'feat.sub': 'Our services are built to be flexible, so they fit different needs and budgets.',
    'f1.t': 'Website Development',
    'f1.d': 'Landing pages, company profile sites, SME websites, product catalogs, digital portfolios, and event websites.',
    'f2.t': 'Graphic Design',
    'f2.d': 'Logos and branding, posters and banners, social media content, plus brochures and flyers that feel consistent and memorable.',
    'f3.t': 'ID Card & Event',
    'f3.d': 'Identity cards, committee ID cards, event publication materials, and print coordination with vendors.',
    'f4.t': 'Flexible to Your Budget',
    'f4.d': 'Choose single services, bundle packages, or request custom add-ons, with transparent pricing from the start.',
    'mk.title': 'Made for Everyone Who Wants to Stand Out',
    'mk.sub': 'From local businesses to event organizers, we are ready to support your digital needs.',
    'mk1.t': 'SMEs & local businesses',
    'mk1.d': 'Websites, logos, and social content for your business.',
    'mk2.t': 'Campus & school organizations',
    'mk2.d': 'Activity posters, ID cards, and organization publications.',
    'mk3.t': 'Students & communities',
    'mk3.d': 'Digital portfolios and visual identity for communities.',
    'mk4.t': 'Event organizers',
    'mk4.d': 'Event landing pages, digital posters, and committee ID cards.',
    'mk.line': 'Website • identity • publication • digital experience',
    'steps.title': 'Seven Stages to Your Best Work',
    'steps.sub': 'A simple flow from consultation to handover, with clarity at every stage.',
    'steps.t1': 'Preparation',
    'steps.t2': 'Start',
    'steps.t3': 'Finish',
    'steps.cta.t': 'Ready to start your project?',
    'steps.cta.d': 'Tell us your idea, and we will help from the very first step.',
    's1.t': 'Consultation',
    's1.d': 'Tell us your needs and goals. It only takes a few minutes.',
    's2.t': 'Brief',
    's2.d': 'Share your style, references, and budget in a short brief.',
    's3.t': 'Quotation',
    's3.d': 'Receive a clear direction and a transparent cost estimate.',
    's4.t': 'Production',
    's4.d': 'Our team builds your website and designs as agreed.',
    's5.t': 'Review',
    's5.d': 'Check the result with us and share your feedback.',
    's6.t': 'Finalization',
    's6.d': 'Polish the result until it is truly ready to use.',
    's7.t': 'Handover',
    's7.d': 'We hand over the final result so you can launch with confidence.',
    'pf.title': 'Portfolio Concept',
    'pf.sub': 'Sample Concept — simulated visuals that show the direction of our design quality.',
    'pf1': 'SME website',
    'pf2': 'Logo & branding',
    'pf3': 'Poster & social content',
    'pf4': 'ID card & banner',
    'price.title': 'Transparent Pricing From the Start',
    'price.sub': 'Simulated prices, subject to change based on project needs.',
    'price.bundle': 'Bundle Packages',
    'price.paket': 'Package price',
    'price.btn': 'Choose plan',
    'price.pop': 'Most affordable',
    'price.pill': 'Pricing',
    'price.per': '/package',
    'p1.desc': 'A website, logo, and social content for businesses starting out online.',
    'p2.desc': 'Posters, ID cards, and announcement templates for organization activities.',
    'p3.desc': 'An event landing page, poster, and committee ID cards, ready to go.',
    'p1.name': 'Digital SME',
    'p1.price': 'IDR 450,000',
    'p1.a': 'Simple landing page',
    'p1.b': 'Simple logo',
    'p1.c': '3 social content designs',
    'p2.name': 'Organization',
    'p2.price': 'IDR 175,000',
    'p2.a': 'Activity poster',
    'p2.b': 'ID card design',
    'p2.c': 'Announcement template',
    'p3.name': 'Event Starter',
    'p3.price': 'IDR 650,000',
    'p3.a': 'Event landing page',
    'p3.b': 'Digital poster',
    'p3.c': 'Committee ID card',
    'price.unit': 'Single Services',
    'u1.n': 'Landing page',
    'u1.p': 'from IDR 150,000',
    'u2.n': 'Company profile / SME website',
    'u2.p': 'from IDR 350,000',
    'u3.n': 'Custom website',
    'u3.p': 'from IDR 500,000',
    'u4.n': 'Poster design',
    'u4.p': 'from IDR 25,000',
    'u5.n': '5 social media posts',
    'u5.p': 'from IDR 75,000',
    'u6.n': 'ID card design',
    'u6.p': 'from IDR 10,000',
    'price.note': 'Simulated prices • Physical printing, domain, hosting, and extra features are charged separately.',
    'price.custom': 'Need an extra custom service?',
    'price.custom.btn': 'Contact us',
    'cta.title': 'Your Best Work Starts Here',
    'cta.text': 'Take the first step toward a stronger digital presence. Tell us what you need, then receive a clear concept and quotation.',
    'cta.btn': 'Consult now',
    'ph2.title': 'Visual identity',
    'ph2.poster': 'Poster',
    'foot.desc': 'Website development, graphic design, and ID card & event services for individuals, small businesses, organizations, and event organizers.'
  };

  var nodes = Array.prototype.slice.call(document.querySelectorAll('[data-i18n]'));
  nodes.forEach(function (el) {
    el.setAttribute('data-id', el.hasAttribute('data-html') ? el.innerHTML : el.textContent);
  });

  var buttons = Array.prototype.slice.call(document.querySelectorAll('[data-lang]'));

  function applyLang(lang) {
    nodes.forEach(function (el) {
      var key = el.getAttribute('data-i18n');
      var html = el.hasAttribute('data-html');
      var val = (lang === 'en' && EN[key] !== undefined) ? EN[key] : el.getAttribute('data-id');
      if (html) { el.innerHTML = val; } else { el.textContent = val; }
    });
    document.documentElement.setAttribute('lang', lang);
    Rotator.setLang(lang);
    buttons.forEach(function (b) {
      b.classList.toggle('active', b.getAttribute('data-lang') === lang);
    });
    try {
      localStorage.setItem('viscode-lang', lang);
    } catch (e) {}
  }

  buttons.forEach(function (b) {
    b.addEventListener('click', function () {
      applyLang(b.getAttribute('data-lang'));
    });
  });

  var saved = null;
  try {
    saved = localStorage.getItem('viscode-lang');
  } catch (e) {}
  if (saved === 'en') applyLang('en');

  var nav = document.getElementById('nav');
  function onScroll() {
    nav.classList.toggle('scrolled', window.scrollY > 10);
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  var navWrap = document.getElementById('navWrap');
  var menuBtn = document.getElementById('menuBtn');
  function setMenu(open) {
    navWrap.classList.toggle('open', open);
    menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  menuBtn.addEventListener('click', function () {
    setMenu(!navWrap.classList.contains('open'));
  });
  Array.prototype.slice.call(document.querySelectorAll('#mobileMenu a')).forEach(function (a) {
    a.addEventListener('click', function () { setMenu(false); });
  });
  document.addEventListener('click', function (e) {
    if (!navWrap.contains(e.target)) setMenu(false);
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') setMenu(false);
  });
  window.addEventListener('resize', function () {
    if (window.innerWidth > 960) setMenu(false);
  });

  var reveals = Array.prototype.slice.call(document.querySelectorAll('.reveal'));
  reveals.forEach(function (el) {
    var siblings = Array.prototype.slice.call(el.parentElement.children).filter(function (n) {
      return n.classList.contains('reveal');
    });
    var idx = siblings.indexOf(el);
    el.style.transitionDelay = Math.min(idx * 90, 360) + 'ms';
  });

  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('in');
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.14, rootMargin: '0px 0px -40px 0px' }
    );
    reveals.forEach(function (el) {
      io.observe(el);
    });

    var heavy = Array.prototype.slice.call(document.querySelectorAll('.hero, .cta'));
    var pauseIO = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        entry.target.classList.toggle('is-offscreen', !entry.isIntersecting);
      });
    }, { rootMargin: '100px 0px' });
    heavy.forEach(function (el) {
      pauseIO.observe(el);
    });
  } else {
    reveals.forEach(function (el) {
      el.classList.add('in');
    });
  }
})();