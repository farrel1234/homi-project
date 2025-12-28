<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - HOMI Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .wave-shape {
            position: absolute;
            top: -10%;
            bottom: -10%;
            left: -8%;
            width: 45%;
            background: white;
            border-left: 16px solid #F8A477;
            border-radius: 50% 0 0 50%;
        }

        @media (max-width: 1024px) {
            .right-panel { display: none; }
        }
    </style>
</head>

<body class="bg-[#F5F6F8] overflow-hidden">
    <div class="flex min-h-screen">

        {{-- LEFT PANEL --}}
        <div class="w-full lg:w-[55%] flex items-center justify-center px-8">
            <div class="w-full max-w-sm space-y-7">

                {{-- LOGO --}}
                <div class="flex justify-center">
                    <div class="bg-white rounded-xl shadow p-4">
                        <img src="{{ asset('images/homi-logo.png') }}" class="h-20" />
                    </div>
                </div>

                <h1 class="text-4xl font-semibold text-[#F8A477] text-center">Masuk</h1>

                {{-- FORM LOGIN (EMAIL + PASSWORD) --}}
                <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
                    @csrf

                    {{-- EMAIL --}}
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Email"
                        required
                        class="w-full px-4 py-3 rounded-2xl border border-gray-300 bg-white
                               focus:outline-none focus:ring-2 focus:ring-[#2F79A0] focus:border-[#2F79A0]
                               text-sm lg:text-base placeholder:text-gray-400">

                    {{-- PASSWORD --}}
                    <input
                        type="password"
                        name="password"
                        placeholder="Kata Sandi"
                        required
                        class="w-full px-4 py-3 rounded-2xl border border-gray-300 bg-white
                               focus:outline-none focus:ring-2 focus:ring-[#2F79A0] focus:border-[#2F79A0]
                               text-sm lg:text-base placeholder:text-gray-400">

                    <button
                        type="submit"
                        class="w-full py-3 rounded-2xl bg-[#F8A477] text-white shadow-md hover:bg-[#e48f63]">
                        Konfirmasi
                    </button>
                </form>

                @if ($errors->any())
                    <p class="text-center text-red-600 text-sm mt-2">{{ $errors->first() }}</p>
                @endif
            </div>
        </div>

        {{-- RIGHT PANEL --}}
        <div class="right-panel hidden lg:block relative w-[45%]">
            <img src="{{ asset('images/login-bg.png') }}"
                 class="w-full h-full object-cover" />

            <div class="wave-shape"></div>
        </div>

    </div>
</body>
</html>
