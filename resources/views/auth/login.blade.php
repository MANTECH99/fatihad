{{-- resources/views/auth/login.blade.php --}}
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - FatiHad</title>

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

        .social-btn {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
        }

        .social-btn:hover {
            border-color: #10B981;
            background-color: #f0fdf4;
            transform: translateY(-1px);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }

        .divider span {
            padding: 0 1rem;
            color: #9ca3af;
            font-size: 0.875rem;
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

        .password-toggle {
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: #10B981;
        }
    </style>
</head>
<body class="flex items-center justify-center p-4 pt-10 md:pt-10">

<div class="w-full max-w-md animate-fade-in">

    {{-- Logo + Titre --}}
    <div class="text-center mb-8">
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 mb-6">
            <div class="  flex items-center justify-center ">
                <img src="{{ asset('images/fatihads.png') }}" alt="FatiHad" class="w-12 h-auto  object-cover -mt-4">
            </div>
            <span class="text-3xl font-black text-gray-900">FatiHad</span>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Heureux de vous revoir 👋</h1>
        <p class="text-gray-600 mt-2">Connectez-vous pour gérer votre boutique</p>
    </div>

    {{-- Card --}}
    <div class="auth-card rounded-3xl shadow-xl p-8">

        {{-- Session Status --}}
        @if (session('status'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm flex items-start gap-3">
                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- Error Summary --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm error-shake">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                    <div>
                        <p class="font-semibold mb-1">Erreur de connexion</p>
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
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-envelope text-primary mr-1"></i>
                    Adresse email
                </label>
                <div class="relative">
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="exemple@email.com"
                        class="input-field w-full px-4 py-3 rounded-xl text-gray-900 placeholder-gray-400 @error('email') border-red-300 bg-red-50 @enderror"
                    >
                </div>
            </div>

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
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="input-field w-full px-4 py-3 rounded-xl text-gray-900 placeholder-gray-400 @error('password') border-red-300 bg-red-50 @enderror"
                    >
                    <button
                        type="button"
                        onclick="togglePassword()"
                        class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"
                    >
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            {{-- Remember Me + Forgot Password --}}
            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center cursor-pointer">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary focus:ring-offset-0"
                    >
                    <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary hover:text-primary-dark transition">
                        Mot de passe oublié ?
                    </a>
                @endif
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="gradient-btn w-full text-white font-semibold py-3 rounded-xl flex items-center justify-center gap-2 text-lg">
                <i class="fas fa-sign-in-alt"></i>
                Se connecter
            </button>

            {{-- Divider --}}
            <div class="divider my-4">
                <span>ou continuer avec</span>
            </div>

            {{-- Social Login Buttons (placeholder) --}}
            <div class="grid grid-cols-2 gap-3">
                <button type="button" class="social-btn bg-white py-3 rounded-xl flex items-center justify-center gap-2 text-gray-700 font-medium">
                    <i class="fab fa-google text-red-500"></i>
                    Google
                </button>
                <button type="button" class="social-btn bg-white py-3 rounded-xl flex items-center justify-center gap-2 text-gray-700 font-medium">
                    <i class="fab fa-facebook text-blue-600"></i>
                    Facebook
                </button>
            </div>
        </form>
    </div>

    {{-- Sign Up Link --}}
    <p class="text-center mt-6 text-gray-600 text-sm">
        Pas encore de boutique ?
        <a href="{{ route('register') }}" class="font-semibold text-primary hover:text-primary-dark transition">
            Créez la vôtre gratuitement
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
    // Toggle password visibility
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    // Auto-hide flash messages after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.bg-green-50.border-green-200, .bg-red-50.border-red-200');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // Add subtle animation on input focus
    document.querySelectorAll('.input-field').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.01)';
            this.parentElement.style.transition = 'transform 0.2s ease';
        });

        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });

    // Keyboard shortcut: Ctrl+Enter to submit
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            document.querySelector('form').submit();
        }
    });
</script>

</body>
</html>
