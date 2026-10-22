<?php
declare(strict_types=1);

/** @var HaloSec\View $this */
$e = static fn (mixed $v): string => HaloSec\View::e($v);

$title = $service['title'] ?? 'Endpoint Security Solutions for Your Business';
$lead = 'Looking to secure your company’s devices and data without the headache of managing each system manually? Our Endpoint Security Services give you total control, strong protection, and total peace of mind—whether you have 5 systems or hundreds.';
?>

<?= $this->include('partials/page_header', [
    'eyebrow' => 'Specialized Practice · Centralized Endpoint Defense',
    'heading' => $title,
    'lead' => $lead,
]) ?>

<!-- Top Split Layout: Core Proposition & Direct Enquiry -->
<section class="max-w-6xl mx-auto px-4 pt-14 pb-12 grid gap-10 lg:grid-cols-5 items-start">
  <!-- Left Column: Core Value & Capability Details -->
  <div class="lg:col-span-3 space-y-8">
    
    <!-- Practice Overview & Value Proposition -->
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-8 md:p-10 relative overflow-hidden">
      <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 border border-sky-200 text-sky-800 text-xs font-bold uppercase tracking-wider">
          <svg class="w-3.5 h-3.5 text-sky-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"></path></svg>
          Enterprise Endpoint Defense
        </span>
        <span class="text-xs font-bold text-slate-500">Works for 5+ to 500+ systems</span>
      </div>

      <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
        Total Control, Strong Protection &amp; Complete Peace of Mind
      </h2>
      <p class="mt-4 text-slate-700 leading-relaxed text-base md:text-lg font-medium">
        Looking to secure your company’s devices and data without the headache of managing each system manually? Our Endpoint Security Services give you total control, strong protection, and total peace of mind—whether you have 5 systems or hundreds.
      </p>

      <!-- Built for Teams of Any Size Highlight Grid -->
      <div class="mt-8 pt-8 border-t border-slate-100">
        <div class="flex items-center gap-2 mb-4">
          <span class="w-2 h-2 rounded-full bg-lime-400"></span>
          <h3 class="text-xs font-bold uppercase tracking-widest text-sky-800">Built for Teams of Any Size</h3>
        </div>

        <div class="grid sm:grid-cols-3 gap-4">
          <div class="rounded-2xl border border-slate-200/90 bg-slate-50/70 p-4 transition-all hover:bg-white hover:border-sky-300 hover:shadow-sm">
            <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 font-extrabold text-xs flex items-center justify-center mb-3">5+</div>
            <h4 class="text-sm font-extrabold text-slate-900">Works for 5+ Systems</h4>
            <p class="mt-1.5 text-xs text-slate-600 leading-relaxed">Designed for businesses starting from 5 computers up to large companies with hundreds of devices.</p>
          </div>

          <div class="rounded-2xl border border-slate-200/90 bg-slate-50/70 p-4 transition-all hover:bg-white hover:border-sky-300 hover:shadow-sm">
            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 font-extrabold text-xs flex items-center justify-center mb-3">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            </div>
            <h4 class="text-sm font-extrabold text-slate-900">Easy to Manage</h4>
            <p class="mt-1.5 text-xs text-slate-600 leading-relaxed">No need to configure each employee's computer separately. Everything is orchestrated centrally.</p>
          </div>

          <div class="rounded-2xl border border-slate-200/90 bg-slate-50/70 p-4 transition-all hover:bg-white hover:border-sky-300 hover:shadow-sm">
            <div class="w-9 h-9 rounded-xl bg-lime-100 text-slate-900 font-extrabold text-xs flex items-center justify-center mb-3">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <h4 class="text-sm font-extrabold text-slate-900">Peace of Mind</h4>
            <p class="mt-1.5 text-xs text-slate-600 leading-relaxed">Keep your company data safe from hackers and data leaks without slowing down daily work.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Why Move Beyond Traditional Antivirus Section -->
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-8 md:p-10">
      <div class="mb-6">
        <span class="text-xs font-bold uppercase tracking-widest text-sky-700">Next-Gen Architecture</span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Why Move Beyond Traditional Antivirus?</h2>
        <p class="mt-3 text-slate-700 font-medium leading-relaxed">
          The days of standalone antivirus software are over. Today’s threats need a centralized, smart security system that covers every endpoint in your network.
        </p>
      </div>

      <div class="space-y-4">
        <!-- Feature 1 -->
        <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-sky-300 transition-all duration-200">
          <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Centrally Managed</h3>
            <p class="text-sm text-slate-600 mt-1 leading-relaxed">
              Set up policies and control every system from one single dashboard instead of going to each PC individually.
            </p>
          </div>
        </div>

        <!-- Feature 2 -->
        <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-sky-300 transition-all duration-200">
          <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Ransomware Protection &amp; Rollback</h3>
            <p class="text-sm text-slate-600 mt-1 leading-relaxed">
              If a ransomware attack tries to encrypt your files, our system stops the process, blocks its communication, and rolls your files back to safety automatically.
            </p>
          </div>
        </div>

        <!-- Feature 3 -->
        <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-sky-300 transition-all duration-200">
          <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Total Device &amp; Web Control</h3>
            <p class="text-sm text-slate-600 mt-1 leading-relaxed">
              Block USB drives, web access, cameras, or risky tools like PowerShell centrally whenever needed to stop unauthorized data egress.
            </p>
          </div>
        </div>

        <!-- Feature 4 -->
        <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-sky-300 transition-all duration-200">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Protection Against New Threats</h3>
            <p class="text-sm text-slate-600 mt-1 leading-relaxed">
              Stay safe from zero-day threats and unpatched vulnerabilities automatically using behavioral intelligence and exploit prevention.
            </p>
          </div>
        </div>

        <!-- Feature 5 -->
        <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-sky-300 transition-all duration-200">
          <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Custom Group Policies</h3>
            <p class="text-sm text-slate-600 mt-1 leading-relaxed">
              Easily set different access levels and confidentiality rules for different teams or departments across your organization.
            </p>
          </div>
        </div>
      </div>

      <div class="mt-8 pt-6 border-t border-slate-100 text-sm text-slate-500 flex flex-wrap gap-4 items-center">
        <span>Need custom scope or compliance mapping?</span>
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
      <h2 class="text-xl font-extrabold text-slate-900">Enquire about this service</h2>
      <p class="text-xs text-slate-600 mt-1">Our Endpoint Security specialists will review your requirements and respond within 24 hours.</p>
    </div>

    <?= $this->include('partials/flash', ['flash' => $flash]) ?>
    <?= $this->include('partials/form_open', ['formAction' => $formAction]) ?>
    <div class="space-y-5">
      <?= $this->include('partials/contact_fields', ['old' => $old, 'errors' => $errors]) ?>
      <?= $this->include('partials/field', [
          'name' => 'message',
          'label' => 'How can we help? (Number of endpoints, OS types, current antivirus)',
          'type' => 'textarea',
          'required' => true,
          'old' => $old,
          'errors' => $errors,
      ]) ?>
      <div class="pt-2">
        <?= $this->include('partials/submit', ['btnLabel' => 'Send Service Enquiry']) ?>
      </div>
    </div>

    <div class="mt-6 pt-5 border-t border-slate-100 space-y-2 text-xs text-slate-500 font-medium">
      <div class="flex items-center gap-2">
        <span class="text-emerald-600 font-bold">✓</span>
        <span>Minimum requirement: <strong>5 systems</strong> to deploy</span>
      </div>
      <div class="flex items-center gap-2">
        <span class="text-emerald-600 font-bold">✓</span>
        <span>Certified Sophos Silver &amp; Zoho Partner</span>
      </div>
      <div class="flex items-center gap-2">
        <span class="text-emerald-600 font-bold">✓</span>
        <span>Zero disruption to employee productivity</span>
      </div>
    </div>
  </div>
</section>

<!-- Real Stories: How We Helped Our Clients -->
<section class="max-w-6xl mx-auto px-4 py-12" aria-label="Client case studies">
  <div class="text-center max-w-3xl mx-auto mb-10">
    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-sky-50 border border-sky-200 text-sky-800 text-xs font-bold uppercase tracking-wider mb-3">
      <span class="relative flex h-2 w-2">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-2 w-2 bg-sky-500"></span>
      </span>
      Proven Track Record
    </div>
    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Real Stories: How We Helped Our Clients</h2>
    <p class="mt-3 text-slate-600 text-base">
      See how organizations stopped active attacks and sealed data leaks with HaloSec's endpoint security deployment.
    </p>
  </div>

  <div class="grid md:grid-cols-2 gap-8">
    <!-- Story 1 -->
    <article class="rounded-3xl bg-white border border-slate-200 p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-sky-300 transition-all duration-200 flex flex-col relative overflow-hidden">
      <div class="flex items-center justify-between gap-3 mb-4">
        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider">
          GCC Tourism Firm
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          Zero Cyber Attacks Since
        </span>
      </div>

      <h3 class="text-xl font-extrabold text-slate-900 mb-5">
        1. Stopping Cyber Fraud for a Tourism Company
      </h3>

      <div class="space-y-4 flex-1 text-sm">
        <div class="p-4 rounded-2xl bg-rose-50/70 border border-rose-100">
          <p class="font-bold text-rose-900 text-xs uppercase tracking-wide mb-1">The Problem</p>
          <p class="text-rose-950 font-medium leading-relaxed">
            A GCC-based tourism firm lost lakhs of rupees when hackers got in and booked fraudulent tickets.
          </p>
        </div>

        <div class="p-4 rounded-2xl bg-sky-50/70 border border-sky-100">
          <p class="font-bold text-sky-900 text-xs uppercase tracking-wide mb-1">What We Did</p>
          <p class="text-sky-950 font-medium leading-relaxed">
            We installed our Endpoint Security solution and set up strict access policies across their systems.
          </p>
        </div>

        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100">
          <p class="font-bold text-emerald-900 text-xs uppercase tracking-wide mb-1">The Result</p>
          <p class="text-emerald-950 font-bold leading-relaxed">
            They now have full visibility over every device, and <span class="underline decoration-emerald-500 decoration-2">zero cyber attacks</span> since.
          </p>
        </div>
      </div>
    </article>

    <!-- Story 2 -->
    <article class="rounded-3xl bg-white border border-slate-200 p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-sky-300 transition-all duration-200 flex flex-col relative overflow-hidden">
      <div class="flex items-center justify-between gap-3 mb-4">
        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider">
          Accounting &amp; Tax Practice
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 border border-sky-200 text-sky-700 text-xs font-bold">
          <span class="w-2 h-2 rounded-full bg-sky-500"></span>
          100% Client Data Protected
        </span>
      </div>

      <h3 class="text-xl font-extrabold text-slate-900 mb-5">
        2. Protecting Sensitive Data for an Accounting Firm
      </h3>

      <div class="space-y-4 flex-1 text-sm">
        <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-100">
          <p class="font-bold text-amber-900 text-xs uppercase tracking-wide mb-1">The Problem</p>
          <p class="text-amber-950 font-medium leading-relaxed">
            An accounting and tax firm handling confidential client financial data needed total data protection.
          </p>
        </div>

        <div class="p-4 rounded-2xl bg-sky-50/70 border border-sky-100">
          <p class="font-bold text-sky-900 text-xs uppercase tracking-wide mb-1">What We Did</p>
          <p class="text-sky-950 font-medium leading-relaxed">
            We deployed Endpoint Security along with Data Loss Prevention (DLP) and blocked unauthorized USB/device access.
          </p>
        </div>

        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100">
          <p class="font-bold text-emerald-900 text-xs uppercase tracking-wide mb-1">The Result</p>
          <p class="text-emerald-950 font-bold leading-relaxed">
            Their client data remains <span class="underline decoration-emerald-500 decoration-2">100% protected</span>, with no risk of unauthorized data transfers.
          </p>
        </div>
      </div>
    </article>
  </div>
</section>

<!-- Quick FAQ Section -->
<section class="max-w-4xl mx-auto px-4 py-12" aria-label="Frequently Asked Questions">
  <div class="text-center mb-10">
    <span class="text-xs font-bold uppercase tracking-widest text-sky-700">Clear Answers</span>
    <h2 class="text-3xl font-extrabold text-slate-900 mt-1">Quick FAQ</h2>
    <p class="mt-2 text-slate-600 text-sm">Key details about implementing HaloSec endpoint security in your business.</p>
  </div>

  <div class="space-y-4">
    <!-- Question 1 -->
    <details class="group rounded-2xl bg-white border border-slate-200 p-6 shadow-sm hover:border-sky-300 transition-all open:shadow-md open:border-sky-400">
      <summary class="flex items-center justify-between cursor-pointer list-none select-none font-extrabold text-slate-900 text-base md:text-lg">
        <span>How many systems do I need to get started?</span>
        <span class="w-7 h-7 rounded-full bg-slate-100 group-open:bg-sky-100 group-open:text-sky-700 flex items-center justify-center text-slate-500 transition-transform group-open:rotate-180 shrink-0 ml-3">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
        </span>
      </summary>
      <div class="mt-4 pt-4 border-t border-slate-100 text-slate-600 leading-relaxed text-sm font-medium">
        You need a minimum of <strong>5 systems</strong> to implement our endpoint security solution.
      </div>
    </details>

    <!-- Question 2 -->
    <details class="group rounded-2xl bg-white border border-slate-200 p-6 shadow-sm hover:border-sky-300 transition-all open:shadow-md open:border-sky-400">
      <summary class="flex items-center justify-between cursor-pointer list-none select-none font-extrabold text-slate-900 text-base md:text-lg">
        <span>How is this different from normal antivirus?</span>
        <span class="w-7 h-7 rounded-full bg-slate-100 group-open:bg-sky-100 group-open:text-sky-700 flex items-center justify-center text-slate-500 transition-transform group-open:rotate-180 shrink-0 ml-3">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
        </span>
      </summary>
      <div class="mt-4 pt-4 border-t border-slate-100 text-slate-600 leading-relaxed text-sm font-medium">
        Normal antivirus works standalone on single PCs. Endpoint Security gives you central control over all systems, ransomware rollback, and strict policy management from one place.
      </div>
    </details>

    <!-- Question 3 -->
    <details class="group rounded-2xl bg-white border border-slate-200 p-6 shadow-sm hover:border-sky-300 transition-all open:shadow-md open:border-sky-400">
      <summary class="flex items-center justify-between cursor-pointer list-none select-none font-extrabold text-slate-900 text-base md:text-lg">
        <span>Can I block USB drives and external devices?</span>
        <span class="w-7 h-7 rounded-full bg-slate-100 group-open:bg-sky-100 group-open:text-sky-700 flex items-center justify-center text-slate-500 transition-transform group-open:rotate-180 shrink-0 ml-3">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
        </span>
      </summary>
      <div class="mt-4 pt-4 border-t border-slate-100 text-slate-600 leading-relaxed text-sm font-medium">
        Yes! You can centrally block USB ports, web access, cameras, and unauthorized software across your company.
      </div>
    </details>
  </div>
</section>

<!-- Ready to Secure Your Systems? High-Impact CTA Banner -->
<section class="max-w-6xl mx-auto px-4 pt-4 pb-20">
  <div class="rounded-3xl bg-slate-900 border border-slate-800 text-white p-8 md:p-12 relative overflow-hidden shadow-2xl">
    <!-- Ambient Blur Aura -->
    <div class="absolute -right-20 -top-20 w-96 h-96 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -left-20 -bottom-20 w-96 h-96 bg-lime-400/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
      <div class="max-w-2xl text-center md:text-left">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lime-400/10 border border-lime-400/20 text-lime-300 text-xs font-bold uppercase tracking-wider mb-3">
          <span class="w-2 h-2 rounded-full bg-lime-400"></span>
          Get Started in 24 Hours
        </div>
        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-white">
          Ready to Secure Your Systems?
        </h2>
        <p class="mt-3 text-slate-300 text-base md:text-lg leading-relaxed">
          Don't wait for a security breach to happen. Contact our team today and let's get your business endpoints secured!
        </p>
      </div>

      <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0 w-full sm:w-auto">
        <a href="#enquiry" class="w-full sm:w-auto text-center bg-gradient-to-r from-lime-300 via-lime-200 to-lime-300 hover:from-lime-400 hover:to-lime-200 text-slate-950 font-extrabold text-xs uppercase px-7 py-3.5 rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 border border-lime-200/50 inline-flex items-center justify-center gap-2">
          <span>Contact Our Team Today</span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24"><line x1="7" x2="17" y1="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
        </a>
        <a href="/consultation" class="w-full sm:w-auto text-center bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase px-6 py-3.5 rounded-xl border border-white/20 transition-all hover:scale-105 active:scale-95">
          Book Consultation
        </a>
      </div>
    </div>
  </div>
</section>
