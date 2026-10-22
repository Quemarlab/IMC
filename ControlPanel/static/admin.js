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
      pending.submit();
    });
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
