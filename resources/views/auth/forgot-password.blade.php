<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>LaundryPro | Reset Password · Fresh Recovery</title>
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
        .reset-wrapper {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* fresh glassmorphic card */
        .reset-card {
            border: none;
            border-radius: 2.2rem;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(2px);
            box-shadow: 0 25px 50px -12px rgba(0, 100, 120, 0.3);
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.3s;
        }

        .reset-card:hover {
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

        /* INPUT GROUP WITH ANIMATED GUIDANCE */
        .animated-input-group {
            position: relative;
            margin-bottom: 2rem;
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

        .form-control-fresh::placeholder {
            color: #b4d6e3;
            font-weight: 400;
            transition: all 0.3s ease;
        }

        .form-control-fresh:focus::placeholder {
            color: #6bc2db;
            transform: translateX(4px);
            opacity: 0.7;
        }

        /* floating guidance badge */
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

        /* button styling */
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

        /* alert styling */
        .alert-success-fresh {
            border-radius: 1rem;
            background: #e0f7ea;
            border-left: 4px solid #10b981;
            font-size: 0.85rem;
            padding: 0.8rem 1rem;
            color: #065f46;
            margin-bottom: 1.5rem;
        }

        .alert-fresh {
            border-radius: 1rem;
            background: #fff5f0;
            border-left: 4px solid #f97316;
            font-size: 0.85rem;
            padding: 0.8rem 1rem;
        }

        .alert-fresh ul, .alert-success-fresh ul {
            margin-bottom: 0;
            padding-left: 1.2rem;
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

        /* shake animation for error */
        @keyframes shakeSoft {
            0%,100%{ transform: translateX(0);}
            25%{ transform: translateX(-4px);}
            75%{ transform: translateX(4px);}
        }
        .shake-effect {
            animation: shakeSoft 0.25s ease-in-out 0s 2;
        }

        @media (max-width: 576px) {
            .reset-wrapper { padding: 1rem; }
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

<div class="reset-wrapper">
    <div class="container" style="max-width: 540px;">
        <div class="card reset-card">
            <div class="card-header-fresh">
                <i class="fas fa-soap"></i>
                <h4>Reset Password</h4>
                <p style="color: rgba(255,255,240,0.95); font-size: 0.8rem; margin-bottom: 0;">fresh recovery · send reset link</p>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <!-- Laravel session status message -->
                @if(session('status'))
                    <div class="alert-success-fresh mb-4">
                        <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
                    </div>
                @endif

                <!-- Laravel validation errors -->
                @if($errors->any())
                    <div class="alert-fresh mb-4">
                        <i class="fas fa-circle-exclamation me-2"></i> Please fix the following:
                        <ul class="mt-2 mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- WORKING FORM SUBMISSION - route('password.email') -->
                <form method="POST" action="{{ route('password.email') }}" id="resetPasswordForm">
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
                            <i class="fas fa-envelope guidance-icon"></i>
                            <span>enter your registered email · we'll send reset link</span>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- SUBMIT BUTTON -->
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-laundry text-white" id="resetSubmitBtn">
                            <i class="fas fa-paper-plane me-2"></i> SEND RESET LINK
                        </button>
                    </div>
                    
                    <div class="divider-clean">
                        <span>OR</span>
                    </div>
                    
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-link-fresh">
                            <i class="fas fa-arrow-left me-1"></i> Back to Login
                        </a>
                        <span class="mx-2 text-muted">|</span>
                        <a href="{{ route('register') }}" class="text-link-fresh">
                            <i class="fas fa-user-plus me-1"></i> Create Account
                        </a>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <small class="text-muted" style="font-size: 0.68rem;">
                        <i class="fas fa-shield-alt"></i> secure reset link · valid for 60 minutes
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function() {
        // DOM elements
        const emailInput = document.getElementById('email');
        const resetForm = document.getElementById('resetPasswordForm');
        const submitBtn = document.getElementById('resetSubmitBtn');

        // DYNAMIC PLACEHOLDER TYPING ANIMATION (professional tips for reset)
        const emailPhrases = [
            "e.g., manager@laundrypro.com",
            "your work email address",
            "ex: care@laundryhub.com",
            "registered email for reset"
        ];
        
        let emailIndex = 0;
        let emailCharIndex = 0;
        let isEmailDeleting = false;
        let emailTimeout;

        function animateEmailPlaceholder() {
            if (!emailInput) return;
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

        // start typing animation
        if (emailInput) {
            emailInput.placeholder = "";
            animateEmailPlaceholder();
            
            // stop animation on focus to not disturb typing
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

        // live validation with shake effect
        function validateEmailWithAnimation() {
            let isValid = true;
            const emailVal = emailInput?.value.trim();
            
            if (!emailVal) {
                emailInput.style.borderColor = '#f97316';
                emailInput.classList.add('shake-effect');
                setTimeout(() => emailInput.classList.remove('shake-effect'), 400);
                isValid = false;
            } else if (!/^[^\s@]+@([^\s@]+\.)+[^\s@]+$/.test(emailVal)) {
                emailInput.style.borderColor = '#f97316';
                emailInput.classList.add('shake-effect');
                setTimeout(() => emailInput.classList.remove('shake-effect'), 400);
                // show inline guidance hint
                const guidanceSpan = document.querySelector('.input-guidance');
                if (guidanceSpan) {
                    guidanceSpan.style.color = '#f97316';
                    setTimeout(() => {
                        if (guidanceSpan) guidanceSpan.style.color = '#3f8eab';
                    }, 2000);
                }
                isValid = false;
            } else {
                emailInput.style.borderColor = '#d4f0f5';
            }
            return isValid;
        }

        // form submission with validation and loading state
        if (resetForm) {
            resetForm.addEventListener('submit', function(e) {
                if (!validateEmailWithAnimation()) {
                    e.preventDefault();
                    // show floating hint
                    let hint = document.createElement('div');
                    hint.innerText = "📧 Please enter a valid email address";
                    hint.style.position = 'fixed';
                    hint.style.bottom = '20px';
                    hint.style.left = '50%';
                    hint.style.transform = 'translateX(-50%)';
                    hint.style.background = '#fef9e3';
                    hint.style.padding = '8px 20px';
                    hint.style.borderRadius = '50px';
                    hint.style.fontSize = '0.75rem';
                    hint.style.fontWeight = '500';
                    hint.style.color = '#cc7b2c';
                    hint.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
                    hint.style.zIndex = '9999';
                    hint.style.borderLeft = '3px solid #f97316';
                    document.body.appendChild(hint);
                    setTimeout(() => hint.remove(), 2500);
                    return false;
                }
                
                // show loading state on button
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-pulse me-2"></i> SENDING ...';
                
                // allow form to submit naturally after a tiny delay for UX
                setTimeout(() => {
                    // form will submit to Laravel route
                    // but we re-enable in case of network issues (form will reload page anyway)
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 800);
                
                return true;
            });
        }

        // smooth card entrance animation
        const resetCard = document.querySelector('.reset-card');
        if (resetCard) {
            resetCard.style.opacity = '0';
            resetCard.style.transform = 'translateY(18px)';
            setTimeout(() => {
                resetCard.style.transition = 'all 0.45s cubic-bezier(0.2, 0.9, 0.4, 1.1)';
                resetCard.style.opacity = '1';
                resetCard.style.transform = 'translateY(0)';
            }, 60);
        }

        // focus guidance animation
        const inputGroup = document.querySelector('.animated-input-group');
        if (inputGroup) {
            const input = inputGroup.querySelector('input');
            const guidanceSpan = inputGroup.querySelector('.input-guidance');
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
                    setTimeout(() => {
                        if (guidanceSpan) guidanceSpan.style.color = '#3f8eab';
                    }, 300);
                });
            }
        }

        // add micro interaction on button hover ripple effect
        if (submitBtn) {
            submitBtn.addEventListener('mouseenter', function(e) {
                this.style.transform = 'translateY(-2px)';
            });
            submitBtn.addEventListener('mouseleave', function(e) {
                this.style.transform = 'translateY(0)';
            });
        }
    })();
</script>
</body>
</html>