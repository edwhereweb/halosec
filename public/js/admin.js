/**
 * Admin conveniences. Progressive enhancement only: every page works without this file.
 * - [data-tip] tooltips for chart marks (hover and keyboard focus)
 * - "/" focuses the inbox search
 * - [data-autosubmit] forms submit when a select or date changes
 * - Inbox bulk selection: select-all, live count, enable the action bar
 * - [data-copy] buttons copy text to the clipboard
 * - [data-confirm] buttons ask before submitting
 */
(function () {
  'use strict';

  // Tooltips
  var tip = document.getElementById('admin-tip');
  if (tip) {
    var show = function (el, x, y) {
      tip.textContent = el.getAttribute('data-tip');
      tip.classList.remove('hidden');
      var r = tip.getBoundingClientRect();
      var left = Math.min(window.innerWidth - r.width - 8, Math.max(8, x - r.width / 2));
      var top = y - r.height - 12;
      if (top < 8) { top = y + 16; }
      tip.style.left = left + 'px';
      tip.style.top = top + 'px';
    };
    var hide = function () { tip.classList.add('hidden'); };
    document.addEventListener('mousemove', function (ev) {
      var el = ev.target.closest && ev.target.closest('[data-tip]');
      if (el) { show(el, ev.clientX, ev.clientY); } else { hide(); }
    });
    document.addEventListener('focusin', function (ev) {
      var el = ev.target.closest && ev.target.closest('[data-tip]');
      if (el) {
        var r = el.getBoundingClientRect();
        show(el, r.left + r.width / 2, r.top);
      }
    });
    document.addEventListener('focusout', hide);
    document.addEventListener('scroll', hide, { passive: true });
  }

  // "/" focuses search (unless typing somewhere already)
  var search = document.querySelector('[data-search]');
  document.addEventListener('keydown', function (ev) {
    if (ev.key !== '/' || ev.ctrlKey || ev.metaKey || ev.altKey) { return; }
    var t = ev.target;
    if (t && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.tagName === 'SELECT' || t.isContentEditable)) { return; }
    if (search) { ev.preventDefault(); search.focus(); search.select(); }
  });

  // Auto-submit filter forms
  document.querySelectorAll('form[data-autosubmit]').forEach(function (form) {
    form.addEventListener('change', function (ev) {
      if (ev.target.matches('select, input[type="date"]')) { form.requestSubmit ? form.requestSubmit() : form.submit(); }
    });
    var apply = form.querySelector('[data-apply]');
    if (apply) { apply.classList.add('sr-only'); }
  });

  // Bulk selection
  var bulk = document.querySelector('[data-bulk]');
  if (bulk) {
    var boxes = Array.prototype.slice.call(document.querySelectorAll('[data-row-check]'));
    var all = document.querySelector('[data-check-all]');
    var counter = bulk.querySelector('[data-selected-count]');
    var buttons = bulk.querySelectorAll('button[name="status"]');
    var update = function () {
      var n = boxes.filter(function (b) { return b.checked; }).length;
      if (counter) { counter.textContent = n === 0 ? 'Select requests to update them together' : n + ' selected'; }
      buttons.forEach(function (b) { b.disabled = n === 0; });
      bulk.classList.toggle('ring-2', n > 0);
      if (all) { all.checked = n > 0 && n === boxes.length; all.indeterminate = n > 0 && n < boxes.length; }
      boxes.forEach(function (b) { var row = b.closest('tr'); if (row) { row.classList.toggle('bg-sky-50', b.checked); } });
    };
    boxes.forEach(function (b) { b.addEventListener('change', update); });
    if (all) { all.addEventListener('change', function () { boxes.forEach(function (b) { b.checked = all.checked; }); update(); }); }
    update();
  }

  // Copy buttons
  document.querySelectorAll('[data-copy]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var text = btn.getAttribute('data-copy');
      if (!navigator.clipboard) { return; }
      navigator.clipboard.writeText(text).then(function () {
        var label = btn.textContent;
        btn.textContent = 'Copied';
        setTimeout(function () { btn.textContent = label; }, 1400);
      });
    });
  });

  // Confirm destructive actions
  document.addEventListener('click', function (ev) {
    var el = ev.target.closest && ev.target.closest('[data-confirm]');
    if (el && !window.confirm(el.getAttribute('data-confirm'))) { ev.preventDefault(); }
  });

  // Close the mobile menu when a link inside it is used
  document.querySelectorAll('details[data-menu] a').forEach(function (a) {
    a.addEventListener('click', function () { a.closest('details').removeAttribute('open'); });
  });
}());
