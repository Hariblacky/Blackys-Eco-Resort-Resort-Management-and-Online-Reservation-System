/* ==========================================================================
   BLACKY'S ECO RESORT — MAIN SCRIPT
   ========================================================================== */
document.addEventListener('DOMContentLoaded', () => {

  /* ---------------------------------------------------------------------
     1. LOADING SCREEN
     --------------------------------------------------------------------- */
  const loader = document.querySelector('.loader');
  if (loader) {
    window.addEventListener('load', () => setTimeout(() => loader.classList.add('hidden'), 500));
    setTimeout(() => loader.classList.add('hidden'), 2500);
  }

  /* ---------------------------------------------------------------------
     2. DARK / LIGHT MODE
     --------------------------------------------------------------------- */
  const root = document.documentElement;
  const themeToggle = document.querySelector('.theme-toggle');
  const savedTheme = localStorage.getItem('ber_theme') ||
    (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  root.setAttribute('data-theme', savedTheme);

  themeToggle?.addEventListener('click', () => {
    const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    localStorage.setItem('ber_theme', next);
  });

  /* ---------------------------------------------------------------------
     3. NAVBAR — scroll transition, hamburger, active link
     --------------------------------------------------------------------- */
  const navbar = document.querySelector('.navbar');
  const hamburger = document.querySelector('.hamburger');
  const navLinks = document.querySelector('.nav-links');

  const onScrollNav = () => navbar && navbar.classList.toggle('scrolled', window.scrollY > 40);
  onScrollNav();
  window.addEventListener('scroll', onScrollNav, { passive: true });

  if (hamburger && navLinks) {
    hamburger.addEventListener('click', () => {
      hamburger.classList.toggle('active');
      navLinks.classList.toggle('open');
    });
    navLinks.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
      hamburger.classList.remove('active');
      navLinks.classList.remove('open');
    }));
  }

  const current = window.location.pathname.split('/').pop() || 'index.php';
  document.querySelectorAll('.nav-links a').forEach(a => {
    const href = a.getAttribute('href');
    if (href === current) a.classList.add('active');
  });

  /* ---------------------------------------------------------------------
     4. AOS + GSAP INIT
     --------------------------------------------------------------------- */
  if (window.AOS) {
    AOS.init({ duration: 800, easing: 'ease-out-cubic', once: true, offset: 60 });
  }

  if (window.gsap) {
    gsap.registerPlugin(window.ScrollTrigger);

    // Hero entrance timeline
    const heroTl = gsap.timeline({ defaults: { ease: 'power3.out' } });
    if (document.querySelector('.hero-eyebrow')) {
      heroTl
        .from('.hero-eyebrow', { y: 20, opacity: 0, duration: .7 })
        .from('.hero-title', { y: 40, opacity: 0, duration: .9 }, '-=.4')
        .from('.hero-desc', { y: 30, opacity: 0, duration: .8 }, '-=.5')
        .from('.hero-actions .btn', { y: 24, opacity: 0, duration: .7, stagger: .12 }, '-=.5')
        .from('.hero-scroll', { opacity: 0, duration: .6 }, '-=.3');
    }

    // Scroll-triggered section titles
    gsap.utils.toArray('.gsap-fade').forEach(el => {
      gsap.from(el, {
        y: 40, opacity: 0, duration: .9, ease: 'power3.out',
        scrollTrigger: { trigger: el, start: 'top 85%' }
      });
    });

    // Parallax on page hero background
    gsap.utils.toArray('.page-hero-bg, .ripple-section-bg').forEach(bg => {
      gsap.to(bg, {
        yPercent: 18, ease: 'none',
        scrollTrigger: { trigger: bg.closest('section'), start: 'top top', end: 'bottom top', scrub: true }
      });
    });
  }

  /* ---------------------------------------------------------------------
     5. SMOOTH SCROLL for in-page anchors
     --------------------------------------------------------------------- */
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const id = a.getAttribute('href');
      if (id.length > 1) {
        const target = document.querySelector(id);
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
      }
    });
  });

  /* ---------------------------------------------------------------------
     6. BUTTON RIPPLE
     --------------------------------------------------------------------- */
  document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
      const rect = this.getBoundingClientRect();
      const ripple = document.createElement('span');
      const size = Math.max(rect.width, rect.height);
      ripple.className = 'ripple-fx';
      ripple.style.width = ripple.style.height = size + 'px';
      ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
      ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
      this.appendChild(ripple);
      setTimeout(() => ripple.remove(), 650);
    });
  });

  /* ---------------------------------------------------------------------
     7. SWIPER SLIDERS
     --------------------------------------------------------------------- */
  if (window.Swiper) {
    if (document.querySelector('.rooms-swiper')) {
      new Swiper('.rooms-swiper', {
        slidesPerView: 1.15, spaceBetween: 24, loop: true,
        autoplay: { delay: 4500, disableOnInteraction: false },
        pagination: { el: '.rooms-swiper .swiper-pagination', clickable: true },
        navigation: { nextEl: '.rooms-swiper .swiper-button-next', prevEl: '.rooms-swiper .swiper-button-prev' },
        breakpoints: { 768: { slidesPerView: 2.2 }, 1100: { slidesPerView: 3.2 } }
      });
    }
    if (document.querySelector('.testi-swiper')) {
      new Swiper('.testi-swiper', {
        slidesPerView: 1, loop: true, autoplay: { delay: 5500, disableOnInteraction: false },
        pagination: { el: '.testi-swiper .swiper-pagination', clickable: true },
      });
    }
    if (document.querySelector('.gallery-swiper')) {
      new Swiper('.gallery-swiper', {
        slidesPerView: 1.2, spaceBetween: 18, loop: true, centeredSlides: true,
        autoplay: { delay: 3800, disableOnInteraction: false },
        pagination: { el: '.gallery-swiper .swiper-pagination', clickable: true },
        breakpoints: { 768: { slidesPerView: 2.4 }, 1100: { slidesPerView: 3.4 } }
      });
    }
    if (document.querySelector('.menu-swiper')) {
      new Swiper('.menu-swiper', {
        slidesPerView: 1.1, spaceBetween: 22, loop: true,
        autoplay: { delay: 4200, disableOnInteraction: false },
        pagination: { el: '.menu-swiper .swiper-pagination', clickable: true },
        breakpoints: { 768: { slidesPerView: 2.1 }, 1100: { slidesPerView: 3.1 } }
      });
    }
  }

  /* ---------------------------------------------------------------------
     8. FILTERING (rooms / menu / activities / gallery)
     --------------------------------------------------------------------- */
  document.querySelectorAll('.filter-bar').forEach(bar => {
    const targetSelector = bar.getAttribute('data-target') || '.item-card';
    const items = document.querySelectorAll(targetSelector);
    bar.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        bar.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const filter = btn.getAttribute('data-filter');
        items.forEach(item => {
          const match = filter === 'all' || item.getAttribute('data-category') === filter;
          item.classList.toggle('hide', !match);
          if (item.classList.contains('masonry-item')) item.style.display = match ? '' : 'none';
        });
      });
    });
  });

  /* ---------------------------------------------------------------------
     9. FAQ ACCORDION
     --------------------------------------------------------------------- */
  document.querySelectorAll('.faq-item').forEach(item => {
    const q = item.querySelector('.faq-q');
    const a = item.querySelector('.faq-a');
    q?.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');
      document.querySelectorAll('.faq-item.open').forEach(open => {
        open.classList.remove('open');
        open.querySelector('.faq-a').style.maxHeight = null;
      });
      if (!isOpen) { item.classList.add('open'); a.style.maxHeight = a.scrollHeight + 'px'; }
    });
  });

  /* ---------------------------------------------------------------------
     10. GALLERY LIGHTBOX
     --------------------------------------------------------------------- */
  const lightbox = document.querySelector('.lightbox');
  const masonryItems = document.querySelectorAll('.masonry-item');
  if (lightbox && masonryItems.length) {
    const lbImg = lightbox.querySelector('img');
    const lbCaption = lightbox.querySelector('.lightbox-caption');
    const items = Array.from(masonryItems);
    let current = 0;
    function openLightbox(i) {
      current = i;
      const img = items[i].querySelector('img');
      lbImg.src = img.getAttribute('src'); lbImg.alt = img.getAttribute('alt') || '';
      lbCaption.textContent = img.getAttribute('alt') || '';
      lightbox.classList.add('active'); document.body.style.overflow = 'hidden';
    }
    function closeLightbox() { lightbox.classList.remove('active'); document.body.style.overflow = ''; }
    items.forEach((item, i) => item.addEventListener('click', () => openLightbox(i)));
    lightbox.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', e => { if (e.target === lightbox) closeLightbox(); });
    lightbox.querySelector('.lightbox-prev').addEventListener('click', () => openLightbox((current - 1 + items.length) % items.length));
    lightbox.querySelector('.lightbox-next').addEventListener('click', () => openLightbox((current + 1) % items.length));
    document.addEventListener('keydown', e => {
      if (!lightbox.classList.contains('active')) return;
      if (e.key === 'Escape') closeLightbox();
      if (e.key === 'ArrowLeft') openLightbox((current - 1 + items.length) % items.length);
      if (e.key === 'ArrowRight') openLightbox((current + 1) % items.length);
    });
  }

  /* ---------------------------------------------------------------------
     11. BOOKING FORM — live summary + client-side validation
     --------------------------------------------------------------------- */
  const bookingForm = document.querySelector('#bookingForm');
  if (bookingForm) {
    const roomSelect = bookingForm.querySelector('[name="room_id"]');
    const checkIn = bookingForm.querySelector('[name="check_in"]');
    const checkOut = bookingForm.querySelector('[name="check_out"]');
    const guests = bookingForm.querySelector('[name="guests"]');

    const sumRoom = document.querySelector('#sumRoom');
    const sumNights = document.querySelector('#sumNights');
    const sumRate = document.querySelector('#sumRate');
    const sumTotal = document.querySelector('#sumTotal');

    function updateSummary() {
      if (!roomSelect) return;
      const opt = roomSelect.options[roomSelect.selectedIndex];
      const rate = parseFloat(opt?.dataset.price || 0);
      const currency = document.body.dataset.currency || '$';
      let nights = 1;
      if (checkIn.value && checkOut.value) {
        const inD = new Date(checkIn.value), outD = new Date(checkOut.value);
        const diff = Math.round((outD - inD) / 86400000);
        nights = diff > 0 ? diff : 1;
      }
      if (sumRoom) sumRoom.textContent = opt?.text || '—';
      if (sumNights) sumNights.textContent = nights;
      if (sumRate) sumRate.textContent = currency + rate.toFixed(2);
      if (sumTotal) sumTotal.textContent = currency + (rate * nights).toFixed(2);
    }
    [roomSelect, checkIn, checkOut, guests].forEach(el => el && el.addEventListener('change', updateSummary));
    updateSummary();

    const today = new Date().toISOString().split('T')[0];
    checkIn?.setAttribute('min', today);
    checkIn?.addEventListener('change', () => checkOut?.setAttribute('min', checkIn.value));

    const successPopup = document.querySelector('.success-popup');
    bookingForm.addEventListener('submit', (e) => {
      let valid = true;
      bookingForm.querySelectorAll('[required]').forEach(field => {
        const wrap = field.closest('.field');
        if (!field.value.trim()) { wrap?.classList.add('has-error'); valid = false; }
        else wrap?.classList.remove('has-error');
      });
      if (checkIn.value && checkOut.value && new Date(checkOut.value) <= new Date(checkIn.value)) {
        checkOut.closest('.field')?.classList.add('has-error');
        const err = checkOut.closest('.field')?.querySelector('.field-error');
        if (err) err.textContent = 'Check-out must be after check-in.';
        valid = false;
      }
      if (!valid) e.preventDefault();
    });
  }

  /* ---------------------------------------------------------------------
     12. CONTACT FORM — basic client-side validation
     --------------------------------------------------------------------- */
  const contactForm = document.querySelector('#contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      let valid = true;
      contactForm.querySelectorAll('[required]').forEach(field => {
        const wrap = field.closest('.field');
        if (!field.value.trim()) { wrap?.classList.add('has-error'); valid = false; }
        else wrap?.classList.remove('has-error');
      });
      if (!valid) e.preventDefault();
    });
  }

  /* ---------------------------------------------------------------------
     13. NEWSLETTER FORMS
     --------------------------------------------------------------------- */
  document.querySelectorAll('.newsletter-form, .newsletter-popup form').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      const input = form.querySelector('input');
      if (input && input.value.trim()) {
        input.value = ''; input.placeholder = 'Subscribed! Thank you.';
        setTimeout(() => { input.placeholder = 'Your email address'; }, 3000);
      }
    });
  });

  const npPopup = document.querySelector('.newsletter-popup');
  if (npPopup) {
    if (!sessionStorage.getItem('ber_newsletter_shown')) {
      setTimeout(() => npPopup.classList.add('show'), 6000);
      sessionStorage.setItem('ber_newsletter_shown', '1');
    }
    npPopup.querySelector('.np-close')?.addEventListener('click', () => npPopup.classList.remove('show'));
  }

  /* ---------------------------------------------------------------------
     14. SCROLL TO TOP
     --------------------------------------------------------------------- */
  const scrollTopBtn = document.querySelector('.scroll-top');
  if (scrollTopBtn) {
    window.addEventListener('scroll', () => scrollTopBtn.classList.toggle('show', window.scrollY > 500), { passive: true });
    scrollTopBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }

  /* ---------------------------------------------------------------------
     15. COUNTERS (used on About page)
     --------------------------------------------------------------------- */
  const counters = document.querySelectorAll('[data-counter]');
  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      const target = parseInt(el.getAttribute('data-counter'), 10);
      const suffix = el.getAttribute('data-suffix') || '';
      let current = 0;
      const duration = 1800;
      const step = Math.max(1, Math.ceil(target / (duration / 16)));
      const tick = () => {
        current += step;
        el.textContent = (current >= target ? target : current) + suffix;
        if (current < target) requestAnimationFrame(tick);
      };
      tick();
      counterObserver.unobserve(el);
    });
  }, { threshold: 0.5 });
  counters.forEach(c => counterObserver.observe(c));

});
