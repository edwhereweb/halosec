/**
 * Free audit check-up. Turns the long audit form into one question at a time with instant feedback, a live
 * score ring, a results reveal (score, strengths, gaps) and then the contact form. Without this file the
 * page is a normal form, and the server scores and validates exactly the same fields either way.
 */
(function () {
  'use strict';

  var root = document.querySelector('[data-audit]');
  if (!root) { return; }
  var questions = Array.prototype.slice.call(root.querySelectorAll('[data-q]'));
  if (questions.length === 0) { return; }

  var header = root.querySelector('[data-audit-header]');
  var stepText = root.querySelector('[data-audit-step]');
  var dots = Array.prototype.slice.call(root.querySelectorAll('[data-audit-dot]'));
  var ring = root.querySelector('[data-audit-ring]');
  var ringArc = root.querySelector('[data-ring-arc]');
  var ringValue = root.querySelector('[data-ring-value]');
  var results = root.querySelector('[data-audit-results]');
  var details = root.querySelector('[data-details]');
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var POINTS = { yes: 2, partial: 1, no: 0, unsure: 0 };
  var ANSWER_LABEL = { yes: 'Yes', partial: 'Partly', no: 'No', unsure: 'Not sure' };
  var LINES = {
    yes: ['Nice. That’s a real strength.', 'Good. That closes a common door.', 'Great. Attackers hate that one.'],
    partial: ['Partly covered. Your report will show what’s missing.', 'A good start. We’ll point out the gaps.'],
    no: ['Common gap. We’ll show you a practical fix.', 'You’re not alone. This is often a quick win.'],
    unsure: ['No problem. We’ll check this for you.', 'That’s fine. Finding out is what the audit is for.']
  };
  var RING = 175.9;   // 2πr for the small ring (r = 28)
  var BIG_RING = 527.8; // 2πr for the results ring (r = 84)
  var current = 0;
  var view = 'q';
  var timer = null;

  function answerOf(q) {
    var checked = q.querySelector('input[type="radio"]:checked');
    return checked ? checked.value : null;
  }

  // Same formula as the server: yes = 2, partially = 1, otherwise 0, out of every question.
  function finalScore() {
    var points = 0;
    questions.forEach(function (q) { points += POINTS[answerOf(q)] || 0; });
    return Math.round(points / (questions.length * 2) * 100);
  }

  // Running score over the questions answered so far, for the live ring.
  function runningScore() {
    var points = 0;
    var answered = 0;
    questions.forEach(function (q) {
      var a = answerOf(q);
      if (a) { points += POINTS[a] || 0; answered++; }
    });
    return answered === 0 ? null : Math.round(points / (answered * 2) * 100);
  }

  function band(score) {
    if (score < 40) {
      return { name: 'Weak', color: '#b42318', cls: 'bg-red-100 text-red-900', title: 'Your business is exposed',
        text: 'Attackers look for exactly these gaps. The good news: most fixes are quick and affordable.' };
    }
    if (score < 70) {
      return { name: 'Fair', color: '#d97706', cls: 'bg-amber-100 text-amber-900', title: 'A solid start, with gaps',
        text: 'You have some good basics in place. A few common gaps could still let an attacker in.' };
    }
    return { name: 'Strong', color: '#15803d', cls: 'bg-emerald-100 text-emerald-900', title: 'Strong foundations',
      text: 'You are ahead of most businesses. Your report will focus on fine-tuning and staying there.' };
  }

  function firstUnanswered() {
    for (var i = 0; i < questions.length; i++) {
      if (!answerOf(questions[i])) { return i; }
    }
    return -1;
  }

  function updateHeader() {
    dots.forEach(function (dot, i) {
      var a = answerOf(questions[i]);
      var colour = 'bg-slate-200';
      if (a === 'yes') { colour = 'bg-emerald-500'; }
      else if (a === 'partial') { colour = 'bg-amber-400'; }
      else if (a === 'no') { colour = 'bg-red-500'; }
      else if (a === 'unsure') { colour = 'bg-slate-400'; }
      if (view === 'q' && i === current) { colour += ' ring-2 ring-brand-ink ring-offset-2'; }
      dot.className = 'h-2 flex-1 rounded-full transition-colors duration-300 ' + colour;
    });
    var score = runningScore();
    if (score === null) {
      ringValue.textContent = '–';
      ringArc.style.strokeDashoffset = RING;
      ring.setAttribute('aria-label', 'Security score so far: not started');
    } else {
      ringValue.textContent = score;
      ringArc.style.strokeDashoffset = RING * (1 - score / 100);
      ringArc.style.stroke = band(score).color;
      ring.setAttribute('aria-label', 'Security score so far: ' + score + ' out of 100');
    }
    var tip = root.querySelector('[data-audit-tip]');
    if (tip) { tip.style.visibility = view === 'q' ? '' : 'hidden'; }
    if (view === 'q') {
      stepText.textContent = 'Question ' + (current + 1) + ' of ' + questions.length + ' · ' + questions[current].getAttribute('data-area');
    } else if (view === 'results') {
      stepText.textContent = 'Your results';
    } else {
      stepText.textContent = 'Last step: where to send your report';
    }
  }

  function bringIntoView() {
    var top = root.getBoundingClientRect().top;
    if (top < 0 || top > window.innerHeight * 0.4) {
      root.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
    }
  }

  function showQuestion(i, focus, backwards) {
    clearTimeout(timer);
    view = 'q';
    current = Math.max(0, Math.min(questions.length - 1, i));
    results.hidden = true;
    details.classList.remove('is-current');
    questions.forEach(function (q, n) {
      q.classList.toggle('is-current', n === current);
      q.classList.toggle('is-back', n === current && !!backwards);
    });
    var q = questions[current];
    q.querySelector('[data-audit-back]').disabled = current === 0;
    q.querySelector('[data-audit-skip]').hidden = !answerOf(q);
    updateHeader();
    if (focus) {
      bringIntoView();
      q.querySelector('[data-q-text]').focus({ preventScroll: true });
    }
  }

  function listItem(title, tag, tagClass) {
    var li = document.createElement('li');
    li.className = 'flex items-center justify-between gap-3 rounded-xl bg-brand-mist px-4 py-2.5 text-[15px] font-semibold text-brand-ink';
    var name = document.createElement('span');
    name.textContent = title;
    li.appendChild(name);
    if (tag) {
      var t = document.createElement('span');
      t.className = 'shrink-0 rounded-full px-2 py-0.5 text-xs font-bold ' + tagClass;
      t.textContent = tag;
      li.appendChild(t);
    }
    return li;
  }

  function showResults(animate) {
    clearTimeout(timer);
    view = 'results';
    questions.forEach(function (q) { q.classList.remove('is-current'); });
    details.classList.remove('is-current');
    results.hidden = false;

    var score = finalScore();
    var b = band(score);
    var arc = results.querySelector('[data-result-arc]');
    var scoreEl = results.querySelector('[data-result-score]');
    var bandEl = results.querySelector('[data-result-band]');
    bandEl.className = 'mx-auto w-fit rounded-full px-3 py-1 text-sm font-bold md:mx-0 ' + b.cls;
    bandEl.textContent = b.name;
    results.querySelector('[data-result-title]').textContent = b.title;
    results.querySelector('[data-result-text]').textContent = b.text;
    arc.style.stroke = b.color;

    var good = results.querySelector('[data-result-good]');
    var gaps = results.querySelector('[data-result-gaps]');
    good.textContent = '';
    gaps.textContent = '';
    var order = { no: 0, unsure: 1, partial: 2 };
    var gapList = [];
    questions.forEach(function (q) {
      var a = answerOf(q);
      if (a === 'yes') {
        good.appendChild(listItem(q.getAttribute('data-title'), null, ''));
      } else if (a) {
        gapList.push({ title: q.getAttribute('data-title'), answer: a });
      }
    });
    gapList.sort(function (x, y) { return order[x.answer] - order[y.answer]; });
    gapList.forEach(function (g) {
      var cls = g.answer === 'no' ? 'bg-red-100 text-red-900' : (g.answer === 'partial' ? 'bg-amber-100 text-amber-900' : 'bg-slate-200 text-slate-700');
      gaps.appendChild(listItem(g.title, ANSWER_LABEL[g.answer], cls));
    });
    if (!good.children.length) { good.appendChild(listItem('Nothing yet. That’s exactly what we’re here for.', null, '')); }
    if (!gaps.children.length) { gaps.appendChild(listItem('No obvious gaps. Impressive.', null, '')); }

    var target = BIG_RING * (1 - score / 100);
    if (animate && !reduceMotion) {
      arc.style.transition = 'none';
      arc.style.strokeDashoffset = BIG_RING;
      scoreEl.textContent = '0';
      // Let the browser paint the empty ring, then sweep it and count up together.
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          arc.style.transition = '';
          arc.style.strokeDashoffset = target;
          var start = null;
          var step = function (ts) {
            if (start === null) { start = ts; }
            var t = Math.min(1, (ts - start) / 1400);
            var eased = 1 - Math.pow(1 - t, 3);
            scoreEl.textContent = Math.round(score * eased);
            if (t < 1) { requestAnimationFrame(step); }
          };
          requestAnimationFrame(step);
        });
      });
    } else {
      arc.style.strokeDashoffset = target;
      scoreEl.textContent = score;
    }
    updateHeader();
    bringIntoView();
    results.querySelector('[data-result-title]').focus({ preventScroll: true });
  }

  function showDetails(focus) {
    clearTimeout(timer);
    view = 'details';
    questions.forEach(function (q) { q.classList.remove('is-current'); });
    results.hidden = true;
    details.classList.add('is-current');
    updateHeader();
    if (focus) {
      bringIntoView();
      var invalid = details.querySelector('[aria-invalid="true"]');
      (invalid || details.querySelector('input:not([type="hidden"])')).focus({ preventScroll: true });
    }
  }

  // Answering: feedback, header update, then move on.
  root.addEventListener('change', function (event) {
    var input = event.target;
    if (!input.matches || !input.matches('[data-q] input[type="radio"]')) { return; }
    var q = input.closest('[data-q]');
    var index = questions.indexOf(q);
    var lines = LINES[input.value] || LINES.unsure;
    var feedback = q.querySelector('[data-feedback]');
    feedback.textContent = lines[Math.floor(Math.random() * lines.length)];
    feedback.classList.remove('audit-pop');
    void feedback.offsetWidth; // restart the pop animation
    feedback.classList.add('audit-pop');
    q.querySelector('[data-audit-skip]').hidden = false;
    current = index;
    updateHeader();
    clearTimeout(timer);
    timer = setTimeout(function () {
      if (index < questions.length - 1 && !answerOf(questions[index + 1])) {
        showQuestion(index + 1, true);
      } else {
        var next = firstUnanswered();
        if (next === -1) { showResults(true); } else if (next !== index) { showQuestion(next, true); }
      }
    }, reduceMotion ? 300 : 750);
  });

  root.addEventListener('click', function (event) {
    var el = event.target.closest('button, a');
    if (!el) { return; }
    if (el.hasAttribute('data-audit-back')) {
      showQuestion(current - 1, true, true);
    } else if (el.hasAttribute('data-audit-skip')) {
      if (current < questions.length - 1) { showQuestion(current + 1, true); } else if (firstUnanswered() === -1) { showResults(true); }
    } else if (el.hasAttribute('data-audit-to-form')) {
      showDetails(true);
    } else if (el.hasAttribute('data-audit-retake')) {
      showQuestion(0, true, true);
    } else if (el.hasAttribute('data-audit-to-results')) {
      showResults(false);
    }
  });

  var startLink = document.querySelector('[data-audit-start]');
  if (startLink) {
    startLink.addEventListener('click', function (event) {
      event.preventDefault();
      if (view === 'q') { showQuestion(current, true); } else { bringIntoView(); }
    });
  }

  // Keyboard: 1–4 answer, ← back, → next (when already answered).
  document.addEventListener('keydown', function (event) {
    if (view !== 'q' || event.ctrlKey || event.metaKey || event.altKey) { return; }
    var t = event.target;
    if (t && (t.tagName === 'TEXTAREA' || t.tagName === 'SELECT' || (t.tagName === 'INPUT' && t.type !== 'radio'))) { return; }
    var rect = root.getBoundingClientRect();
    if (rect.bottom < 0 || rect.top > window.innerHeight) { return; }
    var q = questions[current];
    if (/^[1-4]$/.test(event.key)) {
      var inputs = q.querySelectorAll('input[type="radio"]');
      var input = inputs[parseInt(event.key, 10) - 1];
      if (input) {
        event.preventDefault();
        input.checked = true;
        input.dispatchEvent(new Event('change', { bubbles: true }));
        input.focus({ preventScroll: true });
      }
    } else if (event.key === 'ArrowLeft' && t && t.type !== 'radio' && current > 0) {
      showQuestion(current - 1, true, true);
    } else if (event.key === 'ArrowRight' && t && t.type !== 'radio' && answerOf(q)) {
      if (current < questions.length - 1) { showQuestion(current + 1, true); } else if (firstUnanswered() === -1) { showResults(true); }
    }
  });

  // Start: quiz mode, at the question or step the server asked for (e.g. after a failed submit).
  root.classList.add('is-quiz');
  header.hidden = false;
  var start = root.getAttribute('data-start') || 'q:0';
  if (start === 'details' && firstUnanswered() === -1) {
    showDetails(true);
  } else {
    var i = parseInt(start.split(':')[1] || '0', 10);
    showQuestion(isNaN(i) ? 0 : i, start !== 'q:0');
  }
}());
