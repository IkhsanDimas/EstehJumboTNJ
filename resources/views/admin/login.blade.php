<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — estehjumboTNJ</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;755&display=swap" rel="stylesheet">

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans:    ['Inter', 'system-ui', 'sans-serif'],
                        rounded: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        display: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body {
            background-color: #f1f4f8;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(186, 230, 253, 0.4) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(224, 242, 254, 0.6) 0%, transparent 40%);
            background-attachment: fixed;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 sm:p-12 overflow-x-hidden">
    
    {{-- Main Container Card --}}
    <div class="relative w-full max-w-3xl bg-white rounded-[32px] shadow-[0_30px_70px_-15px_rgba(0,0,0,0.10)] border border-slate-100 flex flex-col md:flex-row overflow-visible">
        
        {{-- LEFT SIDE: Yellow Scooter Rider (Only on desktop) --}}
        <div class="relative hidden md:flex md:w-[48%] items-center justify-center p-6 bg-transparent select-none">
            {{-- Floating shadow below the character --}}
            <div aria-hidden="true" class="absolute bottom-4 left-1/2 -translate-x-1/2 w-32 h-6 bg-slate-900/15 rounded-full blur-md"></div>
            
            {{-- Image overlaps the card on the left --}}
            <img src="{{ asset('images/boys-scooter.png') }}" alt="estehjumboTNJ Rider" 
                 class="relative z-10 h-[430px] w-auto max-w-none -ml-16 transform hover:-translate-y-1.5 transition-transform duration-500 drop-shadow-[0_15px_30px_rgba(0,0,0,0.15)]">
        </div>

        {{-- RIGHT SIDE: Form --}}
        <div class="w-full md:w-[52%] p-8 sm:p-10 md:p-12 flex flex-col justify-center bg-white rounded-[32px]">
            <div class="mb-8">
                <p class="text-blue-600 font-display font-semibold text-xs tracking-[0.15em] uppercase">Welcome to</p>
                <h1 class="font-display font-extrabold text-blue-600 text-3xl sm:text-4xl tracking-tight mt-1">estehjumboTNJ</h1>
            </div>

            {{-- Error validation alerts --}}
            @if ($errors->any())
                <div class="mb-5 bg-rose-50 border border-rose-150 rounded-2xl p-4 text-xs text-rose-800">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST" class="space-y-4">
                @csrf
                
                {{-- Email Input --}}
                <div class="relative">
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="Enter Username or Email Address"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 text-xs font-semibold text-slate-700 placeholder:text-slate-400 placeholder:font-normal focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition">
                </div>

                {{-- Password Input --}}
                <div class="relative">
                    <input type="password" name="password" required
                           placeholder="Enter Password"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 text-xs font-semibold text-slate-700 placeholder:text-slate-400 placeholder:font-normal focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition">
                </div>

                {{-- Log in button + forgot password --}}
                <div class="flex items-center justify-between gap-4 pt-3">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-display font-bold text-[10px] tracking-wider uppercase px-8 py-3.5 rounded-xl transition shadow-md shadow-blue-500/10 hover:-translate-y-0.5 active:translate-y-0 whitespace-nowrap">
                        Log In
                    </button>
                    <a href="#" class="text-[11px] text-slate-450 hover:text-slate-700 transition">Forgot password?</a>
                </div>
            </form>

            {{-- Divider --}}
            <div class="relative my-7 text-center">
                <span class="absolute inset-y-1/2 left-0 right-0 h-px bg-slate-100"></span>
                <span class="relative bg-white px-3 text-[10px] uppercase font-bold text-slate-400 tracking-wider">or Log In with</span>
            </div>

            {{-- Social buttons --}}
            <div>
                <button type="button" class="w-full flex items-center justify-center gap-2 rounded-xl border border-slate-200 hover:bg-slate-50 px-4 py-3.5 transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span class="text-xs font-semibold text-slate-600">Google</span>
                </button>
            </div>

            {{-- Footer Text --}}
            <div class="mt-8 text-center">
                <a href="#" class="text-[11px] text-slate-400 hover:text-slate-600 transition font-medium">Create my estehjumboTNJ account!</a>
            </div>
        </div>
    </div>

</body>
</html>
