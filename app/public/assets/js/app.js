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
    notifBtn.addEventListener('click', async (e) => {
      e.stopPropagation();
      const open = notifPanel.hasAttribute('hidden');
      notifPanel.toggleAttribute('hidden', !open);
      notifBtn.setAttribute('aria-expanded', open ? 'true' : 'false');

      // Saat panel DIBUKA — hapus dot merah & tandai semua dibaca
      if (open) {
        const dot = document.getElementById('notif-dot');
        if (dot) {
          dot.remove();
          // Tandai semua sebagai dibaca di server (silent)
          const cfg = window.KKN || {};
          postForm(`${cfg.notifReadUrl}/read-all`).then(() => {
            document.querySelectorAll('.notif-item.unread').forEach((el) => el.classList.remove('unread'));
            if (readAllBtn) readAllBtn.remove();
          }).catch(() => {});
        }
      }
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

  const initMapEditors = () => {
    if (!window.L) return;

    document.querySelectorAll('[data-map-editor="1"]').forEach((el) => {
      if (el.dataset.mapReady === '1') return;

      const wrapper = el.closest('form') || document;
      const latitudeInput = wrapper.querySelector('[data-location-latitude], #admin-location-latitude');
      const longitudeInput = wrapper.querySelector('[data-location-longitude], #admin-location-longitude');
      const status = wrapper.querySelector('[data-location-status]');
      const useButton = wrapper.querySelector('[data-location-use]');
      const clearButton = wrapper.querySelector('[data-location-clear]');
      const initialLatitude = Number(el.dataset.lat);
      const initialLongitude = Number(el.dataset.lng);
      const hasInitialPoint = Number.isFinite(initialLatitude)
        && Number.isFinite(initialLongitude)
        && initialLatitude >= -90
        && initialLatitude <= 90
        && initialLongitude >= -180
        && initialLongitude <= 180;
      const defaultCenter = [-3.695, 128.183];
      const map = L.map(el).setView(hasInitialPoint ? [initialLatitude, initialLongitude] : defaultCenter, hasInitialPoint ? 15 : 12);
      let marker = null;

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19,
      }).addTo(map);

      const setStatus = (message) => {
        if (status) status.textContent = message;
      };

      const setPoint = (latitude, longitude, updateInputs = true) => {
        if (marker) {
          marker.setLatLng([latitude, longitude]);
        } else {
          marker = L.marker([latitude, longitude]).addTo(map);
        }

        map.setView([latitude, longitude], Math.max(map.getZoom(), 15));

        if (updateInputs) {
          if (latitudeInput) latitudeInput.value = latitude.toFixed(7);
          if (longitudeInput) longitudeInput.value = longitude.toFixed(7);
        }

        setStatus(`Titik dipilih: ${latitude.toFixed(7)}, ${longitude.toFixed(7)}`);
      };

      const clearPoint = () => {
        if (marker) {
          map.removeLayer(marker);
          marker = null;
        }

        if (latitudeInput) latitudeInput.value = '';
        if (longitudeInput) longitudeInput.value = '';
        setStatus('Belum ada titik dipilih.');
      };

      const syncInputs = () => {
        const latitude = Number(latitudeInput?.value);
        const longitude = Number(longitudeInput?.value);

        if (Number.isFinite(latitude) && Number.isFinite(longitude)
          && latitude >= -90 && latitude <= 90 && longitude >= -180 && longitude <= 180) {
          setPoint(latitude, longitude, false);
        }
      };

      if (hasInitialPoint) setPoint(initialLatitude, initialLongitude, false);
      el.addEventListener('click', () => map.invalidateSize());
      map.on('click', (event) => setPoint(event.latlng.lat, event.latlng.lng));
      latitudeInput?.addEventListener('change', syncInputs);
      longitudeInput?.addEventListener('change', syncInputs);
      clearButton?.addEventListener('click', clearPoint);
      useButton?.addEventListener('click', () => {
        if (! navigator.geolocation) {
          setStatus('Browser tidak mendukung pengambilan lokasi.');
          return;
        }

        setStatus('Mengambil lokasi perangkat…');
        navigator.geolocation.getCurrentPosition(
          (position) => setPoint(position.coords.latitude, position.coords.longitude),
          () => setStatus('Lokasi perangkat tidak dapat diambil. Pilih titik di peta.'),
          { enableHighAccuracy: true, timeout: 10000 }
        );
      });
      el.dataset.mapReady = '1';
      setTimeout(() => map.invalidateSize(), 120);
    });
  };

  const initAllMaps = () => {
    initMaps();
    initMapEditors();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllMaps);
  } else {
    initAllMaps();
  }
  window.addEventListener('load', initAllMaps);

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
