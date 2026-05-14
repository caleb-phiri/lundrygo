<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>LaundryPro | Register · Fresh Account</title>
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
        .register-wrapper {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* fresh glassmorphic card */
        .register-card {
            border: none;
            border-radius: 2.2rem;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(2px);
            box-shadow: 0 25px 50px -12px rgba(0, 100, 120, 0.3);
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.3s;
        }

        .register-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 30px 55px rgba(0, 150, 170, 0.28);
        }

        /* fresh header matching login */
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
            animation: iconFloat 3s ease-in-out infinite;
        }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        .card-header-fresh h4 {
            font-weight: 800;
            letter-spacing: -0.3px;
            color: white;
            margin: 0.5rem 0 0.2rem;
            font-size: 1.8rem;
        }

        .card-header-fresh p {
            color: rgba(255,255,240,0.95);
            font-size: 0.8rem;
            margin-bottom: 0;
        }

        .form-label {
            font-weight: 700;
            color: #02698b;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.4rem;
        }

        /* INPUT GROUP WITH ANIMATIONS & GUIDANCE (matching login) */
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

        .animated-input-group:hover .input-guidance,
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

        /* button matching login */
        .btn-laundry {
            background: linear-gradient(105deg, #00b4d8, #0284c7);
            border: none;
            border-radius: 2rem;
            padding: 0.85rem;
            font-weight: 800;
            font-size: 0.95rem;
            transition: all 0.25s;
            box-shadow: 0 8px 18px rgba(0, 150, 170, 0.3);
            color: white;
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

        .alert-fresh ul {
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

        /* checkbox styling */
        .form-check-input {
            accent-color: #00b4d8;
            width: 18px;
            height: 18px;
            margin-top: 0.2rem;
        }

        .form-check-label {
            color: #2c6e8f;
            font-weight: 500;
            font-size: 0.85rem;
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

        /* row spacing for two columns */
        .row-custom {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 0;
        }
        .col-half {
            flex: 1;
            min-width: calc(50% - 0.5rem);
        }

        @media (max-width: 768px) {
            .col-half {
                min-width: 100%;
            }
        }

        @media (max-width: 576px) {
            .register-wrapper { padding: 1rem; }
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

<div class="register-wrapper">
    <div class="container" style="max-width: 720px;">
        <div class="card register-card">
            <div class="card-header-fresh">
                <i class="fas fa-soap"></i>
                <h4>Create Fresh Account</h4>
                <p>join the laundry revolution · animated guidance</p>
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

                <!-- WORKING FORM SUBMISSION - route('register') matching login style -->
                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf
                    
                    <!-- ROW: Full Name + Email -->
                    <div class="row-custom">
                        <div class="col-half">
                            <div class="animated-input-group">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-1"></i> FULL NAME
                                </label>
                                <input type="text" 
                                       class="form-control form-control-fresh @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="e.g., John Doe"
                                       required autofocus>
                                <div class="input-guidance">
                                    <i class="fas fa-droplet guidance-icon"></i>
                                    <span>your legal name for verification</span>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-half">
                            <div class="animated-input-group">
                                <label for="email" class="form-label">
                                    <i class="far fa-envelope me-1"></i> EMAIL ADDRESS
                                </label>
                                <input type="email" 
                                       class="form-control form-control-fresh @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="hello@laundrypro.com"
                                       required>
                                <div class="input-guidance">
                                    <i class="fas fa-lightbulb guidance-icon"></i>
                                    <span>work email preferred</span>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- ROW: Password + Confirm Password -->
                    <div class="row-custom">
                        <div class="col-half">
                            <div class="animated-input-group">
                                <label for="password" class="form-label">
                                    <i class="fas fa-key me-1"></i> PASSWORD
                                </label>
                                <input type="password" 
                                       class="form-control form-control-fresh @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="at least 8 characters"
                                       required>
                                <div class="input-guidance">
                                    <i class="fas fa-shield-alt guidance-icon"></i>
                                    <span>strong & encrypted · min 8 chars</span>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-half">
                            <div class="animated-input-group">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-check-circle me-1"></i> CONFIRM PASSWORD
                                </label>
                                <input type="password" 
                                       class="form-control form-control-fresh" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="repeat your password"
                                       required>
                                <div class="input-guidance">
                                    <i class="fas fa-droplet guidance-icon"></i>
                                    <span>must match above</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Phone Number (Optional) -->
                    <div class="animated-input-group">
                        <label for="phone" class="form-label">
                            <i class="fas fa-phone me-1"></i> PHONE NUMBER (OPTIONAL)
                        </label>
                        <input type="tel" 
                               class="form-control form-control-fresh" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}" 
                               placeholder="+260 *** ******">
                        <div class="input-guidance">
                            <i class="fas fa-water guidance-icon"></i>
                            <span>for order updates &amp; alerts</span>
                        </div>
                    </div>
                    
                    <!-- Terms & Conditions Checkbox -->
                    <div class="mb-3 form-check mt-3">
                        <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" 
                               id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#" class="text-link-fresh">Terms & Conditions</a> and 
                            <a href="#" class="text-link-fresh">Privacy Policy</a>
                        </label>
                        @error('terms')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- SUBMIT BUTTON matching login style -->
                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-laundry text-white" id="registerSubmitBtn">
                            <i class="fas fa-user-plus me-2"></i> CREATE FRESH ACCOUNT <i class="fas fa-droplet ms-2"></i>
                        </button>
                    </div>
                    
                    <div class="divider-clean">
                        <span>ALREADY MEMBER?</span>
                    </div>
                    
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-link-fresh">
                            <i class="fas fa-arrow-right-to-bracket me-1"></i> Sign in to existing account
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
        // DOM elements
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const registerForm = document.getElementById('registerForm');
        const submitBtn = document.getElementById('registerSubmitBtn');

        // DYNAMIC PLACEHOLDER TYPING ANIMATION (matching login style)
        const namePhrases = ["e.g., John Doe", "your full legal name", "as shown on ID"];
        const emailPhrases = ["hello@laundrypro.com", "work email preferred", "registered email"];
        const passwordPhrases = ["min 8 characters", "strong passkey", "use letters & numbers"];
        const confirmPhrases = ["repeat password", "must match above", "confirm credentials"];
        
        let nameIndex = 0, emailIndex = 0, passwordIndex = 0, confirmIndex = 0;
        let nameChar = 0, emailChar = 0, passwordChar = 0, confirmChar = 0;
        let nameDeleting = false, emailDeleting = false, passwordDeleting = false, confirmDeleting = false;
        let nameTimeout, emailTimeout, passwordTimeout, confirmTimeout;

        function animateField(input, phrases, indexVar, charVar, deletingVar, timeoutVar, setIndex, setChar, setDeleting) {
            if (!input) return;
            const currentPhrase = phrases[indexVar];
            if (!deletingVar && charVar <= currentPhrase.length) {
                input.placeholder = currentPhrase.substring(0, charVar);
                charVar++;
                const newTimeout = setTimeout(() => animateField(input, phrases, indexVar, charVar, deletingVar, timeoutVar, setIndex, setChar, setDeleting), 100);
                if (timeoutVar) clearTimeout(timeoutVar);
                if (charVar === currentPhrase.length + 1) {
                    setTimeout(() => {
                        setDeleting(true);
                        animateField(input, phrases, indexVar, charVar, true, timeoutVar, setIndex, setChar, setDeleting);
                    }, 2000);
                }
            } 
            else if (deletingVar && charVar >= 0) {
                input.placeholder = currentPhrase.substring(0, charVar);
                charVar--;
                const newTimeout = setTimeout(() => animateField(input, phrases, indexVar, charVar, true, timeoutVar, setIndex, setChar, setDeleting), 50);
                if (charVar === 0) {
                    setDeleting(false);
                    setIndex((indexVar + 1) % phrases.length);
                    const newTimeout2 = setTimeout(() => animateField(input, phrases, (indexVar + 1) % phrases.length, 0, false, timeoutVar, setIndex, setChar, setDeleting), 300);
                }
            }
        }

        // simplified: just set placeholders with cycling for demo
        const namePlaceholders = ["e.g., John Doe", "your full legal name", "as shown on ID"];
        const emailPlaceholders = ["hello@laundrypro.com", "work email preferred", "registered email"];
        const passwordPlaceholders = ["min 8 characters", "strong passkey", "use letters & numbers"];
        const confirmPlaceholders = ["repeat password", "must match above", "confirm credentials"];
        
        let nameIdx = 0, emailIdx = 0, passIdx = 0, confirmIdx = 0;
        
        function cyclePlaceholder(input, placeholders, idxVar) {
            if (!input) return;
            input.placeholder = placeholders[idxVar];
            setInterval(() => {
                if (document.activeElement !== input) {
                    idxVar = (idxVar + 1) % placeholders.length;
                    input.placeholder = placeholders[idxVar];
                }
            }, 3000);
        }
        
        if (nameInput) cyclePlaceholder(nameInput, namePlaceholders, nameIdx);
        if (emailInput) cyclePlaceholder(emailInput, emailPlaceholders, emailIdx);
        if (passwordInput) cyclePlaceholder(passwordInput, passwordPlaceholders, passIdx);
        if (confirmInput) cyclePlaceholder(confirmInput, confirmPlaceholders, confirmIdx);
        
        // Stop cycling on focus
        [nameInput, emailInput, passwordInput, confirmInput].forEach(input => {
            if (input) {
                input.addEventListener('focus', () => {
                    // keep current placeholder, stop cycling temporarily (handled by interval but fine)
                });
            }
        });

        // Validation with shake effect (matching login)
        function validateRegisterForm() {
            let isValid = true;
            const nameVal = nameInput?.value.trim();
            const emailVal = emailInput?.value.trim();
            const passVal = passwordInput?.value;
            const confirmVal = confirmInput?.value;
            
            // Name validation
            if (!nameVal) {
                nameInput.style.borderColor = '#f97316';
                nameInput.classList.add('shake-effect');
                setTimeout(() => nameInput.classList.remove('shake-effect'), 400);
                isValid = false;
            } else {
                nameInput.style.borderColor = '#d4f0f5';
            }
            
            // Email validation
            if (!emailVal) {
                emailInput.style.borderColor = '#f97316';
                emailInput.classList.add('shake-effect');
                setTimeout(() => emailInput.classList.remove('shake-effect'), 400);
                isValid = false;
            } else if (!/^[^\s@]+@([^\s@]+\.)+[^\s@]+$/.test(emailVal)) {
                emailInput.style.borderColor = '#f97316';
                emailInput.classList.add('shake-effect');
                setTimeout(() => emailInput.classList.remove('shake-effect'), 400);
                isValid = false;
            } else {
                emailInput.style.borderColor = '#d4f0f5';
            }
            
            // Password validation
            if (!passVal) {
                passwordInput.style.borderColor = '#f97316';
                passwordInput.classList.add('shake-effect');
                setTimeout(() => passwordInput.classList.remove('shake-effect'), 400);
                isValid = false;
            } else if (passVal.length < 8) {
                passwordInput.style.borderColor = '#f97316';
                passwordInput.classList.add('shake-effect');
                setTimeout(() => passwordInput.classList.remove('shake-effect'), 400);
                isValid = false;
            } else {
                passwordInput.style.borderColor = '#d4f0f5';
            }
            
            // Confirm password
            if (!confirmVal) {
                confirmInput.style.borderColor = '#f97316';
                confirmInput.classList.add('shake-effect');
                setTimeout(() => confirmInput.classList.remove('shake-effect'), 400);
                isValid = false;
            } else if (passVal !== confirmVal) {
                confirmInput.style.borderColor = '#f97316';
                confirmInput.classList.add('shake-effect');
                setTimeout(() => confirmInput.classList.remove('shake-effect'), 400);
                isValid = false;
            } else {
                confirmInput.style.borderColor = '#d4f0f5';
            }
            
            const termsCheck = document.getElementById('terms');
            if (termsCheck && !termsCheck.checked) {
                termsCheck.classList.add('shake-effect');
                setTimeout(() => termsCheck.classList.remove('shake-effect'), 400);
                isValid = false;
            }
            
            return isValid;
        }
        
        function showHint(message, isError = true) {
            let hint = document.createElement('div');
            hint.innerText = message;
            hint.style.position = 'fixed';
            hint.style.bottom = '20px';
            hint.style.left = '50%';
            hint.style.transform = 'translateX(-50%)';
            hint.style.background = isError ? '#fef9e3' : '#e0f7ea';
            hint.style.padding = '10px 22px';
            hint.style.borderRadius = '50px';
            hint.style.fontSize = '0.8rem';
            hint.style.fontWeight = '600';
            hint.style.color = isError ? '#cc7b2c' : '#065f46';
            hint.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
            hint.style.zIndex = '9999';
            hint.style.borderLeft = `4px solid ${isError ? '#f97316' : '#10b981'}`;
            hint.style.backdropFilter = 'blur(4px)';
            document.body.appendChild(hint);
            setTimeout(() => hint.remove(), 2800);
        }
        
        // FORM SUBMIT HANDLER (matching login pattern)
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                const isValid = validateRegisterForm();
                
                if (!isValid) {
                    e.preventDefault();
                    showHint("💧 Please fill all fields correctly and accept terms", true);
                    // create small visual ripple effect on button
                    const rect = submitBtn.getBoundingClientRect();
                    const ripple = document.createElement('div');
                    ripple.style.position = 'fixed';
                    ripple.style.width = '60px';
                    ripple.style.height = '60px';
                    ripple.style.borderRadius = '50%';
                    ripple.style.background = 'radial-gradient(circle, rgba(0,180,216,0.4), transparent)';
                    ripple.style.left = (rect.left + rect.width/2 - 30) + 'px';
                    ripple.style.top = (rect.top + rect.height/2 - 30) + 'px';
                    ripple.style.pointerEvents = 'none';
                    ripple.style.zIndex = '9999';
                    ripple.style.animation = 'ripple3D 0.6s ease-out forwards';
                    document.body.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                    return false;
                }
                
                // Show success hint
                showHint("✨ Creating your fresh account...", false);
                
                const originalHTML = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-pulse me-2"></i> CREATING FRESH ACCOUNT...';
                
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHTML;
                    if (registerForm.getAttribute('data-submitted') !== 'true') {
                        registerForm.setAttribute('data-submitted', 'true');
                        registerForm.submit();
                    }
                }, 600);
                
                e.preventDefault();
                return false;
            });
        }
        
        // Focus animations for guidance (matching login)
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
        const card = document.querySelector('.register-card');
        if (card) {
            card.style.opacity = '0';
            card.style.transform = 'translateY(18px)';
            setTimeout(() => {
                card.style.transition = 'all 0.45s cubic-bezier(0.2, 0.9, 0.4, 1.1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 60);
        }
        
        // Add ripple3D keyframe if not exists
        if (!document.querySelector('#rippleStyle')) {
            const style = document.createElement('style');
            style.id = 'rippleStyle';
            style.textContent = `@keyframes ripple3D { 0% { transform: scale(0); opacity: 0.8; } 100% { transform: scale(8); opacity: 0; } }`;
            document.head.appendChild(style);
        }
    })();
</script>
</body>
</html>