<?php
/**
 * Body of /guides/dpdp-act-compliance-checklist. Rendered inside .prose-guide, so plain HTML is styled.
 * Facts checked against the DPDP Act, 2023 and the DPDP Rules, 2025 as of October 2026.
 */
?>
<p>If your business keeps a customer list, runs payroll, collects enquiries through a website form or stores CCTV footage digitally, you process personal data. India’s <strong>Digital Personal Data Protection Act, 2023</strong> (the DPDP Act) sets out how you may collect, use, protect and delete it, and the <strong>DPDP Rules, 2025</strong> fill in the practical detail.</p>

<p>Most of the rules that affect day-to-day business start around <strong>May 2027</strong>. That sounds far off, but mapping your data, rewriting notices and fixing security gaps takes most companies several months. This guide explains what the law asks of a small or mid-sized business, then gives you a 12-step checklist to work through.</p>

<h2 id="who">Who the DPDP Act applies to</h2>

<p>The Act covers <strong>digital personal data</strong>: any information about an identifiable person that is held digitally, or collected on paper and later digitised. It applies to processing inside India, and to processing outside India when it is connected to offering goods or services to people in India.</p>

<p>Three terms come up again and again:</p>
<ul>
  <li><strong>Data Principal:</strong> the person the data is about, such as your customer, employee or website visitor. For a child (under 18), it includes their parent or guardian.</li>
  <li><strong>Data Fiduciary:</strong> the business that decides why and how the data is used. That is you.</li>
  <li><strong>Data Processor:</strong> anyone who handles the data on your behalf, such as your cloud host, payroll provider, CRM or IT partner.</li>
</ul>

<p>There is no general exemption for small businesses. The government can exempt some startups from certain duties, and a separate category of <strong>Significant Data Fiduciaries</strong> (large or high-risk processors the government designates) carries extra obligations. For most SMBs, the standard obligations in this guide apply.</p>

<h2 id="timeline">The timeline: what applies when</h2>

<table>
  <thead>
    <tr><th>Date</th><th>What happens</th></tr>
  </thead>
  <tbody>
    <tr><td>August 2023</td><td>The DPDP Act is passed by Parliament.</td></tr>
    <tr><td>November 2025</td><td>The DPDP Rules are notified. Rules setting up the Data Protection Board take effect straight away.</td></tr>
    <tr><td>November 2026</td><td>Rules for registering Consent Managers take effect (12 months after notification).</td></tr>
    <tr><td><strong>Around May 2027</strong></td><td><strong>The main obligations take effect</strong> (18 months after notification): notices, consent, security safeguards, breach reporting, data retention and the rights of Data Principals.</td></tr>
  </tbody>
</table>

<p>The <a href="#cert-in">CERT-In 6-hour reporting rule</a> is separate and already in force. You do not have to wait for 2027 for that one.</p>

<h2 id="checklist">The 12-step DPDP checklist</h2>

<p>Work through these in order. The first four steps tell you how big the job is; the rest put the controls in place.</p>

<div class="step">
  <h3><span class="step-num">1</span> Map the personal data you hold</h3>
  <p>List every place personal data lives: CRM, accounting software, HR and payroll, email, website forms, shared drives, WhatsApp groups, laptops, backups and paper files that get scanned. For each, note what data it holds, whose it is, why you have it, who can access it and which vendors touch it. You cannot protect, explain or delete data you do not know about.</p>
</div>

<div class="step">
  <h3><span class="step-num">2</span> Decide the lawful basis for each use</h3>
  <p>Under the Act you may process personal data either with <strong>consent</strong> or for certain <strong>legitimate uses</strong>. Legitimate uses include data a person gives you voluntarily for a specific purpose (without objecting), employment purposes, legal obligations and medical emergencies. Everything else, such as marketing to existing customers, needs consent.</p>
</div>

<div class="step">
  <h3><span class="step-num">3</span> Rewrite your privacy notice</h3>
  <p>When you ask for consent, you must give a notice that can be understood on its own, without reading anything else. It must list the personal data you collect and the specific purpose for each item, explain how to withdraw consent, how to exercise their rights and how to complain to the Data Protection Board. Plain language matters: a legal wall of text will not meet the standard.</p>
</div>

<div class="step">
  <h3><span class="step-num">4</span> Fix how you collect consent</h3>
  <p>Consent must be free, specific, informed, unconditional and unambiguous, given by a clear action such as ticking an unticked box. Pre-ticked boxes, bundled “agree to everything” checkboxes and consent hidden in terms of service do not count. Withdrawing consent must be as easy as giving it, and you must keep a record that consent was given.</p>
</div>

<div class="step">
  <h3><span class="step-num">5</span> Name a contact person and set up grievance handling</h3>
  <p>Publish the business contact details of someone who can answer questions about how you handle personal data. (Only Significant Data Fiduciaries need a formal Data Protection Officer.) Set up a simple way for people to raise complaints, and respond within the period you publish, which must not exceed 90 days.</p>
</div>

<div class="step">
  <h3><span class="step-num">6</span> Be ready to honour people’s rights</h3>
  <p>Data Principals can ask for a summary of the data you hold about them and who you shared it with, ask you to correct, complete or erase it, raise a grievance, and nominate someone to act for them. Decide who handles these requests, how you verify identity and how you find all of a person’s data across your systems. Step 1 makes this possible.</p>
</div>

<div class="step">
  <h3><span class="step-num">7</span> Put reasonable security safeguards in place</h3>
  <p>This is the obligation with the highest penalty. The Rules list the minimum:</p>
  <ul>
    <li>Encryption, masking, obfuscation or tokenisation of personal data.</li>
    <li>Access controls, so people only see the data their job needs.</li>
    <li>Logging, monitoring and review of who accessed what.</li>
    <li>Backups and measures to keep working if systems are compromised.</li>
    <li>Contracts that require your processors to take the same care.</li>
  </ul>
  <p>In practice, for most SMBs that means <a href="/services/endpoint-security">centrally managed endpoint security</a> with disk encryption and ransomware rollback, <a href="/services/dlp-implementation">data loss prevention</a> to stop data leaving by email, USB or personal cloud, multi-factor authentication on email and admin accounts, and tested offline backups.</p>
</div>

<div class="step">
  <h3><span class="step-num">8</span> Keep logs for at least a year</h3>
  <p>The Rules require you to keep logs of personal data processing, and the personal data itself where needed, for at least one year to help detect and investigate breaches. Check that your firewall, email, endpoint and cloud logs are retained that long and are stored where an attacker cannot delete them. A <a href="/services/soc-setup">Security Operations Center</a> or managed monitoring service handles this for you.</p>
</div>

<div class="step">
  <h3><span class="step-num">9</span> Write a breach response plan</h3>
  <p>If personal data is breached, you must:</p>
  <ul>
    <li>Tell each affected person <strong>without delay</strong>, in plain language: what happened, the likely consequences, what you are doing about it, what they can do to protect themselves, and who to contact.</li>
    <li>Tell the <strong>Data Protection Board without delay</strong>, then send a detailed report within <strong>72 hours</strong> of becoming aware: the facts, the cause, the people affected, what you have done to contain it and any findings about who was responsible.</li>
  </ul>
  <p>Decide now who leads, who calls the lawyer, who drafts the notices and how you reach customers. Keep the plan, and your <a href="/under-attack">incident response contact</a>, somewhere you can reach if your own systems are down.</p>
</div>

<div class="callout-red" id="cert-in">
  <p><strong>Do not forget CERT-In.</strong> Under CERT-In’s 2022 directions, many cyber incidents, including data breaches, ransomware and compromise of servers or email, must be reported to CERT-In within <strong>6 hours</strong> of noticing them. A single incident can require reports to both CERT-In and the Data Protection Board, on different clocks.</p>
</div>

<div class="step">
  <h3><span class="step-num">10</span> Review your vendor contracts</h3>
  <p>You stay responsible for personal data your processors handle. List every vendor from step 1 and check that each contract requires them to protect the data, use it only on your instructions, tell you quickly about any breach and delete or return the data when the contract ends.</p>
</div>

<div class="step">
  <h3><span class="step-num">11</span> Set retention and deletion rules</h3>
  <p>Once the purpose is served, or a person withdraws consent, you must erase their data unless another law requires you to keep it (tax, GST and labour records often do). Write down how long you keep each type of data, then actually delete it, including old exports, spreadsheets on laptops and copies in email.</p>
</div>

<div class="step">
  <h3><span class="step-num">12</span> Check children’s data and transfers abroad</h3>
  <p>If any of your users or customers may be under 18, you need verifiable consent from a parent or guardian, and you must not track, behaviourally monitor or target advertising at children. Transfers of personal data outside India are allowed unless the government restricts a particular country, so keep a list of where your cloud and SaaS providers store data.</p>
</div>

<h2 id="penalties">Penalties</h2>

<p>The Data Protection Board can impose penalties of up to:</p>

<table>
  <thead>
    <tr><th>Breach</th><th>Maximum penalty</th></tr>
  </thead>
  <tbody>
    <tr><td>Failing to take reasonable security safeguards to prevent a breach</td><td>₹250 crore</td></tr>
    <tr><td>Failing to notify the Board or affected people of a breach</td><td>₹200 crore</td></tr>
    <tr><td>Breaking the rules on children’s data</td><td>₹200 crore</td></tr>
    <tr><td>Breaking the extra obligations of a Significant Data Fiduciary</td><td>₹150 crore</td></tr>
    <tr><td>Any other breach of the Act or Rules</td><td>₹50 crore</td></tr>
  </tbody>
</table>

<p>The Board decides the amount based on how serious the breach was, whether it was repeated, what the business gained and what it did to limit the damage. Acting quickly and having safeguards documented counts in your favour.</p>

<h2 id="start">Where to start this month</h2>

<ol>
  <li><strong>Week 1:</strong> name an owner for DPDP compliance and complete the data map in step 1, even roughly.</li>
  <li><strong>Week 2:</strong> fix the quick wins: multi-factor authentication on email, removing ex-employees’ access, and checking that backups restore.</li>
  <li><strong>Week 3:</strong> rewrite the notice and consent wording on your website forms.</li>
  <li><strong>Week 4:</strong> get a security gap assessment against step 7, so you know what to budget for before 2027.</li>
</ol>

<div class="callout">
  <p><strong>Not sure where your gaps are?</strong> A <a href="/services/security-audits">HaloSec security audit</a> checks your endpoints, network, cloud and access controls against the safeguards the DPDP Rules expect, and gives you a prioritised, plain-language fix list. You can start with our <a href="/free-audit">free 3-minute check-up</a>.</p>
</div>
