<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Neo Photo Indonesia – Photobooth Modern</title>
  <meta name="description" content="Neo Photo Indonesia – photobooth modern dengan ratusan pilihan frame unik. Cetak kenangan indah bersama teman, keluarga, dan orang tersayang."/>

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css"/>
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka+One&display=swap" rel="stylesheet"/>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <style>
    /* ── TOKENS ─────────────────────────────────── */
    :root {
      --pink:        #FF6BAE;
      --pink-dark:   #E8458A;
      --pink-light:  #FFD6EC;
      --pink-bg:     #FFF0F7;
      --mint:        #5DD9C4;
      --mint-light:  #C8F5EE;
      --mint-dark:   #38B2A0;
      --yellow:      #FFD166;
      --purple:      #C084FC;
      --dark:        #2D1B3D;
      --text:        #4A3050;
      --muted:       #9B7FAD;
      --border:      #F5D0E8;
      --card-radius: 18px;
    }
.btn-floating:hover img {
  margin-bottom: -3px
}

.btn-floating {
    position: fixed;
    right: 25px;
    overflow: hidden;
    width: 50px;
    height: 50px;
    border-radius: 100px;
    border: 0;
    z-index: 9999;
    color: white;
    transition: .2s;
}

.btn-floating:hover {
    width: auto;
    padding: 0 20px;
    cursor: pointer;
}

.btn-floating span {
    font-size: 16px;
    margin-left: 5px;
    transition: .2s;
    line-height: 0px;
    display: none;
}

.btn-floating:hover span {
    display: inline-block;
}

/* Phone */
.btn-floating.phone {
    bottom: 85px;
    background-color: #760f10;
}

.btn-floating.phone:hover {
    background-color: #c03421;
}

/* WhatsApp */
.btn-floating.whatsapp {
    background-color: #34af23;
    bottom: 25px;
}

.btn-floating.whatsapp:hover {
    background-color: #1f7a12
}
    /* ── BASE ────────────────────────────────────── */
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    html{scroll-behavior:smooth}
    body{font-family:'Nunito',sans-serif;color:var(--text);background:#fff;overflow-x:hidden}
    a{text-decoration:none;color:inherit}
    ul{list-style:none;margin:0;padding:0}

    ::-webkit-scrollbar{width:5px}
    ::-webkit-scrollbar-track{background:var(--pink-bg)}
    ::-webkit-scrollbar-thumb{background:var(--pink);border-radius:99px}

    /* ── UTILITIES ───────────────────────────────── */
    .font-fredoka{font-family:'Fredoka One',cursive}
    .text-pink   {color:var(--pink)!important}
    .text-dark-custom{color:var(--dark)!important}
    .text-muted-custom{color:var(--muted)!important}
    .bg-pink-bg  {background:var(--pink-bg)!important}
    .bg-pink-gradient{background:linear-gradient(135deg,var(--pink),var(--pink-dark))!important}

    .section-tag{
      display:inline-block;background:var(--pink-light);color:var(--pink-dark);
      padding:5px 16px;border-radius:99px;font-size:12px;font-weight:800;letter-spacing:.4px
    }
    .section-title{font-family:'Fredoka One',cursive;font-size:clamp(24px,4vw,34px);color:var(--dark)}
    .section-title span{color:var(--pink)}

    /* ── BUTTONS ─────────────────────────────────── */
    .btn-neo-primary{
      background:linear-gradient(135deg,var(--pink),var(--pink-dark));
      color:#fff;border:none;padding:11px 26px;border-radius:99px;
      font-weight:800;font-size:14px;font-family:'Nunito',sans-serif;
      box-shadow:0 4px 14px rgba(255,107,174,.4);
      transition:transform .2s,box-shadow .2s;display:inline-flex;align-items:center;gap:8px
    }
    .btn-neo-primary:hover{transform:translateY(-2px);box-shadow:0 8px 22px rgba(255,107,174,.5);color:#fff}

    .btn-neo-mint{
      background:linear-gradient(135deg,var(--mint),var(--mint-dark));
      color:#fff;border:none;padding:11px 26px;border-radius:99px;
      font-weight:800;font-size:14px;font-family:'Nunito',sans-serif;
      box-shadow:0 4px 14px rgba(93,217,196,.4);
      transition:transform .2s,box-shadow .2s;display:inline-flex;align-items:center;gap:8px
    }
    .btn-neo-mint:hover{transform:translateY(-2px);box-shadow:0 8px 22px rgba(93,217,196,.5);color:#fff}

    .btn-neo-outline{
      background:transparent;color:var(--pink);border:2px solid var(--pink);
      padding:9px 22px;border-radius:99px;font-weight:800;font-size:13px;
      font-family:'Nunito',sans-serif;transition:all .2s;display:inline-flex;align-items:center;gap:6px
    }
    .btn-neo-outline:hover{background:var(--pink);color:#fff}

    .btn-wa{
      background:#25D366;color:#fff;border:none;
      padding:6px 14px;border-radius:99px;font-size:11px;font-weight:800;
      font-family:'Nunito',sans-serif;display:inline-flex;align-items:center;gap:5px;
      transition:background .2s;cursor:pointer
    }
    .btn-wa:hover{background:#1ebe5b;color:#fff}

    /* ── TOPBAR ──────────────────────────────────── */
    .topbar{background:linear-gradient(90deg,var(--pink),var(--purple));padding:7px 0;text-align:center}
    .topbar p{color:#fff;font-size:12px;font-weight:700;margin:0}
    .topbar .badge-pill{background:#fff;color:var(--pink);padding:2px 10px;border-radius:99px;font-size:11px;margin-left:8px}

    /* ── NAVBAR ──────────────────────────────────── */
    .navbar{background:#fff;box-shadow:0 2px 18px rgba(255,107,174,.1);padding:12px 0}
    .nav-link{font-size:14px;font-weight:700;color:var(--text)!important;padding:6px 14px!important;border-radius:99px;transition:color .2s,background .2s}
    .nav-link:hover,.nav-link.active{color:var(--pink)!important;background:var(--pink-bg)}
    .nav-icon{
      width:36px;height:36px;border-radius:50%;background:var(--pink-bg);
      display:flex;align-items:center;justify-content:center;
      color:var(--pink);font-size:14px;cursor:pointer;transition:background .2s;border:none
    }
    .nav-icon:hover{background:var(--pink-light)}

    /* ── HERO ────────────────────────────────────── */
    .hero{background:linear-gradient(160deg,#FFF0F7 0%,#F0FFFE 100%);padding:70px 0 0;overflow:hidden;position:relative}
    .hero::before{
      content:'';position:absolute;width:480px;height:480px;border-radius:50%;
      background:radial-gradient(circle,rgba(255,107,174,.12),transparent);
      top:-100px;right:-80px;pointer-events:none
    }
    .hero::after{
      content:'';position:absolute;width:280px;height:280px;border-radius:50%;
      background:radial-gradient(circle,rgba(93,217,196,.15),transparent);
      bottom:0;left:-40px;pointer-events:none
    }

    /* pulse dot */
    .pulse-dot{width:8px;height:8px;background:var(--pink);border-radius:50%;animation:pulse 1.5s infinite}
    @keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(1.3)}}

    .stat-divider{width:1px;background:var(--border);height:40px}
    .stat-num{font-family:'Fredoka One',cursive;font-size:26px;color:var(--pink)}
    .stat-label{font-size:11px;color:var(--muted);font-weight:700}

    /* hero visual */
    .hero-booth{
      background:linear-gradient(160deg,var(--pink-light),var(--mint-light));
      border-radius:28px;box-shadow:0 20px 50px rgba(255,107,174,.25);
      display:flex;flex-direction:column;align-items:center;justify-content:center;
      padding:24px;gap:16px
    }
    .booth-screen{
      background:#fff;border-radius:16px;
      box-shadow:0 8px 24px rgba(.1,.1,.1,.1);
      overflow:hidden;width:100%;max-width:260px
    }
    .booth-screen img{width:100%;display:block;object-fit:cover}
    .booth-label{background:var(--pink);color:#fff;padding:8px 20px;border-radius:99px;font-size:12px;font-weight:800}

    .floating-card{
      background:#fff;border-radius:14px;padding:10px 14px;
      box-shadow:0 8px 24px rgba(0,0,0,.12);display:flex;align-items:center;gap:10px;
      animation:floatCard 3s ease-in-out infinite;z-index:5;position:relative
    }
    .floating-card.delay-1{animation-delay:1s}
    @keyframes floatCard{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
    .card-icon{font-size:22px}
    .card-text p{font-size:10px;color:var(--muted);font-weight:700;margin:0}
    .card-text strong{font-size:13px;color:var(--dark);font-weight:800}

    /* ── MARQUEE ─────────────────────────────────── */
    .marquee-section{padding:18px 0;background:#fff;border-bottom:1px solid var(--border);overflow:hidden}
    .marquee-track{display:flex;gap:32px;animation:marquee 22s linear infinite;white-space:nowrap;width:max-content}
    .marquee-track span{font-size:13px;font-weight:800;color:var(--muted);display:flex;align-items:center;gap:8px}
    .marquee-track span i{color:var(--pink)}
    @keyframes marquee{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}

    /* ── FRAME CARDS ─────────────────────────────── */
    .frame-card{
      background:#fff;border-radius:var(--card-radius);overflow:hidden;
      box-shadow:0 4px 18px rgba(255,107,174,.1);border:2px solid transparent;
      transition:all .3s;cursor:pointer;height:100%
    }
    .frame-card:hover{transform:translateY(-6px);box-shadow:0 12px 30px rgba(255,107,174,.2);border-color:var(--pink-light)}

    .frame-img{
      height:190px;display:flex;align-items:center;justify-content:center;
      font-size:60px;position:relative;overflow:hidden
    }
    .frame-img img{width:100%;height:100%;object-fit:cover}
    .frame-type-badge{
      position:absolute;top:10px;left:10px;background:var(--pink);color:#fff;
      padding:4px 12px;border-radius:99px;font-size:11px;font-weight:800
    }
    .frame-info{padding:14px}
    .frame-info h5{font-size:15px;font-weight:800;color:var(--dark);margin-bottom:4px}
    .frame-info p{font-size:12px;color:var(--muted);margin-bottom:12px}
    .cut-tag{background:var(--pink-bg);color:var(--pink-dark);padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700}
    .frame-price{font-family:'Fredoka One',cursive;font-size:18px;color:var(--pink)}

    /* ── EXPERIENCE CARDS ────────────────────────── */
    .exp-card{border-radius:var(--card-radius);overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,.07);transition:all .3s;cursor:pointer;height:100%}
    .exp-card:hover{transform:translateY(-5px);box-shadow:0 12px 30px rgba(0,0,0,.12)}
    .exp-img{height:200px;display:flex;align-items:center;justify-content:center;font-size:68px;position:relative}
    .exp-img.pink-bg {background:linear-gradient(135deg,#FFD6EC,#FFB3D9)}
    .exp-img.mint-bg  {background:linear-gradient(135deg,#C8F5EE,#A0EEE4)}
    .exp-img.purple-bg{background:linear-gradient(135deg,#EDD9FF,#DDB5FF)}
    .exp-overlay{
      position:absolute;bottom:0;left:0;right:0;
      background:linear-gradient(transparent,rgba(45,27,61,.8));
      padding:20px 16px 14px
    }
    .exp-overlay h5{color:#fff;font-size:15px;font-weight:800;margin-bottom:2px}
    .exp-overlay p{color:rgba(255,255,255,.8);font-size:12px;margin:0}

    .video-thumb{
      background:linear-gradient(135deg,var(--dark),#4A3050);
      border-radius:14px;display:flex;align-items:center;justify-content:center;
      cursor:pointer;position:relative;overflow:hidden;min-height:100px
    }
    .video-thumb::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,107,174,.3),rgba(93,217,196,.3))}
    .play-btn{
      width:42px;height:42px;background:#fff;border-radius:50%;
      display:flex;align-items:center;justify-content:center;
      color:var(--pink);font-size:15px;z-index:1;box-shadow:0 4px 12px rgba(0,0,0,.2)
    }

    /* ── OUTLET CARDS ────────────────────────────── */
    .outlet-card{background:#fff;border-radius:var(--card-radius);overflow:hidden;box-shadow:0 4px 18px rgba(255,107,174,.1);transition:all .3s;cursor:pointer;height:100%}
    .outlet-card:hover{transform:translateY(-5px);box-shadow:0 12px 28px rgba(255,107,174,.2)}
    .outlet-img{height:150px;display:flex;align-items:center;justify-content:center;font-size:52px;position:relative}
    .outlet-img.g1{background:linear-gradient(135deg,#FFD6EC,#FFA8D0)}
    .outlet-img.g2{background:linear-gradient(135deg,#C8F5EE,#8EE8DC)}
    .outlet-img.g3{background:linear-gradient(135deg,#EDD9FF,#D4AAFF)}
    .outlet-img.g4{background:linear-gradient(135deg,#FFE8B0,#FFD166)}
    .outlet-status{position:absolute;top:10px;right:10px;padding:4px 12px;border-radius:99px;font-size:10px;font-weight:800}
    .status-open{background:#D1FAE5;color:#059669}
    .status-soon{background:#FEE2E2;color:#DC2626}
    .outlet-info{padding:14px}
    .outlet-info h5{font-size:14px;font-weight:800;color:var(--dark);margin-bottom:4px}
    .outlet-info p{font-size:12px;color:var(--muted);margin-bottom:10px}
    .outlet-rating{display:flex;align-items:center;gap:4px;font-size:12px;font-weight:700;color:var(--dark)}
    .outlet-rating i{color:#FFD166}

    /* ── PROMO CARDS ─────────────────────────────── */
    .promo-card{border-radius:var(--card-radius);overflow:hidden;cursor:pointer;transition:all .3s;height:100%}
    .promo-card:hover{transform:translateY(-5px);box-shadow:0 12px 28px rgba(0,0,0,.12)}
    .promo-bg{height:170px;display:flex;align-items:center;justify-content:center;font-size:52px;position:relative}
    .promo-bg.p1{background:linear-gradient(135deg,#FF6BAE,#FF3D8B)}
    .promo-bg.p2{background:linear-gradient(135deg,#5DD9C4,#38B2A0)}
    .promo-bg.p3{background:linear-gradient(135deg,#FFD166,#FFA500)}
    .promo-bg.p4{background:linear-gradient(135deg,#C084FC,#9333EA)}
    .promo-badge-big{
      position:absolute;top:12px;right:12px;
      background:rgba(255,255,255,.25);backdrop-filter:blur(8px);
      color:#fff;padding:5px 12px;border-radius:99px;font-size:11px;font-weight:800
    }
    .promo-info{background:#fff;padding:14px;border-radius:0 0 var(--card-radius) var(--card-radius)}
    .promo-info h5{font-size:14px;font-weight:800;color:var(--dark);margin-bottom:4px}
    .promo-info p{font-size:11px;color:var(--muted);margin-bottom:10px}
    .promo-period{font-size:10px;color:var(--muted);font-weight:700;display:flex;align-items:center;gap:4px}
    .promo-period i{color:var(--pink)}

    /* ── COLLAB BANNER ───────────────────────────── */
    .collab-banner{
      background:linear-gradient(135deg,var(--pink),var(--purple));
      border-radius:20px;position:relative;overflow:hidden
    }
    .collab-banner::before{content:'🐰';position:absolute;right:180px;font-size:90px;opacity:.15;top:50%;transform:translateY(-50%)}

    /* ── WHY / PARTNER ───────────────────────────── */
    .why-icon-box{
      width:44px;height:44px;border-radius:12px;flex-shrink:0;
      display:flex;align-items:center;justify-content:center;font-size:20px
    }
    .why-icon-box.pink  {background:var(--pink-light)}
    .why-icon-box.mint  {background:var(--mint-light)}
    .why-icon-box.yellow{background:#FFF3CD}
    .why-icon-box.purple{background:#F3E8FF}

    .partner-stat-box{background:var(--pink-bg);border-radius:16px;padding:20px;text-align:center}
    .partner-stat-box.mint{background:var(--mint-light)}
    .partner-stat-num{font-family:'Fredoka One',cursive;font-size:28px;color:var(--pink)}
    .partner-stat-box.mint .partner-stat-num{color:var(--mint-dark)}

    /* ── GROW / FRANCHISE ────────────────────────── */
    .grow-inner{
      background:linear-gradient(160deg,var(--dark) 0%,#4A1F5E 100%);
      border-radius:28px;padding:clamp(32px,5vw,60px);position:relative;overflow:hidden
    }
    .grow-inner::before{
      content:'';position:absolute;width:380px;height:380px;border-radius:50%;
      background:radial-gradient(circle,rgba(255,107,174,.2),transparent);
      top:-80px;right:-80px
    }
    .grow-inner::after{
      content:'';position:absolute;width:260px;height:260px;border-radius:50%;
      background:radial-gradient(circle,rgba(93,217,196,.15),transparent);
      bottom:-60px;left:-60px
    }
    .grow-rel{position:relative;z-index:1}

    .invest-card{
      background:rgba(255,255,255,.08);backdrop-filter:blur(10px);
      border:1px solid rgba(255,255,255,.15);border-radius:16px;padding:20px
    }
    .invest-card.featured{background:linear-gradient(135deg,var(--pink),var(--pink-dark));border-color:transparent}
    .invest-tag{background:var(--yellow);color:var(--dark);padding:3px 10px;border-radius:99px;font-size:10px;font-weight:800;display:inline-block;margin-bottom:8px}
    .invest-price{font-family:'Fredoka One',cursive;font-size:24px;color:var(--yellow)}
    .invest-card.featured .invest-price{color:#fff}
    .benefit-item{display:flex;align-items:center;gap:12px;color:#fff;font-size:14px;font-weight:700}
    .benefit-item i{color:var(--mint);font-size:15px}

    /* ── FOOTER ──────────────────────────────────── */
    footer{background:var(--dark);color:#fff;padding:56px 0 0}
    .footer-brand p{color:rgba(255,255,255,.6);font-size:13px;line-height:1.7}
    .social-btn{
      width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;
      font-size:14px;transition:all .2s;cursor:pointer;border:none
    }
    .social-btn.pink  {background:rgba(255,107,174,.2);color:var(--pink)}
    .social-btn.mint  {background:rgba(93,217,196,.2);color:var(--mint)}
    .social-btn.purple{background:rgba(192,132,252,.2);color:var(--purple)}
    .social-btn.pink:hover  {background:var(--pink);color:#fff}
    .social-btn.mint:hover  {background:var(--mint);color:#fff}
    .social-btn.purple:hover{background:var(--purple);color:#fff}
    .footer-col h6{font-size:14px;font-weight:800;margin-bottom:16px;color:#fff}
    .footer-col li{margin-bottom:8px}
    .footer-col a{color:rgba(255,255,255,.6);font-size:13px;transition:color .2s}
    .footer-col a:hover{color:var(--pink)}
    .footer-divider{border-color:rgba(255,255,255,.08)!important}

    /* ── STICKY FOOTER BAR ───────────────────────── */
    .footer-bar{
      background:linear-gradient(90deg,var(--pink),var(--purple));
      padding:14px 0;position:sticky;bottom:0;z-index:1030
    }
    .footer-bar h5{color:#fff;font-size:14px;font-weight:800;margin:0;white-space:nowrap}
    .footer-bar input{
      flex:1;border:none;outline:none;padding:9px 16px;
      border-radius:99px;font-family:'Nunito',sans-serif;font-size:13px;min-width:0
    }
    .footer-bar button{
      background:var(--dark);color:#fff;border:none;
      padding:9px 22px;border-radius:99px;font-weight:800;font-size:13px;
      cursor:pointer;font-family:'Nunito',sans-serif;white-space:nowrap;transition:background .2s
    }
    .footer-bar button:hover{background:#1a0a2e}
    .footer-bar .policy{color:rgba(255,255,255,.7);font-size:11px}

    /* ── FRANCHISE CTA ───────────────────────────── */
    .franchise-cta{border:2px dashed var(--pink-light);border-radius:var(--card-radius);padding:28px;background:#fff;text-align:center}

    /* ── SEE-ALL LINK ────────────────────────────── */
    .see-all{color:var(--pink);font-weight:800;font-size:13px;display:inline-flex;align-items:center;gap:5px}
    .see-all:hover{text-decoration:underline;color:var(--pink)}

    /* ── WHATSAPP MODAL ──────────────────────────── */
    .modal-title {
      color: #0E3142;
      font-weight: 700;
      font-size: 1.2rem;
    }
    .subtitle {
      font-size: 0.875rem;
      color: #6b7280;
      margin-bottom: 1.25rem;
    }
    .section-divider {
      border-top: 1px solid #e5e7eb;
      margin: 1rem 0 0.75rem;
    }
    .section-heading {
      font-weight: 600;
      color: #0E3142;
      margin-bottom: 1rem;
      font-size: 0.95rem;
    }
    .form-label {
      font-size: 0.875rem;
      font-weight: 500;
      color: #374151;
      margin-bottom: 0.375rem;
    }
    .form-control,
    .form-select {
      font-size: 0.875rem;
      background-color: #f9fafb;
      border-color: #e5e7eb;
      padding: 0.625rem 1rem;
      border-radius: 0.5rem;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus,
    .form-select:focus {
      border-color: #0d9488;
      box-shadow: 0 0 0 0.2rem rgba(13, 148, 136, 0.2);
      background-color: #fff;
    }
    .btn-cancel {
      border: 2px solid #d1d5db;
      color: #374151;
      background: #fff;
      font-size: 0.875rem;
      font-weight: 500;
      border-radius: 0.5rem;
      padding: 0.625rem 1.25rem;
      transition: background 0.2s;
    }
    .btn-cancel:hover {
      background: #f9fafb;
      border-color: #9ca3af;
    }
    .btn-whatsapp {
      background-color: #0d9488;
      color: #fff;
      font-size: 0.875rem;
      font-weight: 500;
      border-radius: 0.5rem;
      padding: 0.625rem 1.25rem;
      border: none;
      box-shadow: 0 2px 6px rgba(13, 148, 136, 0.35);
      transition: background 0.2s, box-shadow 0.2s;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      justify-content: center;
    }
    .btn-whatsapp:hover {
      background-color: #0f766e;
      box-shadow: 0 4px 12px rgba(13, 148, 136, 0.4);
      color: #fff;
    }
    .modal-content {
      border-radius: 1rem;
      border: none;
      box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .btn-close-custom {
      width: 2rem;
      height: 2rem;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      border: none;
      background: transparent;
      transition: background 0.2s;
      cursor: pointer;
      flex-shrink: 0;
    }
    .btn-close-custom:hover { background: #f3f4f6; }
    .btn-close-custom svg  { stroke: #0E3142; stroke-width: 2; }

    /* ── RESPONSIVE TWEAKS ───────────────────────── */
    @media(max-width:768px){
      .hero{padding:50px 0 0}
      .footer-bar .policy{display:none}
      .collab-banner::before{display:none}
      .footer-bar .d-flex{flex-wrap:wrap;gap:8px!important}
    }
    @media(max-width:576px){
      .hero-stats{justify-content:center}
      .footer-bar h5{display:none}
    }
  </style>
</head>
<body>

<!-- ══ TOPBAR ══════════════════════════════════════ -->
<!--<div class="topbar">-->
<!--  <p>🎉 Grand Opening Outlet Baru! Dapatkan promo spesial <span class="badge-pill">GRATIS 1 Strip</span></p>-->
<!--</div>-->

<!-- ══ NAVBAR ══════════════════════════════════════ -->
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand" href="#">
      <img src="assets/img/logoNeo.png" alt="Neo Photo Indonesia" style="width:240px"/>
    </a>

    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation">
      <i class="fas fa-bars" style="color:var(--pink)"></i>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto gap-1">
        <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#frames">Frame</a></li>
        <li class="nav-item"><a class="nav-link" href="#experiences">Experiences</a></li>
        <li class="nav-item"><a class="nav-link" href="#outlet">Outlet</a></li>
        <li class="nav-item"><a class="nav-link" href="#activation">Activation</a></li>
        <li class="nav-item"><a class="nav-link" href="#partnership">Kemitraan</a></li>
      </ul>
      <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
        <a href="#outlet" class="btn-neo-primary" style="padding:9px 18px;font-size:13px">
          <i class="fas fa-store"></i> Cari Outlet
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- ══ FLOATING WHATSAPP BUTTON ════════════════════ -->
<a class="d-flex justify-content-center align-items-center" target="_blank">
  <button type="button" class="btn-floating whatsapp" data-bs-toggle="modal" data-bs-target="#whatsappModal">
    <img src="https://i.imgur.com/LBW2Lso.png" alt="WhatsApp">
    <span>(62) 858-1084-7780</span>
  </button>
</a>

<!-- ══ WHATSAPP MODAL ══════════════════════════════ -->
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 600px;">
    <div class="modal-content p-2">

      <!-- Header -->
      <div class="modal-header d-flex justify-content-between align-items-center px-4 pt-4 border-0">
        <h5 class="modal-title" id="whatsappModalLabel">Hubungi Kami via WhatsApp</h5>
        <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg" width="20" height="20">
            <path d="M5 5L19 19" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M5 19L19 5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>

      <!-- Body -->
      <div class="modal-body px-4 pb-4">
        <p class="subtitle">Isi form di bawah ini dan kami akan menghubungi Anda melalui WhatsApp</p>

        <form id="whatsappForm" novalidate>

          <!-- Nama Lengkap -->
          <div class="mb-3">
            <label for="fullName" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="fullName" name="fullName"
              placeholder="Masukkan nama lengkap" required>
            <div class="invalid-feedback">Nama lengkap wajib diisi.</div>
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="email" name="email"
              placeholder="Masukkan email Anda" required>
            <div class="invalid-feedback">Email yang valid wajib diisi.</div>
          </div>

          <hr class="section-divider">
          <p class="section-heading">Informasi Bisnis</p>

          <!-- Domisili -->
          <div class="mb-3">
            <label for="domicile" class="form-label">Domisili <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="domicile" name="domicile"
              placeholder="Masukkan domisili Anda" required>
            <div class="invalid-feedback">Domisili wajib diisi.</div>
          </div>

          <!-- Budget Range -->
          <div class="mb-3">
            <label for="budgetRange" class="form-label">Pilih Kisaran Budget Investasi <span class="text-danger">*</span></label>
            <select class="form-select" id="budgetRange" name="budgetRange" required>
              <option value="" disabled selected>Pilih kisaran budget...</option>
              <option value="<50jt">Di bawah Rp 50 juta</option>
              <option value="50-100jt">Rp 50 juta – Rp 100 juta</option>
              <option value="100-500jt">Rp 100 juta – Rp 500 juta</option>
              <option value="500jt+">Di atas Rp 500 juta</option>
            </select>
            <div class="invalid-feedback">Kisaran budget wajib dipilih.</div>
          </div>

          <!-- Industri -->
          <div class="mb-3">
            <label for="industry" class="form-label">Industri <span class="text-danger">*</span></label>
            <select class="form-select" id="industry" name="industry" required>
              <option value="" disabled selected>Pilih industri</option>
              <option value="food">Food & Beverage</option>
              <option value="retail">Retail</option>
              <option value="tech">Teknologi</option>
              <option value="health">Kesehatan</option>
              <option value="edu">Pendidikan</option>
              <option value="other">Lainnya</option>
            </select>
            <div class="invalid-feedback">Industri wajib dipilih.</div>
          </div>

          <!-- Has Location -->
          <div class="mb-3">
            <label for="hasLocation" class="form-label">
              Sudah punya lokasi usaha yang mau digunakan? <span class="text-danger">*</span>
            </label>
            <select class="form-select" id="hasLocation" name="hasLocation" required>
              <option value="" disabled selected>Pilih status lokasi</option>
              <option value="yes">Ya, sudah punya</option>
              <option value="no">Belum punya</option>
              <option value="looking">Sedang mencari</option>
            </select>
            <div class="invalid-feedback">Status lokasi wajib dipilih.</div>
          </div>

          <!-- Investment Time -->
          <div class="mb-3">
            <label for="investmentTime" class="form-label">
              Rencana waktu investasi? <span class="text-danger">*</span>
              <span class="text-muted ms-1" style="font-size:0.75rem; cursor:help;"
                title="Kapan Anda berencana untuk memulai investasi">ⓘ</span>
            </label>
            <select class="form-select" id="investmentTime" name="investmentTime" required>
              <option value="" disabled selected>Pilih rencana waktu</option>
              <option value="1m">Kurang dari 1 bulan</option>
              <option value="3m">1–3 bulan</option>
              <option value="6m">3–6 bulan</option>
              <option value="1y">6–12 bulan</option>
              <option value="1y+">Lebih dari 1 tahun</option>
            </select>
            <div class="invalid-feedback">Rencana waktu wajib dipilih.</div>
          </div>

          <!-- Tipe Bisnis -->
          <div class="mb-4">
            <label for="tipeBisnis" class="form-label">Tipe Bisnis <span class="text-danger">*</span></label>
            <select class="form-select" id="tipeBisnis" name="tipeBisnis" required>
              <option value="" disabled selected>Pilih tipe bisnis</option>
              <option value="franchise">Franchise</option>
              <option value="independent">Bisnis Mandiri</option>
              <option value="partnership">Kemitraan</option>
              <option value="online">Online / E-commerce</option>
            </select>
            <div class="invalid-feedback">Tipe bisnis wajib dipilih.</div>
          </div>

          <!-- Buttons -->
          <div class="row g-2">
            <div class="col-6">
              <button type="button" class="btn btn-cancel w-100" data-bs-dismiss="modal">
                Batal
              </button>
            </div>
            <div class="col-6">
              <button type="submit" class="btn btn-whatsapp w-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path>
                </svg>Kirim via WhatsApp
              </button>
            </div>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

<!-- ══ HERO ════════════════════════════════════════ -->
<section class="hero" id="home">
  <div class="container">
    <div class="row align-items-center g-5 pb-5">

      <!-- Copy -->
      <div class="col-lg-6">
        <div class="d-inline-flex align-items-center gap-2 bg-white border px-3 py-2 rounded-pill mb-4" style="border-color:var(--pink-light)!important;box-shadow:0 4px 12px rgba(255,107,174,.1)">
          <div class="pulse-dot"></div>
          <span style="font-size:12px;font-weight:800;color:var(--pink)">📍 Tersedia di Kota-Kota Besar se-Indonesia</span>
        </div>

        <h1 class="font-fredoka mb-3" style="font-size:clamp(36px,5vw,54px);color:var(--dark);line-height:1.1">
          Abadikan<br/><span style="color:var(--pink)">Momen Terbaik</span><br/>Bersama Kamu!
        </h1>

        <p class="mb-4" style="font-size:16px;color:var(--muted);line-height:1.7;max-width:440px">
          Photobooth modern dengan ratusan pilihan frame lucu &amp; unik. Cetak kenangan indah bersama teman, keluarga, dan orang tersayang.
        </p>

        <div class="d-flex flex-wrap gap-3 mb-4">
          <a href="#outlet" class="btn-neo-primary"><i class="fas fa-camera"></i> Coba Sekarang</a>
          <a href="#frames" class="btn-neo-outline">Lihat Frame ✨</a>
        </div>

        <div class="hero-stats d-flex align-items-center gap-4">
          <div class="text-center">
            <div class="stat-num">200+</div>
            <div class="stat-label">Frame Design</div>
          </div>
          <div class="stat-divider"></div>
          <div class="text-center">
            <div class="stat-num">50K+</div>
            <div class="stat-label">Pelanggan Puas</div>
          </div>
          <div class="stat-divider"></div>
          <div class="text-center">
            <div class="stat-num">4</div>
            <div class="stat-label">Outlet Aktif</div>
          </div>
        </div>
      </div>

      <!-- Visual -->
      <div class="col-lg-6 d-flex flex-column align-items-center gap-3">
        <div class="floating-card align-self-start ms-lg-5">
          <div class="card-icon">🌸</div>
          <div class="card-text">
            <p>Frame Terpopuler</p>
            <strong>High Angle</strong>
          </div>
        </div>

        <div class="hero-booth w-100" style="max-width:300px">
            <img src="assets/img/High-Angle.png" alt="High Angle Frame"/>
          <a type="button" href="#outlet" class="booth-label">📸 Mulai Foto</a>
        </div>

        <div class="floating-card align-self-end me-lg-5 delay-1">
          <div class="card-icon">🎀</div>
          <div class="card-text">
            <p>Promo Terus</p>
            <strong>Setiap Hari!</strong>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ══ MARQUEE ══════════════════════════════════════ -->
<div class="marquee-section">
  <div class="marquee-track">
    <span><i class="fas fa-star"></i>4 Cut Basis</span>
    <span><i class="fas fa-star"></i>4 Length Cut</span>
    <span><i class="fas fa-star"></i>6 Cut Basis</span>
    <span><i class="fas fa-star"></i>High Angle</span>
    <span><i class="fas fa-star"></i>Wide Frame</span>
    <span><i class="fas fa-star"></i>Collaboration Frame</span>
    <span><i class="fas fa-star"></i>Special Edition</span>
    <!-- duplicate for seamless loop -->
    <span><i class="fas fa-star"></i>4 Cut Basis</span>
    <span><i class="fas fa-star"></i>4 Length Cut</span>
    <span><i class="fas fa-star"></i>6 Cut Basis</span>
    <span><i class="fas fa-star"></i>High Angle</span>
    <span><i class="fas fa-star"></i>Wide Frame</span>
    <span><i class="fas fa-star"></i>Collaboration Frame</span>
    <span><i class="fas fa-star"></i>Special Edition</span>
  </div>
</div>

<!-- ══ FRAMES ══════════════════════════════════════ -->
<section class="py-5 bg-pink-bg" id="frames">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
      <div>
        <div class="section-tag mb-2">️ Koleksi Frame</div>
        <h2 class="section-title mb-1">Pilih <span>Frame</span> Favoritmu</h2>
        <p style="font-size:14px;color:var(--muted);margin:0">Ratusan pilihan frame unik untuk setiap momen spesialmu</p>
      </div>
      <!--<a href="#" class="see-all">Lihat Semua <i class="fas fa-arrow-right"></i></a>-->
    </div>

    <div class="row g-4">
      <div class="col-sm-6 col-lg-4">
        <div class="frame-card">
          <div class="frame-img" style="background:linear-gradient(135deg,#FFD6EC,#FFB3D9)">
            <img src="assets/HOMEPAGE/FRAME/COVER-FRAME/originalv2.jpg" alt="Original Frame"/>
            <div class="frame-type-badge">Original</div>
          </div>
          <div class="frame-info">
            <h5>Original Neophoto</h5>
            <p>Frame Original Untuk Kamu</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex gap-2">
                  <span class="cut-tag">All Version</span>
              </div>
              <span class="frame-price">Rp 45K</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-4">
        <div class="frame-card">
          <div class="frame-img" style="background:linear-gradient(135deg,#C8F5EE,#8EE8DC);font-size:60px">
                <img src="assets/HOMEPAGE/FRAME/COVER-FRAME/collabv2.jpg" alt="Collab Frames"/>
            <div class="frame-type-badge">Collab's</div>
          </div>
          <div class="frame-info">
            <h5>Collaboration Frames</h5>
            <p>Kolaborasi Dari Fandom Seluruh Indonesia</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex gap-2">
                <span class="cut-tag">All Version</span>
              </div>
              <span class="frame-price">Rp 45K</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-4">
        <div class="frame-card">
          <div class="frame-img" style="background:linear-gradient(135deg,#EDD9FF,#D4AAFF);font-size:60px">
            <img src="assets/HOMEPAGE/FRAME/COVER-FRAME/seasonalv2.jpg" alt="Seasonal Frames"/>
            <div class="frame-type-badge">Seasonal</div>
          </div>
          <div class="frame-info">
            <h5>Seasonal Frames</h5>
            <p>Rayakan Hari Hari Besar-mu di Neophoto</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex gap-2">
                <span class="cut-tag">All Version</span>
              </div>
              <span class="frame-price">Rp 45K</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══ EXPERIENCES ══════════════════════════════════ -->
<section class="py-5 bg-white" id="experiences">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
      <div>
        <div class="section-tag mb-2">✨ Pengalaman</div>
        <h2 class="section-title mb-1">Pengalaman <span>Seru</span> di Neo Photo</h2>
        <p style="font-size:14px;color:var(--muted);margin:0">Lebih dari sekadar foto — ini tentang kenangan</p>
      </div>
      <!--<a href="#" class="see-all">Lihat Semua <i class="fas fa-arrow-right"></i></a>-->
    </div>

    <div class="row g-4 mb-4 d-flex align-items-center justify-content-center">
  <div class="col-sm-6 col-lg-4 d-flex justify-content-center">
    <div class="hero-booth mx-auto" style="max-width:300px">
      <img src="assets/HOMEPAGE/EXPERIENCEv3/1.png" alt="High Angle Frame"/>
      <a type="button" href="#outlet" class="booth-label">📸 Mulai Foto</a>
    </div>
  </div>
  <div class="col-sm-6 col-lg-4 d-flex justify-content-center">
    <div class="hero-booth mx-auto" style="max-width:300px">
      <img src="assets/HOMEPAGE/EXPERIENCEv3/2.png" alt="High Angle Frame"/>
      <a type="button" href="#outlet" class="booth-label">📸 Mulai Foto</a>
    </div>
  </div>
  <div class="col-sm-6 col-lg-4 d-flex justify-content-center">
    <div class="hero-booth mx-auto" style="max-width:300px">
      <img src="assets/HOMEPAGE/EXPERIENCEv3/3.png" alt="High Angle Frame"/>
      <a type="button" href="#outlet" class="booth-label">📸 Mulai Foto</a>
    </div>
  </div>

  <div class="row d-flex justify-content-center">
    <div class="col-sm-12 col-md-4 d-flex justify-content-center">
      <video width="320" height="240" controlsList="nodownload nofullscreen" controls autoplay muted loop>
        <source src="assets/HOMEPAGE/EXPERIENCEv3/model1.mp4" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    </div>
    <div class="col-sm-12 col-md-4 d-flex justify-content-center">
      <video width="320" height="240" controlsList="nodownload nofullscreen" controls autoplay muted loop>
        <source src="assets/HOMEPAGE/EXPERIENCEv3/model2.mp4" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    </div>
    <div class="col-sm-12 col-md-4 d-flex justify-content-center">
      <video width="320" height="240" controlsList="nodownload nofullscreen" controls autoplay muted loop>
        <source src="assets/HOMEPAGE/EXPERIENCEv3/model3.mp4" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    </div>
  </div>
</div>

  </div>
</section>

<!-- ══ OUTLET ═══════════════════════════════════════ -->
<section class="py-5 bg-pink-bg" id="outlet">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
      <div>
        <div class="section-tag mb-2">📍 Outlet Kami</div>
        <h2 class="section-title mb-1">Temukan <span>Outlet</span> Terdekat</h2>
        <p style="font-size:14px;color:var(--muted);margin:0">Tersebar di 20+ kota besar di seluruh Indonesia</p>
      </div>
      <!--<a href="#" class="see-all">Lihat Semua <i class="fas fa-arrow-right"></i></a>-->
    </div>

    <div class="row g-4">
      <div class="col-sm-6 col-xl-3">
        <div class="outlet-card">
          <img class="outlet-img g1" src="assets/HOMEPAGE/OUTLET/gancit.png" style="width:100%;object-fit:cover" alt="Gandaria City"/><span class="outlet-status status-open">Buka</span>
          <div class="outlet-info ">
            <h5>Gandaria City</h5>
            <p>Lt. 2, Gandaria City Mall, Jakarta</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="outlet-rating"><i class="fas fa-star"></i> 4.9</div>
              <a TYPE="button" href="https://maps.app.goo.gl/8S5jzd7gyZe7h8Hj8" class="btn-wa"><i class="fa-solid fa-map-location"></i> Temukan</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="outlet-card">
          <img class="outlet-img g1" src="assets/HOMEPAGE/OUTLET/central park 2.png" style="width:100%;object-fit:cover" alt="CP2"/><span class="outlet-status status-open">Buka</span>
          <div class="outlet-info">
            <h5>Central Park 2</h5>
            <p>Lt. 1, Central Park Mall, Jakarta</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="outlet-rating"><i class="fas fa-star"></i> 4.8</div>
              <a TYPE="button" href="https://maps.app.goo.gl/4r11d2tX4CBWCgbt9" class="btn-wa"><i class="fa-solid fa-map-location"></i> Temukan</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="outlet-card">
           <img class="outlet-img g1" src="assets/HOMEPAGE/OUTLET/kokas.png" style="width:100%;object-fit:cover" alt="Kokas"/><span class="outlet-status status-open">Buka</span>
          <div class="outlet-info">
            <h5>Kota Kasablanka</h5>
            <p>Lt. 3, Kota Kasablanka Mall, Jakarta</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="outlet-rating"><i class="fas fa-star"></i> 5.0</div>
              <a TYPE="button" href="https://maps.app.goo.gl/BoKCxzQxk5GN88ix9" class="btn-wa"><i class="fa-solid fa-map-location"></i> Temukan</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="outlet-card">
          <img class="outlet-img g1" src="assets/HOMEPAGE/OUTLET/blok m.png" style="width:100%;object-fit:cover" alt="Kokas"/><span class="outlet-status status-open">Buka</span>
          <div class="outlet-info">
            <h5>Plaza Blok M</h5>
            <p>Lt. 2, Plaza Blok M, Jakarta</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="outlet-rating"><i class="fas fa-star"></i> 4.7</div>
              <a TYPE="button" href="https://maps.app.goo.gl/cvLKC943gwQG8Ux48" class="btn-wa"><i class="fa-solid fa-map-location"></i> Temukan</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="franchise-cta mt-4">
      <p style="font-size:14px;color:var(--muted);margin-bottom:8px">Mau punya outlet Neo Photo sendiri?</p>
      <h4 class="font-fredoka mb-3" style="color:var(--dark);font-size:22px">Bergabunglah sebagai Mitra Neo 🚀</h4>
      <a href="#grow" class="btn-neo-primary"><i class="fas fa-handshake"></i> Gabung Kemitraan</a>
    </div>
  </div>
</section>

<!-- ══ ACTIVATION / PROMO ══════════════════════════ -->
<section class="py-5 bg-white" id="activation">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
      <div>
        <div class="section-tag mb-2">🎉 Promo & Aktivasi</div>
        <h2 class="section-title mb-1">Promo <span>Spesial</span> Untuk Kamu</h2>
        <p style="font-size:14px;color:var(--muted);margin:0">Jangan sampai ketinggalan penawaran terbaik!</p>
      </div>
    </div>

    <div class="row g-4 mb-4">
      <div class="col-6 col-md-6 col-xl-3">
        <div class="promo-card">
          <div class="promo-bg p1"><img src="assets/HOMEPAGE/ACTIVATION/COVER-PROMOs/HAPPY-HOUR.jpg" style="width:100%;object-fit:cover"><div class="promo-badge-big">NEW!</div></div>
          <div class="promo-info">
            <h5>HAPPY HOUR</h5>
            <p>Makan Siang Makin Happy di Neophoto</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="promo-period"><i class="fas fa-clock"></i> Senin-Kamis</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-6 col-xl-3">
        <div class="promo-card">
          <div class="promo-bg p1"><img src="assets/HOMEPAGE/ACTIVATION/COVER-PROMOs/SNAP N POST.jpg" style="width:100%;object-fit:cover"><div class="promo-badge-big">NEW!</div></div>
          <div class="promo-info">
            <h5>SNAP & POST</h5>
            <p>Upload Fotomu Untuk Claim Discountnya!</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="promo-period"><i class="fas fa-clock"></i> ongoing</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-6 col-xl-3">
        <div class="promo-card">
          <div class="promo-bg p1"><img src="assets/HOMEPAGE/ACTIVATION/COVER-PROMOs/STUDENT PACKAGE.jpg" style="width:100%;object-fit:cover"><div class="promo-badge-big">NEW!</div></div>
          <div class="promo-info">
            <h5>STUDENT PACKAGE</h5>
            <p>Paket Spesial Pelajar</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="promo-period"><i class="fas fa-clock"></i> Senin-Kamis</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-6 col-xl-3">
        <div class="promo-card">
          <div class="promo-bg p1"><img src="assets/HOMEPAGE/ACTIVATION/COVER-PROMOs/CATCH SS.jpg" style="width:100%;object-fit:cover"><div class="promo-badge-big">NEW!</div></div>
          <div class="promo-info">
            <h5>CATCH SS</h5>
            <p>Mainkan Gamesnya Dapatkan Promonya</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="promo-period"><i class="fas fa-clock"></i> ongoing</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Collab Banner -->
    <div class="collab-banner p-4 p-md-5">
      <div class="row align-items-center g-4 grow-rel">
        <div class="col-md-8">
          <h3 class="font-fredoka text-white mb-2" style="font-size:clamp(22px,4vw,30px)">Collaboration Frame 🤝</h3>
          <p class="mb-3" style="color:rgba(255,255,255,.85);font-size:14px">Punya brand atau komunitas? Kolaborasi dengan Neo Photo untuk frame eksklusif!</p>
          <a type="button" href="https://www.instagram.com/m/neophotoindonesia/" class="btn-neo-mint"><i class="fas fa-handshake"></i> Request Kolaborasi</a>
        </div>
        <div class="col-md-4 text-center d-none d-md-block" style="font-size:80px"><img src="assets/HOMEPAGE/FRAME/partner.png"></div>
      </div>
    </div>
  </div>
</section>

<!-- ══ PARTNERSHIP ══════════════════════════════════ -->
<section class="py-5 bg-pink-bg" id="partnership">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="section-tag mb-2">🤝 Kemitraan</div>
        <h2 class="section-title mb-1">Kenapa <span>Harus</span> Neo Photo?</h2>
        <p class="mb-4" style="font-size:14px;color:var(--muted)">Kami hadir dengan konsep yang berbeda dan pengalaman yang tak terlupakan</p>

        <div class="d-flex flex-column gap-3 mb-4">
          <div class="d-flex align-items-start gap-3">
            <div class="why-icon-box pink">📸</div>
            <div>
              <h6 style="font-size:14px;font-weight:800;color:var(--dark);margin-bottom:2px">Kualitas Cetak Premium</h6>
              <p style="font-size:12px;color:var(--muted);margin:0;line-height:1.5">Hasil foto jernih dan tahan lama menggunakan teknologi cetak terkini</p>
            </div>
          </div>
          <div class="d-flex align-items-start gap-3">
            <div class="why-icon-box mint">🎨</div>
            <div>
              <h6 style="font-size:14px;font-weight:800;color:var(--dark);margin-bottom:2px">Frame Frame Unik Untuk Kamu!</h6>
              <p style="font-size:12px;color:var(--muted);margin:0;line-height:1.5">Update frame baru setiap minggu mengikuti tren terkini</p>
            </div>
          </div>
          <div class="d-flex align-items-start gap-3">
            <div class="why-icon-box yellow">⚡</div>
            <div>
              <h6 style="font-size:14px;font-weight:800;color:var(--dark);margin-bottom:2px">Proses Cepat &amp; Mudah</h6>
              <p style="font-size:12px;color:var(--muted);margin:0;line-height:1.5">Cukup 3 menit dari foto hingga hasil cetak di tangan kamu</p>
            </div>
          </div>
          <div class="d-flex align-items-start gap-3">
            <div class="why-icon-box purple">📍</div>
            <div>
              <h6 style="font-size:14px;font-weight:800;color:var(--dark);margin-bottom:2px">Lokasi Strategis</h6>
              <p style="font-size:12px;color:var(--muted);margin:0;line-height:1.5">Hadir di mall-mall besar di seluruh Indonesia, mudah dijangkau</p>
            </div>
          </div>
        </div>

        <a type="button" data-bs-toggle="modal" data-bs-target="#whatsappModal" class="btn-neo-primary"><i class="fas fa-handshake"></i> Gabung Sebagai Mitra</a>
      </div>

      <div class="col-lg-6">
        <div class="bg-white rounded-4 p-4" style="box-shadow:0 12px 40px rgba(255,107,174,.15)">
          <div class="row g-3">
            <div class="col-6"><div class="partner-stat-box"><div class="partner-stat-num">50K+</div><p style="font-size:12px;color:var(--muted);font-weight:700;margin:0">Pelanggan Puas</p></div></div>
            <div class="col-6"><div class="partner-stat-box mint"><div class="partner-stat-num">4+</div><p style="font-size:12px;color:var(--muted);font-weight:700;margin:0">Outlet Aktif</p></div></div>
            <div class="col-6"><div class="partner-stat-box mint"><div class="partner-stat-num">Frame Unik</div><p style="font-size:12px;color:var(--muted);font-weight:700;margin:0">Khusus Buat Kamu!</p></div></div>
            <div class="col-6"><div class="partner-stat-box"><div class="partner-stat-num">4.9⭐</div><p style="font-size:12px;color:var(--muted);font-weight:700;margin:0">Rating Rata-rata</p></div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══ GROW / FRANCHISE ═════════════════════════════ -->
<section class="py-5 bg-white" id="grow">
  <div class="container">
    <div class="grow-inner">
      <div class="row align-items-center g-5 grow-rel">
        <div class="col-lg-6">
          <div class="section-tag mb-3" style="background:rgba(255,107,174,.2);color:var(--pink-light)">Grow With Us!</div>
          <h2 class="font-fredoka text-white mb-3" style="font-size:clamp(28px,4vw,38px);line-height:1.15">Investasi Terbaik<br/>Bareng Neo Photo!</h2>
          <p class="mb-4" style="color:rgba(255,255,255,.75);font-size:15px;line-height:1.7">Bergabunglah bersama ratusan mitra sukses kami. Modal terjangkau, support penuh, profit fantastis!</p>
          <div class="d-flex flex-column gap-3 mb-4">
            <div class="benefit-item"><i class="fas fa-check-circle"></i> Penjualan stabil dengan keuntungan maksimal</div>
            <div class="benefit-item"><i class="fas fa-check-circle"></i> Program asli buatan Korea. Eksklusif, dan terpercaya</div>
            <div class="benefit-item"><i class="fas fa-check-circle"></i> Sistem dan frame dapat diupgrade setiap saat</div>
            <div class="benefit-item"><i class="fas fa-check-circle"></i> Foto dapat langsung dicetak dari mesin canggih</div>
            <div class="benefit-item"><i class="fas fa-check-circle"></i> Biaya operasi ringan dan minim resiko</div>
          </div>
          <a type="button" data-bs-toggle="modal" data-bs-target="#whatsappModal" class="btn-neo-primary"><i class="fas fa-rocket"></i> Gabung Sekarang</a>
        </div>
        <div class="col-lg-6  flex-column gap-3 d-none d-sm-flex">
            <img src="assets/HOMEPAGE/PARTNERSHIP/Franchisev4.png" class="" style="width:100%;height:100%;object-fit:cover">
          <!--<div class="invest-card">-->
          <!--  <h5 class="text-white fw-800 mb-1">Paket Starter</h5>-->
          <!--  <p style="color:rgba(255,255,255,.65);font-size:12px;margin-bottom:12px">Cocok untuk pemula, booth compact, lokasi strategis</p>-->
          <!--  <div class="invest-price">Rp 45 Juta</div>-->
          <!--</div>-->
          <!--<div class="invest-card featured">-->
          <!--  <div class="invest-tag">⭐ Paling Populer</div>-->
          <!--  <h5 class="text-white fw-800 mb-1">Paket Premium</h5>-->
          <!--  <p style="color:rgba(255,255,255,.65);font-size:12px;margin-bottom:12px">Booth lengkap, 2 unit, dukungan marketing penuh</p>-->
          <!--  <div class="invest-price">Rp 85 Juta</div>-->
          <!--</div>-->
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══ FOOTER ═══════════════════════════════════════ -->
<footer>
  <div class="container">
    <div class="row g-5 pb-5">
      <div class="col-lg-4 footer-brand">
        <div class="d-flex align-items-center gap-2 mb-3">
          <a class="navbar-brand" href="#">
      <img src="assets/img/logoNeo.png" alt="Neo Photo Indonesia" style="width:240px"/>
    </a>
        </div>
        <p class="mb-3">Neo Photo Indonesia adalah platform photobooth modern dengan ratusan pilihan frame unik. Hadir di kota-kota besar seluruh Indonesia.</p>
        <div class="d-flex gap-2">
          <a type="button" href=" https://www.instagram.com/neophotoindonesia/" class="social-btn pink"><i class="fab fa-instagram"></i></a>
          <a type="button" href=" https://www.tiktok.com/@neo.photo.id" class="social-btn mint"><i class="fab fa-tiktok"></i></a>
          <!--<button class="social-btn purple"><i class="fab fa-youtube"></i></button>-->
        </div>
      </div>

      <div class="col-6 col-md-3 col-lg-2 footer-col">
        <h6>Perusahaan</h6>
        <ul>
          <li><a href="#">Tentang Kami</a></li>
          <li><a href="#">Tim Kami</a></li>
          <li><a href="#">Karir</a></li>
          <li><a href="#">Blog</a></li>
        </ul>
      </div>

      <div class="col-6 col-md-3 col-lg-3 footer-col">
        <h6>Kontak</h6>
        <ul>
          <li><a href="#">Help &amp; Support</a></li>
          <li><a href="#">Partnership</a></li>
          <li><a href="#">Franchise</a></li>
          <li><a href="#"><i class="fab fa-whatsapp me-1"></i>+62 811 1234 5678</a></li>
        </ul>
      </div>

      <div class="col-6 col-md-3 col-lg-3 footer-col">
        <h6>Legal</h6>
        <ul>
          <li><a href="#">Syarat &amp; Ketentuan</a></li>
          <li><a href="#">Kebijakan Privasi</a></li>
          <li><a href="#">Refund &amp; Pembatalan</a></li>
          <li><a href="#">Cookie Policy</a></li>
        </ul>
      </div>
    </div>

    <hr class="footer-divider"/>
    <div class="d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
      <p style="font-size:12px;color:rgba(255,255,255,.4);margin:0">© 2024 Neo Photo Indonesia. All rights reserved.</p>
      <p style="font-size:12px;color:rgba(255,255,255,.4);margin:0">Made with 💖 by Neo Photo Team</p>
    </div>
  </div>
</footer>

<!-- ══ STICKY FRANCHISE BAR ═════════════════════════ -->
<div class="footer-bar">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
      <h5>Gabung Kemitraan</h5>
      <div>
        <button data-bs-toggle="modal" data-bs-target="#whatsappModal">Daftar Sekarang</button>
        <span class="policy">Dengan mendaftar, kamu menyetujui kebijakan privasi kami.</span>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
  /* Active nav on scroll */
  const sections  = document.querySelectorAll('section[id]');
  const navLinks  = document.querySelectorAll('.nav-link');

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        navLinks.forEach(a => {
          a.classList.toggle('active', a.getAttribute('href') === '#' + entry.target.id);
        });
      }
    });
  }, { threshold: 0.35 });

  sections.forEach(s => observer.observe(s));

  /* WhatsApp form validation */
  const waForm = document.getElementById('whatsappForm');

if (waForm) {
  waForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;

    // Validasi bawaan browser
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      return;
    }

    // Ambil nilai form
    const data = {
      fullName:       form.fullName.value.trim(),
      email:          form.email.value.trim(),
      domicile:       form.domicile.value.trim(),
      budgetRange:    form.budgetRange.options[form.budgetRange.selectedIndex].text,
      industry:       form.industry.options[form.industry.selectedIndex].text,
      hasLocation:    form.hasLocation.options[form.hasLocation.selectedIndex].text,
      investmentTime: form.investmentTime.options[form.investmentTime.selectedIndex].text,
      tipeBisnis:     form.tipeBisnis.options[form.tipeBisnis.selectedIndex].text,
    };

    // Susun pesan
    const message =
      `Halo, saya ingin mengetahui lebih lanjut mengenai peluang kemitraan.\n\n` +
      `*DATA CALON MITRA*\n` +
    //   `━━━━━━━━━━━━━━━━━━━━\n` +
      `• Nama         : ${data.fullName}\n` +
      `• Email        : ${data.email}\n` +
      `• Domisili     : ${data.domicile}\n\n` +
      `*INFORMASI BISNIS*\n` +
    //   `━━━━━━━━━━━━━━━━━━━━\n` +
      `• Budget       : ${data.budgetRange}\n` +
      `• Industri     : ${data.industry}\n` +
      `• Lokasi usaha : ${data.hasLocation}\n` +
      `• Rencana mulai: ${data.investmentTime}\n` +
      `• Tipe bisnis  : ${data.tipeBisnis}\n\n` +
      `Mohon informasi selanjutnya. Terima kasih!`;

    const phoneNumber = '6285810847780';
    const waURL = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;

    window.open(waURL, '_blank');
  });
 }
</script>
</body>
</html>