document.addEventListener('DOMContentLoaded', () => {
  'use strict';
  const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const reduced = () => motion.matches;

  // Préparer les délais des cartes avant l'observation.
  document.querySelectorAll('[data-stagger="true"]').forEach(group => {
    [...group.children].forEach((child, index) => {
      child.classList.add('wbp-animate');
      if (!child.dataset.animation) child.dataset.animation = 'fade-up';
      if (!child.hasAttribute('data-delay')) child.dataset.delay = String(Math.min(index * 150, 2000));
    });
  });

  document.querySelectorAll('.wbp-slider').forEach(slider => {
    const slides = [...slider.querySelectorAll('.wbp-slide')];
    if (!slides.length) return;
    const dots = slider.querySelector('.wbp-dots');
    const prev = slider.querySelector('.wbp-prev');
    const next = slider.querySelector('.wbp-next');
    const interval = Math.max(1500, Math.min(15000, Number(slider.dataset.interval) || 4500));
    const autoplay = ['true', 'oui', '1'].includes(slider.dataset.autoplay);
    let current = Math.max(0, slides.findIndex(s => s.classList.contains('is-active')));
    let timer = null, visible = true;
    const buttons = [];
    if (dots) {
      dots.replaceChildren();
      slides.forEach((_, i) => {
        const b = document.createElement('button');
        b.type = 'button'; b.setAttribute('aria-label', `Afficher la diapositive ${i + 1}`);
        b.addEventListener('click', () => { show(i); restart(); });
        dots.append(b); buttons.push(b);
      });
    }
    function show(index) {
      current = (index + slides.length) % slides.length;
      slides.forEach((slide, i) => {
        const active = i === current;
        slide.classList.toggle('is-active', active);
        slide.setAttribute('aria-hidden', String(!active));
        if ('inert' in slide) slide.inert = !active;
        else slide.querySelectorAll('a,button,input,select,textarea').forEach(el => {
          if (!el.hasAttribute('data-wbp-tabindex')) el.dataset.wbpTabindex = el.getAttribute('tabindex') ?? '';
          if (!active) el.tabIndex = -1;
          else if (el.dataset.wbpTabindex === '') el.removeAttribute('tabindex');
          else el.setAttribute('tabindex', el.dataset.wbpTabindex);
        });
      });
      buttons.forEach((b, i) => {
        b.classList.toggle('active', i === current);
        if (i === current) b.setAttribute('aria-current', 'true');
        else b.removeAttribute('aria-current');
      });
    }
    function stop() { if (timer !== null) clearInterval(timer); timer = null; }
    function restart() {
      stop();
      if (autoplay && !reduced() && slides.length > 1 && !document.hidden && visible && !slider.matches(':hover') && !slider.matches(':focus-within')) {
        timer = setInterval(() => show(current + 1), interval);
      }
    }
    prev?.addEventListener('click', () => { show(current - 1); restart(); });
    next?.addEventListener('click', () => { show(current + 1); restart(); });
    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', restart);
    slider.addEventListener('focusin', stop);
    slider.addEventListener('focusout', restart);
    slider.addEventListener('keydown', e => {
      if (!['ArrowLeft','ArrowRight'].includes(e.key)) return;
      e.preventDefault(); show(current + (e.key === 'ArrowRight' ? 1 : -1)); restart();
    });
    document.addEventListener('visibilitychange', restart);
    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver(entries => { visible = entries[0]?.isIntersecting ?? false; restart(); }, { threshold: .05 });
      observer.observe(slider);
    }
    if (slides.length < 2) { if (prev) prev.hidden = true; if (next) next.hidden = true; if (dots) dots.hidden = true; }
    motion.addEventListener?.('change', restart);
    show(current); restart();
  });

  const animated = [...document.querySelectorAll('.wbp-animate[data-animation]')]
    .filter(el => el.dataset.animation && el.dataset.animation !== 'none');
  const reveal = el => { el.classList.remove('wbp-pending'); el.classList.add('wbp-visible'); };
  if ('IntersectionObserver' in window && !reduced()) {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => { if (entry.isIntersecting) { reveal(entry.target); observer.unobserve(entry.target); } });
    }, { threshold: .08, rootMargin: '0px 0px -25px 0px' });
    animated.forEach(el => {
      const duration = Number(el.dataset.duration), delay = Number(el.dataset.delay);
      if (Number.isFinite(duration) && duration >= 200) el.style.setProperty('--wbp-duration', `${Math.min(duration,2500)}ms`);
      if (Number.isFinite(delay) && delay >= 0) el.style.setProperty('--wbp-delay', `${Math.min(delay,2000)}ms`);
      const r = el.getBoundingClientRect();
      if (r.top < innerHeight * .85) reveal(el);
      else { el.classList.add('wbp-pending'); observer.observe(el); }
    });
    motion.addEventListener?.('change', () => { if (reduced()) { animated.forEach(reveal); observer.disconnect(); } });
  } else animated.forEach(reveal);

  function counter(el) {
    const target = Number(el.dataset.wbpCounter);
    if (!Number.isFinite(target)) return;
    const prefix = el.dataset.prefix || '', suffix = el.dataset.suffix || '';
    const format = new Intl.NumberFormat('fr-FR', { maximumFractionDigits: Number.isInteger(target) ? 0 : 1 });
    const render = v => { el.textContent = prefix + format.format(v) + suffix; };
    if (reduced()) return render(target);
    const duration = 1700, start = performance.now();
    function frame(now) {
      const p = Math.min((now - start) / duration, 1);
      render(target * (1 - Math.pow(1 - p, 3)));
      if (p < 1) requestAnimationFrame(frame); else render(target);
    }
    requestAnimationFrame(frame);
  }
  const counters = document.querySelectorAll('[data-wbp-counter]');
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => entries.forEach(e => {
      if (e.isIntersecting) { counter(e.target); observer.unobserve(e.target); }
    }), { threshold: .25 });
    counters.forEach(el => observer.observe(el));
  } else counters.forEach(counter);

  const parallax = [...document.querySelectorAll('[data-parallax="true"]')];
  let ticking = false;
  function updateParallax() {
    ticking = false;
    parallax.forEach(el => {
      const r = el.getBoundingClientRect();
      const offset = reduced() ? 0 : Math.max(-35, Math.min(35, -(r.top + r.height / 2 - innerHeight / 2) * .06));
      el.style.setProperty('--wbp-parallax', `${offset.toFixed(1)}px`);
    });
  }
  function schedule() { if (!ticking) { ticking = true; requestAnimationFrame(updateParallax); } }
  if (parallax.length) {
    window.addEventListener('scroll', schedule, { passive: true });
    window.addEventListener('resize', schedule);
    motion.addEventListener?.('change', schedule);
    schedule();
  }

  const top = document.createElement('button');
  top.type = 'button'; top.className = 'wbp-back-to-top'; top.textContent = '↑';
  top.setAttribute('aria-label', 'Retour en haut de la page'); document.body.append(top);
  const toggle = () => top.classList.toggle('is-visible', scrollY > 450);
  window.addEventListener('scroll', toggle, { passive: true }); toggle();
  top.addEventListener('click', () => window.scrollTo({ top: 0, behavior: reduced() ? 'instant' : 'smooth' }));
});
