/* HaloSec site enhancements. Everything here is optional: pages work without JavaScript. */
(function () {
  'use strict';

  // Scope chips on enquiry forms write a "[Scope: value]" prefix into the message box.
  document.querySelectorAll('[data-prefix-group]').forEach(function (group) {
    var form = group.closest('form');
    var textarea = form ? form.querySelector('textarea[name="message"]') : null;
    var prefix = group.getAttribute('data-prefix') || 'Scope';
    var chips = group.querySelectorAll('[data-chip]');
    if (!textarea) return;
    var pattern = new RegExp('^\\[' + prefix.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ': [^\\]]+\\]\\s*');
    chips.forEach(function (chip) {
      chip.addEventListener('click', function () {
        chips.forEach(function (c) { c.setAttribute('aria-pressed', c === chip ? 'true' : 'false'); });
        var rest = textarea.value.replace(pattern, '');
        textarea.value = '[' + prefix + ': ' + chip.getAttribute('data-chip') + '] ' + rest;
      });
    });
  });

  // "Choose this plan" buttons select the matching chip and jump to the enquiry form.
  document.querySelectorAll('[data-pick-chip]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var value = btn.getAttribute('data-pick-chip');
      var chip = document.querySelector('[data-chip="' + value.replace(/"/g, '\\"') + '"]');
      if (chip) chip.click();
      var target = document.getElementById('enquiry');
      if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      var textarea = target ? target.querySelector('textarea') : null;
      if (textarea) setTimeout(function () { textarea.focus({ preventScroll: true }); }, 450);
    });
  });

  // Founder video: click-to-load, so YouTube is only contacted after the visitor presses play.
  // Without JS the placeholder is a plain link to the video on YouTube.
  function loadFounderVideo(startSeconds) {
    var existing = document.getElementById('founder-video-iframe');
    if (existing) return existing;
    var link = document.getElementById('founder-video');
    var id = link ? link.getAttribute('data-video-id') : '';
    if (!link || !/^[a-zA-Z0-9_-]{11}$/.test(id || '')) return null;
    var iframe = document.createElement('iframe');
    iframe.id = 'founder-video-iframe';
    iframe.className = 'absolute inset-0 h-full w-full';
    iframe.title = 'Founder security audit briefing';
    iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
    iframe.allowFullscreen = true;
    iframe.src = 'https://www.youtube-nocookie.com/embed/' + id + '?enablejsapi=1&rel=0&modestbranding=1&autoplay=1'
      + (startSeconds ? '&start=' + startSeconds : '');
    link.replaceWith(iframe);
    return null; // freshly created: it starts at startSeconds on its own
  }
  var videoLink = document.getElementById('founder-video');
  if (videoLink) {
    videoLink.addEventListener('click', function (event) {
      event.preventDefault();
      loadFounderVideo(0);
    });
  }

  // Founder video chapters seek the embedded YouTube player (loading it first if needed).
  document.querySelectorAll('[data-seek]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var seconds = parseInt(btn.getAttribute('data-seek'), 10) || 0;
      var iframe = loadFounderVideo(seconds);
      if (!iframe || !iframe.contentWindow) return;
      iframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'seekTo', args: [seconds, true] }), '*');
      iframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'playVideo', args: [] }), '*');
    });
  });

  // Close the mobile menu when a link inside it is chosen or the user clicks elsewhere.
  var menu = document.querySelector('[data-mobile-menu]');
  if (menu) {
    document.addEventListener('click', function (event) {
      if (menu.open && !menu.contains(event.target)) menu.open = false;
    });
  }
})();
