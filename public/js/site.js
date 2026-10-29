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

  // Guides: reading progress bar across the top of the page.
  var progress = document.querySelector('[data-read-progress]');
  var article = document.querySelector('[data-article]');
  if (progress && article) {
    var ticking = false;
    var updateProgress = function () {
      var rect = article.getBoundingClientRect();
      var total = rect.height - window.innerHeight;
      var done = total > 0 ? Math.min(1, Math.max(0, -rect.top / total)) : 1;
      progress.style.transform = 'scaleX(' + done + ')';
      ticking = false;
    };
    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; window.requestAnimationFrame(updateProgress); }
    }, { passive: true });
    updateProgress();
  }

  // Guides: highlight the section being read in the "On this page" menus.
  var tocLinks = document.querySelectorAll('.toc-link');
  if (tocLinks.length) {
    var sections = [];
    tocLinks.forEach(function (link) {
      var target = document.getElementById(link.getAttribute('href').slice(1));
      if (target && sections.indexOf(target) === -1) sections.push(target);
    });
    var spyQueued = false;
    var updateToc = function () {
      // The current section is the last heading that has scrolled past the upper third of the screen.
      var current = sections[0];
      sections.forEach(function (s) { if (s.getBoundingClientRect().top < window.innerHeight / 3) current = s; });
      tocLinks.forEach(function (link) { link.setAttribute('aria-current', current && link.getAttribute('href') === '#' + current.id ? 'true' : 'false'); });
      spyQueued = false;
    };
    window.addEventListener('scroll', function () {
      if (!spyQueued) { spyQueued = true; window.requestAnimationFrame(updateToc); }
    }, { passive: true });
    updateToc();
  }

  // Fade items up as they scroll into view. Only items still below the fold wait, so nothing visible flickers.
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var revealItems = document.querySelectorAll('[data-reveal], .prose-guide > :is(h2, figure, table, .step, .callout, .callout-red)');
  if (revealItems.length && !reduceMotion && 'IntersectionObserver' in window) {
    var reveal = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('reveal-in');
        entry.target.classList.remove('reveal-wait');
        reveal.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -8% 0px' });
    revealItems.forEach(function (el) {
      if (el.getBoundingClientRect().top > window.innerHeight) {
        el.classList.add('reveal-wait');
        reveal.observe(el);
      }
    });
  }

  // Guides index: topic filter chips (hidden without JS, when every guide simply shows).
  var filter = document.querySelector('[data-guide-filter]');
  if (filter) {
    filter.classList.remove('hidden');
    filter.classList.add('flex');
    var filterButtons = filter.querySelectorAll('[data-filter]');
    filterButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var topic = btn.getAttribute('data-filter');
        filterButtons.forEach(function (b) { b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'); });
        document.querySelectorAll('[data-category]').forEach(function (item) {
          item.classList.toggle('hidden', topic !== '' && item.getAttribute('data-category') !== topic);
        });
      });
    });
  }

  // Close the mobile menu when a link inside it is chosen or the user clicks elsewhere.
  var menu = document.querySelector('[data-mobile-menu]');
  if (menu) {
    document.addEventListener('click', function (event) {
      if (menu.open && !menu.contains(event.target)) menu.open = false;
    });
  }
})();
