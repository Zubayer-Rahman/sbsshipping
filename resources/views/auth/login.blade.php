<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SBS Shipping — Login</title>
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
            font-family: 'inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0f1f4b;
            overflow: hidden;
        }

        .container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            margin: auto;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 48px rgba(26, 86, 219, .25);
        }

        /* Left panel */
        .login-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 30% 50%, rgba(26, 86, 219, .35) 0%, transparent 70%),
                radial-gradient(ellipse at 80% 20%, rgba(6, 182, 212, .2) 0%, transparent 60%);
        }

        .left-content {
            position: relative;
            z-index: 1;
        }

        .left-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 60px;
        }

        .brand-icon-lg {
            width: 52px;
            height: 52px;
            background: #1a56db;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #fff;
            box-shadow: 0 8px 24px rgba(26, 86, 219, .5);
        }

        .brand-name {
            font-size: 24px;
            line-height: normal;
            font-weight: 800;
            color: #fff;
        }

        .brand-name span {
            color: #06b6d4;
        }

        .left-heading {
            font-size: 42px;
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .left-heading em {
            font-style: normal;
            background: linear-gradient(135deg, #06b6d4, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .left-desc {
            font-size: 16px;
            color: rgba(255, 255, 255, .6);
            line-height: 1.7;
            max-width: 400px;
        }

        /* Floating decoration dots */
        .deco {
            position: absolute;
            border-radius: 50%;
            opacity: .12;
        }

        .deco-1 {
            width: 400px;
            height: 400px;
            background: #1a56db;
            top: -100px;
            right: -100px;
        }

        .deco-2 {
            width: 200px;
            height: 200px;
            background: #06b6d4;
            bottom: 80px;
            left: 40px;
        }

        .deco-3 {
            width: 80px;
            height: 80px;
            background: #fff;
            bottom: 200px;
            right: 60px;
        }

        /* Right panel - form */
        .login-right {
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 45px;
        }

        .form-heading {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .form-subheading {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 36px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 7px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 17px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }

        .form-control:focus {
            border-color: #1a56db;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(26, 86, 219, .12);
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #475569;
            cursor: pointer;
        }

        .remember input {
            accent-color: #1a56db;
            width: 15px;
            height: 15px;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: #1a56db;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 4px 16px rgba(26, 86, 219, .35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            background: #1340b0;
            box-shadow: 0 8px 24px rgba(26, 86, 219, .45);
            transform: translateY(-1px);
        }

        .error-msg {
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

        @media (max-width: 900px) {
            .login-left{
                display: none;
            }
            .login-right {
                width: 100%;
                padding: 40px 28px;
            }
            .container {
                margin: 10px auto;
            }
        }
    </style>
</head>

<body>
    <div class="deco deco-1"></div>
    <div class="deco deco-2"></div>
    <div class="deco deco-3"></div>

    <!-- Left Branding -->
    <div class="container">
        <div class="login-left">
            <div class="left-content">
                <div class="left-brand">
                    <div class="brand-icon-lg"><i class="bi bi-tsunami"></i></div>
                    <div class="brand-name"><span>SBS</span> Shipping</div>
                </div>
                <h1 class="left-heading">
                    Manage Your<br>
                    <em>Shipping Operations</em><br>
                    Seamlessly.
                </h1>
                <p class="left-desc">
                    Track jobs, manage clients, generate bills, and monitor your
                    business performance — all in one place.
                </p>
            </div>
        </div>

        <!-- Right Form -->
        <div class="login-right">
            <h2 class="form-heading">Welcome back 👋</h2>
            <p class="form-subheading">Sign in to your dashboard</p>

            @if($errors->any())
            <div class="error-msg">
                <i class="bi bi-exclamation-circle-fill"></i>
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="/login">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="input-wrapper">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" name="email" class="form-control"
                            placeholder="you@example.com"
                            value="{{ old('email') }}" autocomplete="email">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" name="password" class="form-control"
                            placeholder="••••••••" autocomplete="current-password">
                    </div>
                </div>
                <div class="form-options">
                    <label class="remember">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                </div>
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>
            </form>
        </div>
    </div>
</body>

</html>