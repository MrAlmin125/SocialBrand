// Saved tracking links: QR code (with the white margin scanners need), download, copy, quick-fill buttons
(function () {
  document.querySelectorAll('.quick').forEach(function (b) {
    b.addEventListener('click', function () {
      document.getElementById('tl_name').value = b.dataset.name;
      document.getElementById('tl_source').value = b.dataset.source;
      document.getElementById('tl_medium').value = b.dataset.medium;
    });
  });
  var list = document.getElementById('qrlist');
  if (!list || !list.querySelector('.qrcard')) return;
  function qrCanvas(text) {
    var tmp = document.createElement('div'); tmp.style.cssText = 'position:absolute;left:-9999px;top:0';
    document.body.appendChild(tmp);
    new QRCode(tmp, { text: text, width: 256, height: 256, correctLevel: QRCode.CorrectLevel.M });
    var src = tmp.querySelector('canvas'), m = 32, c = document.createElement('canvas');
    c.width = c.height = 256 + m * 2;
    var x = c.getContext('2d'); x.fillStyle = '#fff'; x.fillRect(0, 0, c.width, c.height); x.drawImage(src, m, m);
    document.body.removeChild(tmp);
    return c;
  }
  function init() {
    list.querySelectorAll('.qrcard').forEach(function (card) {
      var url = card.dataset.url, box = card.querySelector('.qr'), inp = card.querySelector('input'), c = qrCanvas(url);
      box.appendChild(c); inp.addEventListener('focus', function () { inp.select(); });
      card.querySelector('.dl').addEventListener('click', function () { var a = document.createElement('a'); a.download = 'qr-' + card.dataset.file + '.png'; a.href = c.toDataURL('image/png'); a.click(); });
      var cp = card.querySelector('.cp');
      cp.addEventListener('click', function () {
        var done = function () { var old = cp.textContent; cp.textContent = list.dataset.copied; setTimeout(function () { cp.textContent = old; }, 1500); };
        if (navigator.clipboard) navigator.clipboard.writeText(url).then(done); else { inp.select(); document.execCommand('copy'); done(); }
      });
    });
  }
  function fail() { var p = document.createElement('p'); p.className = 'err'; p.textContent = list.dataset.fail; list.parentNode.insertBefore(p, list); }
  if (window.QRCode) init();
  else {
    var sc = document.createElement('script'); sc.src = 'https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js';
    sc.onload = function () { window.QRCode ? init() : fail(); }; sc.onerror = fail; document.head.appendChild(sc);
  }
})();
