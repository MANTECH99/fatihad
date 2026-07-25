{{-- resources/views/auth/register.blade.php --}}
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Créer votre boutique - Seneshop</title>

    {{-- Tailwind via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#10B981',
                        'primary-dark': '#059669',
                        secondary: '#1F2937',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }

        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 50%, #f0fdf4 100%);
            min-height: 100vh;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(16, 185, 129, 0.1);
        }

        .input-field {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
        }

        .input-field:focus {
            border-color: #10B981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            outline: none;
        }

        .input-field:hover {
            border-color: #d1d5db;
        }

        .gradient-btn {
            background: linear-gradient(135deg, #10B981, #059669);
            transition: all 0.3s ease;
        }

        .gradient-btn:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        }

        .plan-card {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
            cursor: pointer;
        }

        .plan-card:hover {
            border-color: #10B981;
            transform: translateY(-2px);
        }

        .plan-card.selected {
            border-color: #10B981;
            background-color: #f0fdf4;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }

        .plan-card.selected .plan-radio {
            background-color: #10B981;
            border-color: #10B981;
        }

        .plan-radio {
            width: 20px;
            height: 20px;
            border: 2px solid #d1d5db;
            border-radius: 50%;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .plan-card.selected .plan-radio::after {
            content: '';
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .error-shake {
            animation: shake 0.4s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .step-indicator {
            transition: all 0.3s ease;
        }

        .step-indicator.active {
            background: #10B981;
            color: white;
        }

        .step-indicator.completed {
            background: #10B981;
            color: white;
        }

        .password-strength-bar {
            height: 4px;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .phone-prefix {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-right: none;
            border-radius: 12px 0 0 12px;
            padding: 0 12px;
            display: flex;
            align-items: center;
            color: #6b7280;
            font-weight: 500;
            white-space: nowrap;
        }

        .phone-input {
            border-radius: 0 12px 12px 0 !important;
        }

        .benefit-item {
            transition: all 0.2s ease;
        }

        .benefit-item:hover {
            transform: translateX(4px);
        }
    </style>
</head>
<body class="flex items-center justify-center p-4 pt-10 md:pt-10">

<div class="w-full max-w-2xl animate-fade-in">

    {{-- Logo + Titre --}}
    <div class="text-center mb-8">
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 mb-6">
            <div class=" flex items-center justify-center ">
                <img src="{{ asset('images/fatihads.png') }}" alt="FatiHad" class="w-12 h-auto  object-cover -mt-4">
            </div>
            <span class="text-3xl font-black text-gray-900">FatiHad</span>
        </a>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Lancez votre boutique en 5 minutes 🚀</h1>
        <p class="text-gray-600 mt-2">Créez votre compte et commencez à vendre en ligne</p>
    </div>

    {{-- Card --}}
    <div class="auth-card rounded-3xl shadow-xl p-6 lg:p-8">

        {{-- Steps Indicator --}}
        <div class="flex items-center justify-center gap-3 mb-8">
            <div class="step-indicator active w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">1</div>
            <div class="w-12 h-0.5 bg-gray-200"></div>
            <div class="step-indicator w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold bg-gray-100 text-gray-500">2</div>
        </div>

        {{-- Error Summary --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm error-shake">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                    <div>
                        <p class="font-semibold mb-1">Erreur d'inscription</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Formulaire --}}
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            {{-- Plan Selection --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-crown text-yellow-500 mr-1"></i>
                    Choisissez votre plan
                </label>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <label class="plan-card selected rounded-xl p-3 text-center" onclick="selectPlan('free', this)">
                        <input type="radio" name="plan" value="free" class="hidden" checked>
                        <span class="plan-radio mx-auto mb-2"></span>
                        <span class="block text-sm font-bold text-gray-900">Gratuit</span>
                        <span class="block text-xs text-gray-500 mt-0.5">0 FCFA/mois</span>
                        <span class="block text-xs text-gray-400 mt-0.5">10 produits</span>
                    </label>
                    <label class="plan-card rounded-xl p-3 text-center" onclick="selectPlan('starter', this)">
                        <input type="radio" name="plan" value="starter" class="hidden">
                        <span class="plan-radio mx-auto mb-2"></span>
                        <span class="block text-sm font-bold text-gray-900">Débutant</span>
                        <span class="block text-xs text-gray-500 mt-0.5">4 900 FCFA</span>
                        <span class="block text-xs text-gray-400 mt-0.5">50 produits</span>
                    </label>
                    <label class="plan-card rounded-xl p-3 text-center" onclick="selectPlan('business', this)">
                        <input type="radio" name="plan" value="business" class="hidden">
                        <span class="plan-radio mx-auto mb-2"></span>
                        <span class="block text-sm font-bold text-gray-900">Professionel</span>
                        <span class="block text-xs text-gray-500 mt-0.5">9 900 FCFA</span>
                        <span class="block text-xs text-gray-400 mt-0.5">Illimité</span>
                    </label>
                    <label class="plan-card rounded-xl p-3 text-center" onclick="selectPlan('enterprise', this)">
                        <input type="radio" name="plan" value="enterprise" class="hidden">
                        <span class="plan-radio mx-auto mb-2"></span>
                        <span class="block text-sm font-bold text-gray-900">Business</span>
                        <span class="block text-xs text-gray-500 mt-0.5">19 900 FCFA</span>
                        <span class="block text-xs text-gray-400 mt-0.5">Sur mesure</span>
                    </label>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-5">
                {{-- Nom complet --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-primary mr-1"></i>
                        Nom complet
                    </label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Votre nom"
                        class="input-field w-full px-4 py-3 rounded-xl text-gray-900 placeholder-gray-400 @error('name') border-red-300 bg-red-50 @enderror"
                    >
                    @error('name')
                    <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope text-primary mr-1"></i>
                        Adresse email
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="username"
                        placeholder="exemple@email.com"
                        class="input-field w-full px-4 py-3 rounded-xl text-gray-900 placeholder-gray-400 @error('email') border-red-300 bg-red-50 @enderror"
                    >
                    @error('email')
                    <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            {{-- Téléphone --}}
            <div>
                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-phone text-primary mr-1"></i>
                    Téléphone
                    <span class="text-gray-400 font-normal">(optionnel)</span>
                </label>
                <div class="flex">
                    <span class="phone-prefix">
                        <img src="https://flagcdn.com/w20/sn.png" alt="SN" class="w-5 h-3.5 mr-2 rounded-sm">
                        +221
                    </span>
                    <input
                        id="phone"
                        type="text"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="77 123 45 67"
                        class="phone-input input-field flex-1 px-4 py-3 text-gray-900 placeholder-gray-400 @error('phone') border-red-300 bg-red-50 @enderror"
                    >
                </div>
                @error('phone')
                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                    <i class="fas fa-info-circle"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="grid lg:grid-cols-2 gap-5">
                {{-- Mot de passe --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock text-primary mr-1"></i>
                        Mot de passe
                    </label>
                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Min. 8 caractères"
                            class="input-field w-full px-4 py-3 rounded-xl text-gray-900 placeholder-gray-400 @error('password') border-red-300 bg-red-50 @enderror"
                            oninput="checkPasswordStrength()"
                        >
                        <button
                            type="button"
                            onclick="togglePassword('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary transition"
                        >
                            <i class="fas fa-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <div class="password-strength-bar w-full bg-gray-200" id="strengthBar"></div>
                        <p class="text-xs text-gray-500 mt-1" id="strengthText"></p>
                    </div>
                    @error('password')
                    <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Confirmation mot de passe --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-check-circle text-primary mr-1"></i>
                        Confirmer le mot de passe
                    </label>
                    <div class="relative">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Répétez le mot de passe"
                            class="input-field w-full px-4 py-3 rounded-xl text-gray-900 placeholder-gray-400 @error('password_confirmation') border-red-300 bg-red-50 @enderror"
                            oninput="checkPasswordMatch()"
                        >
                        <span id="matchIndicator" class="absolute right-3 top-1/2 -translate-y-1/2 text-lg hidden">
                            <i class="fas fa-check-circle text-green-500" id="matchIcon"></i>
                        </span>
                    </div>
                    @error('password_confirmation')
                    <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            {{-- Conditions --}}
            <div class="bg-gray-50 rounded-xl p-4">
                <label for="terms" class="flex items-start cursor-pointer">
                    <input
                        id="terms"
                        type="checkbox"
                        required
                        class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary focus:ring-offset-0 mt-0.5"
                    >
                    <span class="ml-3 text-sm text-gray-600">
                        J'accepte les
                        <a href="#" class="text-primary hover:underline font-medium">conditions générales</a>
                        et la
                        <a href="#" class="text-primary hover:underline font-medium">politique de confidentialité</a>
                    </span>
                </label>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="gradient-btn w-full text-white font-semibold py-3.5 rounded-xl flex items-center justify-center gap-2 text-lg">
                <i class="fas fa-rocket"></i>
                Créer ma boutique gratuitement
            </button>

            {{-- Garantie --}}
            <div class="flex items-center justify-center gap-6 text-xs text-gray-500">
                <span class="flex items-center gap-1">
                    <i class="fas fa-shield-alt text-primary"></i>
                    14 jours d'essai gratuit
                </span>
                <span class="flex items-center gap-1">
                    <i class="fas fa-credit-card text-primary"></i>
                    Sans carte bancaire
                </span>
                <span class="flex items-center gap-1">
                    <i class="fas fa-ban text-primary"></i>
                    Sans engagement
                </span>
            </div>
        </form>
    </div>

    {{-- Benefits Sidebar (desktop) --}}
    <div class="hidden lg:block mt-8">
        <div class="grid grid-cols-3 gap-4">
            <div class="benefit-item bg-white/80 backdrop-blur-sm rounded-xl p-4 text-center border border-gray-100">
                <i class="fas fa-boxes text-2xl text-primary mb-2"></i>
                <p class="text-sm font-semibold text-gray-900">Catalogue produits</p>
            </div>
            <div class="benefit-item bg-white/80 backdrop-blur-sm rounded-xl p-4 text-center border border-gray-100">
                <i class="fab fa-whatsapp text-2xl text-green-500 mb-2"></i>
                <p class="text-sm font-semibold text-gray-900">Commandes WhatsApp</p>
            </div>
            <div class="benefit-item bg-white/80 backdrop-blur-sm rounded-xl p-4 text-center border border-gray-100">
                <i class="fas fa-money-bill-wave text-2xl text-orange-500 mb-2"></i>
                <p class="text-sm font-semibold text-gray-900">Paiement Wave/OM</p>
            </div>
        </div>
    </div>

    {{-- Login Link --}}
    <p class="text-center mt-6 text-gray-600 text-sm">
        Déjà une boutique ?
        <a href="{{ route('login') }}" class="font-semibold text-primary hover:text-primary-dark transition">
            Connectez-vous
        </a>
    </p>

    {{-- Back to Home --}}
    <div class="text-center mt-4">
        <a href="{{ url('/') }}" class="text-sm text-gray-400 hover:text-gray-600 transition inline-flex items-center gap-1">
            <i class="fas fa-arrow-left"></i>
            Retour à l'accueil
        </a>
    </div>

</div>

<script>
    // Plan Selection
    function selectPlan(plan, element) {
        document.querySelectorAll('.plan-card').forEach(card => {
            card.classList.remove('selected');
        });
        element.classList.add('selected');
        element.querySelector('input[type="radio"]').checked = true;

        // Update button text based on selected plan
        const button = document.querySelector('button[type="submit"]');
        const planNames = {
            'free': 'Créer ma boutique gratuitement',
            'starter': 'Essai gratuit 14 jours - Starter',
            'business': 'Essai gratuit 14 jours - Business',
            'enterprise': 'Essai gratuit 14 jours - Enterprise'
        };
        button.innerHTML = `<i class="fas fa-rocket"></i> ${planNames[plan]}`;
    }

    // Toggle Password Visibility
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(inputId + 'Icon');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Password Strength Checker
    function checkPasswordStrength() {
        const password = document.getElementById('password').value;
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');

        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;

        const colors = ['#ef4444', '#f59e0b', '#f59e0b', '#10b981', '#10b981'];
        const labels = ['Très faible', 'Faible', 'Moyen', 'Bon', 'Excellent'];
        const widths = ['20%', '40%', '60%', '80%', '100%'];

        strengthBar.style.width = widths[strength - 1] || '0%';
        strengthBar.style.backgroundColor = colors[strength - 1] || '#e5e7eb';
        strengthText.textContent = password.length > 0 ? labels[strength - 1] || 'Très faible' : '';
        strengthText.style.color = colors[strength - 1] || '#6b7280';
    }

    // Password Match Checker
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;
        const indicator = document.getElementById('matchIndicator');

        if (confirmation.length > 0) {
            indicator.classList.remove('hidden');
            if (password === confirmation) {
                indicator.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
            } else {
                indicator.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
            }
        } else {
            indicator.classList.add('hidden');
        }
    }

    // Auto-hide flash messages
    setTimeout(() => {
        const alerts = document.querySelectorAll('.bg-red-50');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 8000);

    // Phone number formatting
    document.getElementById('phone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 9) value = value.slice(0, 9);

        // Format as XX XXX XX XX
        if (value.length >= 8) {
            value = value.replace(/(\d{2})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4');
        } else if (value.length >= 5) {
            value = value.replace(/(\d{2})(\d{3})(\d{1,2})/, '$1 $2 $3');
        } else if (value.length >= 2) {
            value = value.replace(/(\d{2})(\d{1,3})/, '$1 $2');
        }

        e.target.value = value;
    });

    // Keyboard shortcut: Ctrl+Enter to submit
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            document.querySelector('form').submit();
        }
    });

    // Pre-select plan from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const planParam = urlParams.get('plan');
    if (planParam) {
        const planCard = document.querySelector(`.plan-card input[value="${planParam}"]`);
        if (planCard) {
            selectPlan(planParam, planCard.closest('.plan-card'));
        }
    }
</script>

</body>
</html>
