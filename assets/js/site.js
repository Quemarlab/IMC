/* Young Masters LTD — public site behaviour (no jQuery) */
(function () {
  'use strict';

  // Sticky nav shadow + back-to-top
  const nav = document.getElementById('siteNav');
  const toTop = document.getElementById('toTop');
  const onScroll = () => {
    const y = window.scrollY;
    nav && nav.classList.toggle('scrolled', y > 10);
    toTop && toTop.classList.toggle('show', y > 600);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
  toTop && toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  // Scroll animations
  if (window.AOS) {
    AOS.init({ duration: 700, easing: 'ease-out-cubic', once: true, offset: 60,
      disable: () => window.matchMedia('(prefers-reduced-motion: reduce)').matches });
  }

  // Lightbox
  if (window.GLightbox && document.querySelector('.glightbox')) {
    GLightbox({ selector: '.glightbox', touchNavigation: true, loop: true });
  }

  // AJAX forms (contact + newsletter): progressive enhancement over normal POST.
  document.querySelectorAll('form.js-ajax-form').forEach((form) => {
    const status = form.querySelector('.form-status');
    const button = form.querySelector('button[type="submit"]');

    const show = (ok, message) => {
      if (!status) return;
      status.className = 'form-status ' + (ok ? 'ok' : 'err');
      status.textContent = message;
    };

    form.addEventListener('submit', async (ev) => {
      ev.preventDefault();
      if (!form.checkValidity()) { form.reportValidity(); return; }

      const original = button ? button.innerHTML : '';
      if (button) { button.disabled = true; button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending…'; }
      try {
        const res = await fetch(form.action, {
          method: 'POST',
          body: new FormData(form),
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'fetch' },
        });
        const data = await res.json();
        show(!!data.ok, data.message || (data.ok ? 'Thank you!' : 'Something went wrong.'));
        if (data.ok) form.reset();
      } catch (e) {
        show(false, 'Network error. Please check your connection and try again.');
      } finally {
        if (button) { button.disabled = false; button.innerHTML = original; }
      }
    });
  });
})();
