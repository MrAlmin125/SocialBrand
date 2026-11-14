(function () {
  var root = document.documentElement;
  var still = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // ---- Preloader: hidden once the page is loaded (shown at least 600ms so the animation is visible)
  (function () {
    var t0 = Date.now();
    function done() { setTimeout(function () { root.classList.add('loaded'); document.dispatchEvent(new Event('kpre')); }, Math.max(0, 600 - (Date.now() - t0))); }
    if (!document.getElementById('preloader')) { root.classList.add('loaded'); return; }
    if (document.readyState === 'complete') done(); else window.addEventListener('load', done);
  })();
  function onLoaded(fn) { if (root.classList.contains('loaded')) fn(); else document.addEventListener('kpre', fn, { once: true }); }

  // ---- Buttons layout switch: list or 2 per row (choice is remembered on the visitor's device)
  var ul = document.getElementById('links');
  if (ul) {
    var btns = document.querySelectorAll('.layoutsw button');
    var set = function (m) {
      ul.classList.toggle('cols2', m === 'grid');
      btns.forEach(function (b) { var on = b.dataset.mode === m; b.classList.toggle('on', on); b.setAttribute('aria-pressed', on ? 'true' : 'false'); });
    };
    var saved = null; try { saved = localStorage.getItem('kl'); } catch (e) {}
    set(saved === 'grid' || saved === 'list' ? saved : ul.dataset.default);
    btns.forEach(function (b) { b.addEventListener('click', function () { set(b.dataset.mode); try { localStorage.setItem('kl', b.dataset.mode); } catch (e) {} }); });

    // ---- Entrance: left-side buttons slide in from the left, right-side buttons from the right
    onLoaded(function () {
      if (still) return;
      var rtl = getComputedStyle(root).direction === 'rtl', grid = ul.classList.contains('cols2'), items = ul.children, n = items.length;
      Array.prototype.forEach.call(items, function (li, i) {
        var side;
        if (grid) { side = (n % 2 === 1 && i === n - 1) ? 'u' : (((i % 2 === 0) !== rtl) ? 'l' : 'r'); }
        else side = (i % 2 === 0) ? 'l' : 'r';
        li.style.setProperty('--i', i); li.classList.add('fx-' + side);
      });
    });
  }

  // ---- Scroll reveal: sections rise up when scrolling down and drop down when scrolling up
  (function () {
    var els = document.querySelectorAll('.hdwrap, .bslider, .card, .more, .map, .hours, .reviews, .contact, .catbar, .sec-title');
    if (!els.length || !('IntersectionObserver' in window) || still) return;
    root.classList.add('reveal-on');
    var col = 0;
    Array.prototype.forEach.call(els, function (el) {
      el.classList.add('reveal');
      if (el.classList.contains('card')) { el.style.setProperty('--d', ((col++ % 2) * 90) + 'ms'); }
    });
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        var el = en.target;
        if (en.isIntersecting) { el.classList.add('in'); }
        else {
          var r = en.boundingClientRect;                       // left through the top => will come back from the top
          el.classList.remove('in'); el.classList.toggle('from-top', r.bottom <= 0);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -5% 0px' });
    onLoaded(function () { Array.prototype.forEach.call(els, function (el) { io.observe(el); }); });
  })();

  // ---- Live visitor counter heartbeat (only while the page is visible)
  function ping() {
    if (document.visibilityState !== 'visible') return;
    fetch('ping.php', { method: 'POST', credentials: 'same-origin', keepalive: true }).catch(function () {});
  }
  ping(); setInterval(ping, 30000); document.addEventListener('visibilitychange', ping);

  // ---- Approximate visitor location for the statistics (looked up in the visitor's browser from their IP)
  if (window.KV_GEO) {
    var save = function (d) { fetch('geo.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(d), credentials: 'same-origin', keepalive: true }).catch(function () {}); };
    fetch('https://ipwho.is/').then(function (r) { return r.json(); }).then(function (j) {
      if (j && j.success !== false && j.country) save({ country: j.country, cc: j.country_code, region: j.region, city: j.city }); else throw new Error('no data');
    }).catch(function () {
      fetch('https://ipapi.co/json/').then(function (r) { return r.json(); }).then(function (j) {
        if (j && j.country_name) save({ country: j.country_name, cc: j.country_code, region: j.region, city: j.city });
      }).catch(function () {});
    });
  }

  // ---- Welcome popup (on/off, frequency and delay come from the admin panel)
  (function () {
    var pop = document.getElementById('wpop');
    if (!pop) return;
    var freq = pop.dataset.freq, key = 'wp_' + pop.dataset.key, delay = (parseInt(pop.dataset.delay, 10) || 0) * 1000;
    function get(st, k) { try { return window[st].getItem(k); } catch (e) { return null; } }
    function put(st, k, v) { try { window[st].setItem(k, v); } catch (e) {} }
    var skip = (freq === 'session' && get('sessionStorage', key)) || (freq === 'once' && get('localStorage', key)) ||
               (freq === 'day' && get('localStorage', key) && Date.now() - parseInt(get('localStorage', key), 10) < 86400000);
    if (skip) return;
    var last = null;
    function close() { pop.classList.remove('show'); setTimeout(function () { pop.hidden = true; }, 250); document.removeEventListener('keydown', onKey); if (last && last.focus) last.focus(); }
    function onKey(e) { if (e.key === 'Escape') close(); }
    function open() {
      last = document.activeElement; pop.hidden = false;
      requestAnimationFrame(function () { pop.classList.add('show'); });
      var x = pop.querySelector('[data-close]'); if (x) x.focus();
      document.addEventListener('keydown', onKey);
      if (freq === 'session') put('sessionStorage', key, '1'); else if (freq === 'once') put('localStorage', key, '1'); else if (freq === 'day') put('localStorage', key, String(Date.now()));
    }
    pop.addEventListener('click', function (e) { if (e.target === pop || e.target.closest('[data-close]')) close(); });
    onLoaded(function () { setTimeout(open, delay); });
  })();

  // ---- Banner sliders: arrows, dots, swipe and auto-play (RTL aware)
  document.querySelectorAll('.bslider').forEach(function (el) {
    var track = el.querySelector('.bs-track'), n = track.children.length, i = 0, timer = null;
    if (n === 0) return;
    var rtl = getComputedStyle(root).direction === 'rtl', dots = el.querySelector('.bs-dots');
    function go(k) {
      i = (k + n) % n;
      track.style.transform = 'translateX(' + ((rtl ? 1 : -1) * i * 100) + '%)';
      if (dots) Array.prototype.forEach.call(dots.children, function (d, x) { d.classList.toggle('on', x === i); });
    }
    if (dots && el.dataset.dots === '1') {
      for (var x = 0; x < n; x++) (function (x) {
        var b = document.createElement('button'); b.type = 'button'; b.setAttribute('aria-label', (x + 1) + ' / ' + n);
        b.addEventListener('click', function () { go(x); restart(); }); dots.appendChild(b);
      })(x);
    }
    var prev = el.querySelector('.bs-nav.prev'), next = el.querySelector('.bs-nav.next');
    if (prev) prev.addEventListener('click', function () { go(i - 1); restart(); });
    if (next) next.addEventListener('click', function () { go(i + 1); restart(); });
    function stop() { if (timer) { clearInterval(timer); timer = null; } }
    function start() { if (el.dataset.autoplay === '1' && !still && n > 1) timer = setInterval(function () { go(i + 1); }, Math.max(2, parseInt(el.dataset.interval, 10) || 5) * 1000); }
    function restart() { stop(); start(); }
    var x0 = null;
    el.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; stop(); }, { passive: true });
    el.addEventListener('touchend', function (e) {
      if (x0 !== null) { var dx = e.changedTouches[0].clientX - x0; if (Math.abs(dx) > 40) go(((dx < 0) !== rtl) ? i + 1 : i - 1); }
      x0 = null; start();
    }, { passive: true });
    el.addEventListener('mouseenter', stop); el.addEventListener('mouseleave', start);
    go(0); start();
  });

  // ---- Product search + category filter
  (function () {
    var grid = document.getElementById('pgrid'); if (!grid) return;
    var q = document.getElementById('psearch'), chips = document.getElementById('pchips'), nores = document.getElementById('nores'), cat = '0';
    function apply() {
      var term = ((q && q.value) || '').trim().toLowerCase(), n = 0;
      grid.querySelectorAll('.card').forEach(function (c) { var ok = (cat === '0' || c.dataset.cat === cat) && (!term || c.dataset.s.indexOf(term) > -1); c.classList.toggle('fhide', !ok); if (ok) n++; });
      if (nores) nores.hidden = n > 0;
    }
    if (q) q.addEventListener('input', apply);
    if (chips) chips.addEventListener('click', function (e) {
      var b = e.target.closest('.chip'); if (!b) return; cat = b.dataset.cat;
      chips.querySelectorAll('.chip').forEach(function (x) { x.classList.toggle('on', x === b); }); apply();
    });
  })();

  // ---- Sale countdown (reloads once when the sale ends so the price goes back to normal)
  (function () {
    var els = document.querySelectorAll('.cd'); if (!els.length) return;
    function pad(n) { return n < 10 ? '0' + n : '' + n; }
    function tick() {
      var now = Math.floor(Date.now() / 1000);
      els.forEach(function (el) {
        var left = parseInt(el.dataset.end, 10) - now, t = el.querySelector('.cd-t');
        if (left <= 0) {
          var key = 'cdr' + el.dataset.end;
          try { if (!sessionStorage.getItem(key)) { sessionStorage.setItem(key, '1'); location.reload(); return; } } catch (e) {}
          el.hidden = true; return;
        }
        var d = Math.floor(left / 86400), h = Math.floor(left % 86400 / 3600), m = Math.floor(left % 3600 / 60), s = left % 60;
        t.textContent = (d > 0 ? d + el.dataset.u + ' ' : '') + pad(h) + ':' + pad(m) + ':' + pad(s);
      });
    }
    tick(); setInterval(tick, 1000);
  })();

  // ---- Review + contact forms (sent in the background, saved for approval / in the admin inbox)
  document.querySelectorAll('.pform').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      e.preventDefault();
      var msg = f.querySelector('.pmsg'), btn = f.querySelector('.pbtn'); msg.className = 'pmsg';
      if (f.classList.contains('ct-form') && !f.elements.phone.value.trim() && !f.elements.email.value.trim()) { msg.textContent = f.dataset.need; msg.classList.add('bad'); return; }
      btn.disabled = true;
      fetch(f.dataset.url, { method: 'POST', body: new FormData(f), credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (d) {
        if (d && d.ok) { msg.textContent = f.dataset.ok; msg.classList.add('good'); f.reset(); if (f.classList.contains('rv-form')) setTimeout(function () { f.hidden = true; }, 3000); }
        else { msg.textContent = (d && d.e === 'rate') ? f.dataset.rate : f.dataset.err; msg.classList.add('bad'); }
      }).catch(function () { msg.textContent = f.dataset.err; msg.classList.add('bad'); }).then(function () { btn.disabled = false; });
    });
  });
  document.querySelectorAll('.rv-open').forEach(function (b) {
    b.addEventListener('click', function () { var f = b.closest('.reviews').querySelector('.rv-form'); f.hidden = !f.hidden; if (!f.hidden) f.elements.name.focus(); });
  });
  // "Order" buttons that open the form: pick the product in the form
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a[data-order]'); if (!a) return;
    var sel = document.getElementById('ct_product'); if (sel) sel.value = a.dataset.order;
    setTimeout(function () { var t = document.querySelector('#contact input[name=name]'); if (t) t.focus({ preventScroll: true }); }, 500);
  });

  // ---- Install like an app (service worker + "Add to home screen" button)
  if ('serviceWorker' in navigator && document.querySelector('link[rel=manifest]')) window.addEventListener('load', function () { navigator.serviceWorker.register('sw.js').catch(function () {}); });
  (function () {
    var box = document.getElementById('pwa'); if (!box) return;
    if ((window.matchMedia && matchMedia('(display-mode: standalone)').matches) || navigator.standalone) return;
    var dis = null; try { dis = localStorage.getItem('pwa_x'); } catch (e) {}
    if (dis && Date.now() - parseInt(dis, 10) < 7 * 864e5) return;
    var add = document.getElementById('pwaAdd'), x = document.getElementById('pwaX'), ios = document.getElementById('pwaIos'), ev = null;
    window.addEventListener('beforeinstallprompt', function (e) { e.preventDefault(); ev = e; box.hidden = false; });
    window.addEventListener('appinstalled', function () { box.hidden = true; });
    if (/iphone|ipad|ipod/i.test(navigator.userAgent) && !window.MSStream) box.hidden = false;
    add.addEventListener('click', function () { if (ev) { ev.prompt(); ev.userChoice.then(function () { ev = null; box.hidden = true; }); } else if (ios) ios.hidden = !ios.hidden; });
    x.addEventListener('click', function () { box.hidden = true; try { localStorage.setItem('pwa_x', String(Date.now())); } catch (e) {} });
  })();

  // ---- Cover images: slide / fade, dots, swipe, auto-play
  (function () {
    var c = document.getElementById('cover'); if (!c) return;
    var slides = c.querySelectorAll('.cv-slide'), n = slides.length; if (n < 2) return;
    var track = c.querySelector('.cv-track'), dots = c.querySelector('.cv-dots'), fade = c.classList.contains('cv-fade'), rtl = getComputedStyle(root).direction === 'rtl', i = 0, timer = null;
    function go(k) {
      i = (k + n) % n;
      if (fade) slides.forEach(function (s, x) { s.classList.toggle('on', x === i); }); else track.style.transform = 'translateX(' + ((rtl ? 1 : -1) * i * 100) + '%)';
      if (dots) Array.prototype.forEach.call(dots.children, function (d, x) { d.classList.toggle('on', x === i); });
    }
    if (dots) for (var x = 0; x < n; x++) (function (x) { var b = document.createElement('button'); b.type = 'button'; b.setAttribute('aria-label', (x + 1) + ' / ' + n); b.addEventListener('click', function () { go(x); restart(); }); dots.appendChild(b); })(x);
    function stop() { if (timer) { clearInterval(timer); timer = null; } }
    function start() { if (c.dataset.auto === '1' && !still) timer = setInterval(function () { go(i + 1); }, Math.max(2, parseInt(c.dataset.interval, 10) || 4) * 1000); }
    function restart() { stop(); start(); }
    var x0 = null;
    c.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; stop(); }, { passive: true });
    c.addEventListener('touchend', function (e) { if (x0 !== null) { var dx = e.changedTouches[0].clientX - x0; if (Math.abs(dx) > 40) go(((dx < 0) !== rtl) ? i + 1 : i - 1); } x0 = null; start(); }, { passive: true });
    go(0); start();
  })();

  // ---- Typewriter animation (shop name / slogan); with "repeat forever" it types, waits, erases and starts again
  document.querySelectorAll('.an-typewriter').forEach(function (el) {
    if (still || el.children.length || el.tagName === 'IMG' || el.classList.contains('profile') || el.classList.contains('cover')) return;
    var text = el.textContent.trim(); if (!text) return;
    var chars = Array.from(text), loop = el.classList.contains('an-rep'); el.setAttribute('aria-label', text); el.style.minHeight = el.offsetHeight + 'px'; el.textContent = '';
    function run() {
      var i = 0; el.classList.add('tw-caret');
      var t = setInterval(function () {
        el.textContent = chars.slice(0, ++i).join('');
        if (i >= chars.length) {
          clearInterval(t);
          if (loop) setTimeout(function () { var k = chars.length, e2 = setInterval(function () { el.textContent = chars.slice(0, --k).join(''); if (k <= 0) { clearInterval(e2); setTimeout(run, 500); } }, 25); }, 2600);
          else setTimeout(function () { el.classList.remove('tw-caret'); }, 1500);
        }
      }, 55);
    }
    onLoaded(run);
  });
})();
