// Preloader: hidden when the page has loaded (works on every backend page)
(function () {
  var t0 = Date.now();
  function done() { setTimeout(function () { document.documentElement.classList.add('loaded'); document.dispatchEvent(new Event('kpre')); }, Math.max(0, 350 - (Date.now() - t0))); }
  if (!document.getElementById('preloader')) { document.documentElement.classList.add('loaded'); return; }
  if (document.readyState === 'complete') done(); else window.addEventListener('load', done);
})();
// Hamburger drawer (mobile)
var burger = document.getElementById('burger'), overlay = document.getElementById('overlay');
function setOpen(v) { document.body.classList.toggle('open', v); if (burger) burger.setAttribute('aria-expanded', v ? 'true' : 'false'); }
if (burger) burger.addEventListener('click', function () { setOpen(!document.body.classList.contains('open')); });
if (overlay) overlay.addEventListener('click', function () { setOpen(false); });
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
// Delete confirmation
document.addEventListener('submit', function (e) {
  var m = e.target.getAttribute('data-confirm');
  if (m && !confirm(m)) e.preventDefault();
});
// Image preview
document.querySelectorAll('input[type=file][data-preview]').forEach(function (inp) {
  inp.addEventListener('change', function () {
    var img = document.getElementById(inp.dataset.preview);
    if (img && inp.files[0]) { img.src = URL.createObjectURL(inp.files[0]); img.style.display = 'block'; }
  });
});
// Live "online now" counter
(function () {
  var el = document.getElementById('online');
  if (!el) return;
  var list = document.getElementById('live-list');
  function flag(cc) { return /^[A-Za-z]{2}$/.test(cc) ? String.fromCodePoint(127397 + cc.toUpperCase().charCodeAt(0), 127397 + cc.toUpperCase().charCodeAt(1)) : ''; }
  function poll() {
    fetch('online.php', { credentials: 'same-origin', cache: 'no-store' })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        el.textContent = d.online;
        if (!list) return;
        list.textContent = '';
        d.visitors.forEach(function (v) {
          var li = document.createElement('li'), f = document.createElement('span'), t = document.createElement('span'), dv = document.createElement('span');
          f.className = 'fl'; f.textContent = flag(v.country_code) || '\u{1F310}';
          t.textContent = [v.city, v.country].filter(Boolean).join(', ') || list.dataset.unknown;
          dv.className = 'dv'; dv.textContent = [v.ip, v.label || v.device].filter(Boolean).join(' · ');
          li.appendChild(f); li.appendChild(t); li.appendChild(dv); list.appendChild(li);
        });
      })
      .catch(function () {});
  }
  setInterval(poll, 10000);
  poll();
})();

// Statistics: blocks appear one after the other (slow), then their bars grow and the numbers count up
(function () {
  var blocks = document.querySelectorAll('.aniblock'); if (!blocks.length) return;
  var root = document.documentElement, still = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function countUp(box) {
    box.querySelectorAll('.stat b').forEach(function (el, i) {
      var n = parseInt(el.dataset.n, 10); if (isNaN(n) || n <= 0) return;
      setTimeout(function () { var t0 = null; (function step(ts) { if (!t0) t0 = ts; var p = Math.min(1, (ts - t0) / 1800); el.textContent = Math.round(n * (1 - Math.pow(1 - p, 3))); if (p < 1) requestAnimationFrame(step); })(performance.now()); }, i * 250);
    });
  }
  document.querySelectorAll('.stat b').forEach(function (el) { el.dataset.n = parseInt(el.textContent, 10) || 0; if (!still && el.dataset.n > 0) el.textContent = '0'; });
  function reveal(el, delay) { setTimeout(function () { el.classList.add('go'); if (el.classList.contains('stats') && !still) countUp(el); }, delay); }
  function start() {
    if (still || !('IntersectionObserver' in window)) { blocks.forEach(function (b) { b.classList.add('go'); }); return; }
    var first = true, n = 0;
    var io = new IntersectionObserver(function (entries) {
      var vis = entries.filter(function (e) { return e.isIntersecting; }).sort(function (a, b) { return a.target.compareDocumentPosition(b.target) & 2 ? 1 : -1; });
      vis.forEach(function (e) { io.unobserve(e.target); reveal(e.target, first ? n++ * 500 : 0); });
      first = false;
    }, { threshold: 0.1 });
    blocks.forEach(function (b) { io.observe(b); });
  }
  if (root.classList.contains('loaded')) start(); else document.addEventListener('kpre', start, { once: true });
})();
