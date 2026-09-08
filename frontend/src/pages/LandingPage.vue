<template>
<div class="landing-page">

<!-- NAVIGATION -->
<nav class="navbar navbar-expand-lg navbar-dark main-navbar">
<div class="container">
<RouterLink to="/" class="navbar-brand d-flex align-items-center">
<div class="logo-wrapper">
<img src="@/assets/images/logo/sfxc-logo-only.png" alt="SFXC Logo">
</div>
<div class="brand-text">
<strong>St. Francis Xavier College</strong>
<span>Online Enrollment System</span>
</div>
</RouterLink>

<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbar">
<ul class="navbar-nav ms-auto align-items-lg-center">
<li class="nav-item">
<a class="nav-link active" href="#home">Home</a>
</li>
<li class="nav-item">
<a class="nav-link" href="#announcements">Announcements</a>
</li>
<li class="nav-item">
<a class="nav-link" href="#calendar">Calendar</a>
</li>
<li class="nav-item ms-lg-3">
<RouterLink to="/login" class="login-btn">
<i class="bi bi-box-arrow-in-right"></i> Login
</RouterLink>
</li>
</ul>
</div>
</div>
</nav>

<!-- HERO -->
<section id="home" class="hero-section">
<div class="hero-pattern"></div>

<div class="container hero-container">
<div class="row align-items-center">

<div class="col-lg-7">
<div class="hero-badge">
<i class="bi bi-stars"></i> Welcome to SFXC
</div>

<h1 class="hero-title">
Begin Your
<span>Academic Journey</span>
With Us
</h1>

<p class="hero-description">
Experience a faster, easier, and more convenient enrollment process through the St. Francis Xavier College Online Enrollment System.
</p>

<div class="hero-buttons">
<RouterLink to="/register" class="primary-btn">
<i class="bi bi-pencil-square"></i> Enroll Now
</RouterLink>

<RouterLink to="/login" class="secondary-btn">
<i class="bi bi-person-circle"></i> Student Login
</RouterLink>
</div>

<div class="hero-note">
<div class="note-icon">
<i class="bi bi-shield-check"></i>
</div>
<div>
<strong>Secure Online Enrollment</strong>
<small>Your information is protected and handled securely.</small>
</div>
</div>
</div>

<div class="col-lg-5">
<div class="hero-card">

<div class="hero-card-top">
<div class="hero-icon">
<i class="bi bi-mortarboard-fill"></i>
</div>
<div>
<small>ONLINE ENROLLMENT</small>
<h4>2026–2027</h4>
</div>
</div>

<div class="hero-card-divider"></div>

<div class="hero-feature">
<i class="bi bi-check-circle-fill"></i>
<span>Submit your enrollment application online</span>
</div>

<div class="hero-feature">
<i class="bi bi-check-circle-fill"></i>
<span>Upload your enrollment requirements</span>
</div>

<div class="hero-feature">
<i class="bi bi-check-circle-fill"></i>
<span>Track your application status</span>
</div>

<div class="hero-feature">
<i class="bi bi-check-circle-fill"></i>
<span>Complete enrollment payment</span>
</div>

<RouterLink to="/register" class="hero-card-btn">
Start Your Enrollment <i class="bi bi-arrow-right"></i>
</RouterLink>

</div>
</div>

</div>
</div>

<div class="hero-wave"></div>
</section>

<!-- STATISTICS -->
<section class="stats-section">
<div class="container">
<div class="stats-card">
<div class="row g-0">

<div class="col-6 col-lg-3">
<div class="stat-item">
<div class="stat-icon"><i class="bi bi-people-fill"></i></div>
<h2>5000+</h2>
<p>Students</p>
</div>
</div>

<div class="col-6 col-lg-3">
<div class="stat-item">
<div class="stat-icon"><i class="bi bi-book-half"></i></div>
<h2>20+</h2>
<p>Programs</p>
</div>
</div>

<div class="col-6 col-lg-3">
<div class="stat-item">
<div class="stat-icon"><i class="bi bi-person-workspace"></i></div>
<h2>50+</h2>
<p>Faculty</p>
</div>
</div>

<div class="col-6 col-lg-3">
<div class="stat-item">
<div class="stat-icon"><i class="bi bi-building"></i></div>
<h2>1950</h2>
<p>Established</p>
</div>
</div>

</div>
</div>
</div>
</section>

<!-- WELCOME -->
<section class="welcome-section">
<div class="container">

<div class="section-heading text-center">
<span>ST. FRANCIS XAVIER COLLEGE</span>
<h2>Your Future Starts Here</h2>
<p>Everything you need to begin your enrollment journey, all in one convenient online platform.</p>
</div>

<div class="row g-4 mt-3">

<div class="col-md-4">
<div class="feature-card">
<div class="feature-icon"><i class="bi bi-lightning-charge-fill"></i></div>
<h4>Fast & Convenient</h4>
<p>Complete your enrollment application without unnecessary visits and long queues.</p>
</div>
</div>

<div class="col-md-4">
<div class="feature-card">
<div class="feature-icon"><i class="bi bi-shield-lock-fill"></i></div>
<h4>Secure</h4>
<p>Your personal information and enrollment documents are handled securely.</p>
</div>
</div>

<div class="col-md-4">
<div class="feature-card">
<div class="feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
<h4>Track Your Progress</h4>
<p>Monitor your enrollment status from application submission until official enrollment.</p>
</div>
</div>

</div>
</div>
</section>

<!-- PROCESS -->
<section class="process-section">
<div class="container">

<div class="section-heading text-center light-heading">
<span>SIMPLE PROCESS</span>
<h2>How Enrollment Works</h2>
<p>Complete your enrollment in just a few simple steps.</p>
</div>

<div class="row process-row">

<div class="col-md-3">
<div class="process-card">
<div class="process-number">01</div>
<i class="bi bi-person-plus-fill"></i>
<h5>Create Account</h5>
<p>Register your student account.</p>
</div>
</div>

<div class="col-md-3">
<div class="process-card">
<div class="process-number">02</div>
<i class="bi bi-file-earmark-text-fill"></i>
<h5>Submit Application</h5>
<p>Complete your enrollment information.</p>
</div>
</div>

<div class="col-md-3">
<div class="process-card">
<div class="process-number">03</div>
<i class="bi bi-check-circle-fill"></i>
<h5>Get Approved</h5>
<p>Wait for registrar verification.</p>
</div>
</div>

<div class="col-md-3">
<div class="process-card">
<div class="process-number">04</div>
<i class="bi bi-mortarboard-fill"></i>
<h5>Get Enrolled</h5>
<p>Complete payment and enrollment.</p>
</div>
</div>

</div>
</div>
</section>

<!-- ANNOUNCEMENTS -->
<section id="announcements" class="announcements-section">
<div class="container">

<div class="section-heading">
<span>STAY UPDATED</span>
<h2>Latest Announcements</h2>
<p>Keep up with important enrollment and academic updates.</p>
</div>

<div class="row g-4">

<div class="col-md-4" v-for="i in 3" :key="i">
<div class="announcement-card">

<div class="announcement-image">
<div class="announcement-icon">
<i :class="[
'bi',
i===1?'bi-megaphone-fill':i===2?'bi-calendar-event-fill':'bi-info-circle-fill'
]"></i>
</div>
</div>

<div class="announcement-body">

<div class="announcement-date">
<i class="bi bi-calendar3"></i> August 2026
</div>

<h5>
{{ i===1?'Enrollment is Now Open':i===2?'Academic Calendar':'Important Enrollment Reminder' }}
</h5>

<p>
Stay informed about the latest announcements and important activities from St. Francis Xavier College.
</p>

<a href="#">
Read More <i class="bi bi-arrow-right"></i>
</a>

</div>
</div>
</div>

</div>
</div>
</section>

<!-- CALENDAR -->
<section id="calendar" class="calendar-section">
<div class="container">

<div class="row align-items-center g-5">

<div class="col-lg-5">
<div class="section-heading">
<span>IMPORTANT DATES</span>
<h2>Academic Calendar</h2>
<p>Plan ahead and keep track of important academic activities.</p>
</div>

<RouterLink to="/register" class="primary-btn">
Start Enrollment <i class="bi bi-arrow-right"></i>
</RouterLink>
</div>

<div class="col-lg-7">
<div class="calendar-card">

<div class="calendar-header">
<div>
<small>ACADEMIC YEAR</small>
<h4>2026–2027</h4>
</div>
<i class="bi bi-calendar3"></i>
</div>

<div class="calendar-item">
<div class="calendar-date">
<strong>01</strong>
<span>AUG</span>
</div>
<div>
<h6>Enrollment Period</h6>
<p>Online enrollment application</p>
</div>
</div>

<div class="calendar-item">
<div class="calendar-date">
<strong>15</strong>
<span>AUG</span>
</div>
<div>
<h6>Student Orientation</h6>
<p>New student orientation program</p>
</div>
</div>

<div class="calendar-item">
<div class="calendar-date">
<strong>01</strong>
<span>SEP</span>
</div>
<div>
<h6>Start of Classes</h6>
<p>First day of regular classes</p>
</div>
</div>

</div>
</div>

</div>
</div>
</section>

<!-- CTA -->
<section class="cta-section">
<div class="container">
<div class="cta-card">

<div>
<span>READY TO BEGIN?</span>
<h2>Start Your Journey With SFXC</h2>
<p>Create your account and begin your online enrollment application today.</p>
</div>

<RouterLink to="/register" class="cta-btn">
Enroll Now <i class="bi bi-arrow-right"></i>
</RouterLink>

</div>
</div>
</section>

<!-- FOOTER -->
<footer class="footer">
<div class="container">

<div class="row g-5">

<div class="col-lg-5">
<div class="footer-brand">

<img src="@/assets/images/logo/sfxc-logo-only.png" alt="SFXC Logo">

<div>
<h5>St. Francis Xavier College</h5>
<span>Online Enrollment System</span>
</div>

</div>

<p class="footer-description">
Providing students with a convenient, secure, and accessible online enrollment experience.
</p>
</div>

<div class="col-6 col-lg-3">
<h6>Quick Links</h6>
<a href="#home">Home</a>
<a href="#announcements">Announcements</a>
<a href="#calendar">Academic Calendar</a>
<RouterLink to="/login">Student Login</RouterLink>
</div>

<div class="col-6 col-lg-4">
<h6>Contact Us</h6>
<p><i class="bi bi-geo-alt-fill"></i> St. Francis Xavier College</p>
<p><i class="bi bi-telephone-fill"></i> Contact Information</p>
<p><i class="bi bi-envelope-fill"></i> Email Address</p>
</div>

</div>

<div class="footer-bottom">
<span>© 2026 St. Francis Xavier College. All Rights Reserved.</span>
<span>Online Enrollment System</span>
</div>

</div>
</footer>

</div>
</template>

<script setup>
</script>

<style scoped>
.landing-page{background:#f5f8f6;color:#18352a;min-height:100vh;overflow:hidden}
.navbar-brand,.logo-wrapper,.brand-text{background:transparent!important;box-shadow:none!important;border:none!important}
.main-navbar{background:#064e2a!important;min-height:78px;padding:10px 0;position:sticky;top:0;z-index:1000;box-shadow:0 5px 25px rgba(0,0,0,.15)}
.logo-wrapper{width:55px;height:55px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-right:12px;padding:0!important;border:3px solid #c6a15b!important}
.logo-wrapper img{width:45px;height:45px;object-fit:contain}
.brand-text{display:flex;flex-direction:column}
.brand-text strong{font-size:17px}
.brand-text span{font-size:11px;opacity:.75}
.nav-link{color:rgba(255,255,255,.85)!important;font-weight:500;margin:0 6px;transition:.3s}
.nav-link:hover,.nav-link.active{color:#fff!important}
.login-btn{display:inline-flex;align-items:center;gap:8px;background:white;color:#064e2a;padding:10px 20px;border-radius:12px;text-decoration:none;font-weight:700;transition:.3s}
.login-btn:hover{background:#c6a15b;color:white;transform:translateY(-2px)}

.hero-section{position:relative;min-height:620px;background:linear-gradient(135deg,#064e2a 0%,#08763d 55%,#0a8b49 100%);color:white;overflow:hidden}
.hero-pattern{position:absolute;inset:0;opacity:.07;background-image:radial-gradient(circle at 20% 20%,white 2px,transparent 2px);background-size:35px 35px}
.hero-container{position:relative;z-index:2;padding-top:90px;padding-bottom:110px}
.hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);padding:9px 16px;border-radius:50px;font-size:13px;margin-bottom:22px}
.hero-title{font-size:clamp(42px,6vw,68px);line-height:1.05;font-weight:900;letter-spacing:-2px;max-width:700px}
.hero-title span{color:#e4c777;display:block}
.hero-description{max-width:650px;font-size:18px;line-height:1.7;color:rgba(255,255,255,.82);margin:25px 0}
.hero-buttons{display:flex;gap:14px;flex-wrap:wrap}
.primary-btn,.secondary-btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;padding:14px 24px;border-radius:13px;font-weight:700;text-decoration:none;transition:.3s}
.primary-btn{background:#c6a15b;color:white;box-shadow:0 10px 25px rgba(0,0,0,.15)}
.primary-btn:hover{background:#d8b86e;color:white;transform:translateY(-3px)}
.secondary-btn{color:white;border:1px solid rgba(255,255,255,.4)}
.secondary-btn:hover{background:white;color:#064e2a}
.hero-note{display:flex;align-items:center;gap:12px;margin-top:30px;font-size:13px}
.note-icon{width:42px;height:42px;border-radius:50%;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;font-size:20px}
.hero-note div:last-child{display:flex;flex-direction:column}
.hero-note small{color:rgba(255,255,255,.65)}

.hero-card{background:white;color:#18352a;border-radius:25px;padding:30px;box-shadow:0 30px 70px rgba(0,0,0,.25);position:relative;animation:floatCard 5s ease-in-out infinite}
@keyframes floatCard{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
.hero-card-top{display:flex;align-items:center;gap:15px}
.hero-icon{width:60px;height:60px;border-radius:18px;background:#e9f5ee;color:#08763d;display:flex;align-items:center;justify-content:center;font-size:28px}
.hero-card-top small{color:#777;font-size:10px;font-weight:700;letter-spacing:1px}
.hero-card-top h4{margin:2px 0;font-weight:800}
.hero-card-divider{height:1px;background:#e5e7eb;margin:25px 0}
.hero-feature{display:flex;gap:12px;align-items:flex-start;margin:16px 0;color:#52635a;font-size:14px}
.hero-feature i{color:#08763d;font-size:18px}
.hero-card-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;margin-top:25px;padding:14px;background:#064e2a;color:white;border-radius:13px;text-decoration:none;font-weight:700;transition:.3s}
.hero-card-btn:hover{background:#08763d;color:white}
.hero-wave{position:absolute;bottom:-1px;left:-5%;width:110%;height:80px;background:#f5f8f6;border-radius:50% 50% 0 0/100% 100% 0 0}

.stats-section{margin-top:-35px;position:relative;z-index:10}
.stats-card{background:white;border-radius:22px;box-shadow:0 15px 45px rgba(0,0,0,.08);overflow:hidden}
.stat-item{text-align:center;padding:30px 15px;border-right:1px solid #edf0ee}
.stat-icon{width:48px;height:48px;margin:auto auto 10px;border-radius:14px;background:#e9f5ee;color:#08763d;display:flex;align-items:center;justify-content:center;font-size:21px}
.stat-item h2{margin:0;font-size:32px;font-weight:900;color:#064e2a}
.stat-item p{margin:3px 0 0;color:#777}

.section-heading span{color:#08763d;font-size:12px;font-weight:800;letter-spacing:2px}
.section-heading h2{font-size:38px;font-weight:900;color:#064e2a;margin:8px 0 12px}
.section-heading p{color:#6b7280;max-width:650px;line-height:1.7}
.section-heading.text-center p{margin-left:auto;margin-right:auto}

.welcome-section{padding:100px 0}
.feature-card{background:white;padding:35px;border-radius:20px;height:100%;border:1px solid #e9eeeb;transition:.3s}
.feature-card:hover{transform:translateY(-8px);box-shadow:0 18px 40px rgba(0,0,0,.08)}
.feature-icon{width:58px;height:58px;border-radius:17px;background:#e9f5ee;color:#08763d;display:flex;align-items:center;justify-content:center;font-size:25px;margin-bottom:20px}
.feature-card h4{color:#064e2a;font-weight:800}
.feature-card p{color:#6b7280;line-height:1.7;margin:0}

.process-section{background:#064e2a;padding:100px 0;color:white}
.light-heading span{color:#c6a15b}
.light-heading h2{color:white}
.light-heading p{color:rgba(255,255,255,.65)}
.process-row{margin-top:50px}
.process-card{text-align:center;padding:25px 15px;position:relative}
.process-number{font-size:12px;color:#c6a15b;font-weight:800;margin-bottom:15px}
.process-card>i{font-size:40px;color:white;margin-bottom:18px}
.process-card h5{font-weight:800}
.process-card p{color:rgba(255,255,255,.6);font-size:14px}

.announcements-section{padding:100px 0}
.announcement-card{background:white;border-radius:20px;overflow:hidden;border:1px solid #e8ece9;height:100%;transition:.3s}
.announcement-card:hover{transform:translateY(-6px);box-shadow:0 18px 40px rgba(0,0,0,.08)}
.announcement-image{height:160px;background:linear-gradient(135deg,#064e2a,#0b8144);display:flex;align-items:center;justify-content:center}
.announcement-icon{width:70px;height:70px;border-radius:22px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;color:white;font-size:30px}
.announcement-body{padding:25px}
.announcement-date{font-size:12px;color:#08763d;margin-bottom:10px}
.announcement-body h5{font-weight:800;color:#064e2a}
.announcement-body p{color:#6b7280;font-size:14px;line-height:1.7}
.announcement-body a{color:#08763d;text-decoration:none;font-weight:700;font-size:14px}

.calendar-section{background:white;padding:100px 0}
.calendar-card{background:#f5f8f6;border-radius:22px;padding:30px}
.calendar-header{display:flex;justify-content:space-between;align-items:center;padding-bottom:20px;border-bottom:1px solid #dde5df}
.calendar-header small{color:#08763d;font-weight:800;letter-spacing:1px}
.calendar-header h4{color:#064e2a;font-weight:900;margin:3px 0}
.calendar-header>i{font-size:38px;color:#c6a15b}
.calendar-item{display:flex;gap:18px;align-items:center;padding:20px 0;border-bottom:1px solid #dde5df}
.calendar-item:last-child{border-bottom:none}
.calendar-date{min-width:65px;height:65px;background:#064e2a;color:white;border-radius:15px;display:flex;flex-direction:column;align-items:center;justify-content:center}
.calendar-date strong{font-size:22px;line-height:1}
.calendar-date span{font-size:11px;font-weight:700}
.calendar-item h6{margin:0 0 4px;color:#064e2a;font-weight:800}
.calendar-item p{margin:0;color:#777;font-size:13px}

.cta-section{padding:70px 0;background:#f5f8f6}
.cta-card{background:linear-gradient(135deg,#064e2a,#08763d);border-radius:25px;padding:45px;color:white;display:flex;align-items:center;justify-content:space-between;gap:30px;box-shadow:0 20px 50px rgba(6,78,42,.2)}
.cta-card span{color:#c6a15b;font-size:12px;font-weight:800;letter-spacing:2px}
.cta-card h2{font-size:32px;font-weight:900;margin:7px 0}
.cta-card p{margin:0;color:rgba(255,255,255,.7)}
.cta-btn{white-space:nowrap;display:inline-flex;align-items:center;gap:10px;background:#c6a15b;color:white;padding:14px 25px;border-radius:13px;text-decoration:none;font-weight:800;transition:.3s}
.cta-btn:hover{background:#d8b86e;color:white;transform:translateY(-3px)}

.footer{background:#032d18;color:white;padding:70px 0 25px}
.footer-brand{display:flex;align-items:center;gap:13px}
.footer-brand img{width:55px;height:55px;background:white;border-radius:50%;padding:5px}
.footer-brand h5{margin:0;font-weight:800}
.footer-brand span{color:rgba(255,255,255,.55);font-size:12px}
.footer-description{max-width:450px;color:rgba(255,255,255,.55);line-height:1.8;margin-top:20px}
.footer h6{font-weight:800;margin-bottom:20px}
.footer a{display:block;color:rgba(255,255,255,.55);text-decoration:none;margin-bottom:12px;transition:.3s}
.footer a:hover{color:white}
.footer p{color:rgba(255,255,255,.55);font-size:14px}
.footer p i{color:#c6a15b;margin-right:8px}
.footer-bottom{margin-top:50px;padding-top:20px;border-top:1px solid rgba(255,255,255,.1);display:flex;justify-content:space-between;gap:20px;color:rgba(255,255,255,.4);font-size:12px}

@media(max-width:991px){
.hero-container{padding-top:60px}
.hero-card{margin-top:50px}
.stats-section{margin-top:0;padding-top:30px}
.stat-item{border-bottom:1px solid #edf0ee}
}

@media(max-width:767px){
.brand-text strong{font-size:14px}
.brand-text span{font-size:9px}
.logo-wrapper{width:48px;height:48px}
.logo-wrapper img{width:38px;height:38px}
.hero-section{min-height:auto}
.hero-title{font-size:43px}
.hero-description{font-size:16px}
.hero-container{padding-top:50px;padding-bottom:70px}
.hero-wave{display:none}
.welcome-section,.announcements-section,.calendar-section,.process-section{padding:70px 0}
.section-heading h2{font-size:30px}
.cta-card{flex-direction:column;align-items:flex-start;padding:30px}
.cta-card h2{font-size:27px}
.footer-bottom{flex-direction:column}
}

@media(max-width:400px){
.hero-title{font-size:36px}
.hero-buttons{flex-direction:column}
.primary-btn,.secondary-btn{width:100%}
.hero-card{padding:22px}
}
</style>
