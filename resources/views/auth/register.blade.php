<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SBS Shipping — Create Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0f1f4b;
            overflow-x: hidden;
        }

        /* ── Animated background particles ─────────────────────── */
        .bg-particles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            opacity: 0;
            animation: float linear infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: .4;
            }

            100% {
                transform: translateY(-20vh) scale(1);
                opacity: 0;
            }
        }

        /* ── Left panel ─────────────────────────────────────────── */
        .left-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 56px;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 60%, rgba(26, 86, 219, .4) 0%, transparent 65%),
                radial-gradient(ellipse at 85% 15%, rgba(6, 182, 212, .25) 0%, transparent 55%),
                radial-gradient(ellipse at 60% 90%, rgba(99, 102, 241, .2) 0%, transparent 50%);
        }

        /* Geometric grid overlay */
        .left-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, .03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .03) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        .left-content {
            position: relative;
            z-index: 2;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 64px;
        }

        .brand-icon {
            width: 50px;
            height: 50px;
            background: #1a56db;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #fff;
            box-shadow: 0 8px 28px rgba(26, 86, 219, .5);
            animation: pulse-icon 3s ease-in-out infinite;
        }

        @keyframes pulse-icon {

            0%,
            100% {
                box-shadow: 0 8px 28px rgba(26, 86, 219, .5);
            }

            50% {
                box-shadow: 0 8px 40px rgba(26, 86, 219, .8);
            }
        }

        .brand-name {
            font-family: 'Syne', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: #fff;
        }

        .brand-name span {
            color: #06b6d4;
        }

        .left-heading {
            font-family: 'Syne', sans-serif;
            font-size: 40px;
            font-weight: 800;
            color: #fff;
            line-height: 1.18;
            margin-bottom: 22px;
        }

        .left-heading em {
            font-style: normal;
            background: linear-gradient(135deg, #06b6d4 0%, #60a5fa 50%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: shimmer 4s linear infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: 0% center;
            }

            100% {
                background-position: 200% center;
            }
        }

        .left-desc {
            font-size: 15px;
            color: rgba(255, 255, 255, .58);
            line-height: 1.75;
            max-width: 380px;
            margin-bottom: 44px;
        }

        /* Feature checklist */
        .features {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 12px;
            opacity: 0;
            animation: slide-in .5s ease forwards;
        }

        .feature:nth-child(1) {
            animation-delay: .1s;
        }

        .feature:nth-child(2) {
            animation-delay: .25s;
        }

        .feature:nth-child(3) {
            animation-delay: .4s;
        }

        .feature:nth-child(4) {
            animation-delay: .55s;
        }

        @keyframes slide-in {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .feature-icon {
            width: 34px;
            height: 34px;
            background: rgba(26, 86, 219, .3);
            border: 1px solid rgba(26, 86, 219, .5);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #60a5fa;
            flex-shrink: 0;
        }

        .feature-text {
            font-size: 14px;
            color: rgba(255, 255, 255, .7);
            font-weight: 500;
        }

        /* Floating deco shapes */
        .deco-circle {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .06);
            animation: spin linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Right panel (form) ──────────────────────────────────── */
        .right-panel {
            width: 500px;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 48px 52px;
            position: relative;
            z-index: 1;
            overflow-y: auto;
        }

        .form-top {
            margin-bottom: 28px;
        }

        .form-heading {
            font-family: 'Syne', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .form-sub {
            font-size: 13.5px;
            color: #64748b;
        }

        .form-sub a {
            color: #1a56db;
            font-weight: 600;
            text-decoration: none;
        }

        .form-sub a:hover {
            text-decoration: underline;
        }

        /* Progress steps */
        .progress-steps {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 26px;
        }

        .ps {
            flex: 1;
            height: 3px;
            border-radius: 2px;
            background: #e2e8f0;
            transition: background .3s;
        }

        .ps.active {
            background: #1a56db;
        }

        .ps.done {
            background: #10b981;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 7px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .label-hint {
            font-weight: 400;
            font-size: 11px;
            color: #94a3b8;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
        }

        .input-suffix {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            font-size: 16px;
            transition: color .2s;
        }

        .input-suffix:hover {
            color: #1a56db;
        }

        .form-control {
            width: 100%;
            padding: 11px 13px 11px 40px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-family: inherit;
            font-size: 13.5px;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }

        .form-control:focus {
            border-color: #1a56db;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(26, 86, 219, .1);
        }

        .form-control.has-error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, .1);
        }

        .form-control.is-valid {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, .1);
        }

        .field-error {
            font-size: 11px;
            color: #ef4444;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Two-column row */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        /* Password strength */
        .pw-strength {
            margin-top: 8px;
        }

        .pw-bars {
            display: flex;
            gap: 4px;
            margin-bottom: 4px;
        }

        .pw-bar {
            flex: 1;
            height: 3px;
            border-radius: 2px;
            background: #e2e8f0;
            transition: background .3s;
        }

        .pw-label {
            font-size: 11px;
            color: #94a3b8;
        }

        /* Terms checkbox */
        .terms-row {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .terms-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            margin-top: 2px;
            accent-color: #1a56db;
            flex-shrink: 0;
            cursor: pointer;
        }

        .terms-text {
            font-size: 12.5px;
            color: #475569;
            line-height: 1.5;
        }

        .terms-text a {
            color: #1a56db;
            font-weight: 600;
            text-decoration: none;
        }

        /* Submit button */
        .btn-register {
            width: 100%;
            padding: 13px;
            background: #1a56db;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 4px 16px rgba(26, 86, 219, .35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .btn-register::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, .15), transparent);
            opacity: 0;
            transition: opacity .2s;
        }

        .btn-register:hover {
            background: #1340b0;
            box-shadow: 0 8px 24px rgba(26, 86, 219, .45);
            transform: translateY(-1px);
        }

        .btn-register:hover::before {
            opacity: 1;
        }

        .btn-register:active {
            transform: translateY(0);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0;
            font-size: 12px;
            color: #94a3b8;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        /* Error alert */
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @media (max-width: 960px) {
            .left-panel {
                display: none;
            }

            .right-panel {
                width: 100%;
                padding: 40px 28px;
            }
        }

        @media (max-width: 500px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- Animated background particles -->
    <div class="bg-particles" id="particles"></div>

    <!-- Deco circles on left -->
    <div class="deco-circle" style="width:500px;height:500px;top:-100px;left:-150px;animation-duration:40s"></div>
    <div class="deco-circle" style="width:300px;height:300px;bottom:50px;left:200px;animation-duration:28s;animation-direction:reverse"></div>

    <!-- ── LEFT PANEL ───────────────────────────────────────────────── -->
    <div class="left-panel">
        <div class="left-content">
            <div class="brand">
                <div class="brand-icon"><i class="bi bi-tsunami"></i></div>
                <div class="brand-name"><span>SBS</span> Shipping</div>
            </div>

            <h1 class="left-heading">
                Your shipping<br>
                operations,<br>
                <em>elevated.</em>
            </h1>

            <p class="left-desc">
                Join SBS Shipping to manage jobs, track shipments,
                generate invoices, and monitor your business — all from one dashboard.
            </p>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon"><i class="bi bi-briefcase-fill"></i></div>
                    <span class="feature-text">Full job lifecycle management</span>
                </div>
                <div class="feature">
                    <div class="feature-icon"><i class="bi bi-bar-chart-fill"></i></div>
                    <span class="feature-text">Real-time analytics & reports</span>
                </div>
                <div class="feature">
                    <div class="feature-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
                    <span class="feature-text">Instant invoice & bill generation</span>
                </div>
                <div class="feature">
                    <div class="feature-icon"><i class="bi bi-shield-lock-fill"></i></div>
                    <span class="feature-text">Secure, role-based access</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── RIGHT PANEL ──────────────────────────────────────────────── -->
    <div class="right-panel">
        <div class="form-top">
            <h2 class="form-heading">Create your account</h2>
            <p class="form-sub">
                Already have an account? <a href="/login">Sign in</a>
            </p>
        </div>

        <!-- Progress indicator -->
        <div class="progress-steps">
            <div class="ps active" id="ps1"></div>
            <div class="ps" id="ps2"></div>
            <div class="ps" id="ps3"></div>
        </div>

        @if($errors->any())
        <div class="alert-error">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="/register" id="registerForm">
            @csrf

            <!-- Name row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" name="first_name" class="form-control"
                            placeholder="Rahman"
                            value="{{ old('first_name') }}" autocomplete="given-name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" name="last_name" class="form-control"
                            placeholder="Khan"
                            value="{{ old('last_name') }}" autocomplete="family-name">
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control"
                        placeholder="you@company.com"
                        value="{{ old('email') }}" autocomplete="email"
                        id="emailInput">
                </div>
            </div>

            <!-- Phone -->
            <div class="form-group">
                <label class="form-label">
                    Phone Number
                    <span class="label-hint">optional</span>
                </label>
                <div class="input-wrapper">
                    <i class="bi bi-telephone input-icon"></i>
                    <input type="tel" name="phone" class="form-control"
                        placeholder="+880 1700 000000"
                        value="{{ old('phone') }}" autocomplete="tel">
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrapper">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" class="form-control"
                        placeholder="Min. 8 characters"
                        id="passwordInput" autocomplete="new-password">
                    <i class="bi bi-eye input-suffix" id="pwToggle"></i>
                </div>
                <!-- Password strength -->
                <div class="pw-strength">
                    <div class="pw-bars">
                        <div class="pw-bar" id="bar1"></div>
                        <div class="pw-bar" id="bar2"></div>
                        <div class="pw-bar" id="bar3"></div>
                        <div class="pw-bar" id="bar4"></div>
                    </div>
                    <div class="pw-label" id="pwLabel">Enter a password</div>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <div class="input-wrapper">
                    <i class="bi bi-lock-fill input-icon"></i>
                    <input type="password" name="password_confirmation" class="form-control"
                        placeholder="Repeat your password"
                        id="confirmInput" autocomplete="new-password">
                    <i class="bi bi-eye input-suffix" id="confirmToggle"></i>
                </div>
            </div>

            <!-- Terms -->
            <div class="terms-row">
                <input type="checkbox" name="terms" id="termsCheck">
                <label class="terms-text" for="termsCheck">
                    I agree to the <a href="#">Terms of Service</a> and
                    <a href="#">Privacy Policy</a> of SBS Shipping Management System.
                </label>
            </div>

            <button type="submit" class="btn-register">
                <i class="bi bi-person-plus-fill"></i>
                Create Account
            </button>

            <div class="divider">or</div>

            <div style="text-align:center; font-size:13px; color:#64748b">
                Already registered?
                <a href="/login" style="color:#1a56db;font-weight:600;text-decoration:none">
                    Sign in to your dashboard →
                </a>
            </div>
        </form>
    </div>

    <script>
        // ── Particles ─────────────────────────────────────────────────────────
        const container = document.getElementById('particles');
        const colors = ['rgba(26,86,219,.5)', 'rgba(6,182,212,.4)', 'rgba(99,102,241,.3)', 'rgba(96,165,250,.4)'];
        for (let i = 0; i < 18; i++) {
            const p = document.createElement('div');
            const size = Math.random() * 6 + 3;
            p.className = 'particle';
            p.style.cssText = `
            width:${size}px; height:${size}px;
            left:${Math.random() * 60}%;
            background:${colors[Math.floor(Math.random() * colors.length)]};
            animation-duration:${Math.random() * 12 + 10}s;
            animation-delay:${Math.random() * 10}s;
        `;
            container.appendChild(p);
        }

        // ── Password visibility toggle ────────────────────────────────────────
        function setupToggle(inputId, toggleId) {
            document.getElementById(toggleId).addEventListener('click', function() {
                const input = document.getElementById(inputId);
                const isText = input.type === 'text';
                input.type = isText ? 'password' : 'text';
                this.className = `bi bi-eye${isText ? '' : '-slash'} input-suffix`;
            });
        }
        setupToggle('passwordInput', 'pwToggle');
        setupToggle('confirmInput', 'confirmToggle');

        // ── Password strength meter ───────────────────────────────────────────
        const bars = [1, 2, 3, 4].map(i => document.getElementById('bar' + i));
        const pwLabel = document.getElementById('pwLabel');
        const strengthColors = ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
        const strengthLabels = ['Weak', 'Fair', 'Good', 'Strong'];

        document.getElementById('passwordInput').addEventListener('input', function() {
            const v = this.value;
            let score = 0;
            if (v.length >= 8) score++;
            if (/[A-Z]/.test(v)) score++;
            if (/[0-9]/.test(v)) score++;
            if (/[^A-Za-z0-9]/.test(v)) score++;

            bars.forEach((b, i) => {
                b.style.background = i < score ? strengthColors[score - 1] : '#e2e8f0';
            });
            pwLabel.textContent = v.length === 0 ? 'Enter a password' : strengthLabels[score - 1] || 'Weak';
            pwLabel.style.color = v.length === 0 ? '#94a3b8' : strengthColors[score - 1];

            // progress step 2
            document.getElementById('ps2').style.background = score >= 2 ? '#1a56db' : '#e2e8f0';
            document.getElementById('ps3').style.background = score >= 4 ? '#10b981' : '#e2e8f0';
        });

        // ── Confirm match indicator ───────────────────────────────────────────
        document.getElementById('confirmInput').addEventListener('input', function() {
            const pw = document.getElementById('passwordInput').value;
            if (this.value.length === 0) {
                this.className = 'form-control';
                return;
            }
            this.className = 'form-control ' + (this.value === pw ? 'is-valid' : 'has-error');
        });

        // ── Progress step on email fill ───────────────────────────────────────
        document.getElementById('emailInput').addEventListener('blur', function() {
            if (this.value.includes('@')) {
                document.getElementById('ps1').style.background = '#10b981';
            }
        });
    </script>
</body>

</html>