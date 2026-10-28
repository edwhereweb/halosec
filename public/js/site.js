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

  // Stepped forms (free audit): one step at a time with a progress bar.
  document.querySelectorAll('form[data-stepper]').forEach(function (form) {
    var steps = Array.prototype.slice.call(form.querySelectorAll('[data-step]'));
    if (steps.length < 2) return;
    var label = document.querySelector('[data-step-label]');
    var bar = document.querySelector('[data-step-bar]');
    // After a failed submit, open the first step that has an error.
    var current = 0;
    for (var s = 0; s < steps.length; s++) {
      if (steps[s].querySelector('[aria-invalid="true"], [data-has-error]')) { current = s; break; }
    }

    function show(i) {
      current = i;
      steps.forEach(function (s, n) { s.classList.toggle('is-active', n === i); });
      if (label) label.textContent = 'Step ' + (i + 1) + ' of ' + steps.length + ' · ' + steps[i].getAttribute('data-step');
      if (bar) bar.style.width = Math.round(((i + 1) / steps.length) * 100) + '%';
    }

    function valid(step) {
      var ok = true;
      var seen = {};
      step.querySelectorAll('[required]').forEach(function (input) {
        if (input.type === 'radio') {
          if (seen[input.name]) return;
          seen[input.name] = true;
          if (!form.querySelector('input[name="' + input.name + '"]:checked')) ok = false;
        } else if (!input.value.trim()) {
          ok = false;
        }
      });
      var msg = step.querySelector('[data-step-error]');
      if (msg) msg.hidden = ok;
      return ok;
    }

    form.addEventListener('click', function (event) {
      var next = event.target.closest('[data-next]');
      var prev = event.target.closest('[data-prev]');
      if (next) {
        event.preventDefault();
        if (valid(steps[current]) && current < steps.length - 1) {
          show(current + 1);
          form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      } else if (prev) {
        event.preventDefault();
        if (current > 0) show(current - 1);
      }
    });

    form.classList.add('is-stepped');
    show(current);
  });

  // Founder video chapters seek the embedded YouTube player.
  document.querySelectorAll('[data-seek]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var iframe = document.getElementById('founder-video-iframe');
      if (!iframe || !iframe.contentWindow) return;
      var seconds = parseInt(btn.getAttribute('data-seek'), 10) || 0;
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
