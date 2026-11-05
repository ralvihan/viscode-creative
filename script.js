(function () {
  /* ============================================================
     GANTI NOMOR WHATSAPP DI SINI (format internasional, tanpa + / spasi)
     Contoh: '6281234567890'. Semua tombol "Konsultasi" ikut otomatis.
     ============================================================ */
  var WA_NUMBER = '62XXXXXXXXXX';
  var WA_TEXT = 'Halo Viscode Creative, saya ingin konsultasi.';

  var waLinks = Array.prototype.slice.call(document.querySelectorAll('[data-wa]'));
  waLinks.forEach(function (a) {
    a.setAttribute('href', 'https://wa.me/' + WA_NUMBER + '?text=' + encodeURIComponent(WA_TEXT));
    a.setAttribute('target', '_blank');
    a.setAttribute('rel', 'noopener');
  });

  var EN = {
    'nav.features': 'Services',
    'nav.process': 'Process',
    'nav.portfolio': 'Portfolio',
    'nav.pricing': 'Pricing',
    'nav.contact': 'Contact',
    'nav.cta': 'Consult',
    'hero.title': 'Bring your ideas to life with design made for you',
    'hero.sub': 'From websites to graphic design, Viscode Creative blends technology and creativity into digital solutions that are functional, engaging, and flexible.',
    'hero.cta': 'Consult now',
    'hero.prices': 'View prices',
    'ph.h': 'Your business, looking more professional',
    'ph.p': 'A fast, clean website that is ready for customers anytime.',
    'ph.btn': 'View products',
    'about.text': 'We help individuals, small businesses, organizations, and event organizers look their best. Website development and graphic design live in one place, tailored to your needs and budget.',
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
    'foot.desc': 'Website development, graphic design, and ID card & event services for individuals, small businesses, organizations, and event organizers.',
    'foot.menu': 'Menu',
    'foot.contact': 'Contact',
    'foot.rights': 'All rights reserved.'
  };

  var nodes = Array.prototype.slice.call(document.querySelectorAll('[data-i18n]'));
  nodes.forEach(function (el) {
    el.setAttribute('data-id', el.textContent);
  });

  var buttons = Array.prototype.slice.call(document.querySelectorAll('[data-lang]'));

  function applyLang(lang) {
    nodes.forEach(function (el) {
      var key = el.getAttribute('data-i18n');
      if (lang === 'en' && EN[key] !== undefined) {
        el.textContent = EN[key];
      } else {
        el.textContent = el.getAttribute('data-id');
      }
    });
    document.documentElement.setAttribute('lang', lang);
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

    // Pause animasi kalau hero/CTA lagi di luar layar (biar ringan)
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

  var year = document.getElementById('year');
  if (year) year.textContent = new Date().getFullYear();
})();