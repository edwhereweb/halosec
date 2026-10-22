<?php
declare(strict_types=1);

/** @var HaloSec\View $this */
$e = static fn (mixed $v): string => HaloSec\View::e($v);

$title = $service['title'] ?? 'A Cybersecurity Team Built for Small & Mid-Sized Businesses';
$lead = 'Hiring a full-time, in-house security team is way too expensive for most growing businesses. Halosec offers flexible Security Team Outsourcing—hire certified cybersecurity experts tailored to your exact scope and budget, only when you need them.';
?>

<?= $this->include('partials/page_header', [
    'eyebrow' => 'Flexible Fractional Expertise · SMB & Mid-Market',
    'heading' => 'A Cybersecurity Team Built for Small & Mid-Sized Businesses',
    'lead' => $lead,
]) ?>

<!-- Top Split Layout: Core Value Proposition & Direct Talent Enquiry -->
<section class="max-w-6xl mx-auto px-4 pt-14 pb-12 grid gap-10 lg:grid-cols-5 items-start">
  <!-- Left Column: Core Value & Model Details -->
  <div class="lg:col-span-3 space-y-8">

    <!-- Practice Overview -->
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-8 md:p-10 relative overflow-hidden">
      <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-lime-50 border border-lime-200 text-lime-900 text-xs font-bold uppercase tracking-wider">
          <span class="w-2 h-2 rounded-full bg-lime-500"></span>
          Fractional Security Talent
        </span>
        <span class="text-xs font-bold text-slate-500">Hourly &amp; Monthly Retainers</span>
      </div>

      <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
        High-Quality Security Talent Without the Full-Time Overhead
      </h2>

      <div class="mt-4 space-y-4 text-slate-700 leading-relaxed text-base md:text-lg font-medium">
        <p>
          Hiring a full-time, in-house security team is way too expensive for most growing businesses. That’s why Halosec offers <strong class="text-slate-900 font-extrabold">Security Team Outsourcing</strong>—a flexible way to hire certified cybersecurity experts only when you need them.
        </p>
        <p class="text-sm md:text-base text-slate-600">
          You get high-quality security talent tailored to your exact requirements, scope, and budget, without the cost, hiring headache, or long-term commitment of a full-time hire.
        </p>
      </div>

      <!-- Verified Certifications & Quick Jump -->
      <div class="mt-6 flex flex-wrap items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-800 text-xs font-semibold">
          <span class="w-2 h-2 rounded-full bg-sky-600"></span>
          EC-Council CEH Certified
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-800 text-xs font-semibold">
          <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
          CompTIA Security+ / CySA+
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-800 text-xs font-semibold">
          <span class="w-2 h-2 rounded-full bg-red-600"></span>
          Sophos Silver Partner
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-800 text-xs font-semibold">
          <span class="w-2 h-2 rounded-full bg-amber-500"></span>
          Cloud Security (AWS/Azure)
        </span>
        <a href="#enquiry" class="inline-flex items-center gap-1.5 text-xs font-bold text-sky-700 hover:text-sky-900 bg-sky-50 hover:bg-sky-100 border border-sky-200 px-3 py-1 rounded-full transition-all ml-auto">
          <span>Jump to Talent Request</span>
          <span>↓</span>
        </a>
      </div>

      <!-- Quick Highlights Bar -->
      <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Model</p>
          <p class="text-sm font-extrabold text-slate-900 mt-0.5">Flexible Hours</p>
        </div>
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Certifications</p>
          <p class="text-sm font-extrabold text-slate-900 mt-0.5">CEH, CySA+, Cloud</p>
        </div>
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Engagement</p>
          <p class="text-sm font-extrabold text-slate-900 mt-0.5">Hourly / Monthly</p>
        </div>
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Onboarding</p>
          <p class="text-sm font-extrabold text-slate-900 mt-0.5">&lt; 48 Hours</p>
        </div>
      </div>
    </div>

    <!-- Why Choose Us? -->
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-8 md:p-10">
      <div class="mb-6">
        <span class="text-xs font-bold uppercase tracking-widest text-sky-700">Enterprise Talent, SMB Budgets</span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Why Choose Us?</h2>
        <p class="mt-2 text-slate-600 text-sm font-medium">Built from the ground up for businesses that need certified security capability without building an expensive in-house division.</p>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <!-- Certified Engineers -->
        <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-sky-300 transition-all duration-200">
          <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold mb-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
          </div>
          <h3 class="text-base font-extrabold text-slate-900">Certified Engineers</h3>
          <p class="mt-1.5 text-xs text-slate-600 leading-relaxed">
            Get access to experts certified by EC-Council (CEH), CompTIA (Security+, CySA+), major cloud platforms (AWS, Azure, GCP), and vendors like Sophos (Endpoint Security Architecture).
          </p>
        </div>

        <!-- 100% Customized -->
        <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-sky-300 transition-all duration-200">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold mb-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"></path></svg>
          </div>
          <h3 class="text-base font-extrabold text-slate-900">100% Customized</h3>
          <p class="mt-1.5 text-xs text-slate-600 leading-relaxed">
            Choose the exact skills, certifications, and support level your business needs right now—no paying for capabilities you won't use.
          </p>
        </div>

        <!-- Flexible Plans -->
        <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-sky-300 transition-all duration-200">
          <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold mb-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <h3 class="text-base font-extrabold text-slate-900">Flexible Plans</h3>
          <p class="mt-1.5 text-xs text-slate-600 leading-relaxed">
            Pay hourly or set up a monthly engagement with fixed hours—whatever fits your operational workflow and budget cycles.
          </p>
        </div>

        <!-- Scalable Support -->
        <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-sky-300 transition-all duration-200">
          <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold mb-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
          </div>
          <h3 class="text-base font-extrabold text-slate-900">Scalable Support</h3>
          <p class="mt-1.5 text-xs text-slate-600 leading-relaxed">
            Scale up during major rollouts, audits, or busy quarters, and scale down smoothly as operations stabilize.
          </p>
        </div>
      </div>
    </div>

    <!-- How It Works (3 Steps) -->
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-8 md:p-10">
      <div class="mb-6">
        <span class="text-xs font-bold uppercase tracking-widest text-sky-700">Simple Process</span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">How It Works</h2>
        <p class="mt-2 text-slate-600 text-sm font-medium">Getting an expert cybersecurity resource onboarded into your team takes just three simple steps.</p>
      </div>

      <div class="space-y-4">
        <!-- Step 1 -->
        <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-sky-300 transition-all">
          <div class="w-10 h-10 rounded-xl bg-sky-600 text-white font-extrabold text-sm flex items-center justify-center shrink-0 mt-0.5 shadow-sm">1</div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Tell Us What You Need</h3>
            <p class="text-sm text-slate-600 mt-1 leading-relaxed">
              Share your security goals, active tooling, compliance deadlines, or current setup issues.
            </p>
          </div>
        </div>

        <!-- Step 2 -->
        <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-sky-300 transition-all">
          <div class="w-10 h-10 rounded-xl bg-sky-600 text-white font-extrabold text-sm flex items-center justify-center shrink-0 mt-0.5 shadow-sm">2</div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Pick Your Plan</h3>
            <p class="text-sm text-slate-600 mt-1 leading-relaxed">
              Choose the engineer's certification level and pick an hourly or monthly plan that matches your workload.
            </p>
          </div>
        </div>

        <!-- Step 3 -->
        <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-sky-300 transition-all">
          <div class="w-10 h-10 rounded-xl bg-lime-400 text-slate-950 font-extrabold text-sm flex items-center justify-center shrink-0 mt-0.5 shadow-sm">3</div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Get Started</h3>
            <p class="text-sm text-slate-600 mt-1 leading-relaxed">
              Your assigned security expert starts working as part of your team—plugged into your Slack, Jira, or email.
            </p>
          </div>
        </div>
      </div>

      <div class="mt-8 pt-6 border-t border-slate-100 text-sm text-slate-500 flex flex-wrap gap-4 items-center">
        <span>Need guidance on sizing your hours?</span>
        <a class="text-sky-700 font-bold hover:underline" href="/consultation">Book a Consultation →</a>
        <span>·</span>
        <a class="text-slate-700 font-bold hover:underline" href="/services">Browse All Services →</a>
      </div>
    </div>

  </div>

  <!-- Right Column: Sticky Direct Enquiry Form -->
  <div class="lg:col-span-2 lg:sticky lg:top-28 rounded-3xl bg-white border border-slate-200 shadow-xl p-8" id="enquiry">
    <div class="mb-5 pb-4 border-b border-slate-100">
      <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-lime-400/20 text-lime-800 text-[11px] font-bold uppercase tracking-wider mb-2">
        <span class="w-1.5 h-1.5 rounded-full bg-lime-500"></span>
        Direct Enquiry
      </div>
      <h2 class="text-xl font-extrabold text-slate-900">Enquire about security talent</h2>
      <p class="text-xs text-slate-600 mt-1">Tell us what you need. Our team will tailor a profile and engagement plan within 24 hours.</p>
    </div>

    <?= $this->include('partials/flash', ['flash' => $flash]) ?>
    <?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
    <div class="space-y-5">
      <?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors]) ?>

      <!-- Quick Engagement Scope Selector -->
      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
          Estimated Engagement Scope
        </label>
        <div class="grid grid-cols-2 gap-2" id="talent-scope-pills">
          <button type="button" data-scope="10–15 hrs/mo (Starter Triage)" class="talent-pill text-xs font-bold py-2 px-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:border-sky-300 hover:bg-sky-50 transition-all text-center">
            10–15 hrs/mo
          </button>
          <button type="button" data-scope="20–40 hrs/mo (Growth Management)" class="talent-pill text-xs font-bold py-2 px-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:border-sky-300 hover:bg-sky-50 transition-all text-center">
            20–40 hrs/mo
          </button>
          <button type="button" data-scope="60+ hrs/mo (Dedicated / vCISO)" class="talent-pill text-xs font-bold py-2 px-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:border-sky-300 hover:bg-sky-50 transition-all text-center">
            60+ hrs/mo
          </button>
          <button type="button" data-scope="Ad-hoc Project / Audit" class="talent-pill text-xs font-bold py-2 px-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:border-sky-300 hover:bg-sky-50 transition-all text-center">
            Ad-hoc Project
          </button>
        </div>
      </div>

      <?= $this->include('partials/field', [
          'name' => 'message',
          'label' => 'Describe your requirements (e.g. SOC monitoring, endpoint tuning, weekly hours needed)',
          'type' => 'textarea',
          'required' => true,
          'old' => $old,
          'errors' => $errors,
      ]) ?>
      <div class="pt-2">
        <?= $this->include('partials/submit', ['btnLabel' => 'Request Security Talent']) ?>
      </div>
    </div>

    <div class="mt-6 pt-5 border-t border-slate-100 space-y-2 text-xs text-slate-500 font-medium">
      <div class="flex items-center gap-2">
        <span class="text-emerald-600 font-bold">✓</span>
        <span>Hourly or fixed monthly hours available</span>
      </div>
      <div class="flex items-center gap-2">
        <span class="text-emerald-600 font-bold">✓</span>
        <span>Certified engineers (CEH, CompTIA, Cloud, Sophos)</span>
      </div>
      <div class="flex items-center gap-2">
        <span class="text-emerald-600 font-bold">✓</span>
        <span>Seamless integration with your internal team</span>
      </div>
    </div>
  </div>
</section>

<!-- Recommended Engagement Tiers for SMBs -->
<section class="max-w-6xl mx-auto px-4 py-8" aria-label="Engagement plans">
  <div class="text-center max-w-3xl mx-auto mb-10">
    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-lime-50 border border-lime-200 text-lime-900 text-xs font-bold uppercase tracking-wider mb-2">
      <span class="w-2 h-2 rounded-full bg-lime-500"></span>
      Engagement Sizing Guide
    </div>
    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Recommended Engagement Tiers for SMBs</h2>
    <p class="mt-2 text-slate-600 text-sm">Choose the tier that matches your team's operational rhythm, or talk to us for custom hours.</p>
  </div>

  <div class="grid md:grid-cols-3 gap-6">
    <!-- Tier 1 -->
    <div class="rounded-3xl bg-white border border-slate-200 p-7 shadow-sm hover:shadow-xl hover:border-sky-300 transition-all flex flex-col justify-between">
      <div>
        <span class="text-xs font-bold uppercase tracking-widest text-sky-700">Tier 1 · Starter</span>
        <h3 class="text-xl font-extrabold text-slate-900 mt-1">10–15 hrs / month</h3>
        <p class="mt-2 text-xs text-slate-600 leading-relaxed">Best for businesses with 10–50 systems that need recurring posture health checks without a full-time hire.</p>
        <ul class="mt-5 space-y-2.5 text-xs text-slate-700">
          <li class="flex items-center gap-2"><span class="text-emerald-600 font-bold">✓</span> Weekly SOC &amp; alert log triage</li>
          <li class="flex items-center gap-2"><span class="text-emerald-600 font-bold">✓</span> Monthly policy &amp; patch audits</li>
          <li class="flex items-center gap-2"><span class="text-emerald-600 font-bold">✓</span> Incident standby response</li>
        </ul>
      </div>
      <button type="button" data-tier="Tier 1: Starter (10–15 hrs/month)" class="tier-select-btn mt-6 w-full text-center py-2.5 px-4 rounded-xl border border-slate-300 hover:border-slate-900 bg-slate-50 hover:bg-slate-900 hover:text-white text-xs font-extrabold transition-all">
        Select Starter Tier
      </button>
    </div>

    <!-- Tier 2 -->
    <div class="rounded-3xl bg-white border-2 border-sky-500 p-7 shadow-md hover:shadow-xl transition-all flex flex-col justify-between relative overflow-hidden">
      <div class="absolute top-0 right-0 bg-sky-600 text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-bl-xl">
        Most Popular
      </div>
      <div>
        <span class="text-xs font-bold uppercase tracking-widest text-sky-700">Tier 2 · Growth</span>
        <h3 class="text-xl font-extrabold text-slate-900 mt-1">20–40 hrs / month</h3>
        <p class="mt-2 text-xs text-slate-600 leading-relaxed">For software/SaaS teams, fintechs, or firms handling sensitive client financial or health data.</p>
        <ul class="mt-5 space-y-2.5 text-xs text-slate-700">
          <li class="flex items-center gap-2"><span class="text-emerald-600 font-bold">✓</span> DevSecOps &amp; app security testing</li>
          <li class="flex items-center gap-2"><span class="text-emerald-600 font-bold">✓</span> Active endpoint &amp; USB rule push</li>
          <li class="flex items-center gap-2"><span class="text-emerald-600 font-bold">✓</span> Compliance audit readiness (ISO/SOC2)</li>
        </ul>
      </div>
      <button type="button" data-tier="Tier 2: Growth (20–40 hrs/month)" class="tier-select-btn mt-6 w-full text-center py-2.5 px-4 rounded-xl bg-gradient-to-r from-lime-300 via-lime-200 to-lime-300 hover:from-lime-400 hover:to-lime-200 text-slate-950 text-xs font-extrabold transition-all shadow-sm">
        Select Growth Tier
      </button>
    </div>

    <!-- Tier 3 -->
    <div class="rounded-3xl bg-white border border-slate-200 p-7 shadow-sm hover:shadow-xl hover:border-sky-300 transition-all flex flex-col justify-between">
      <div>
        <span class="text-xs font-bold uppercase tracking-widest text-sky-700">Tier 3 · Dedicated</span>
        <h3 class="text-xl font-extrabold text-slate-900 mt-1">60+ hrs / month</h3>
        <p class="mt-2 text-xs text-slate-600 leading-relaxed">Acts as your embedded Virtual CISO &amp; senior hands-on engineer for high-growth operations.</p>
        <ul class="mt-5 space-y-2.5 text-xs text-slate-700">
          <li class="flex items-center gap-2"><span class="text-emerald-600 font-bold">✓</span> Direct executive &amp; board reporting</li>
          <li class="flex items-center gap-2"><span class="text-emerald-600 font-bold">✓</span> Vendor security risk reviews</li>
          <li class="flex items-center gap-2"><span class="text-emerald-600 font-bold">✓</span> Full architecture &amp; roadmap leadership</li>
        </ul>
      </div>
      <button type="button" data-tier="Tier 3: Dedicated (60+ hrs/month)" class="tier-select-btn mt-6 w-full text-center py-2.5 px-4 rounded-xl border border-slate-300 hover:border-slate-900 bg-slate-50 hover:bg-slate-900 hover:text-white text-xs font-extrabold transition-all">
        Select Dedicated Tier
      </button>
    </div>
  </div>
</section>

<!-- What We Can Help You With Section -->
<section class="max-w-6xl mx-auto px-4 py-12" aria-label="Services and capabilities">
  <div class="text-center max-w-3xl mx-auto mb-10">
    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-sky-50 border border-sky-200 text-sky-800 text-xs font-bold uppercase tracking-wider mb-3">
      <span class="w-2 h-2 rounded-full bg-sky-500"></span>
      Tailored Capabilities
    </div>
    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">What We Can Help You With</h2>
    <p class="mt-3 text-slate-600 text-base">
      Deploy fractional engineers across specific disciplines or engage a multi-skilled generalist who handles multiple security duties.
    </p>
  </div>

  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Area 1 -->
    <div class="rounded-3xl bg-white border border-slate-200 p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-sky-300 transition-all duration-200 flex flex-col">
      <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm mb-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
      </div>
      <h3 class="text-lg font-extrabold text-slate-900">1. SOC Monitoring &amp; Reports</h3>
      <ul class="mt-3 space-y-2.5 text-xs text-slate-600 flex-1 leading-relaxed">
        <li class="flex items-start gap-2">
          <span class="text-sky-600 font-bold">▪</span>
          <span>We monitor your Security Operations Center (SOC) around the clock.</span>
        </li>
        <li class="flex items-start gap-2">
          <span class="text-sky-600 font-bold">▪</span>
          <span>We analyze logs, give you clear reports, and tell you exactly what steps to take.</span>
        </li>
      </ul>
    </div>

    <!-- Area 2 -->
    <div class="rounded-3xl bg-white border border-slate-200 p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-sky-300 transition-all duration-200 flex flex-col">
      <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm mb-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
      </div>
      <h3 class="text-lg font-extrabold text-slate-900">2. DevSecOps &amp; App Security</h3>
      <ul class="mt-3 space-y-2.5 text-xs text-slate-600 flex-1 leading-relaxed">
        <li class="flex items-start gap-2">
          <span class="text-emerald-600 font-bold">▪</span>
          <span>Security support while you develop your software or apps.</span>
        </li>
        <li class="flex items-start gap-2">
          <span class="text-emerald-600 font-bold">▪</span>
          <span>We handle security testing, keep proper documentation, and manage patch updates.</span>
        </li>
      </ul>
    </div>

    <!-- Area 3 -->
    <div class="rounded-3xl bg-white border border-slate-200 p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-sky-300 transition-all duration-200 flex flex-col">
      <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm mb-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
      </div>
      <h3 class="text-lg font-extrabold text-slate-900">3. Endpoint Security Management</h3>
      <ul class="mt-3 space-y-2.5 text-xs text-slate-600 flex-1 leading-relaxed">
        <li class="flex items-start gap-2">
          <span class="text-indigo-600 font-bold">▪</span>
          <span>We monitor and manage all your endpoints (laptops, desktops, servers).</span>
        </li>
        <li class="flex items-start gap-2">
          <span class="text-indigo-600 font-bold">▪</span>
          <span>We build, test, and push custom policies and settings across your network.</span>
        </li>
      </ul>
    </div>

    <!-- Area 4 -->
    <div class="rounded-3xl bg-white border border-slate-200 p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-sky-300 transition-all duration-200 flex flex-col">
      <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm mb-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
      </div>
      <h3 class="text-lg font-extrabold text-slate-900">4. Vulnerability Checks &amp; Policy Setup</h3>
      <ul class="mt-3 space-y-2.5 text-xs text-slate-600 flex-1 leading-relaxed">
        <li class="flex items-start gap-2">
          <span class="text-amber-600 font-bold">▪</span>
          <span>We find security gaps in your websites, applications, and network systems.</span>
        </li>
        <li class="flex items-start gap-2">
          <span class="text-amber-600 font-bold">▪</span>
          <span>We build custom security policies to keep your infrastructure safe.</span>
        </li>
      </ul>
    </div>

    <!-- Area 5 -->
    <div class="rounded-3xl bg-white border border-slate-200 p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-sky-300 transition-all duration-200 flex flex-col md:col-span-2 lg:col-span-2">
      <div class="w-12 h-12 rounded-2xl bg-lime-100 text-lime-800 flex items-center justify-center font-bold text-sm mb-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
      </div>
      <h3 class="text-lg font-extrabold text-slate-900">5. Generalist / Multi-Role Support</h3>
      <p class="mt-2 text-xs text-slate-600 font-medium">Need someone who can handle a bit of everything across the business?</p>
      <ul class="mt-3 space-y-2.5 text-xs text-slate-600 flex-1 leading-relaxed">
        <li class="flex items-start gap-2">
          <span class="text-lime-700 font-bold">▪</span>
          <span>We provide multi-skilled engineers who can manage multiple security tasks at once—from triage and policy tuning to patch oversight.</span>
        </li>
        <li class="flex items-start gap-2">
          <span class="text-lime-700 font-bold">▪</span>
          <span>Acts as your go-to security point of contact, joining meetings and advising management.</span>
        </li>
      </ul>
    </div>
  </div>
</section>

<!-- Real Examples of How We Help -->
<section class="max-w-6xl mx-auto px-4 py-12" aria-label="Real examples of client engagements">
  <div class="text-center max-w-3xl mx-auto mb-10">
    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-sky-50 border border-sky-200 text-sky-800 text-xs font-bold uppercase tracking-wider mb-3">
      <span class="relative flex h-2 w-2">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-2 w-2 bg-sky-500"></span>
      </span>
      Proven Impact
    </div>
    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Real Examples of How We Help</h2>
    <p class="mt-3 text-slate-600 text-base">
      Real client scenarios where fractional security professionals solved operational bottlenecks.
    </p>
  </div>

  <div class="grid md:grid-cols-2 gap-8">
    <!-- Example 1 -->
    <article class="rounded-3xl bg-white border border-slate-200 p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-sky-300 transition-all duration-200 flex flex-col">
      <div class="flex items-center justify-between gap-3 mb-4">
        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider">
          Audit &amp; Compliance Firm
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          Monthly Hourly Plan
        </span>
      </div>

      <h3 class="text-xl font-extrabold text-slate-900 mb-5">
        Example 1: Audit Firm Support
      </h3>

      <div class="space-y-4 flex-1 text-sm">
        <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-100">
          <p class="font-bold text-amber-900 text-xs uppercase tracking-wide mb-1">The Problem</p>
          <p class="text-amber-950 font-medium leading-relaxed">
            A client implemented Endpoint Security, but didn't have the time or expertise to read the deep technical reports, investigate threats, or change settings.
          </p>
        </div>

        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100">
          <p class="font-bold text-emerald-900 text-xs uppercase tracking-wide mb-1">Our Solution</p>
          <p class="text-emerald-950 font-medium leading-relaxed">
            They hired a Halosec security expert on a monthly hourly plan. Our engineer handled report analysis, investigated threats, updated settings, and pushed new policies regularly.
          </p>
        </div>
      </div>
    </article>

    <!-- Example 2 -->
    <article class="rounded-3xl bg-white border border-slate-200 p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-sky-300 transition-all duration-200 flex flex-col">
      <div class="flex items-center justify-between gap-3 mb-4">
        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider">
          Enterprise Office
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 border border-sky-200 text-sky-700 text-xs font-bold">
          <span class="w-2 h-2 rounded-full bg-sky-500"></span>
          System Baseline Hardening
        </span>
      </div>

      <h3 class="text-xl font-extrabold text-slate-900 mb-5">
        Example 2: Custom Windows Security Setup
      </h3>

      <div class="space-y-4 flex-1 text-sm">
        <div class="p-4 rounded-2xl bg-rose-50/70 border border-rose-100">
          <p class="font-bold text-rose-900 text-xs uppercase tracking-wide mb-1">The Problem</p>
          <p class="text-rose-950 font-medium leading-relaxed">
            A company needed specific, strict security policies pushed across all Windows systems in their office.
          </p>
        </div>

        <div class="p-4 rounded-2xl bg-sky-50/70 border border-sky-100">
          <p class="font-bold text-sky-900 text-xs uppercase tracking-wide mb-1">Our Solution</p>
          <p class="text-sky-950 font-medium leading-relaxed">
            We created custom baseline security checklists based on their needs and set up the entire endpoint configuration from scratch.
          </p>
        </div>
      </div>
    </article>
  </div>
</section>

<!-- Ready to Secure Your Business? High-Impact CTA Banner -->
<section class="max-w-6xl mx-auto px-4 pt-4 pb-20">
  <div class="rounded-3xl bg-slate-900 border border-slate-800 text-white p-8 md:p-12 relative overflow-hidden shadow-2xl">
    <!-- Ambient Blur Aura -->
    <div class="absolute -right-20 -top-20 w-96 h-96 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -left-20 -bottom-20 w-96 h-96 bg-lime-400/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
      <div class="max-w-2xl text-center md:text-left">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lime-400/10 border border-lime-400/20 text-lime-300 text-xs font-bold uppercase tracking-wider mb-3">
          <span class="w-2 h-2 rounded-full bg-lime-400"></span>
          Ready to Get Started?
        </div>
        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-white">
          Add a Certified Security Expert to Your Team
        </h2>
        <p class="mt-3 text-slate-300 text-base md:text-lg leading-relaxed">
          Skip the long recruitment cycle and payroll burdens. Tell us what you need and engage experienced cybersecurity talent on your terms.
        </p>
      </div>

      <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0 w-full sm:w-auto">
        <a href="#enquiry" class="w-full sm:w-auto text-center bg-gradient-to-r from-lime-300 via-lime-200 to-lime-300 hover:from-lime-400 hover:to-lime-200 text-slate-950 font-extrabold text-xs uppercase px-7 py-3.5 rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 border border-lime-200/50 inline-flex items-center justify-center gap-2">
          <span>Request Security Talent</span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24"><line x1="7" x2="17" y1="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
        </a>
        <a href="/consultation" class="w-full sm:w-auto text-center bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase px-6 py-3.5 rounded-xl border border-white/20 transition-all hover:scale-105 active:scale-95">
          Book Consultation
        </a>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var pills = document.querySelectorAll('.talent-pill');
  var tierButtons = document.querySelectorAll('.tier-select-btn');
  var textarea = document.querySelector('textarea[name="message"]');
  var enquirySection = document.getElementById('enquiry');
  if (!textarea) return;

  function updateScope(scopeText) {
    pills.forEach(function (p) {
      if (p.getAttribute('data-scope') === scopeText) {
        p.classList.remove('bg-slate-50', 'text-slate-700', 'border-slate-200');
        p.classList.add('bg-slate-900', 'text-white', 'border-slate-900', 'shadow-sm');
      } else {
        p.classList.remove('bg-slate-900', 'text-white', 'border-slate-900', 'shadow-sm');
        p.classList.add('bg-slate-50', 'text-slate-700', 'border-slate-200');
      }
    });

    var prefix = '[Engagement: ' + scopeText + '] ';
    var current = textarea.value.replace(/\[Engagement: [^\]]+\]\s*/g, '');
    textarea.value = prefix + current.trim();
  }

  pills.forEach(function (pill) {
    pill.addEventListener('click', function () {
      var scope = pill.getAttribute('data-scope');
      updateScope(scope);
      textarea.focus();
    });
  });

  tierButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var tier = btn.getAttribute('data-tier');
      updateScope(tier);
      if (enquirySection) {
        enquirySection.scrollIntoView({ behavior: 'smooth' });
      }
      setTimeout(function () { textarea.focus(); }, 400);
    });
  });
});
</script>

