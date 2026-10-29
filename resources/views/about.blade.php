<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tentang Kami – Neo Photo Indonesia</title>
  <meta name="description" content="Kenali Neo Photo Indonesia, photobooth bergaya Korea dengan pengalaman foto kreatif, teknologi dari Korea, dan layanan untuk berbagai momen."/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka+One&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    :root {
      --pink:#FF6BAE;
      --pink-dark:#E8458A;
      --pink-light:#FFD6EC;
      --pink-bg:#FFF0F7;
      --mint:#5DD9C4;
      --mint-light:#C8F5EE;
      --purple:#C084FC;
      --dark:#2D1B3D;
      --text:#4A3050;
      --muted:#9B7FAD;
      --border:#F5D0E8;
      --card-radius:20px;
    }
    *,*::before,*::after{box-sizing:border-box}
    html{scroll-behavior:smooth}
    body{margin:0;font-family:'Nunito',sans-serif;color:var(--text);background:#fff}
    a{text-decoration:none}
    .about-nav{background:rgba(255,255,255,.94);box-shadow:0 2px 18px rgba(255,107,174,.1)}
    .about-nav .nav-link{color:var(--text);font-size:14px;font-weight:800}
    .about-nav .nav-link:hover,.about-nav .nav-link.active{color:var(--pink-dark)}
    .brand-logo{width:205px;height:auto}
    .section-tag{display:inline-block;padding:6px 15px;border-radius:99px;background:var(--pink-light);color:var(--pink-dark);font-size:12px;font-weight:900;letter-spacing:.04em}
    .section-title{font-family:'Fredoka One',cursive;color:var(--dark);font-size:clamp(28px,4vw,42px)}
    .section-title span{color:var(--pink)}
    .about-hero{position:relative;overflow:hidden;padding:90px 0;background:linear-gradient(140deg,#FFF0F7 0%,#fff 54%,#E9FBF8 100%)}
    .about-hero::before,.about-hero::after{position:absolute;content:"";border-radius:50%;pointer-events:none}
    .about-hero::before{width:360px;height:360px;right:-110px;top:-150px;background:rgba(255,107,174,.12)}
    .about-hero::after{width:300px;height:300px;left:-180px;bottom:-170px;background:rgba(93,217,196,.14)}
    .hero-copy,.hero-art{position:relative;z-index:1}
    .hero-title{max-width:660px;margin:18px 0;color:var(--dark);font-family:'Fredoka One',cursive;font-size:clamp(40px,6vw,68px);line-height:1.04}
    .hero-title span{color:var(--pink)}
    .hero-copy>p{max-width:590px;color:var(--muted);font-size:17px;line-height:1.8}
    .btn-neo{display:inline-flex;align-items:center;gap:9px;padding:12px 23px;border:0;border-radius:99px;background:linear-gradient(135deg,var(--pink),var(--pink-dark));box-shadow:0 5px 16px rgba(232,69,138,.25);color:#fff;font-size:14px;font-weight:900}
    .btn-neo:hover{color:#fff;transform:translateY(-2px)}
    .hero-photo{position:relative;max-width:350px;margin:auto;padding:16px;border:1px solid rgba(255,255,255,.8);border-radius:30px;background:rgba(255,255,255,.66);box-shadow:0 24px 60px rgba(45,27,61,.14);transform:rotate(3deg)}
    .hero-photo img{display:block;width:100%;border-radius:18px}
    .hero-photo-caption{position:absolute;right:-20px;bottom:25px;padding:12px 16px;border-radius:16px;background:#fff;box-shadow:0 10px 24px rgba(45,27,61,.12);color:var(--dark);font-size:13px;font-weight:900}
    .hero-photo-caption i{margin-right:6px;color:var(--pink)}
    .about-section{padding:82px 0}
    .about-section.soft{background:var(--pink-bg)}
    .intro-card,.vision-card,.feature-card,.timeline-card,.collab-card{height:100%;border:1px solid rgba(245,208,232,.8);border-radius:var(--card-radius);background:#fff;box-shadow:0 8px 26px rgba(45,27,61,.05)}
    .intro-card{padding:30px}
    .intro-card p{margin:0;color:var(--muted);line-height:1.85}
    .profile-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:24px}
    .profile-stat{padding:16px 10px;border-radius:16px;background:var(--pink-bg);text-align:center}
    .profile-stat strong{display:block;color:var(--pink-dark);font-family:'Fredoka One',cursive;font-size:25px}
    .profile-stat span{display:block;margin-top:3px;color:var(--muted);font-size:11px;font-weight:800}
    .feature-card{padding:25px;transition:transform .2s,box-shadow .2s}
    .feature-card:hover,.vision-card:hover,.timeline-card:hover{transform:translateY(-4px);box-shadow:0 14px 30px rgba(45,27,61,.09)}
    .feature-icon{display:flex;width:52px;height:52px;align-items:center;justify-content:center;margin-bottom:17px;border-radius:16px;background:var(--pink-bg);color:var(--pink-dark);font-size:21px}
    .feature-card:nth-child(2n) .feature-icon{background:#E8FBF7;color:#269B87}
    .feature-card h3,.vision-card h3,.collab-card h3{margin-bottom:9px;color:var(--dark);font-size:17px;font-weight:900}
    .feature-card p,.vision-card p,.collab-card p{margin:0;color:var(--muted);font-size:14px;line-height:1.75}
    .timeline{position:relative;display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-top:38px}
    .timeline::before{position:absolute;top:24px;right:10%;left:10%;height:2px;background:var(--pink-light);content:""}
    .timeline-card{position:relative;padding:50px 20px 22px;transition:transform .2s,box-shadow .2s}
    .timeline-dot{position:absolute;top:13px;left:20px;width:22px;height:22px;border:6px solid var(--pink-light);border-radius:50%;background:var(--pink)}
    .timeline-card time{display:block;margin-bottom:8px;color:var(--pink-dark);font-size:12px;font-weight:900}
    .timeline-card h3{margin-bottom:6px;color:var(--dark);font-size:16px;font-weight:900}
    .timeline-card p{margin:0;color:var(--muted);font-size:13px;line-height:1.6}
    .vision-card{padding:22px;transition:transform .2s,box-shadow .2s}
    .vision-number{display:block;margin-bottom:14px;color:var(--pink);font-family:'Fredoka One',cursive;font-size:28px}
    .collab-card{padding:30px;background:linear-gradient(135deg,#2D1B3D,#51345F);color:#fff}
    .collab-card h3{color:#fff;font-family:'Fredoka One',cursive;font-size:26px}
    .collab-card p{color:rgba(255,255,255,.76)}
    .collab-stat{display:flex;align-items:center;gap:16px;margin-top:24px}
    .collab-stat strong{color:#fff;font-family:'Fredoka One',cursive;font-size:38px}
    .collab-stat span{color:rgba(255,255,255,.76);font-size:13px;line-height:1.45}
    .contact-section{padding:64px 0;background:linear-gradient(135deg,var(--pink),var(--pink-dark));color:#fff}
    .contact-section h2{color:#fff;font-family:'Fredoka One',cursive;font-size:clamp(28px,4vw,40px)}
    .contact-link{display:inline-flex;align-items:center;gap:9px;margin:7px 12px;color:#fff;font-weight:800}
    .contact-link:hover{color:#fff;text-decoration:underline}
    .about-footer{padding:22px 0;color:var(--muted);font-size:12px}
    @media(max-width:767.98px){
      .about-hero{padding:60px 0}
      .about-section{padding:58px 0}
      .timeline{grid-template-columns:1fr 1fr}
      .timeline::before{display:none}
      .profile-stats{grid-template-columns:1fr}
    }
    @media(max-width:480px){
      .timeline{grid-template-columns:1fr}
      .hero-title{font-size:42px}
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top about-nav">
  <div class="container">
    <a class="navbar-brand" href="{{ route('index') }}" aria-label="Neo Photo Indonesia — Beranda">
      <img class="brand-logo" src="{{ asset('assets/img/logoNeo.png') }}" alt="Neo Photo Indonesia"/>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#aboutNav" aria-controls="aboutNav" aria-expanded="false" aria-label="Buka navigasi">
      <i class="fa-solid fa-bars" style="color:var(--pink-dark)"></i>
    </button>
    <div class="collapse navbar-collapse" id="aboutNav">
      <ul class="navbar-nav mx-auto gap-lg-3">
        <li class="nav-item"><a class="nav-link" href="{{ route('index') }}">Beranda</a></li>
        <li class="nav-item"><a class="nav-link active" href="#about">Tentang Kami</a></li>
        <li class="nav-item"><a class="nav-link" href="#journey">Perjalanan</a></li>
        <li class="nav-item"><a class="nav-link" href="#vision">Visi</a></li>
      </ul>
      <a class="btn-neo mt-3 mt-lg-0" href="{{ route('index') }}#outlet"><i class="fa-solid fa-location-dot"></i>Temukan Outlet</a>
    </div>
  </div>
</nav>

<main>
  <section class="about-hero" id="about">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-7 hero-copy">
          <span class="section-tag">PT. NEO PHOTO INDONESIA · EST. 2023</span>
          <h1 class="hero-title">Merayakan momen dengan sentuhan <span>Korea.</span></h1>
          <p>Neo Photo Indonesia menghadirkan pengalaman photobooth bergaya Korea yang seru dan interaktif—tempat untuk berekspresi, berkumpul, dan membawa pulang kenangan.</p>
        </div>
        <div class="col-lg-5 hero-art">
          <div class="hero-photo">
            <img src="{{ asset('assets/HOMEPAGE/EXPERIENCEv3/1.png') }}" alt="Contoh strip foto Neo Photo" fetchpriority="high"/>
            <div class="hero-photo-caption"><i class="fa-solid fa-heart"></i>Momenmu, gayamu</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="about-section" aria-labelledby="story-title">
    <div class="container">
      <div class="row g-4 align-items-stretch">
        <div class="col-lg-5">
          <span class="section-tag mb-3">CERITA KAMI</span>
          <h2 class="section-title mb-3" id="story-title">Lebih dari sekadar <span>foto.</span></h2>
          <p style="color:var(--muted);line-height:1.8">Berawal pada 2023, Neo Photo tumbuh dari kecintaan pada budaya Korea dan cara baru untuk menikmati foto bersama.</p>
        </div>
        <div class="col-lg-7">
          <div class="intro-card">
            <p>Berangkat dari tren Korean-style photobooth, kami menghadirkan booth self-photo dengan mesin berteknologi dari Korea, kamera DSLR, beragam tema, serta aksesori dan kostum. Pengunjung dapat mengambil foto sendiri, mencetak hasilnya, dan memindai barcode untuk mengunduh foto maupun video BTS.</p>

          </div>
        </div>
      </div>
      <div class="row g-3 mt-4">
        <div class="col-md-4">
          <article class="feature-card">
            <div class="feature-icon"><i class="fa-solid fa-camera-retro"></i></div>
            <h3>Foto berkualitas</h3>
            <p>Kamera DSLR dan teknologi mesin dari Korea mendukung hasil foto yang jernih dan menyenangkan.</p>
          </article>
        </div>
        <div class="col-md-4">
          <article class="feature-card">
            <div class="feature-icon"><i class="fa-solid fa-qrcode"></i></div>
            <h3>Kenangan digital</h3>
            <p>Pindai barcode pada hasil cetak untuk mengunduh foto dan video BTS ke perangkatmu.</p>
          </article>
        </div>
        <div class="col-md-4">
          <article class="feature-card">
            <div class="feature-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
            <h3>Bebas berekspresi</h3>
            <p>Pilih tema, gaya, aksesori, dan kostum untuk membuat foto yang terasa personal.</p>
          </article>
        </div>
      </div>
    </div>
  </section>

  <section class="about-section soft" id="journey" aria-labelledby="journey-title">
    <div class="container">
      <div class="text-center mx-auto" style="max-width:700px">
        <span class="section-tag mb-3">PERJALANAN KAMI</span>
        <h2 class="section-title mb-3" id="journey-title">Tumbuh bersama <span>momenmu</span></h2>
      </div>
      <div class="timeline">
        <article class="timeline-card">
          <span class="timeline-dot" aria-hidden="true"></span>
          <time datetime="2023-06-27">27 Juni 2023</time>
          <h3>Gandaria City</h3>
          <p>Lokasi pertama Neo Photo, di lantai 2.</p>
        </article>
        <article class="timeline-card">
          <span class="timeline-dot" aria-hidden="true"></span>
          <time datetime="2024-06-14">14 Juni 2024</time>
          <h3>Plaza Blok M</h3>
          <p>Lokasi keempat, di lantai 4.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="about-section" id="vision" aria-labelledby="vision-title">
    <div class="container">
      <div class="row g-5 align-items-end mb-4">
        <div class="col-lg-7">
          <span class="section-tag mb-3">ARAH &amp; CITA-CITA</span>
          <h2 class="section-title mb-0" id="vision-title">Membawa pengalaman foto yang <span>lebih seru.</span></h2>
        </div>
        <div class="col-lg-5">
          <p class="mb-0" style="color:var(--muted);line-height:1.8">Visi yang menjadi arah Neo Photo: hiburan visual berkualitas, pengalaman interaktif, dan ruang untuk berbagi momen.</p>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-md-6 col-lg">
          <article class="vision-card">
            <span class="vision-number">01</span>
            <h3>Destinasi favorit</h3>
            <p>Menjadi tujuan pilihan untuk hiburan visual berkualitas.</p>
          </article>
        </div>
        <div class="col-md-6 col-lg">
          <article class="vision-card">
            <span class="vision-number">02</span>
            <h3>Budaya Korea</h3>
            <p>Mengenalkan budaya pop Korea lewat pengalaman interaktif.</p>
          </article>
        </div>
        <div class="col-md-6 col-lg">
          <article class="vision-card">
            <span class="vision-number">03</span>
            <h3>Teknologi inovatif</h3>
            <p>Menjadi trendsetter melalui inovasi teknologi photobooth.</p>
          </article>
        </div>
        <div class="col-md-6 col-lg">
          <article class="vision-card">
            <span class="vision-number">04</span>
            <h3>Tempat berkumpul</h3>
            <p>Menjadi tempat favorit untuk berkumpul dan berbagi momen.</p>
          </article>
        </div>
        <div class="col-md-6 col-lg">
          <article class="vision-card">
            <span class="vision-number">05</span>
            <h3>Kualitas &amp; nilai</h3>
            <p>Memberikan layanan berkualitas dengan harga terjangkau.</p>
          </article>
        </div>
      </div>
    </div>
  </section>

  <section class="about-section soft" aria-labelledby="collaboration-title">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-6">
          <article class="collab-card h-100">
            <span class="section-tag mb-3">KOLABORASI</span>
            <h3 id="collaboration-title">Momen seru, di mana pun acaranya.</h3>
            <p>Kami membuka kolaborasi event untuk brand awareness, engagement, dan media sosial. Mesin Neo Photo juga dapat disewa untuk ulang tahun, konferensi pers, gathering, dan acara lainnya.</p>
            <a class="btn-neo mt-3" href="mailto:neophotoindonesia@gmail.com"><i class="fa-solid fa-envelope"></i>Hubungi tim kami</a>
          </article>
        </div>
        <div class="col-lg-6">
          <article class="collab-card h-100" style="background:linear-gradient(135deg,#E8458A,#FF8BC1)">
            <span class="section-tag mb-3" style="background:rgba(255,255,255,.2);color:#fff">RENCANA EKSPANSI</span>
            <h3>Lebih dekat dengan komunitas.</h3>
            <p>Company profile Neo Photo mencantumkan rencana ekspansi ke lebih dari 100 lokasi di Indonesia, termasuk kolaborasi Neo Photo x CGV dengan target lebih dari 35 booth.</p>
            <div class="collab-stat">
              <strong>10+</strong>
              <span>target booth dalam rencana<br/>kolaborasi Neo Photo x CGV</span>
            </div>
          </article>
        </div>
      </div>
    </div>
  </section>

  <section class="contact-section text-center" id="contact">
    <div class="container">
      <span class="section-tag mb-3" style="background:rgba(255,255,255,.2);color:#fff">SAPA KAMI</span>
      <h2 class="mb-3">Yuk, ciptakan momen bersama.</h2>
      <p class="mb-4" style="color:rgba(255,255,255,.82)">Untuk informasi dan kolaborasi, hubungi tim Neo Photo Indonesia.</p>
      <div>
        <a class="contact-link" href="mailto:neophotoindonesia@gmail.com"><i class="fa-solid fa-envelope"></i>neophotoindonesia@gmail.com</a>
        <a class="contact-link" href="https://wa.me/6285810847780" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp"></i>+62 858 1084 7780</a>
      </div>
      <a class="btn btn-light rounded-pill fw-bold px-4 py-2 mt-3" href="{{ route('index') }}">Kembali ke beranda</a>
    </div>
  </section>
</main>

<footer class="about-footer">
  <div class="container d-flex flex-wrap justify-content-between gap-2">
    <span>© {{ date('Y') }} PT. Neo Photo Indonesia</span>
    <a href="{{ route('index') }}" style="color:var(--pink-dark);font-weight:800">Beranda Neo Photo</a>
  </div>
</footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
