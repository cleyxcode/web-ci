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

  /* ----- Dashboard count-up ----- */
  const countUpElements = document.querySelectorAll('.js-count-up[data-count-up]');
  const reduceMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

  const formatCount = (value, decimals, suffix) => {
    const formatted = new Intl.NumberFormat('id-ID', {
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals,
    }).format(value);

    return `${formatted}${suffix}`;
  };

  countUpElements.forEach((element) => {
    const target = Number(element.dataset.countUp);
    const suffix = element.dataset.countSuffix || '';
    const decimals = Number.isInteger(Number(element.dataset.countDecimals))
      ? Number(element.dataset.countDecimals)
      : (Number.isInteger(target) ? 0 : 2);

    if (!Number.isFinite(target)) return;

    if (reduceMotion || target === 0) {
      element.textContent = formatCount(target, decimals, suffix);
      return;
    }

    const duration = Math.min(1100, Math.max(500, 450 + Math.abs(target) * 8));
    const startAt = performance.now();

    const draw = (now) => {
      const progress = Math.min(1, (now - startAt) / duration);
      const eased = 1 - Math.pow(1 - progress, 3);
      element.textContent = formatCount(target * eased, decimals, suffix);

      if (progress < 1) window.requestAnimationFrame(draw);
    };

    element.textContent = formatCount(0, decimals, suffix);
    window.requestAnimationFrame(draw);
  });

  /* ----- UI notifications ----- */
  const toastMeta = {
    success: { title: 'Berhasil', icon: '✓' },
    error: { title: 'Perlu diperiksa', icon: '!' },
    danger: { title: 'Perlu diperiksa', icon: '!' },
    warning: { title: 'Perhatian', icon: '!' },
    info: { title: 'Informasi', icon: 'i' },
  };
  const toast = document.getElementById('toast');
  const toastTitle = document.getElementById('toast-title');
  const toastMessage = document.getElementById('toast-message');
  const toastIcon = document.getElementById('toast-icon');
  const toastClose = document.getElementById('toast-close');

  const hideToast = () => {
    if (toast) toast.classList.add('hidden');
  };

  window.showToast = (msg, type = 'info', title = '') => {
    if (!toast || !toastMessage) return;
    const safeType = toastMeta[type] ? type : 'info';
    const meta = toastMeta[safeType];
    toast.className = `toast toast-${safeType} fixed bottom-24 right-4 z-50 flex w-[min(24rem,calc(100vw-2rem))] items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900`;
    if (toastTitle) toastTitle.textContent = title || meta.title;
    toastMessage.textContent = msg || '';
    if (toastIcon) toastIcon.textContent = meta.icon;
    toast.classList.remove('hidden');
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(hideToast, 5200);
  };

  toastClose?.addEventListener('click', hideToast);

  document.querySelectorAll('.flash-message[data-toast-message]').forEach((flash) => {
    showToast(
      flash.dataset.toastMessage,
      flash.dataset.toastType || 'info',
      flash.dataset.toastTitle || '',
    );
    flash.remove();
  });

  /* ----- Custom confirmation card: never use the browser confirm dialog ----- */
  const confirmModal = document.getElementById('ui-confirm');
  const confirmMessage = document.getElementById('ui-confirm-message');
  const confirmSubmit = document.getElementById('ui-confirm-submit');
  let pendingForm = null;
  let lastFocusedElement = null;

  const closeConfirm = () => {
    if (!confirmModal) return;
    confirmModal.classList.add('hidden');
    pendingForm = null;
    lastFocusedElement?.focus?.();
  };

  const openConfirm = (form) => {
    if (!confirmModal || !confirmMessage || !confirmSubmit) return;
    pendingForm = form;
    lastFocusedElement = document.activeElement;
    confirmMessage.textContent = form.dataset.confirm || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
    confirmModal.classList.remove('hidden');
    confirmSubmit.focus();
  };

  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (form.dataset.confirmed === '1') return;
      event.preventDefault();
      openConfirm(form);
    });
  });

  confirmSubmit?.addEventListener('click', () => {
    if (!pendingForm) return closeConfirm();
    pendingForm.dataset.confirmed = '1';
    pendingForm.submit();
  });

  confirmModal?.querySelectorAll('[data-confirm-cancel]').forEach((button) => {
    button.addEventListener('click', closeConfirm);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && confirmModal && !confirmModal.classList.contains('hidden')) {
      closeConfirm();
    }
  });

  /* ----- Shared responsive table semantics ----- */
  document.querySelectorAll('.table-wrap table.data').forEach((table) => {
    const wrapper = table.closest('.table-wrap');
    const headers = [...table.querySelectorAll('thead th')].map((header) => header.textContent.trim());
    wrapper?.classList.add('responsive-table');
    table.querySelectorAll('tbody tr').forEach((row) => {
      row.querySelectorAll('td').forEach((cell, index) => {
        if (!cell.classList.contains('empty') && !cell.dataset.label) {
          cell.dataset.label = headers[index] || 'Data';
        }
      });
    });
  });

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

  /* ----- Profile menu and logout confirmation ----- */
  const profileBtn = document.getElementById('profile-btn');
  const profilePanel = document.getElementById('profile-panel');
  const profileWrap = document.getElementById('profile-wrap');

  const closeProfileMenu = () => {
    if (!profilePanel || !profileBtn) return;
    profilePanel.classList.add('hidden');
    profileBtn.setAttribute('aria-expanded', 'false');
  };

  if (profileBtn && profilePanel) {
    profileBtn.addEventListener('click', (event) => {
      event.stopPropagation();
      const isOpen = !profilePanel.classList.contains('hidden');
      profilePanel.classList.toggle('hidden', isOpen);
      profileBtn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
      if (!isOpen && notifPanel && notifBtn) {
        notifPanel.setAttribute('hidden', '');
        notifBtn.setAttribute('aria-expanded', 'false');
      }
    });

    profilePanel.addEventListener('click', (event) => event.stopPropagation());
    document.addEventListener('click', (event) => {
      if (profileWrap && !profileWrap.contains(event.target)) closeProfileMenu();
    });
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') closeProfileMenu();
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
      const rawLatitude = (el.dataset.lat || '').trim();
      const rawLongitude = (el.dataset.lng || '').trim();
      const initialLatitude = rawLatitude === '' ? NaN : Number(rawLatitude);
      const initialLongitude = rawLongitude === '' ? NaN : Number(rawLongitude);
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
        const rawLatitude = (latitudeInput?.value || '').trim();
        const rawLongitude = (longitudeInput?.value || '').trim();
        const latitude = rawLatitude === '' ? NaN : Number(rawLatitude);
        const longitude = rawLongitude === '' ? NaN : Number(rawLongitude);

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
      showToast(data.pesan || data.judul || 'Notifikasi baru', data.type || 'info', data.judul || 'Notifikasi baru');
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
        item.className = `notif-item unread notif-type-${data.type || 'info'} cursor-pointer bg-violet-50/60 px-4 py-3 transition hover:bg-slate-50 dark:bg-violet-950/20 dark:hover:bg-slate-800`;
        item.innerHTML = `<strong class="block text-sm text-slate-800 dark:text-slate-100">${data.judul}</strong><p class="mt-1 text-xs text-slate-500">${data.pesan || ''}</p><small class="mt-1 block text-[11px] text-slate-400">Baru saja</small>`;
        list.prepend(item);
      }
    });
    channel.bind_global((event, data) => {
      if (event.startsWith('pusher:')) return;
      if (event !== 'notifikasi.new' && data?.nama_mahasiswa) {
        showToast(`Aktivitas: ${data.nama_mahasiswa}`, 'info', 'Aktivitas baru');
      }
    });
  }
})();
