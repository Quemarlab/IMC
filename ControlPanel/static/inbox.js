/* Young Masters admin — live inbox: open messages in place, mark as read, reply and act without reloading. */
(function () {
  'use strict';

  const pane = document.getElementById('msgPane');
  const list = document.getElementById('msgList');
  const empty = document.getElementById('msgEmpty');
  const base = location.pathname;
  const wide = () => window.matchMedia('(min-width: 992px)').matches;
  const ajaxHeaders = { 'X-Requested-With': 'fetch' };
  let current = 0;

  const rowFor = (id) => list && list.querySelector('.msg-row[data-id="' + id + '"]');

  function syncRow(id, status) {
    const row = rowFor(id);
    if (!row) return;
    row.dataset.status = status;
    row.classList.toggle('unread', status === 'new');
    const slot = row.querySelector('.msg-status');
    const badge = pane && pane.querySelector('.msg-detail .badge');
    if (slot) slot.innerHTML = status === 'new' || !badge ? '' : badge.outerHTML;
    const filter = new URLSearchParams(location.search).get('status') || '';
    if (status === 'archived' && filter !== 'archived') row.remove();
    if (list && empty) empty.classList.toggle('d-none', list.children.length > 0);
  }

  async function open(id) {
    current = id;
    if (list) list.querySelectorAll('.msg-row.active').forEach((r) => r.classList.remove('active'));
    const row = rowFor(id);
    row && row.classList.add('active');
    pane.classList.add('loading');
    try {
      const res = await fetch(base + '?id=' + id + '&partial=1', { credentials: 'same-origin', headers: ajaxHeaders });
      if (!res.ok) throw new Error();
      pane.innerHTML = await res.text();
      const unread = res.headers.get('X-Unread');
      if (unread !== null && window.ymSetUnread) window.ymSetUnread(+unread);
      const detail = pane.querySelector('.msg-detail');
      detail && syncRow(id, detail.dataset.status);
      pane.scrollTop = 0;
    } catch (e) {
      window.ymToast && window.ymToast('Could not load that message.', 'danger');
    }
    pane.classList.remove('loading');
  }

  // Click a row (or a "new message" toast) -> open in the side pane on wide screens, full page otherwise.
  document.addEventListener('click', (ev) => {
    const a = ev.target.closest('.msg-open');
    if (!a || !pane || !wide() || ev.metaKey || ev.ctrlKey || ev.shiftKey) return;
    ev.preventDefault();
    open(+a.dataset.id);
  });

  // data-ajax forms (reply / status / delete) post in the background.
  document.addEventListener('submit', async (ev) => {
    const form = ev.target;
    if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-ajax')) return;
    if (form.dataset.confirm && !form.dataset.confirmed) return; // the confirm dialog re-submits once approved
    ev.preventDefault();
    delete form.dataset.confirmed;
    const btn = form.querySelector('button:not([type=button])');
    const label = btn && btn.innerHTML;
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Working…'; }
    try {
      const res = await fetch(form.action, { method: 'POST', body: new FormData(form), credentials: 'same-origin', headers: ajaxHeaders });
      const j = await res.json();
      window.ymToast && window.ymToast(j.message, j.ok ? 'success' : 'danger');
      if (typeof j.unread === 'number' && window.ymSetUnread) window.ymSetUnread(j.unread);
      if (j.ok) {
        if (j.deleted) {
          if (!pane) { location.href = base; return; }
          const row = rowFor(current);
          row && row.remove();
          pane.innerHTML = '<div class="empty-admin"><i class="bi bi-envelope-open"></i>Message deleted.</div>';
          empty && empty.classList.toggle('d-none', list.children.length > 0);
        } else if (pane) {
          await open(current || +form.closest('.msg-detail').dataset.id);
        } else {
          location.reload();
        }
        return;
      }
    } catch (e) {
      window.ymToast && window.ymToast('Something went wrong. Please try again.', 'danger');
    }
    if (btn) { btn.disabled = false; btn.innerHTML = label; }
  });

  // New messages pushed by the poller in admin.js are added to the top of the list.
  document.addEventListener('ym:messages', (ev) => {
    const inbox = document.querySelector('.inbox');
    if (!list || !inbox || !inbox.dataset.live) return;
    ev.detail.items.forEach((item) => {
      if (rowFor(item.id)) return;
      const tpl = document.createElement('template');
      tpl.innerHTML = item.html.trim();
      const row = tpl.content.firstElementChild;
      row.classList.add('flash-new');
      list.prepend(row);
    });
    empty && empty.classList.add('d-none');
  });
})();
