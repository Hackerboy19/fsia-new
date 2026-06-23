<?php
/**
 * FSIA — Reusable Form Header & Dynamic Support Components
 * Re-engineered with premium Tailwind CSS interactions.
 * Components: Hero Header · Trust Strip · Account Manager Panel
 */

/* ─────────────────────────────────────────────────────────────
   1. HERO HEADER — render_form_hero()
   ───────────────────────────────────────────────────────────── */
if (!function_exists('render_form_hero')) {
    function render_form_hero($context = null) {
        if ($context === null) {
            $current_page = basename($_SERVER['PHP_SELF']);
            if (in_array($current_page, ['registration-success.php', 'payment-success.php', 'payment-new.php'])) {
                $context = 'status';
            } else {
                $context = 'register';
            }
        }

        if ($context === 'status') {
            $title = 'Registration Status';
        } else {
            // Auto-detect based on current page context
            $current_page = basename($_SERVER['PHP_SELF']);
            if (strpos($current_page, 'mrs-universe') !== false) {
                $title = 'Mrs Universe 2026';
            } elseif (strpos($current_page, 'miss-universe') !== false) {
                $title = 'Miss Universe 2026';
            } elseif (strpos($current_page, 'mrs-world') !== false) {
                $title = 'Mrs World 2026';
            } elseif (strpos($current_page, 'miss-world') !== false) {
                $title = 'Miss World 2026';
            } elseif (strpos($current_page, 'forever-miss-india') !== false) {
                $title = 'Forever Miss India 2026';
            } elseif (strpos($current_page, 'forever-mrs-india') !== false) {
                $title = 'Forever Mrs India 2026';
            } elseif (strpos($current_page, 'forever-miss-teen') !== false) {
                $title = 'Forever Miss Teen India 2026';
            } elseif (strpos($current_page, 'register-miss-fsia') !== false) {
                $title = 'Miss FSIA International 2026';
            } elseif (strpos($current_page, 'register-mrs-fsia') !== false) {
                $title = 'Mrs FSIA International 2026';
            } elseif (strpos($current_page, 'super-woman') !== false) {
                $title = 'Super Woman 2026';
            } elseif (strpos($current_page, 'super-hero') !== false) {
                $title = 'Super Hero 2026';
            } else {
                $title = 'Miss Universe 2026';
            }
        }

        $description = ($context === 'status')
            ? 'Thank you for connecting with the Globe\'s top beauty platform. Review your transaction tracking updates and official next steps below.'
            : 'As the globe\'s premier platform for world-class talent and pageantry, Forever Star India proudly hosts an elite international competition across 139 nations. Secure your place on the global stage. Completing our secure participation profile below grants you immediate entry into the international selection framework from anywhere in the world.';
        ?>

        <!-- ╔══════════════════════════════════════════════════╗ -->
        <!-- ║           HERO HEADER SECTION                   ║ -->
        <!-- ╚══════════════════════════════════════════════════╝ -->
        <style>
            /* Keyframe Animations */
            @keyframes shimmer-bar {
                0%   { background-position: -200% center; }
                100% { background-position: 200% center; }
            }
            @keyframes glow-pulse {
                0%, 100% { box-shadow: 0 0 0px 0px rgba(245, 158, 11, 0); }
                50%       { box-shadow: 0 0 28px 6px rgba(245, 158, 11, 0.13); }
            }
            @keyframes live-ping {
                0%        { transform: scale(1); opacity: 1; }
                70%, 100% { transform: scale(2.2); opacity: 0; }
            }
            @keyframes gradient-shift {
                0%   { background-position: 0% 50%; }
                50%  { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            @keyframes count-up {
                from { opacity: 0; transform: translateY(8px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            @keyframes fade-slide-down {
                from { opacity: 0; transform: translateY(-10px); }
                to   { opacity: 1; transform: translateY(0); }
            }

            .fsia-shimmer-bar {
                background: linear-gradient(
                    90deg,
                    #f59e0b 0%, #fcd34d 40%, #f59e0b 60%, #d97706 100%
                );
                background-size: 200% auto;
                animation: shimmer-bar 3s linear infinite;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            .fsia-desc-glow:hover {
                animation: glow-pulse 1.8s ease-in-out infinite;
            }
            .fsia-live-dot {
                animation: live-ping 1.6s cubic-bezier(0.215,0.61,0.355,1) infinite;
            }
            .fsia-gradient-btn {
                background: linear-gradient(270deg, #1e293b, #a6093d, #c97d10, #1e293b);
                background-size: 300% 300%;
                animation: gradient-shift 5s ease infinite;
            }
            .fsia-manager-panel {
                max-height: 0;
                overflow: hidden;
                opacity: 0;
                transition: max-height 0.45s cubic-bezier(0.4,0,0.2,1),
                            opacity 0.35s ease,
                            transform 0.35s ease;
                transform: translateY(-8px);
            }
            .fsia-manager-panel.open {
                max-height: 1400px;
                opacity: 1;
                transform: translateY(0);
            }
            .fsia-trust-badge {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            .fsia-trust-badge:hover {
                transform: scale(1.02) translateY(-2px);
                box-shadow: 0 8px 32px rgba(166, 9, 61, 0.10), 0 2px 8px rgba(245, 158, 11, 0.08);
            }
            .fsia-manager-card {
                transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            }
            .fsia-manager-card:hover {
                transform: scale(1.02);
                box-shadow: 0 6px 24px rgba(0,0,0,0.07);
                border-color: #d1d5db;
            }
        </style>

        <div class="text-center py-10 px-4" style="font-family: 'Outfit', sans-serif;">

            <!-- Brand Sub-Header -->
            <span class="text-[11px] sm:text-xs font-black tracking-[0.5em] uppercase mb-3 block text-[#f1a80a]">
                ✦   F O R E V E R   ✦
            </span>

            <!-- Main Title Heading -->
            <h1 class="text-3xl sm:text-4xl md:text-[3.25rem] font-bold text-slate-900 tracking-tight leading-none font-serif mb-4"
                style="font-family: 'Playfair Display', Georgia, serif;">
                <?php echo htmlspecialchars($title); ?>
            </h1>

            <!-- Decorative Gold Underline -->
            <div class="flex items-center justify-center gap-2 mb-6">
                <div class="h-px w-10 bg-gradient-to-r from-transparent to-amber-400 rounded-full"></div>
                <div class="h-[3px] w-12 rounded-full bg-gradient-to-r from-amber-400 via-amber-500 to-amber-400"></div>
                <div class="w-2 h-2 rounded-full bg-amber-500 ring-2 ring-amber-300/40"></div>
                <div class="h-[3px] w-12 rounded-full bg-gradient-to-r from-amber-400 via-amber-500 to-amber-400"></div>
                <div class="h-px w-10 bg-gradient-to-l from-transparent to-amber-400 rounded-full"></div>
            </div>

            <!-- Sub-Description with Glow Hover -->
            <div class="fsia-desc-glow max-w-2xl mx-auto rounded-2xl px-5 py-4 transition-all duration-500 cursor-default
                        bg-gradient-to-br from-white/60 to-slate-50/60 border border-slate-100/80 shadow-sm">
                <p class="text-sm sm:text-[15px] text-slate-500 leading-relaxed font-normal tracking-wide">
                    <?php echo htmlspecialchars($description); ?>
                </p>
            </div>

            <!-- ════════════════════════════════════════════
                 TRUST STRIP — 3 interactive badge anchors
                 ════════════════════════════════════════════ -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-7 mb-1 max-w-3xl mx-auto text-left">

                <!-- Badge 1: Global Reach -->
                <div class="fsia-trust-badge flex items-start gap-3 bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm cursor-default">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <circle cx="12" cy="12" r="10"></circle>
                          <path d="M2 12h20"></path>
                          <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-800 tracking-tight leading-tight mb-0.5">
                            139+ Nations Covered
                        </p>
                        <p class="text-[10px] sm:text-[11px] text-slate-400 font-medium leading-snug">
                            The largest global participation framework.
                        </p>
                    </div>
                </div>

                <!-- Badge 2: ISO Credential -->
                <div class="fsia-trust-badge flex items-start gap-3 bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm cursor-default">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-800 tracking-tight leading-tight mb-0.5">
                            ISO 9001:2015 Certified Platform
                        </p>
                        <p class="text-[10px] sm:text-[11px] text-slate-400 font-medium leading-snug">
                            Secure data encryption &amp; official international processing.
                        </p>
                    </div>
                </div>

                <!-- Badge 3: Live Registration Counter -->
                <div class="fsia-trust-badge flex items-start gap-3 bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm cursor-default">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 0 0 1.946-.806 3.42 3.42 0 0 1 4.438 0 3.42 3.42 0 0 0 1.946.806 3.42 3.42 0 0 1 3.138 3.138 3.42 3.42 0 0 0 .806 1.946 3.42 3.42 0 0 1 0 4.438 3.42 3.42 0 0 0-.806 1.946 3.42 3.42 0 0 1-3.138 3.138 3.42 3.42 0 0 0-1.946.806 3.42 3.42 0 0 1-4.438 0 3.42 3.42 0 0 0-1.946-.806 3.42 3.42 0 0 1-3.138-3.138 3.42 3.42 0 0 0-.806-1.946 3.42 3.42 0 0 1 0-4.438 3.42 3.42 0 0 0 .806-1.946 3.42 3.42 0 0 1 3.138-3.138z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <p class="text-xs font-black text-slate-800 tracking-tight leading-tight">
                                45,000+ Verified
                            </p>
                            <!-- Live Pulse Dot -->
                            <span class="relative flex h-2 w-2 flex-shrink-0">
                                <span class="fsia-live-dot absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                            </span>
                        </div>
                        <p class="text-[10px] sm:text-[11px] text-slate-400 font-medium leading-snug">
                            Registrations validated in real-time globally.
                        </p>
                    </div>
                </div>

            </div>
            <!-- END TRUST STRIP -->

        </div>
        <?php
    }
}


/* ─────────────────────────────────────────────────────────────
   2. EMERGENCY SUPPORT MODULE — render_emergency_support()
   ───────────────────────────────────────────────────────────── */
if (!function_exists('render_emergency_support')) {
    function render_emergency_support() {

        $managers = [
            [
                'name'    => 'Rajesh Sharma',
                'role'    => 'Chief Audition Coordinator',
                'dept'    => 'Online Audition Inquiries',
                'phone'   => '919983286999',
                'payload' => 'Hi Rajesh! I need help coordinating my Audition scheduling for Miss Universe 2026. Could you please assist me?',
            ],
            [
                'name'    => 'Priya Patel',
                'role'    => 'Payment & Documentation Desk',
                'dept'    => 'North-Zone Document Desk',
                'phone'   => '919983599666',
                'payload' => 'Hi Priya! I have a query regarding my payment confirmation and document verification for Miss Universe 2026. Please guide me.',
            ],
            [
                'name'    => 'Anjali Mehta',
                'role'    => 'Grooming & Training Support',
                'dept'    => 'Finalist Grooming Division',
                'phone'   => '919983286999',
                'payload' => 'Hi Anjali! I have a query about the training schedule and grooming sessions for Miss Universe 2026.',
            ],
            [
                'name'    => 'Vikram Singh',
                'role'    => 'Regional Audition Coordinator',
                'dept'    => 'State & City Auditions Desk',
                'phone'   => '919983599666',
                'payload' => 'Hi Vikram! I need assistance with local audition venues and dates for Miss Universe 2026 in my city.',
            ],
            [
                'name'    => 'Neha Verma',
                'role'    => 'Candidate Support Helpdesk',
                'dept'    => 'Applicant Eligibility Support',
                'phone'   => '919983286999',
                'payload' => 'Hi Neha! I would like to inquire about my eligibility and application status for Miss Universe 2026.',
            ],
            [
                'name'    => 'Amit Kulkarni',
                'role'    => 'Global Crown Nomination Desk',
                'dept'    => 'Global Crown Nomination Desk',
                'phone'   => '919983599666',
                'payload' => 'Hi Amit! I am experiencing a technical issue with the registration form or OTP verification. Please help me.',
            ],
        ];
        ?>

        <!-- ╔══════════════════════════════════════════════════╗ -->
        <!-- ║       ACCOUNT MANAGER SUPPORT MODULE            ║ -->
        <!-- ╚══════════════════════════════════════════════════╝ -->
        <div class="w-full mt-5" style="font-family: 'Outfit', sans-serif;" id="fsia-support-module">

            <!-- ── Primary Gradient CTA Trigger ── -->
            <button type="button"
                    id="btnToggleSupport"
                    aria-expanded="false"
                    aria-controls="fsiaManagerPanel"
                    class="fsia-gradient-btn w-full text-white font-bold py-4 px-6 rounded-2xl
                           transition duration-300 flex items-center justify-between
                           shadow-lg hover:shadow-xl hover:scale-[1.01] active:scale-[0.99]
                           focus:outline-none focus:ring-2 focus:ring-amber-500/40 text-sm sm:text-base">
                <span class="flex items-center gap-2.5">
                    <span class="text-lg">❓</span>
                    <span>Unsure about your eligibility or processing fees? Click here to view assigned account managers.</span>
                </span>
                <svg id="supportToggleChevron"
                     xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5 flex-shrink-0 transition-transform duration-300"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- ── Expandable Manager Panel ── -->
            <div id="fsiaManagerPanel"
                 role="region"
                 class="fsia-manager-panel mt-3">

                <!-- Panel Header -->
                <div class="mb-4 px-1">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">
                        ● Select Your Assigned Manager
                    </p>
                </div>

                <!-- 2-Column Manager Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php foreach ($managers as $i => $manager):
                        $wa_link = 'https://wa.me/' . $manager['phone'] . '?text=' . urlencode($manager['payload']);
                        // Alternate subtle background tint
                        $card_accent = ($i % 2 === 0) ? 'from-slate-50 to-white' : 'from-white to-slate-50/50';
                    ?>
                    <div class="fsia-manager-card bg-gradient-to-br <?php echo $card_accent; ?>
                                border border-slate-200/70 rounded-2xl p-4
                                flex flex-col justify-between gap-3 shadow-sm"
                         style="animation: fade-slide-down 0.3s ease both; animation-delay: <?php echo ($i * 60); ?>ms;">

                        <!-- Top: Name + Status + Dept -->
                        <div>
                            <!-- Status + Dept Row -->
                            <div class="flex items-center justify-between mb-2.5 flex-wrap gap-1">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700
                                             bg-emerald-50 border border-emerald-200/70 rounded-full px-2.5 py-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                    Online &amp; Active Now
                                </span>
                                <span class="text-[9px] font-bold text-amber-600 bg-amber-50
                                             border border-amber-200/60 rounded-full px-2 py-0.5 uppercase tracking-wide">
                                    <?php echo htmlspecialchars($manager['dept']); ?>
                                </span>
                            </div>

                            <!-- Name & Role -->
                            <h4 class="font-extrabold text-slate-800 text-sm leading-tight mb-0.5">
                                <?php echo htmlspecialchars($manager['name']); ?>
                            </h4>
                            <p class="text-slate-400 text-[11px] font-medium">
                                <?php echo htmlspecialchars($manager['role']); ?>
                            </p>
                        </div>

                        <!-- WhatsApp CTA -->
                        <a href="<?php echo $wa_link; ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="group inline-flex items-center justify-center gap-2
                                  bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                                  text-white text-xs font-bold py-2.5 px-4 rounded-xl
                                  transition duration-200 hover:scale-[1.02] shadow-sm hover:shadow-emerald-500/25
                                  w-full text-center">
                            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
                                <path d="M16.04 4C9.96 4 5.02 8.94 5.02 15.02c0 1.94.51 3.83 1.47 5.5L4.9 27.2l6.84-1.79c1.61.88 3.43 1.34 5.28 1.34h.01c6.08 0 11.02-4.94 11.02-11.02C28.05 8.94 23.11 4 16.04 4zm0 20.2h-.01c-1.65 0-3.27-.44-4.68-1.28l-.34-.2-3.55.93.95-3.46-.22-.36a9.13 9.13 0 0 1-1.4-4.86c0-5.05 4.11-9.16 9.17-9.16 2.45 0 4.75.96 6.48 2.69a9.1 9.1 0 0 1 2.68 6.48c0 5.05-4.11 9.16-9.16 9.16zm5.03-6.86c-.28-.14-1.63-.8-1.88-.9-.25-.09-.43-.14-.62.14-.18.28-.71.9-.87 1.08-.16.18-.32.2-.6.07-.28-.14-1.16-.43-2.21-1.36-.82-.73-1.37-1.63-1.53-1.91-.16-.28-.02-.43.12-.57.13-.13.28-.32.42-.49.14-.16.18-.28.28-.46.09-.18.05-.35-.02-.49-.07-.14-.62-1.5-.85-2.05-.22-.54-.45-.47-.62-.48l-.53-.01c-.18 0-.48.07-.74.35-.25.28-.96.94-.96 2.3 0 1.36.99 2.67 1.12 2.85.14.18 1.95 2.98 4.73 4.18.66.28 1.18.45 1.58.58.66.21 1.27.18 1.74.11.53-.08 1.63-.67 1.86-1.31.23-.64.23-1.19.16-1.31-.07-.12-.25-.18-.53-.32z"/>
                            </svg>
                            <span>Chat with <?php echo htmlspecialchars(explode(' ', $manager['name'])[0]); ?> →</span>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Panel Footer Note -->
                <div class="mt-4 text-center">
                    <p class="text-[10px] text-slate-400 font-medium">
                        🔒 All conversations are private &amp; secure. Response time: &lt; 2 minutes.
                    </p>
                </div>

            </div>
            <!-- END MANAGER PANEL -->

        </div>

        <script>
        (function () {
            'use strict';
            var btn    = document.getElementById('btnToggleSupport');
            var panel  = document.getElementById('fsiaManagerPanel');
            var chevron = document.getElementById('supportToggleChevron');
            if (!btn || !panel) return;

            var isOpen = false;

            btn.addEventListener('click', function () {
                isOpen = !isOpen;

                if (isOpen) {
                    panel.classList.add('open');
                    btn.setAttribute('aria-expanded', 'true');
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                    // Smooth scroll to panel on mobile
                    if (window.innerWidth < 768) {
                        setTimeout(function () {
                            panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }, 100);
                    }
                } else {
                    panel.classList.remove('open');
                    btn.setAttribute('aria-expanded', 'false');
                    if (chevron) chevron.style.transform = 'rotate(0deg)';
                }
            });

            // Close on Escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && isOpen) {
                    isOpen = false;
                    panel.classList.remove('open');
                    btn.setAttribute('aria-expanded', 'false');
                    if (chevron) chevron.style.transform = 'rotate(0deg)';
                    btn.focus();
                }
            });
        })();
        </script>
        <?php
    }
}
?>
