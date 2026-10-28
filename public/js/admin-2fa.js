/**
 * Draws the 2FA setup QR code on /admin/account.
 * The otpauth:// link (which contains the secret) never leaves this page: the QR is drawn in the browser
 * by the self-hosted qrcode-generator library. Without JavaScript the manual setup key still works.
 */
(function () {
  'use strict';
  var box = document.querySelector('[data-otpauth]');
  if (!box || typeof window.qrcode !== 'function') {
    return;
  }
  try {
    var qr = window.qrcode(0, 'M');
    qr.addData(box.getAttribute('data-otpauth'));
    qr.make();
    box.innerHTML = qr.createSvgTag({ cellSize: 4, margin: 2, scalable: true });
    var svg = box.querySelector('svg');
    if (svg) {
      svg.setAttribute('role', 'img');
      svg.setAttribute('aria-label', 'QR code for setting up two-factor authentication');
      svg.setAttribute('class', 'h-full w-full');
    }
    box.hidden = false;
    // The QR worked, so tuck the manual key away (it stays one tap away).
    var fallback = document.querySelector('[data-otpauth-fallback]');
    if (fallback) {
      fallback.removeAttribute('open');
    }
  } catch (e) {
    // Leave the QR hidden; the manual key below is the fallback.
  }
}());
