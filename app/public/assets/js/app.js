(() => {
  'use strict';

  /* ----- Password toggle ----- */
  document.querySelectorAll('.password-toggle').forEach((btn) => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-target');
      const input = id ? document.getElementById(id) : btn.previousElementSibling;
      if (!input) return;

      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.classList.toggle('is-visible', show);
      btn.setAttribute('aria-pressed', show ? 'true' : 'false');
      btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
    });
  });

  /* ----- Dark mode ----- */
  const themeBtn = document.getElementById('theme-toggle');
  const applyTheme = (dark) => {
    document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
    localStorage.setItem('kkn-theme', dark ? 'dark' : 'light');
  };

  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
      applyTheme(!isDark);
    });
  }

  /* ----- Toast ----- */
  window.showToast = (msg, type = 'info') => {
    const toast = document.getElementById('toast');
    const msgEl = document.getElementById('toast-message');
    if (!toast || !msgEl) return;
    toast.className = `toast toast-${type}`;
    msgEl.textContent = msg;
    toast.classList.remove('hidden');
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => toast.classList.add('hidden'), 4000);
  };

  /* ----- Notifications ----- */
  const notifBtn = document.getElementById('notif-btn');
  const notifPanel = document.getElementById('notif-panel');
  const notifWrap = document.getElementById('notif-wrap');
  const readAllBtn = document.getElementById('notif-read-all');

  const postForm = async (url) => {
    const cfg = window.KKN || {};
    const body = new URLSearchParams();
    if (cfg.csrfName && cfg.csrf) body.set(cfg.csrfName, cfg.csrf);
    return fetch(url, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body });
  };

  if (notifBtn && notifPanel) {
    notifBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const open = notifPanel.hasAttribute('hidden');
      notifPanel.toggleAttribute('hidden', !open);
      notifBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    document.addEventListener('click', (e) => {
      if (notifWrap && !notifWrap.contains(e.target)) {
        notifPanel.setAttribute('hidden', '');
        notifBtn.setAttribute('aria-expanded', 'false');
      }
    });

    notifPanel.querySelectorAll('.notif-item[data-id]').forEach((item) => {
      item.addEventListener('click', async () => {
        const id = item.dataset.id;
        const cfg = window.KKN || {};
        const res = await postForm(`${cfg.notifReadUrl}/${id}/read`);
        if (res.ok) {
          item.classList.remove('unread');
          const dot = document.getElementById('notif-dot');
          const data = await res.json();
          if (dot && data.unreadCount === 0) dot.remove();
        }
      });
    });
  }

  if (readAllBtn) {
    readAllBtn.addEventListener('click', async () => {
      const cfg = window.KKN || {};
      const res = await postForm(`${cfg.notifReadUrl}/read-all`);
      if (res.ok) {
        document.querySelectorAll('.notif-item.unread').forEach((el) => el.classList.remove('unread'));
        const dot = document.getElementById('notif-dot');
        if (dot) dot.remove();
        readAllBtn.remove();
        showToast('Semua notifikasi ditandai dibaca', 'success');
      }
    });
  }

  /* ----- Leaflet maps (data-map + data-points) ----- */
  const initMaps = () => {
    if (!window.L) return;
    document.querySelectorAll('[data-map="1"]').forEach((el) => {
      if (el.dataset.mapReady === '1') return;
      let points = [];
      try {
        points = JSON.parse(el.dataset.points || '[]');
      } catch (_) {
        points = [];
      }
      if (!points.length) return;

      const zoom = parseInt(el.dataset.zoom || '13', 10);
      const map = L.map(el).setView([points[0].lat, points[0].lng], zoom);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19,
      }).addTo(map);

      const bounds = [];
      points.forEach((p) => {
        const marker = L.marker([p.lat, p.lng]).addTo(map);
        if (p.popup) marker.bindPopup(p.popup);
        bounds.push([p.lat, p.lng]);
      });
      if (bounds.length > 1) map.fitBounds(bounds, { padding: [28, 28] });
      el.dataset.mapReady = '1';
      setTimeout(() => map.invalidateSize(), 120);
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMaps);
  } else {
    initMaps();
  }
  window.addEventListener('load', initMaps);

  /* ----- Pusher realtime ----- */
  if (window.KKN?.pusherEnabled && window.Pusher && window.KKN.userId) {
    const pusher = new Pusher(window.KKN.pusherKey, { cluster: window.KKN.pusherCluster });
    const channel = pusher.subscribe(`user-${window.KKN.userId}`);
    channel.bind('notifikasi.new', (data) => {
      showToast(data.judul || 'Notifikasi baru', data.type || 'info');
      const dot = document.getElementById('notif-dot');
      if (!dot && notifBtn) {
        const span = document.createElement('span');
        span.className = 'dot';
        span.id = 'notif-dot';
        notifBtn.appendChild(span);
      }
      const list = document.getElementById('notif-list');
      if (list && data.judul) {
        const empty = list.querySelector('.notif-empty');
        if (empty) empty.remove();
        const item = document.createElement('div');
        item.className = `notif-item unread notif-type-${data.type || 'info'}`;
        item.innerHTML = `<strong>${data.judul}</strong><p>${data.pesan || ''}</p><small>Baru saja</small>`;
        list.prepend(item);
      }
    });
    channel.bind_global((event, data) => {
      if (event.startsWith('pusher:')) return;
      if (event !== 'notifikasi.new' && data?.nama_mahasiswa) {
        showToast(`Aktivitas: ${data.nama_mahasiswa}`, 'info');
      }
    });
  }
})();
