<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>LaundryPro | Smart Login · Animated Guidance</title>
    <!-- Bootstrap 5 CSS + Icons + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #d4f1f9 0%, #b9e6f0 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* animated bubbles background */
        .bubble-bg {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .bubble {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.5) 0%, rgba(0,180,216,0.12) 100%);
            animation: floatBubble 22s infinite alternate ease-in-out;
        }

        .bubble-1 { width: 420px; height: 420px; top: -140px; left: -170px; animation-duration: 26s; }
        .bubble-2 { width: 550px; height: 550px; bottom: -200px; right: -200px; animation-duration: 32s; animation-delay: -5s; }
        .bubble-3 { width: 280px; height: 280px; top: 45%; left: 75%; animation-duration: 19s; animation-delay: -7s; }

        @keyframes floatBubble {
            0% { transform: translate(0, 0) scale(1); opacity: 0.5; }
            100% { transform: translate(3%, 5%) scale(1.08); opacity: 0.85; }
        }

        /* main container */
        .login-wrapper {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* fresh glassmorphic card */
        .login-card {
            border: none;
            border-radius: 2.2rem;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(2px);
            box-shadow: 0 25px 50px -12px rgba(0, 100, 120, 0.3);
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.3s;
        }

        .login-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 30px 55px rgba(0, 150, 170, 0.28);
        }

        /* fresh header */
        .card-header-fresh {
            background: linear-gradient(115deg, #00b4d8, #0284c7);
            padding: 1.6rem 1.2rem;
            text-align: center;
            border-bottom: none;
        }

        .card-header-fresh i {
            font-size: 2.4rem;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.08));
            color: white;
        }

        .card-header-fresh h4 {
            font-weight: 800;
            letter-spacing: -0.3px;
            color: white;
            margin: 0.5rem 0 0.2rem;
            font-size: 1.8rem;
        }

        .form-label {
            font-weight: 700;
            color: #02698b;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.4rem;
        }

        /* INPUT GROUP WITH INSIDE ANIMATIONS & GUIDANCE */
        .animated-input-group {
            position: relative;
            margin-bottom: 1.8rem;
        }

        .form-control-fresh {
            width: 100%;
            border-radius: 1.3rem;
            border: 1.5px solid #d4f0f5;
            padding: 0.85rem 1.2rem;
            font-size: 0.95rem;
            background: white;
            transition: all 0.25s ease;
            font-weight: 500;
        }

        .form-control-fresh:focus {
            border-color: #00b4d8;
            box-shadow: 0 0 0 4px rgba(0, 180, 216, 0.15);
            outline: none;
        }

        /* animated hint text inside input (placeholder animation + typing effect) */
        .form-control-fresh::placeholder {
            color: #b4d6e3;
            font-weight: 400;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
        }

        .form-control-fresh:focus::placeholder {
            color: #6bc2db;
            transform: translateX(4px);
            opacity: 0.7;
        }

        /* floating guidance badge (micro animation) */
        .input-guidance {
            position: absolute;
            bottom: -1.8rem;
            left: 1rem;
            font-size: 0.7rem;
            color: #3f8eab;
            display: flex;
            align-items: center;
            gap: 6px;
            opacity: 0;
            transform: translateY(-5px);
            transition: all 0.2s ease;
            pointer-events: none;
            font-weight: 500;
        }

        .animated-input-group:hover .input-guidance {
            opacity: 1;
            transform: translateY(0);
        }

        .animated-input-group:focus-within .input-guidance {
            opacity: 1;
            transform: translateY(0);
            color: #0284c7;
        }

        .guidance-icon {
            font-size: 0.7rem;
            animation: gentlePulse 1.2s infinite;
        }

        @keyframes gentlePulse {
            0% { opacity: 0.5; transform: scale(0.9);}
            100% { opacity: 1; transform: scale(1.1);}
        }

        /* dynamic typing simulation for placeholder (css only but we add JS magic) */
        .btn-laundry {
            background: linear-gradient(105deg, #00b4d8, #0284c7);
            border: none;
            border-radius: 2rem;
            padding: 0.85rem;
            font-weight: 800;
            font-size: 0.95rem;
            transition: all 0.25s;
            box-shadow: 0 8px 18px rgba(0, 150, 170, 0.3);
        }

        .btn-laundry:hover {
            transform: translateY(-2px);
            background: linear-gradient(105deg, #1fc6e8, #0a74a8);
            box-shadow: 0 12px 22px rgba(0, 180, 216, 0.4);
        }

        .alert-fresh {
            border-radius: 1rem;
            background: #fff5f0;
            border-left: 4px solid #f97316;
            font-size: 0.85rem;
            padding: 0.8rem 1rem;
        }

        .text-link-fresh {
            color: #0284c7;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .text-link-fresh:hover {
            color: #0369a1;
            text-decoration: underline;
        }

        .divider-clean {
            display: flex;
            align-items: center;
            margin: 1.5rem 0 1rem;
        }

        .divider-clean::before,
        .divider-clean::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #cbe9f0;
        }

        .divider-clean span {
            padding: 0 1rem;
            font-size: 0.7rem;
            color: #5fa3b9;
            font-weight: 600;
        }

        /* micro animation on error */
        @keyframes shakeSoft {
            0%,100%{ transform: translateX(0);}
            25%{ transform: translateX(-4px);}
            75%{ transform: translateX(4px);}
        }
        .shake-effect {
            animation: shakeSoft 0.25s ease-in-out 0s 2;
        }

        @media (max-width: 576px) {
            .login-wrapper { padding: 1rem; }
            .card-body { padding: 1.5rem; }
        }
    </style>
</head>
<body>

<div class="bubble-bg">
    <div class="bubble bubble-1"></div>
    <div class="bubble bubble-2"></div>
    <div class="bubble bubble-3"></div>
</div>

<div class="login-wrapper">
    <div class="container" style="max-width: 540px;">
        <div class="card login-card">
            <div class="card-header-fresh">
                <i class="fas fa-soap"></i>
                <h4>LaundryPro</h4>
                <p style="color: rgba(255,255,240,0.95); font-size: 0.8rem; margin-bottom: 0;">intelligent login · animated guidance</p>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <!-- Laravel validation errors -->
                @if($errors->any())
                    <div class="alert alert-fresh mb-4">
                        <i class="fas fa-circle-exclamation me-2"></i> Please fix the following:
                        <ul class="mt-2 mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- WORKING FORM SUBMISSION - route('login') -->
                <form method="POST" action="{{ route('login') }}" id="freshLoginForm">
                    @csrf
                    
                    <!-- EMAIL FIELD with animated guidance -->
                    <div class="animated-input-group">
                        <label for="email" class="form-label">
                            <i class="far fa-envelope me-1"></i> EMAIL ADDRESS
                        </label>
                        <input type="email" 
                               class="form-control form-control-fresh @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="e.g., manager@laundrypro.com"
                               required autofocus>
                        <div class="input-guidance">
                            <i class="fas fa-lightbulb guidance-icon"></i>
                            <span>use work email · example: hello@laundryhub.com</span>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- PASSWORD FIELD with dynamic animation -->
                    <div class="animated-input-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-key me-1"></i> PASSWORD
                        </label>
                        <input type="password" 
                               class="form-control form-control-fresh @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="at least 6 characters • secure"
                               required>
                        <div class="input-guidance">
                            <i class="fas fa-shield-alt guidance-icon"></i>
                            <span>strong & encrypted · min 6 chars</span>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- REMEMBER ME check -->
                    <div class="mb-3 form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" style="accent-color: #00b4d8;">
                        <label class="form-check-label" for="remember" style="color:#2c6e8f; font-weight: 500;">
                            <i class="fas fa-clock me-1"></i> Keep me signed in
                        </label>
                    </div>
                    
                    <!-- SUBMIT -->
                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-laundry text-white" id="loginSubmitBtn">
                            <i class="fas fa-arrow-right-to-bracket me-2"></i> SECURE LOGIN
                        </button>
                    </div>
                    
                    <div class="divider-clean">
                        <span>FRESH ACCESS</span>
                    </div>
                    
                    <div class="text-center">
                        <a href="{{ route('password.request') }}" class="text-link-fresh">
                            <i class="fas fa-lock me-1"></i> Recover password
                        </a>
                        <span class="mx-2 text-muted">|</span>
                        <a href="{{ route('register') }}" class="text-link-fresh">
                            <i class="fas fa-user-plus me-1"></i> Register new account
                        </a>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <small class="text-muted" style="font-size: 0.68rem;">
                        <i class="fas fa-soap"></i> animated smart fields · LaundryPro 2026
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function() {
        // Advanced animations inside inputs: dynamic placeholder typing effect + guidance micro-interactions
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const loginForm = document.getElementById('freshLoginForm');

        // DYNAMIC PLACEHOLDER TYPING ANIMATION (CYCLE professional tips)
        const emailPhrases = [
            "e.g., manager@laundrypro.com",
            "your work email address",
            "ex: care@laundryhub.com",
            "registered email · secure"
        ];
        const passwordPhrases = [
            "at least 6 characters • secure",
            "your strong passkey",
            "encrypted credential",
            "use your login token"
        ];
        
        let emailIndex = 0, passwordIndex = 0;
        let emailCharIndex = 0, passwordCharIndex = 0;
        let isEmailDeleting = false, isPasswordDeleting = false;
        let emailTimeout, passwordTimeout;

        function animateEmailPlaceholder() {
            const currentPhrase = emailPhrases[emailIndex];
            if (!isEmailDeleting && emailCharIndex <= currentPhrase.length) {
                emailInput.placeholder = currentPhrase.substring(0, emailCharIndex);
                emailCharIndex++;
                emailTimeout = setTimeout(animateEmailPlaceholder, 100);
                if (emailCharIndex === currentPhrase.length + 1) {
                    // keep full phrase for 2s then delete
                    setTimeout(() => {
                        isEmailDeleting = true;
                        animateEmailPlaceholder();
                    }, 2000);
                }
            } 
            else if (isEmailDeleting && emailCharIndex >= 0) {
                emailInput.placeholder = currentPhrase.substring(0, emailCharIndex);
                emailCharIndex--;
                emailTimeout = setTimeout(animateEmailPlaceholder, 50);
                if (emailCharIndex === 0) {
                    isEmailDeleting = false;
                    emailIndex = (emailIndex + 1) % emailPhrases.length;
                    emailTimeout = setTimeout(animateEmailPlaceholder, 300);
                }
            } else {
                emailTimeout = setTimeout(animateEmailPlaceholder, 100);
            }
        }

        function animatePasswordPlaceholder() {
            const currentPhrase = passwordPhrases[passwordIndex];
            if (!isPasswordDeleting && passwordCharIndex <= currentPhrase.length) {
                passwordInput.placeholder = currentPhrase.substring(0, passwordCharIndex);
                passwordCharIndex++;
                passwordTimeout = setTimeout(animatePasswordPlaceholder, 100);
                if (passwordCharIndex === currentPhrase.length + 1) {
                    setTimeout(() => {
                        isPasswordDeleting = true;
                        animatePasswordPlaceholder();
                    }, 2000);
                }
            } 
            else if (isPasswordDeleting && passwordCharIndex >= 0) {
                passwordInput.placeholder = currentPhrase.substring(0, passwordCharIndex);
                passwordCharIndex--;
                passwordTimeout = setTimeout(animatePasswordPlaceholder, 50);
                if (passwordCharIndex === 0) {
                    isPasswordDeleting = false;
                    passwordIndex = (passwordIndex + 1) % passwordPhrases.length;
                    passwordTimeout = setTimeout(animatePasswordPlaceholder, 300);
                }
            } else {
                passwordTimeout = setTimeout(animatePasswordPlaceholder, 100);
            }
        }

        // start typing animations only if inputs exist
        if (emailInput) {
            emailInput.placeholder = "";
            animateEmailPlaceholder();
            // add extra focus effect: stop animation to not disturb user while typing
            emailInput.addEventListener('focus', () => {
                clearTimeout(emailTimeout);
                if (emailInput.placeholder === "" || emailInput.placeholder.length < 3) {
                    emailInput.placeholder = emailPhrases[0];
                }
            });
            emailInput.addEventListener('blur', () => {
                if (!emailInput.value) {
                    emailCharIndex = 0;
                    isEmailDeleting = false;
                    animateEmailPlaceholder();
                }
            });
        }

        if (passwordInput) {
            passwordInput.placeholder = "";
            animatePasswordPlaceholder();
            passwordInput.addEventListener('focus', () => {
                clearTimeout(passwordTimeout);
                if (passwordInput.placeholder === "" || passwordInput.placeholder.length < 4) {
                    passwordInput.placeholder = passwordPhrases[0];
                }
            });
            passwordInput.addEventListener('blur', () => {
                if (!passwordInput.value) {
                    passwordCharIndex = 0;
                    isPasswordDeleting = false;
                    animatePasswordPlaceholder();
                }
            });
        }

        // Live Validation Guidance: show visual feedback + shake effect if user tries empty
        const submitBtn = document.getElementById('loginSubmitBtn');
        
        function validateInputsWithAnimation() {
            let isValid = true;
            const emailVal = emailInput?.value.trim();
            const passVal = passwordInput?.value;
            
            if (!emailVal) {
                emailInput.style.borderColor = '#f97316';
                emailInput.classList.add('shake-effect');
                setTimeout(() => emailInput.classList.remove('shake-effect'), 400);
                isValid = false;
            } else {
                emailInput.style.borderColor = '#d4f0f5';
            }
            if (!passVal) {
                passwordInput.style.borderColor = '#f97316';
                passwordInput.classList.add('shake-effect');
                setTimeout(() => passwordInput.classList.remove('shake-effect'), 400);
                isValid = false;
            } else if (passVal.length < 3) {
                passwordInput.style.borderColor = '#f97316';
                passwordInput.classList.add('shake-effect');
                setTimeout(() => passwordInput.classList.remove('shake-effect'), 400);
                isValid = false;
            } else {
                passwordInput.style.borderColor = '#d4f0f5';
            }
            return isValid;
        }

        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                if (!validateInputsWithAnimation()) {
                    e.preventDefault();
                    // show small guidance hint (dynamic inline)
                    let guidanceHint = document.createElement('div');
                    guidanceHint.innerText = "✨ Please fill both fields correctly ✨";
                    guidanceHint.style.position = 'fixed';
                    guidanceHint.style.bottom = '20px';
                    guidanceHint.style.left = '50%';
                    guidanceHint.style.transform = 'translateX(-50%)';
                    guidanceHint.style.background = '#fef9e3';
                    guidanceHint.style.padding = '8px 18px';
                    guidanceHint.style.borderRadius = '50px';
                    guidanceHint.style.fontSize = '0.75rem';
                    guidanceHint.style.fontWeight = '500';
                    guidanceHint.style.color = '#cc7b2c';
                    guidanceHint.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
                    guidanceHint.style.zIndex = '9999';
                    guidanceHint.style.borderLeft = '3px solid #f97316';
                    document.body.appendChild(guidanceHint);
                    setTimeout(() => guidanceHint.remove(), 2500);
                    return false;
                }
                // else form submits normally to Laravel route
                return true;
            });
        }

        // extra animations: focus guidance icon bounce
        const inputGroups = document.querySelectorAll('.animated-input-group');
        inputGroups.forEach(group => {
            const input = group.querySelector('input');
            const guidanceSpan = group.querySelector('.input-guidance');
            if (input && guidanceSpan) {
                input.addEventListener('focus', () => {
                    guidanceSpan.style.opacity = '1';
                    guidanceSpan.style.transform = 'translateY(0)';
                    guidanceSpan.style.color = '#0284c7';
                });
                input.addEventListener('blur', () => {
                    if (!input.value) {
                        guidanceSpan.style.opacity = '0.7';
                    } else {
                        guidanceSpan.style.opacity = '0.6';
                    }
                });
            }
        });

        // Smooth card entrance
        const loginCard = document.querySelector('.login-card');
        if (loginCard) {
            loginCard.style.opacity = '0';
            loginCard.style.transform = 'translateY(18px)';
            setTimeout(() => {
                loginCard.style.transition = 'all 0.45s cubic-bezier(0.2, 0.9, 0.4, 1.1)';
                loginCard.style.opacity = '1';
                loginCard.style.transform = 'translateY(0)';
            }, 60);
        }
    })();
</script>
</body>
</html>