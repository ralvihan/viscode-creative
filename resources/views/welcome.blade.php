<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>VISCODE CREATIVE — Design. Develop. Innovate.</title>
<meta name="description" content="Viscode Creative: jasa pengembangan website, desain grafis, serta ID card dan event untuk individu, UMKM, organisasi, dan penyelenggara acara.">
<link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
<link rel="apple-touch-icon" href="{{ asset('assets/favicon.png') }}">
<meta name="theme-color" content="#0a1a3c">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('style.css') }}">
<script>document.documentElement.classList.add('js')</script>
<style>
  .vc-auth-link {
    display: inline-flex;
    align-items: center;
    height: 36px;
    padding: 0 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    color: var(--navy);
    background: rgba(15, 29, 69, .06);
    transition: background .2s ease, transform .2s ease;
    white-space: nowrap;
  }
  .vc-auth-link:hover {
    background: rgba(15, 29, 69, .12);
    transform: translateY(-1px);
  }
  .vc-consult-card {
    position: relative;
    z-index: 6;
    max-width: 780px;
    margin: 36px auto 0;
    padding: 36px;
    border-radius: 28px;
    background: rgba(255, 255, 255, .94);
    -webkit-backdrop-filter: blur(18px);
    backdrop-filter: blur(18px);
    box-shadow: 0 28px 70px -20px rgba(15, 29, 69, .28), inset 0 0 0 1px rgba(255, 255, 255, .85);
    text-align: left;
  }
  .vc-consult-head {
    text-align: center;
    margin-bottom: 24px;
  }
  .vc-consult-badge {
    display: inline-block;
    padding: 5px 14px;
    border-radius: 999px;
    background: rgba(59, 111, 224, .1);
    color: var(--blue);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    margin-bottom: 8px;
  }
  .vc-consult-head h3 {
    font-size: clamp(20px, 3vw, 26px);
    font-weight: 700;
    color: var(--navy);
    letter-spacing: -.02em;
  }
  .vc-consult-head p {
    font-size: 13.5px;
    color: var(--muted);
    margin-top: 4px;
  }
  .vc-form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 16px;
  }
  @media (max-width: 640px) {
    .vc-consult-card { padding: 24px 18px; }
    .vc-form-grid { grid-template-columns: 1fr; }
  }
  .vc-field label {
    display: block;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--navy);
    margin-bottom: 6px;
  }
  .vc-field label span { color: #dc2626; }
  .vc-input, .vc-select, .vc-textarea {
    width: 100%;
    padding: 12px 15px;
    border-radius: 14px;
    border: 1px solid rgba(15, 29, 69, .14);
    background: #fff;
    color: var(--ink);
    font: inherit;
    font-size: 13.5px;
    transition: border-color .2s ease, box-shadow .2s ease;
  }
  .vc-input:focus, .vc-select:focus, .vc-textarea:focus {
    outline: none;
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(59, 111, 224, .16);
  }
  .vc-submit {
    width: 100%;
    height: 48px;
    margin-top: 8px;
    border-radius: 999px;
    background: linear-gradient(180deg, #2563EB, #17243B);
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 12px 26px -8px rgba(15, 29, 69, .55);
    cursor: pointer;
    transition: transform .2s ease, box-shadow .2s ease;
  }
  .vc-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 16px 32px -8px rgba(15, 29, 69, .65);
  }
  .vc-alert-success {
    padding: 16px 20px;
    border-radius: 16px;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #065f46;
    font-size: 13.5px;
    margin-bottom: 20px;
    text-align: center;
  }
  .vc-alert-error {
    padding: 14px 18px;
    border-radius: 16px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    font-size: 12.5px;
    margin-bottom: 20px;
  }
</style>
</head>
<body>

<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <linearGradient id="lgNavy" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#1b3a82"/>
      <stop offset="1" stop-color="#061230"/>
    </linearGradient>
    <linearGradient id="lgBlue" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0" stop-color="#92bcf8"/>
      <stop offset="1" stop-color="#3f86f0"/>
    </linearGradient>
    <symbol id="spark" viewBox="0 0 24 24">
      <path d="M12 2c.6 5.4 4.6 9.4 10 10-5.4.6-9.4 4.6-10 10-.6-5.4-4.6-9.4-10-10 5.4-.6 9.4-4.6 10-10z" fill="currentColor"/>
    </symbol>
    <symbol id="check" viewBox="0 0 24 24">
      <path d="M5 12.5l4.5 4.5L19 7.5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
    </symbol>
    <symbol id="check-c" viewBox="0 0 24 24">
      <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.6"/>
      <path d="M8 12.4l2.7 2.7L16 9.6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </symbol>
    <symbol id="target" viewBox="0 0 24 24">
      <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.8"/>
      <circle cx="12" cy="12" r="4.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
    </symbol>
    <symbol id="arrow" viewBox="0 0 24 24">
      <path d="M7 17L17 7M9 7h8v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </symbol>
    <symbol id="info" viewBox="0 0 24 24">
      <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.8"/>
      <path d="M12 11v5.2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      <circle cx="12" cy="7.8" r="1.2" fill="currentColor"/>
    </symbol>
    <symbol id="image" viewBox="0 0 24 24">
      <rect x="3" y="4" width="18" height="16" rx="3" fill="none" stroke="currentColor" stroke-width="1.8"/>
      <circle cx="9" cy="10" r="1.8" fill="currentColor"/>
      <path d="M4 18l5-5 4 4 3-3 4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </symbol>
  </defs>
</svg>

<header class="nav-wrap" id="navWrap">
  <nav class="nav" id="nav">
    <a href="#top" class="brand" aria-label="VISCODE Creative">
      <img class="brand-mark" src="{{ asset('assets/logo.png') }}" alt="VISCODE Creative" style="height:44px;width:auto;object-fit:contain">
    </a>
    <ul class="nav-links">
      <li><a href="#about" data-i18n="nav.about">Tentang</a></li>
      <li><a href="#features" data-i18n="nav.features">Layanan</a></li>
      <li><a href="#market" data-i18n="nav.market">Untuk Siapa</a></li>
      <li><a href="#portfolio" data-i18n="nav.portfolio">Portofolio</a></li>
      <li><a href="#process" data-i18n="nav.process">Proses</a></li>
      <li><a href="#pricing" data-i18n="nav.pricing">Harga</a></li>
      <li><a href="#vision" data-i18n="nav.vision">Visi &amp; Misi</a></li>
      <li><a href="#team" data-i18n="nav.team">Tim</a></li>
      <li><a href="#download" data-i18n="nav.contact">Kontak</a></li>
    </ul>
    <div class="nav-right">
      <div class="lang" role="group" aria-label="Language">
        <button type="button" data-lang="id" class="active">ID</button>
        <button type="button" data-lang="en">EN</button>
      </div>
      @auth
        <a href="{{ route('dashboard') }}" class="vc-auth-link">Dashboard BMS</a>
      @else
        <a href="{{ route('login') }}" class="vc-auth-link">Login Tim</a>
      @endauth
      <a href="#download" class="nav-cta" data-i18n="nav.cta">Konsultasi</a>
      <button type="button" class="menu-btn" id="menuBtn" aria-label="Menu" aria-expanded="false" aria-controls="mobileMenu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </nav>

  <div class="mobile-menu" id="mobileMenu">
    <a href="#about" data-i18n="nav.about">Tentang</a>
    <a href="#features" data-i18n="nav.features">Layanan</a>
    <a href="#market" data-i18n="nav.market">Untuk Siapa</a>
    <a href="#portfolio" data-i18n="nav.portfolio">Portofolio</a>
    <a href="#process" data-i18n="nav.process">Proses</a>
    <a href="#pricing" data-i18n="nav.pricing">Harga</a>
    <a href="#vision" data-i18n="nav.vision">Visi &amp; Misi</a>
    <a href="#team" data-i18n="nav.team">Tim</a>
    <a href="#download" data-i18n="nav.contact">Kontak</a>
    @auth
      <a href="{{ route('dashboard') }}">Dashboard BMS</a>
    @else
      <a href="{{ route('login') }}">Login Tim Internal</a>
    @endauth
    <a href="#download" class="m-cta" data-i18n="nav.cta">Konsultasi</a>
  </div>
</header>

<main id="top">

  <section class="hero">
    <div class="sky"></div>
    <div class="glow"></div>
    <div class="cloud cloud-back"></div>
    <div class="cloud cloud-mid"></div>

    <div class="hero-inner">
      <h1 class="hero-title reveal">
        <span class="ht-line ht-l1" data-i18n="hero.l1" data-html><span class="tw g">Wujudkan</span> idemu jadi</span>
        <span class="ht-line ht-l2">
          <span class="hp" id="hpill" aria-hidden="true"><svg class="hp-i on" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.5 7L4 12l4.5 5M15.5 7L20 12l-4.5 5M13.5 5l-3 14"/></svg><svg class="hp-i" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l5 8-5 10-5-10z"/><circle cx="12" cy="11" r="1.4"/><path d="M12 12.4V21"/></svg><svg class="hp-i" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l9 5-9 5-9-5z"/><path d="M3 13l9 5 9-5"/></svg><svg class="hp-i" viewBox="0 0 24 24" aria-hidden="true"><rect x="4.5" y="3" width="15" height="18" rx="3"/><circle cx="12" cy="10" r="2.4"/><path d="M8 17c.8-2 2.2-3 4-3s3.2 1 4 3"/></svg><svg class="hp-i" viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="5" width="17" height="15" rx="3"/><path d="M3.5 10h17M8 3v4M16 3v4"/></svg></span>
          <span class="rw" id="rw">
            <span class="rw-sel" id="rwSel"></span>
            <span class="rw-box"><span class="rw-words" id="rwWords"><span class="rw-w is-on">website</span></span></span>
            <span class="rw-fx" id="rwFx" aria-hidden="true">
              <span class="rw-hs" id="rwHs"><i class="rw-h a"></i><i class="rw-h b"></i><i class="rw-h c"></i><i class="rw-h d"></i></span>
              <svg class="rw-circ" id="rwCirc" viewBox="0 0 200 80" preserveAspectRatio="none"><path id="rwCircPath" pathLength="1" d="M32 15C84 1 172 5 191 33C206 62 122 77 60 70C13 65 -3 40 22 22C42 8 92 5 136 8"/></svg>
              <span class="rw-tb" id="rwTb"><span class="tb-b" data-k="b">B</span><span class="tb-i" data-k="i">I</span><span class="tb-u" data-k="u">U</span></span>
              <span class="rw-cur" id="rwCur"><svg viewBox="0 0 24 24"><path d="M5 3l13.5 6.3-5.7 1.7L9.6 17z" fill="#14182b" stroke="#fff" stroke-width="1.5" stroke-linejoin="round"/></svg></span>
              <span class="rw-pen" id="rwPen"><svg viewBox="0 0 24 24"><path d="M3 21l1.6-5L16.2 4.4a2.1 2.1 0 0 1 3 0l.4.4a2.1 2.1 0 0 1 0 3L8 19.4z" fill="#ffd23f" stroke="#1a2140" stroke-width="1.4" stroke-linejoin="round"/><path d="M14.6 6l3.4 3.4M4.6 16L8 19.4" fill="none" stroke="#1a2140" stroke-width="1.4" stroke-linejoin="round"/></svg></span>
            </span>
          </span>
        </span>
        <span class="ht-line ht-l3" data-i18n="hero.l3" data-html><span class="tw g">yang</span> berkesan</span>
      </h1>
      <nav class="hero-svc reveal" aria-label="Layanan">
        <a href="#features"><span data-i18n="hero.s1">Website</span><svg><use href="#arrow"/></svg></a>
        <a href="#features"><span data-i18n="hero.s2">Desain Grafis</span><svg><use href="#arrow"/></svg></a>
        <a href="#features"><span data-i18n="hero.s3">ID Card &amp; Event</span><svg><use href="#arrow"/></svg></a>
      </nav>
    </div>

    <div class="hero-laptop-wrap">
      <div class="laptop laptop-hero">
        <div class="lid">
          <span class="lcam"></span>
          <div class="lscreen">
            <div class="lbar"><i></i><i></i><i></i><span class="lurl">websitemu.com</span></div>
            <div class="lsite">
              <div class="ls-nav">
                <b class="ls-logo"></b>
                <span class="ls-links"><i></i><i></i><i></i></span>
                <em class="ls-btn"></em>
              </div>
              <div class="ls-hero">
                <div class="ls-copy">
                  <div class="ls-h" data-i18n="ph.h">Bisnismu tampil lebih profesional</div>
                  <div class="ls-p" data-i18n="ph.p">Website cepat, rapi, dan siap dikunjungi pelanggan kapan saja.</div>
                  <span class="ls-cta" data-i18n="ph.btn">Lihat produk</span>
                </div>
                <div class="ls-art"><span class="blob b1"></span><span class="blob b2"></span><img class="ls-mark" src="{{ asset('assets/logo.png') }}" alt="Viscode Creative"></div>
              </div>
              <div class="ls-cards"><div></div><div></div><div></div></div>
            </div>
          </div>
        </div>
        <div class="lbase"></div>
      </div>
    </div>

    <div class="cloud cloud-front"></div>
    <div class="hero-fade"></div>
  </section>

  <section class="about" id="about">
    <div class="wrap-narrow">
      <h2 class="about-text reveal" data-i18n="about.text" data-html>Kami membantu individu, UMKM, organisasi, dan penyelenggara acara <span class="scribble"><span class="scr-word">tampil</span><svg class="scr-svg" viewBox="0 0 200 80" preserveAspectRatio="none" aria-hidden="true"><path pathLength="1" d="M32 15C84 1 172 5 191 33C206 62 122 77 60 70C13 65 -3 40 22 22C42 8 92 5 136 8"/></svg></span> lebih baik. Pengembangan website dan desain grafis hadir dalam satu tempat, sesuai kebutuhan dan anggaranmu.</h2>
      <div class="stats reveal">
        <div class="stat"><div class="stat-n">3</div><div class="stat-l" data-i18n="about.s1">Layanan utama kreatif</div></div>
        <div class="stat"><div class="stat-n">360°</div><div class="stat-l" data-i18n="about.s2">Solusi digital menyeluruh</div></div>
        <div class="stat"><div class="stat-n" data-i18n="about.v3">1 tempat</div><div class="stat-l" data-i18n="about.s3">Semua kebutuhan kreatifmu</div></div>
      </div>
    </div>
  </section>

  <section class="features" id="features">
    <div class="wrap">
      <div class="section-head">
        <h2 class="h2 reveal" data-i18n="feat.title">Semua yang kamu butuhkan untuk tampil di dunia digital</h2>
        <p class="lead reveal" data-i18n="feat.sub">Layanan kami dirancang fleksibel supaya cocok untuk kebutuhan dan anggaran yang berbeda-beda.</p>
      </div>

      <div class="feat-grid">
        <article class="fcard b-web reveal">
          <div class="b-text">
            <h3 data-i18n="f1.t">Pengembangan Website</h3>
            <p data-i18n="f1.d">Landing page, website profil perusahaan, website UMKM, katalog produk, portofolio digital, hingga website acara.</p>
          </div>
          <div class="fpop pop-web" aria-hidden="true">
            <div class="sheet s-a"></div>
            <div class="sheet s-b"></div>
            <div class="sheet s-c"><span class="code">&lt;/&gt;</span><i></i><i></i><i class="short"></i><i></i><i class="short"></i></div>
          </div>
        </article>

        <article class="fcard b-design reveal">
          <div class="b-text">
            <h3 data-i18n="f2.t">Desain Grafis</h3>
            <p data-i18n="f2.d">Logo dan branding, poster dan banner, konten media sosial, serta brosur dan flyer yang konsisten dan memorable.</p>
          </div>
          <div class="fart" aria-hidden="true">
            <div class="swatch sw1"></div>
            <div class="swatch sw2"></div>
            <div class="swatch sw3"></div>
          </div>
          <div class="fpop pop-spark" aria-hidden="true"><svg width="26" height="26"><use href="#spark"/></svg></div>
        </article>

        <article class="fcard b-budget reveal">
          <div class="b-text">
            <h3 data-i18n="f4.t">Fleksibel Sesuai Anggaran</h3>
            <p data-i18n="f4.d">Pilih layanan satuan, paket bundling, atau minta layanan tambahan khusus, dengan harga transparan sejak awal.</p>
          </div>
          <div class="sliders" aria-hidden="true">
            <div class="sl"><span class="sl-t"><b style="--w:30%"></b></span><i style="--w:30%"></i></div>
            <div class="sl"><span class="sl-t"><b style="--w:68%"></b></span><i style="--w:68%"></i></div>
            <div class="sl"><span class="sl-t"><b style="--w:48%"></b></span><i style="--w:48%"></i></div>
          </div>
        </article>

        <article class="fcard b-id reveal">
          <div class="b-text">
            <h3 data-i18n="f3.t">ID Card &amp; Event</h3>
            <p data-i18n="f3.d">Kartu identitas, ID card panitia, materi publikasi acara, dan koordinasi cetak dengan vendor.</p>
          </div>
          <div class="fart" aria-hidden="true">
            <div class="idc idc-a"><span class="idc-photo"></span><i></i><i class="short"></i></div>
            <div class="idc idc-b"><span class="idc-photo"></span><i></i><i class="short"></i></div>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section class="market" id="market">
    <div class="wrap">
      <div class="section-head">
        <h2 class="h2 reveal" data-i18n="mk.title">Dibuat untuk Siapa Saja yang Ingin Tampil</h2>
        <p class="lead reveal" data-i18n="mk.sub">Dari usaha lokal sampai penyelenggara acara, kami siap membantu kebutuhan digitalmu.</p>
      </div>
      <div class="market-grid reveal">
        <article class="mcard">
          <span class="mnum">01</span>
          <span class="micon" aria-hidden="true"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l1.5-5h15L21 9"/><path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"/><path d="M5 12v8h14v-8"/><path d="M10 20v-5h4v5"/></svg></span>
          <h3 data-i18n="mk1.t">UMKM &amp; usaha lokal</h3>
          <p data-i18n="mk1.d">Website, logo, dan konten sosial untuk usahamu.</p>
        </article>
        <article class="mcard">
          <span class="mnum">02</span>
          <span class="micon" aria-hidden="true"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9l10-5 10 5-10 5z"/><path d="M6 11.5V16c0 1.5 2.7 3 6 3s6-1.5 6-3v-4.5"/><path d="M22 9v6"/></svg></span>
          <h3 data-i18n="mk2.t">Organisasi kampus &amp; sekolah</h3>
          <p data-i18n="mk2.d">Poster kegiatan, ID card, dan publikasi organisasi.</p>
        </article>
        <article class="mcard">
          <span class="mnum">03</span>
          <span class="micon" aria-hidden="true"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.4"/><path d="M17 14.2c2.4 0 4.3 1.9 4.3 4.3"/></svg></span>
          <h3 data-i18n="mk3.t">Mahasiswa &amp; komunitas</h3>
          <p data-i18n="mk3.d">Portofolio digital dan identitas visual komunitas.</p>
        </article>
        <article class="mcard">
          <span class="mnum">04</span>
          <span class="micon" aria-hidden="true"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M3 10h18"/><path d="M8 3v4M16 3v4"/><path d="M12 13.2l1 2 2.2.3-1.6 1.5.4 2.2-2-1.1-2 1.1.4-2.2-1.6-1.5 2.2-.3z"/></svg></span>
          <h3 data-i18n="mk4.t">Event organizer</h3>
          <p data-i18n="mk4.d">Landing page acara, poster digital, dan ID card panitia.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="portfolio" id="portfolio">
    <div class="wrap">
      <div class="section-head">
        <h2 class="h2 reveal" data-i18n="pf.title">Portofolio Konsep</h2>
        <p class="lead reveal" data-i18n="pf.sub">Sample Concept — visual simulasi untuk menunjukkan arah kualitas desain.</p>
      </div>

      <div class="pf-folder reveal" id="pfFolder">
        <div class="pf-tabs" role="tablist" aria-label="Kategori portofolio">
          <button type="button" class="pf-tab on" role="tab" id="pft-web" aria-selected="true" aria-controls="pfp-web" data-tab="web" tabindex="0"><i style="--c:#f2b705"></i><span data-i18n="pft.web">Website</span></button>
          <button type="button" class="pf-tab" role="tab" id="pft-design" aria-selected="false" aria-controls="pfp-design" data-tab="design" tabindex="-1"><i style="--c:#ff5d8f"></i><span data-i18n="pft.design">Desain Grafis</span></button>
          <button type="button" class="pf-tab" role="tab" id="pft-id" aria-selected="false" aria-controls="pfp-id" data-tab="id" tabindex="-1"><i style="--c:#34d399"></i><span data-i18n="pft.id">ID Card &amp; Event</span></button>
        </div>

        <div class="pf-board">
          <span class="pf-expand" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M14 4h6v6M10 20H4v-6M20 4l-7 7M4 20l7-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>

          <div class="pf-head">
            <div class="pf-sel">
              <i class="h a"></i><i class="h b"></i><i class="h c"></i><i class="h d"></i>
              <h3 class="pf-title" id="pfTitle">website</h3>
              <span class="pf-cursor" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 3l13.5 6.3-5.7 1.7L9.6 17z" fill="#14182b" stroke="#fff" stroke-width="1.5" stroke-linejoin="round"/></svg></span>
            </div>
          </div>

          <div class="pf-panels">
            <div class="pf-panel on" role="tabpanel" id="pfp-web" aria-labelledby="pft-web">
              <article class="pf-item" style="--n:0">
                <div class="pf-img"><img src="{{ asset('assets/portfolio/web-1.svg') }}" alt="Kopi Senja Nusantara" loading="lazy" width="640" height="400"><span class="pf-yr">2026</span></div>
                <div class="pf-body">
                  <h4 class="pf-brand">Kopi Senja Nusantara</h4>
                  <p class="pf-desc" data-i18n="pfi.web1.d">Website katalog menu dengan pemesanan lewat WhatsApp.</p>
                  <span class="pf-tag"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17L17 7M9 7h8v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span data-i18n="pfi.web1.t">Website UMKM</span></span>
                </div>
              </article>
              <article class="pf-item" style="--n:1">
                <div class="pf-img"><img src="{{ asset('assets/portfolio/web-2.svg') }}" alt="Batik Laras" loading="lazy" width="640" height="400"><span class="pf-yr">2025</span></div>
                <div class="pf-body">
                  <h4 class="pf-brand">Batik Laras</h4>
                  <p class="pf-desc" data-i18n="pfi.web2.d">Company profile dengan galeri produk dan cerita brand.</p>
                  <span class="pf-tag"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17L17 7M9 7h8v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span data-i18n="pfi.web2.t">Company profile</span></span>
                </div>
              </article>
              <article class="pf-item" style="--n:2">
                <div class="pf-img"><img src="{{ asset('assets/portfolio/web-3.svg') }}" alt="TechTalk Fest" loading="lazy" width="640" height="400"><span class="pf-yr">2025</span></div>
                <div class="pf-body">
                  <h4 class="pf-brand">TechTalk Fest</h4>
                  <p class="pf-desc" data-i18n="pfi.web3.d">Landing page acara lengkap dengan hitung mundur dan form daftar.</p>
                  <span class="pf-tag"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17L17 7M9 7h8v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span data-i18n="pfi.web3.t">Landing page</span></span>
                </div>
              </article>
            </div>
            <div class="pf-panel" role="tabpanel" id="pfp-design" aria-labelledby="pft-design">
              <article class="pf-item" style="--n:0">
                <div class="pf-img"><img src="{{ asset('assets/portfolio/design-1.svg') }}" alt="Rumah Roti Hangat" loading="lazy" width="640" height="400"><span class="pf-yr">2026</span></div>
                <div class="pf-body">
                  <h4 class="pf-brand">Rumah Roti Hangat</h4>
                  <p class="pf-desc" data-i18n="pfi.design1.d">Logo, palet warna, dan panduan branding singkat.</p>
                  <span class="pf-tag"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17L17 7M9 7h8v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span data-i18n="pfi.design1.t">Logo &amp; branding</span></span>
                </div>
              </article>
              <article class="pf-item" style="--n:1">
                <div class="pf-img"><img src="{{ asset('assets/portfolio/design-2.svg') }}" alt="Fitra Gym Studio" loading="lazy" width="640" height="400"><span class="pf-yr">2025</span></div>
                <div class="pf-body">
                  <h4 class="pf-brand">Fitra Gym Studio</h4>
                  <p class="pf-desc" data-i18n="pfi.design2.d">Poster promo pembukaan dan 5 konten Instagram.</p>
                  <span class="pf-tag"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17L17 7M9 7h8v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span data-i18n="pfi.design2.t">Poster &amp; sosmed</span></span>
                </div>
              </article>
              <article class="pf-item" style="--n:2">
                <div class="pf-img"><img src="{{ asset('assets/portfolio/design-3.svg') }}" alt="Lari Pagi Community" loading="lazy" width="640" height="400"><span class="pf-yr">2026</span></div>
                <div class="pf-body">
                  <h4 class="pf-brand">Lari Pagi Community</h4>
                  <p class="pf-desc" data-i18n="pfi.design3.d">Banner, flyer, dan template konten rutin komunitas.</p>
                  <span class="pf-tag"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17L17 7M9 7h8v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span data-i18n="pfi.design3.t">Banner &amp; flyer</span></span>
                </div>
              </article>
            </div>
            <div class="pf-panel" role="tabpanel" id="pfp-id" aria-labelledby="pft-id">
              <article class="pf-item" style="--n:0">
                <div class="pf-img"><img src="{{ asset('assets/portfolio/id-1.svg') }}" alt="Seminar Nasional Digitalia" loading="lazy" width="640" height="400"><span class="pf-yr">2026</span></div>
                <div class="pf-body">
                  <h4 class="pf-brand">Seminar Nasional Digitalia</h4>
                  <p class="pf-desc" data-i18n="pfi.id1.d">ID card panitia, lanyard, dan publikasi acara.</p>
                  <span class="pf-tag"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17L17 7M9 7h8v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span data-i18n="pfi.id1.t">ID card panitia</span></span>
                </div>
              </article>
              <article class="pf-item" style="--n:1">
                <div class="pf-img"><img src="{{ asset('assets/portfolio/id-2.svg') }}" alt="OSIS Harapan Bangsa" loading="lazy" width="640" height="400"><span class="pf-yr">2025</span></div>
                <div class="pf-body">
                  <h4 class="pf-brand">OSIS Harapan Bangsa</h4>
                  <p class="pf-desc" data-i18n="pfi.id2.d">Kartu anggota organisasi siap cetak.</p>
                  <span class="pf-tag"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17L17 7M9 7h8v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span data-i18n="pfi.id2.t">Kartu anggota</span></span>
                </div>
              </article>
              <article class="pf-item" style="--n:2">
                <div class="pf-img"><img src="{{ asset('assets/portfolio/id-3.svg') }}" alt="Pensi Senja Fest" loading="lazy" width="640" height="400"><span class="pf-yr">2025</span></div>
                <div class="pf-body">
                  <h4 class="pf-brand">Pensi Senja Fest</h4>
                  <p class="pf-desc" data-i18n="pfi.id3.d">Tiket, ID card panitia, dan backdrop acara.</p>
                  <span class="pf-tag"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17L17 7M9 7h8v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span data-i18n="pfi.id3.t">Event kit</span></span>
                </div>
              </article>
            </div>
          </div>

          <div class="pf-foot" aria-hidden="true">
            <svg class="pf-chk" viewBox="0 0 3 3" shape-rendering="crispEdges"><path d="M1 0h1v1h-1zM0 1h1v1h-1zM2 1h1v1h-1zM1 2h1v1h-1z"/></svg>
            <svg class="pf-chk" viewBox="0 0 3 3" shape-rendering="crispEdges"><path d="M1 0h1v1h-1zM0 1h1v1h-1zM2 1h1v1h-1zM1 2h1v1h-1z"/></svg>
            <svg class="pf-chk" viewBox="0 0 3 3" shape-rendering="crispEdges"><path d="M1 0h1v1h-1zM0 1h1v1h-1zM2 1h1v1h-1zM1 2h1v1h-1z"/></svg>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="steps" id="process">
    <div class="wrap-mid">
      <div class="section-head">
        <h2 class="h2 reveal" data-i18n="steps.title">Tujuh Tahap Menuju Karya Terbaik</h2>
        <p class="lead reveal" data-i18n="steps.sub">Alur sederhana dari konsultasi sampai serah terima, jelas di setiap tahap.</p>
      </div>
      <div class="step-list">
        <div class="step s1 reveal">
          <span class="step-dot">1</span>
          <span class="step-tag" data-i18n="steps.t1">Persiapan</span>
          <i class="ln"></i>
          <h3 data-i18n="s1.t">Konsultasi</h3>
          <p data-i18n="s1.d">Ceritakan kebutuhan dan tujuanmu. Cukup beberapa menit.</p>
        </div>
        <div class="step s2 reveal">
          <span class="step-dot">2</span>
          <span class="step-tag" data-i18n="steps.t2">Mulai</span>
          <i class="ln"></i>
          <h3 data-i18n="s2.t">Brief</h3>
          <p data-i18n="s2.d">Sampaikan gaya, referensi, dan anggaran lewat brief singkat.</p>
        </div>
        <div class="step s3 reveal">
          <span class="step-dot">3</span>
          <i class="ln"></i>
          <h3 data-i18n="s3.t">Penawaran</h3>
          <p data-i18n="s3.d">Terima arahan dan estimasi biaya yang jelas serta transparan.</p>
        </div>
        <div class="step s4 reveal">
          <span class="step-dot">4</span>
          <i class="curve out"></i>
          <h3 data-i18n="s4.t">Pengerjaan</h3>
          <p data-i18n="s4.d">Tim kami mengerjakan website dan desainmu sesuai kesepakatan.</p>
        </div>
        <div class="step s5 reveal">
          <span class="step-dot">5</span>
          <i class="curve in"></i>
          <i class="ln rev"></i>
          <h3 data-i18n="s5.t">Review</h3>
          <p data-i18n="s5.d">Cek hasilnya bersama kami dan sampaikan masukanmu.</p>
        </div>
        <div class="step s6 reveal">
          <span class="step-dot">6</span>
          <span class="step-tag" data-i18n="steps.t3">Selesai</span>
          <i class="ln rev"></i>
          <h3 data-i18n="s6.t">Finalisasi</h3>
          <p data-i18n="s6.d">Sempurnakan hasilnya sampai benar-benar siap dipakai.</p>
        </div>
        <div class="step s7 reveal">
          <span class="step-dot">7</span>
          <h3 data-i18n="s7.t">Serah Terima</h3>
          <p data-i18n="s7.d">Hasil akhir diserahkan, lalu kamu siap meluncur dengan percaya diri.</p>
        </div>
        <div class="step-cta reveal">
          <p class="step-cta-t" data-i18n="steps.cta.t">Siap mulai proyekmu?</p>
          <p class="step-cta-d" data-i18n="steps.cta.d">Ceritakan idemu, kami bantu dari tahap pertama.</p>
          <a href="#download" class="btn btn-dark"><span data-i18n="nav.cta">Konsultasi</span></a>
        </div>
      </div>
    </div>
  </section>

  <section class="pricing" id="pricing">
    <div class="wrap">
      <div class="section-head">
        <h2 class="h2 reveal" data-i18n="price.title">Harga Transparan Sejak Awal</h2>
        <p class="lead reveal" data-i18n="price.sub">Harga simulasi, dapat berubah sesuai kebutuhan proyek.</p>
      </div>

      <div class="ptabs reveal" role="tablist" aria-label="Jenis harga">
        <button type="button" class="ptab on" role="tab" id="ptab-bundle" aria-selected="true" aria-controls="pp-bundle" data-ptab="bundle" data-i18n="price.bundle">Paket Bundling</button>
        <button type="button" class="ptab" role="tab" id="ptab-unit" aria-selected="false" aria-controls="pp-unit" data-ptab="unit" tabindex="-1" data-i18n="price.unit">Harga Satuan</button>
      </div>

      <div class="ppanel on" role="tabpanel" id="pp-bundle" aria-labelledby="ptab-bundle">
        <div class="price-grid">
          <article class="pcard reveal">
            <h3 data-i18n="p1.name">UMKM Digital</h3>
            <div class="price"><div class="was"><s data-i18n="p1.old">Rp600.000</s><em data-i18n="p1.off">Hemat 25%</em></div><b data-i18n="p1.price">Rp450.000</b><span data-i18n="price.per">/paket</span></div>
            <p class="pdesc" data-i18n="p1.desc">Website, logo, dan konten sosial untuk usaha yang baru mulai tampil online.</p>
            <a href="#download" onclick="document.getElementById('service_interest_select').value='Paket UMKM Digital (Bundling)'; document.getElementById('budget_select').value='450000';" class="pbtn" data-i18n="price.btn">Pilih paket</a>
            <ul>
              <li><svg width="18" height="18"><use href="#check-c"/></svg><span data-i18n="p1.a">Landing page sederhana</span></li>
              <li><svg width="18" height="18"><use href="#check-c"/></svg><span data-i18n="p1.b">Logo sederhana</span></li>
              <li><svg width="18" height="18"><use href="#check-c"/></svg><span data-i18n="p1.c">3 desain konten sosial</span></li>
            </ul>
          </article>
          <div class="pfeat reveal">
            <div class="pband"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M13.2 2L4.5 13.4h6L9.6 22l8.9-11.6h-6z"/></svg><span data-i18n="price.pop">Paling terjangkau</span></div>
            <article class="pcard featured">
              <h3 data-i18n="p2.name">Organisasi</h3>
              <div class="price"><div class="was"><s data-i18n="p2.old">Rp250.000</s><em data-i18n="p2.off">Hemat 30%</em></div><b data-i18n="p2.price">Rp175.000</b><span data-i18n="price.per">/paket</span></div>
              <p class="pdesc" data-i18n="p2.desc">Poster, ID card, dan template pengumuman untuk kegiatan organisasi.</p>
              <a href="#download" onclick="document.getElementById('service_interest_select').value='Paket Organisasi (Bundling)'; document.getElementById('budget_select').value='175000';" class="pbtn" data-i18n="price.btn">Pilih paket</a>
              <ul>
                <li><svg width="18" height="18"><use href="#check-c"/></svg><span data-i18n="p2.a">Poster kegiatan</span></li>
                <li><svg width="18" height="18"><use href="#check-c"/></svg><span data-i18n="p2.b">Desain ID card</span></li>
                <li><svg width="18" height="18"><use href="#check-c"/></svg><span data-i18n="p2.c">Template pengumuman</span></li>
              </ul>
            </article>
          </div>
          <article class="pcard reveal">
            <h3 data-i18n="p3.name">Event Starter</h3>
            <div class="price"><div class="was"><s data-i18n="p3.old">Rp1.000.000</s><em data-i18n="p3.off">Hemat 35%</em></div><b data-i18n="p3.price">Rp650.000</b><span data-i18n="price.per">/paket</span></div>
            <p class="pdesc" data-i18n="p3.desc">Landing page, poster, dan ID card panitia untuk acara yang siap tampil.</p>
            <a href="#download" onclick="document.getElementById('service_interest_select').value='Paket Event Starter (Bundling)'; document.getElementById('budget_select').value='650000';" class="pbtn" data-i18n="price.btn">Pilih paket</a>
            <ul>
              <li><svg width="18" height="18"><use href="#check-c"/></svg><span data-i18n="p3.a">Landing page acara</span></li>
              <li><svg width="18" height="18"><use href="#check-c"/></svg><span data-i18n="p3.b">Poster digital</span></li>
              <li><svg width="18" height="18"><use href="#check-c"/></svg><span data-i18n="p3.c">ID card panitia</span></li>
            </ul>
          </article>
        </div>
      </div>

      <div class="ppanel" role="tabpanel" id="pp-unit" aria-labelledby="ptab-unit">
        <div class="unit-grid">
          <div class="ucard reveal"><small data-i18n="u1.n">Landing page</small><b data-i18n="u1.p">mulai Rp150.000</b></div>
          <div class="ucard reveal"><small data-i18n="u2.n">Website profil / UMKM</small><b data-i18n="u2.p">mulai Rp350.000</b></div>
          <div class="ucard reveal"><small data-i18n="u3.n">Website custom</small><b data-i18n="u3.p">mulai Rp500.000</b></div>
          <div class="ucard reveal"><small data-i18n="u4.n">Desain poster</small><b data-i18n="u4.p">mulai Rp25.000</b></div>
          <div class="ucard reveal"><small data-i18n="u5.n">5 konten sosial</small><b data-i18n="u5.p">mulai Rp75.000</b></div>
          <div class="ucard reveal"><small data-i18n="u6.n">Desain ID card</small><b data-i18n="u6.p">mulai Rp10.000</b></div>
        </div>
        <div class="unote reveal"><svg class="unote-i" width="20" height="20" aria-hidden="true"><use href="#info"/></svg><p data-i18n="un.d">Harga satuan adalah harga mulai dari. Kalau kamu ingin hasil yang lebih detail dan proper, atau punya permintaan khusus, biayanya bisa lebih tinggi dari harga awal. Konsultasi dulu untuk estimasi yang pasti.</p></div>
      </div>

      <p class="price-note reveal" data-i18n="price.note">Harga simulasi • Cetak fisik, domain, hosting, dan fitur tambahan dikenakan biaya terpisah.</p>
      <p class="price-custom reveal"><span data-i18n="price.custom">Butuh layanan tambahan (custom)?</span> <a href="#download" data-i18n="price.custom.btn">Hubungi kami</a></p>
    </div>
  </section>

  <section class="vm" id="vision">
    <div class="wrap vm-grid">
      <article class="vcard dark reveal">
        <span class="vico"><svg width="26" height="26"><use href="#target"/></svg></span>
        <h3 data-i18n="vm.visi">Visi</h3>
        <p data-i18n="vm.visi.t">Menjadi partner digital terpercaya yang menggabungkan teknologi dan seni.</p>
      </article>
      <article class="vcard reveal">
        <span class="vico"><svg width="26" height="26"><use href="#arrow"/></svg></span>
        <h3 data-i18n="vm.misi">Misi</h3>
        <p data-i18n="vm.misi.t">Memberikan layanan web dan desain kreatif yang inovatif, fleksibel, dan berkualitas.</p>
      </article>
    </div>
  </section>

  <section class="team" id="team">
    <div class="wrap">
      <div class="section-head">
        <h2 class="h2 reveal" data-i18n="team.title">Orang-Orang di Balik Viscode</h2>
        <p class="lead reveal" data-i18n="team.sub">Delapan kreator yang siap mewujudkan idemu. Klik kartunya untuk kenalan lebih dekat.</p>
      </div>
      <div class="team-board" id="teamBoard"></div>
    </div>
  </section>

  <!-- CTA & INTEGRATED CUSTOMER CONSULTATION FORM (Terhubung Langsung ke VC-BMS) -->
  <section class="cta" id="download">
    <div class="cta-sky"></div>
    <div class="cta-cloud-mid"></div>
    <div class="orbit" id="orbit" aria-hidden="true"></div>
    <div class="wrap cta-stage" id="consultation">
      <div class="cta-copy">
        <h2 class="cta-title reveal" data-i18n="cta.title">Karya Hebatmu Dimulai Dari Sini</h2>
        <p class="cta-text reveal" data-i18n="cta.text">Ambil langkah pertama menuju kehadiran digital yang lebih kuat. Ceritakan kebutuhanmu, lalu terima konsep dan penawaran yang jelas.</p>
      </div>

      <!-- Integrated VC-BMS Customer Inquiry Form -->
      <div class="vc-consult-card reveal">
        <div class="vc-consult-head">
          <span class="vc-consult-badge">Gratis &bull; Terintegrasi Sistem VC-BMS</span>
          <h3>Konsultasikan Proyek Anda</h3>
          <p>Isi data singkat sekali di bawah ini — tim Sales kami langsung memproses penawaran resmi untuk Anda.</p>
        </div>

        @if(session('public_success'))
          <div class="vc-alert-success">
            <strong>✓ Pengajuan Konsultasi Berhasil Diterima!</strong><br>
            <span>{{ session('public_success') }}</span>
          </div>
        @endif

        @if($errors->any())
          <div class="vc-alert-error">
            <strong>Harap periksa kembali isian Anda:</strong>
            <ul style="margin-top:4px;padding-left:18px;list-style:disc">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('public.lead.store') }}" method="POST">
          @csrf
          <div class="vc-form-grid">
            <div class="vc-field">
              <label>Nama Lengkap / Kontak <span>*</span></label>
              <input type="text" name="customer_name" value="{{ old('customer_name') }}" required placeholder="Contoh: Budi Santoso" class="vc-input">
            </div>
            <div class="vc-field">
              <label>Nomor WhatsApp Aktif <span>*</span></label>
              <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" required placeholder="Contoh: 081234567890" class="vc-input">
            </div>
          </div>

          <div class="vc-form-grid">
            <div class="vc-field">
              <label>Alamat Email (Opsional)</label>
              <input type="email" name="email" value="{{ old('email') }}" placeholder="budi@example.com" class="vc-input">
            </div>
            <div class="vc-field">
              <label>Nama Usaha / Organisasi (Opsional)</label>
              <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="UMKM / Organisasi / Event Anda" class="vc-input">
            </div>
          </div>

          <div class="vc-form-grid">
            <div class="vc-field">
              <label>Paket / Layanan Diminati <span>*</span></label>
              <select id="service_interest_select" name="service_interest" required class="vc-select">
                <option value="Website Development">Pengembangan Website (Landing Page / UMKM / Custom)</option>
                <option value="Paket UMKM Digital (Bundling)">Paket Bundling: UMKM Digital (Rp450.000)</option>
                <option value="Paket Organisasi (Bundling)">Paket Bundling: Organisasi (Rp175.000)</option>
                <option value="Paket Event Starter (Bundling)">Paket Bundling: Event Starter (Rp650.000)</option>
                <option value="Desain Grafis & Branding">Desain Grafis, Logo &amp; Konten Sosial</option>
                <option value="ID Card & Event">Desain ID Card Panitia &amp; Kebutuhan Event</option>
              </select>
            </div>
            <div class="vc-field">
              <label>Estimasi Anggaran Proyek (Rp)</label>
              <select id="budget_select" name="estimated_budget" class="vc-select">
                <option value="175000">Rp 150.000 – Rp 350.000 (Starter / Organisasi)</option>
                <option value="450000" selected>Rp 350.000 – Rp 650.000 (UMKM Digital / Event)</option>
                <option value="1500000">Rp 650.000 – Rp 2.000.000 (Website Custom)</option>
                <option value="3500000">&gt; Rp 2.000.000 (Full Custom Solution)</option>
              </select>
            </div>
          </div>

          <div class="vc-field" style="margin-bottom:14px">
            <label>Ceritakan Kebutuhan Proyek Anda</label>
            <textarea name="notes" rows="3" placeholder="Jelaskan gambaran singkat website, desain, atau jadwal acara Anda..." class="vc-textarea">{{ old('notes') }}</textarea>
          </div>

          <button type="submit" class="vc-submit">
            Kirim Konsultasi &amp; Dapatkan Penawaran Resmi &rarr;
          </button>
        </form>
      </div>
    </div>
    <div class="cta-cloud"></div>
  </section>
</main>

<footer class="footer">
  <div class="wrap">
    <div class="foot-social">
      <a href="#" aria-label="Instagram" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.3" cy="6.7" r=".6" fill="currentColor"/></svg>
      </a>
      <a href="#" aria-label="TikTok" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3v11.2a3.8 3.8 0 1 1-3.8-3.8"/><path d="M14 3c.3 2.7 2 4.5 5 4.8"/></svg>
      </a>
      <a href="#download" aria-label="Konsultasi">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3.5 20.5l1.4-4.3A8.5 8.5 0 1 1 8 19.3z"/><path d="M9 8.6c0 3.3 2.7 6 6 6l1.3-1.5-2.1-1-1 .9c-.9-.4-1.9-1.4-2.3-2.3l.9-1-1-2.1z" fill="currentColor" stroke="none"/></svg>
      </a>
    </div>

    <p class="foot-desc" data-i18n="foot.desc">Layanan pengembangan website, desain grafis, serta ID card dan event untuk individu, UMKM, organisasi, dan penyelenggara acara.</p>

    <a href="#top" class="foot-logo" aria-label="Viscode Creative">
      <img class="brand-mark" src="{{ asset('assets/logo.png') }}" alt="Viscode Creative">
    </a>

    <p class="foot-region">Indonesia | Rp &bull; VISCODE Creative BMS</p>
  </div>
</footer>

<script src="{{ asset('script.js') }}"></script>
@if(session('public_success') || $errors->any())
<script>
  window.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('consultation');
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
  });
</script>
@endif
</body>
</html>
