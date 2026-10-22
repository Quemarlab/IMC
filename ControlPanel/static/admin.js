/* Young Masters admin behaviour */
(function () {
  'use strict';

  // Sidebar (mobile)
  const sidebar = document.getElementById('sidebar');
  const backdrop = document.getElementById('backdrop');
  const toggle = document.getElementById('sidebarToggle');
  const setOpen = (open) => {
    sidebar && sidebar.classList.toggle('open', open);
    backdrop && backdrop.classList.toggle('show', open);
  };
  toggle && toggle.addEventListener('click', () => setOpen(!sidebar.classList.contains('open')));
  backdrop && backdrop.addEventListener('click', () => setOpen(false));

  // Dark mode
  const themeBtn = document.getElementById('themeToggle');
  const icon = themeBtn && themeBtn.querySelector('i');
  const paintIcon = () => {
    if (icon) icon.className = 'bi ' + (document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'bi-sun' : 'bi-moon-stars');
  };
  paintIcon();
  themeBtn && themeBtn.addEventListener('click', () => {
    const next = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-bs-theme', next);
    try { localStorage.setItem('ym-theme', next); } catch (e) { /* private mode */ }
    paintIcon();
  });

  // Confirmation modal for destructive forms: <form data-confirm="Delete this?">
  const modalEl = document.getElementById('confirmModal');
  if (modalEl && window.bootstrap) {
    const modal = new bootstrap.Modal(modalEl);
    let pending = null;
    document.addEventListener('submit', (ev) => {
      const form = ev.target;
      if (!(form instanceof HTMLFormElement) || !form.dataset.confirm || form.dataset.confirmed) return;
      ev.preventDefault();
      pending = form;
      document.getElementById('confirmTitle').textContent = form.dataset.confirm;
      document.getElementById('confirmText').textContent = form.dataset.confirmText || 'This cannot be undone.';
      modal.show();
    });
    document.getElementById('confirmOk').addEventListener('click', () => {
      if (!pending) return;
      pending.dataset.confirmed = '1';
      if (pending.requestSubmit) pending.requestSubmit(); else pending.submit();
    });
  }


  // Toasts: window.ymToast(text, 'success'|'danger'|'info', {href, id})
  const toastWrap = document.getElementById('toastWrap');
  window.ymToast = (text, type, opts) => {
    if (!toastWrap || !window.bootstrap) return;
    opts = opts || {};
    const el = document.createElement('div');
    el.className = 'toast align-items-center border-0 text-bg-' + (type || 'info');
    el.setAttribute('role', 'alert');
    const body = document.createElement('div');
    body.className = 'toast-body';
    if (opts.href) {
      const a = document.createElement('a');
      a.href = opts.href; a.className = 'msg-open text-reset fw-semibold text-decoration-none d-block';
      a.dataset.id = opts.id || '';
      a.textContent = text;
      body.appendChild(a);
    } else {
      body.textContent = text;
    }
    const close = document.createElement('button');
    close.type = 'button'; close.className = 'btn-close btn-close-white me-2 m-auto'; close.setAttribute('data-bs-dismiss', 'toast'); close.setAttribute('aria-label', 'Close');
    const flex = document.createElement('div');
    flex.className = 'd-flex';
    flex.append(body, close);
    el.appendChild(flex);
    toastWrap.appendChild(el);
    const t = new bootstrap.Toast(el, { delay: opts.href ? 9000 : 4500 });
    el.addEventListener('hidden.bs.toast', () => el.remove());
    t.show();
  };

  // Live messages: poll for new enquiries, update the badge/title, toast + desktop alert + soft chime.
  const pollUrl = document.body.dataset.pollUrl;
  if (pollUrl) {
    const baseTitle = document.title;
    const messagesUrl = pollUrl.split('?')[0];
    let latest = +document.body.dataset.latestMsg || 0;
    let unread = +document.body.dataset.unread || 0;

    const paint = (pulse) => {
      const link = document.querySelector('.nav-item[href$="/messages"], .nav-item[href$="messages"]');
      if (link) {
        let badge = link.querySelector('.badge-count');
        if (unread > 0 && !badge) { badge = document.createElement('span'); badge.className = 'badge-count'; link.appendChild(badge); }
        if (badge) {
          if (unread > 0) { badge.textContent = unread; if (pulse) { badge.classList.remove('pulse'); void badge.offsetWidth; badge.classList.add('pulse'); } }
          else badge.remove();
        }
      }
      document.title = unread > 0 ? '(' + unread + ') ' + baseTitle : baseTitle;
    };
    window.ymSetUnread = (n) => { unread = n; paint(false); };
    paint(false);

    const chime = () => {
      try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const o = ctx.createOscillator(), g = ctx.createGain();
        o.type = 'sine'; o.frequency.value = 880; g.gain.setValueAtTime(0.0001, ctx.currentTime);
        g.gain.exponentialRampToValueAtTime(0.15, ctx.currentTime + 0.02);
        g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.45);
        o.connect(g); g.connect(ctx.destination); o.start(); o.stop(ctx.currentTime + 0.5);
      } catch (e) { /* audio blocked until the user interacts with the page */ }
    };

    const notifyBtn = document.getElementById('notifyToggle');
    if (notifyBtn && 'Notification' in window && Notification.permission === 'default') {
      notifyBtn.classList.remove('d-none');
      notifyBtn.addEventListener('click', () => Notification.requestPermission().then(() => notifyBtn.classList.add('d-none')));
    }

    let timer = null;
    const tick = async () => {
      try {
        const res = await fetch(pollUrl + '&after=' + latest, { credentials: 'same-origin', headers: { 'X-Requested-With': 'fetch' }, cache: 'no-store' });
        if (res.status === 401) { clearInterval(timer); return; } // signed out or idle timeout
        const j = await res.json();
        if (!j.ok) return;
        const fresh = j.items || [];
        if (fresh.length) {
          latest = fresh[fresh.length - 1].id;
          document.dispatchEvent(new CustomEvent('ym:messages', { detail: { items: fresh } }));
          fresh.slice(-3).forEach((m) => {
            window.ymToast('New message from ' + m.name + ' — ' + m.subject, 'dark', { href: messagesUrl + '?id=' + m.id, id: m.id });
            if (document.hidden && 'Notification' in window && Notification.permission === 'granted') {
              const n = new Notification('New message from ' + m.name, { body: m.subject + '\n' + m.excerpt, tag: 'ym-msg-' + m.id });
              n.onclick = () => { window.focus(); location.href = messagesUrl + '?id=' + m.id; };
            }
          });
          chime();
        }
        if (j.unread !== unread) { unread = j.unread; paint(fresh.length > 0); }
      } catch (e) { /* offline: try again next tick */ }
    };
    timer = setInterval(tick, 10000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) tick(); });
  }

  // Image input preview
  document.querySelectorAll('input[type=file][data-preview]').forEach((input) => {
    const img = document.getElementById(input.dataset.preview);
    input.addEventListener('change', () => {
      const f = input.files && input.files[0];
      if (f && img) { img.src = URL.createObjectURL(f); img.style.display = 'block'; }
    });
  });

  // Auto-generate slug hint & character counters
  document.querySelectorAll('[data-maxlen-counter]').forEach((el) => {
    const out = document.querySelector(el.dataset.maxlenCounter);
    const upd = () => { if (out) out.textContent = el.value.length + ' / ' + el.maxLength; };
    el.addEventListener('input', upd); upd();
  });

  // Rich text editors (Quill, loaded from CDN only on pages that need it)
  if (window.Quill) {
    document.querySelectorAll('textarea.richtext').forEach((ta) => {
      const holder = document.createElement('div');
      holder.innerHTML = ta.value;
      ta.parentNode.insertBefore(holder, ta);
      ta.style.display = 'none';
      const quill = new Quill(holder, {
        theme: 'snow',
        placeholder: ta.getAttribute('placeholder') || 'Write here…',
        modules: { toolbar: [[{ header: [2, 3, false] }], ['bold', 'italic', 'underline'], [{ list: 'ordered' }, { list: 'bullet' }], ['blockquote', 'link'], ['clean']] },
      });
      ta.form.addEventListener('submit', () => {
        ta.value = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
      });
    });
  }
})();
