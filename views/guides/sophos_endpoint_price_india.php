<?php
/**
 * Body of /guides/sophos-endpoint-price-india. Rendered inside .prose-guide, so plain HTML is styled.
 * Sophos names, dates and licensing checked against Sophos's portfolio announcement and licensing policy, October 2026.
 * Prices and process come from HaloSec's own sales and deployments (interview with Manu Francis, 30 Oct 2026).
 */
?>
<p>If you are buying endpoint protection for a company with 5, 50 or 500 computers, Sophos is one of the first names you will hear. It is also one of the hardest to price. Sophos does not publish an Indian price list, partners quote very differently, and in late 2025 Sophos renamed most of its endpoint products, so older articles and quotes may use names that no longer exist.</p>

<p>This guide explains the current Sophos editions, how licensing works, what Sophos Endpoint really costs in India in 2026, and how buying, setup and support work with an authorised partner. It is written from what we see selling and deploying Sophos for businesses across Kerala.</p>

<h2 id="what-changed">Intercept X is now Sophos Endpoint</h2>

<p>In October 2025 Sophos refreshed its endpoint range and dropped the “Intercept X” name from its licences (<a href="https://www.sophos.com/partner-news/powerful-refresh-of-the-sophos-endpoint-security-portfolio/" rel="noopener">Sophos announcement</a>). If a quote still says “Intercept X Essentials”, ask why.</p>

<table>
  <thead>
    <tr><th>Old name</th><th>Name in 2026</th></tr>
  </thead>
  <tbody>
    <tr><td>Intercept X Essentials</td><td><strong>Discontinued.</strong> No new orders after 1 Nov 2025, no renewals after 7 Jan 2026.</td></tr>
    <tr><td>Intercept X Advanced</td><td><strong>Sophos Endpoint – User</strong></td></tr>
    <tr><td>Intercept X Advanced for Server</td><td><strong>Sophos Endpoint – Server</strong></td></tr>
    <tr><td>Intercept X Advanced with XDR</td><td><strong>Sophos XDR</strong>, with a new <strong>Sophos EDR</strong> tier in between</td></tr>
    <tr><td>Managed Threat Response / MDR</td><td><strong>Sophos MDR</strong></td></tr>
  </tbody>
</table>

<div class="callout">
  <p><strong>Still on Intercept X Essentials?</strong> It cannot be renewed any more. At renewal you move to Sophos Endpoint, which keeps your Sophos Central account and adds the protection Essentials lacked. Talk to your partner a month before expiry so there is no gap.</p>
</div>

<h2 id="editions">Which Sophos edition does your business need?</h2>

<figure class="guide-figure tone-sky">
  <?= $this->include('partials/guide_art', ['name' => 'editions']) ?>
  <figcaption>Each Sophos edition builds on the one below it.</figcaption>
</figure>

<h3>Sophos Endpoint</h3>
<p>The standard choice for most companies with 5 to a few hundred computers and no full-time security team. It covers malware and ransomware protection, ransomware rollback, exploit protection, device and web control and data loss prevention rules, all managed from one dashboard.</p>

<h3>Sophos EDR and Sophos XDR</h3>
<p>EDR (endpoint detection and response) adds tools to investigate what happened on a computer: which process ran, what it touched, where it connected. XDR (extended detection and response) adds data from your firewall, email and cloud services, so you can follow an attack across systems. Both suit companies with an IT or security person who will actually look at the data.</p>

<h3>Sophos MDR</h3>
<p>Managed detection and response: Sophos’s own analysts watch your systems 24/7 and respond to threats for you. This is for companies that need round-the-clock cover but cannot staff a security team at night. HaloSec is a Sophos MDR accredited partner, so we can set it up and stay involved after go-live.</p>

<h3>Servers</h3>
<p>Servers need their own <strong>Sophos Endpoint – Server</strong> licence (or the server version of EDR or XDR). Count them separately when you ask for a quote.</p>

<table>
  <thead>
    <tr><th>Edition</th><th>Adds</th><th>Best for</th></tr>
  </thead>
  <tbody>
    <tr><td>Sophos Endpoint</td><td>Full protection, central control, DLP rules</td><td>Most businesses with 5+ users</td></tr>
    <tr><td>Sophos EDR</td><td>Investigation of endpoint activity</td><td>Teams with an IT person who investigates alerts</td></tr>
    <tr><td>Sophos XDR</td><td>Firewall, email and cloud data in one view</td><td>Companies running several security tools</td></tr>
    <tr><td>Sophos MDR</td><td>Sophos analysts monitoring and responding 24/7</td><td>Companies with no security staff out of hours</td></tr>
  </tbody>
</table>

<p>Sophos also launched <strong>Sophos Endpoint 100</strong>, a fixed-price plan for organisations with up to 100 employees. Availability through Indian partners varies, so ask before you plan around it.</p>

<h2 id="features">What you get with Sophos Endpoint</h2>

<ul>
  <li><strong>One dashboard for every computer.</strong> In Sophos Central you set company policies once and push them to all devices at the same time, and you see the status of every machine in one place.</li>
  <li><strong>Ransomware protection with rollback.</strong> Sophos’s CryptoGuard watches for files being encrypted. When ransomware starts encrypting, it stops the process and puts the affected files back the way they were.</li>
  <li><strong>Protection against new and unknown threats.</strong> Exploit prevention and behaviour detection catch attacks that have no signature yet.</li>
  <li><strong>Network attack protection.</strong> It blocks malicious network traffic and intrusion attempts, including when staff connect to public Wi-Fi.</li>
  <li><strong>Device and web control.</strong> Block USB pen drives, cameras and other devices, or risky websites, across the company from one place.</li>
  <li><strong>Data loss prevention (DLP).</strong> Rules that watch where sensitive files go, such as uploads, email attachments or copies to removable drives, are included in the licence. For a deeper setup, see our <a href="/services/dlp-implementation">DLP implementation service</a>.</li>
  <li><strong>A lighter agent.</strong> Sophos says the 2025 Windows agent uses about 40% less memory than before.</li>
</ul>

<figure class="guide-figure tone-lime">
  <?= $this->include('partials/guide_art', ['name' => 'rollback']) ?>
  <figcaption>Ransomware rollback stops the encryption and puts the affected files back.</figcaption>
</figure>

<h3>Why not just use antivirus?</h3>
<p>This is the question we hear most. Antivirus gives you threat protection on one computer, and each copy works on its own. Nobody sees the whole company, policies are set PC by PC, and nothing stops a pen drive walking out with your client files. Managed endpoint security puts every device under one set of rules and one view. If you are weighing it up, our <a href="/services/endpoint-security">endpoint security service</a> page explains how we run it.</p>

<h2 id="licensing">How Sophos licensing works</h2>

<figure class="guide-figure tone-sky">
  <?= $this->include('partials/guide_art', ['name' => 'licence']) ?>
  <figcaption>One user licence covers that person’s computers. Servers are licensed separately.</figcaption>
</figure>

<ul>
  <li><strong>Per user, per year.</strong> You license the number of people, not devices. Sophos’s <a href="https://www.sophos.com/en-us/legal/license-entitlement-and-usage-policy" rel="noopener">licensing policy</a> lets each user protect “a reasonable number of computers”, so someone with a desktop and a laptop needs one licence.</li>
  <li><strong>Servers are separate.</strong> Each server needs a server licence.</li>
  <li><strong>1-year or 3-year terms.</strong> A 3-year term usually costs only slightly less per year, because prices rise each year. Its real benefit is that your price is locked for all three years.</li>
  <li><strong>Minimum of 5 users</strong> with HaloSec, which is where central management starts to pay off.</li>
  <li><strong>The licence lives in your own Sophos Central account.</strong> You own the account; your partner gets access only when you switch on partner support.</li>
</ul>

<h2 id="price">Sophos Endpoint price in India (2026)</h2>

<p>Partners cannot publish one fixed price, because the price per user depends on how many users you buy, the edition, the term and the partner. These are the ranges we see in the Indian market today, before GST:</p>

<table>
  <thead>
    <tr><th>Item</th><th>Typical range</th><th>Notes</th></tr>
  </thead>
  <tbody>
    <tr><td>Sophos Endpoint – User</td><td>₹1,500 to ₹4,000 per user per year</td><td>Some partners start around ₹1,200. The price per user drops as the number of users rises.</td></tr>
    <tr><td>Sophos Endpoint – Server</td><td>Quoted per server</td><td>Costs more than a user licence.</td></tr>
    <tr><td>Sophos EDR / XDR</td><td>Quoted</td><td>Priced above Sophos Endpoint, per user and per server.</td></tr>
    <tr><td>Sophos MDR</td><td>Quoted</td><td>Includes Sophos’s 24/7 monitoring and response.</td></tr>
    <tr><td>Setup (one-time)</td><td>₹100 to ₹500 per system</td><td>Depends on how many devices there are and how they are reached (on-site or remote).</td></tr>
  </tbody>
</table>

<p>GST at <strong>18%</strong> is added to all of these, with a GST invoice.</p>

<figure class="guide-figure tone-lime">
  <?= $this->include('partials/guide_art', ['name' => 'pricing']) ?>
  <figcaption>The more users you license, the lower the price per user.</figcaption>
</figure>

<h3>What moves the price</h3>
<ul>
  <li><strong>Number of users.</strong> The biggest factor: the more users, the lower the price per user.</li>
  <li><strong>Edition.</strong> Endpoint, EDR, XDR and MDR are separate price levels.</li>
  <li><strong>Servers.</strong> Licensed and priced separately.</li>
  <li><strong>Term.</strong> A long-term commitment gives some room to negotiate, and 3 years locks the price.</li>
  <li><strong>What the partner includes.</strong> Setup, policy tuning and ongoing support are where quotes differ most.</li>
</ul>

<h3>A worked example</h3>
<p>Say a 25-person office in Kochi is quoted ₹2,500 per user per year for Sophos Endpoint and ₹300 per system for setup. The figures are for illustration only:</p>
<ul>
  <li>Licences: 25 × ₹2,500 = <strong>₹62,500</strong> a year</li>
  <li>Setup: 25 × ₹300 = <strong>₹7,500</strong>, once</li>
  <li>First year: ₹70,000 + 18% GST = <strong>₹82,600</strong></li>
</ul>

<div class="callout">
  <p><strong>Comparing quotes?</strong> Check four things: the edition is Sophos Endpoint and not old Essentials stock, the licence is in your own Sophos Central account, setup and policy configuration are included or priced, and you know what renewal will cost. A cheap licence with no setup often ends up as expensive antivirus.</p>
</div>

<h2 id="kochi-kerala">Buying Sophos Endpoint in Kochi and Kerala</h2>

<p>HaloSec is a <strong>Sophos Authorized Partner</strong> based in Kochi, with Sophos accreditation for Endpoint and XDR and for MDR, and several Sophos-certified engineers on the team. In practice that means:</p>
<ul>
  <li><strong>On-site support anywhere in Kerala</strong>, and remote support anywhere in India. For visits outside Kochi, travel costs are billed to the client.</li>
  <li><strong>A real person when something goes wrong.</strong> When clients have had an attack or an urgent problem, we have gone on site and helped directly, often at no extra cost.</li>
  <li><strong>Renewal reminders</strong> before your licences expire, so protection never lapses.</li>
</ul>

<figure class="guide-figure tone-sky">
  <?= $this->include('partials/guide_art', ['name' => 'support']) ?>
  <figcaption>An office in Kochi, on-site support across Kerala, remote support anywhere in India.</figcaption>
</figure>
<p>Whoever you buy from, check that they appear in Sophos’s partner locator on sophos.com, and that the licence is issued to your company.</p>

<h2 id="how-buying-works">How buying works, step by step</h2>

<div class="step">
  <h3><span class="step-num">1</span> Count users and servers</h3>
  <p>Tell us how many people use computers, how many servers you run and what you protect today. That is enough for a first estimate.</p>
</div>

<div class="step">
  <h3><span class="step-num">2</span> Try it free for 30 days</h3>
  <p>We set up a 30-day trial in a Sophos Central account so you can see the dashboard, alerts and policies on your own machines before paying.</p>
</div>

<div class="step">
  <h3><span class="step-num">3</span> Get a written quote</h3>
  <p>Licences per user and per server, the term, the setup fee and GST, itemised.</p>
</div>

<div class="step">
  <h3><span class="step-num">4</span> Deployment, usually within a week</h3>
  <p>We remove the old antivirus, install the Sophos agent on every computer and server, and set up the policies you need: USB and device control, web control, DLP rules and alerts. Most rollouts are fully set up about a week after the order.</p>
</div>

<div class="step">
  <h3><span class="step-num">5</span> Ongoing support</h3>
  <p>You switch on <strong>partner support</strong> in your Sophos Central dashboard, which lets us help remotely without visiting. On-site support is available whenever you prefer it, and we remind you before renewal.</p>
</div>

<h2 id="case-study">A real rollout: an audit firm with offices in Thrissur and the UAE</h2>

<p>An auditing firm with 100 to 200 staff across Thrissur and the UAE came to us with one main worry: they could not see where their data was going. Client financial records are the business, and a single leak would be serious.</p>

<p>They wanted three things: protection from ransomware, something stronger than standalone antivirus on each PC, and a way to stop staff copying data to pen drives. We gathered their requirements first, then deployed Sophos Endpoint across the firm, blocked unapproved USB storage, and used the DLP rules included in Sophos Endpoint to monitor sensitive client data wherever it moved.</p>

<p>The firm was very happy with the result, and we still look after their IT security today. The lesson for similar firms: the DLP and device control you need are often already in the endpoint licence, but they only work once someone sets the rules for your business.</p>

<h2 id="mistakes">Security mistakes we see at Kerala businesses</h2>

<p>Endpoint protection is one layer. These are the gaps we find most often at small and mid-sized companies, and they cause more damage than any missing tool:</p>
<ul>
  <li>One flat network with no segmentation, so one infected PC can reach everything.</li>
  <li>No multi-factor authentication on email and remote access.</li>
  <li>No monitoring, so nobody notices an attack until files are already encrypted.</li>
  <li>Passwords shared with outside vendors and never changed.</li>
  <li>Passwords saved on the desktop or on sticky notes.</li>
  <li>Weak, easy-to-guess passwords on important systems.</li>
</ul>
<p>If you are not sure which of these apply to you, our <a href="/free-audit">free security audit</a> takes a few minutes and gives you a plain-language report. Protecting customer data is also a legal duty now: see our <a href="/guides/dpdp-act-compliance-checklist">DPDP Act compliance checklist</a>.</p>

<h2 id="next">Next step</h2>

<p>Send us your number of users and servers through the <a href="/services/endpoint-security#enquiry">endpoint security enquiry form</a> for a written quote and a free 30-day trial. If you would rather have someone manage Sophos for you each month, see our <a href="/services/security-team-outsourcing">outsourced security team</a>.</p>
