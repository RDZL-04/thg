<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
<x-guest-layout>
    <x-jet-authentication-card>
            <x-slot name="logo">
                <img src="{{url('asset/icon/thg-logo-new.png')}}" class="card-logo" style="width:200px"/>
            </x-slot>
        @include('/flash-message')


@yield('content')
        <x-jet-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        

        <form method="POST" action="{{ route('login_user.store')}}">
            @csrf

            <div>
                <x-jet-label for="email" value="{{ __('auth.Email-or-username') }}" />
                <x-jet-input id="email" class="block mt-1 w-full" type="text" name="email" :value="old('email')" required autofocus />
            </div>

            <div class="mt-4">
                <x-jet-label for="password" value="{{ __('Password') }}" />
                <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            {{-- <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <input id="remember_me" type="checkbox" class="form-checkbox" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div> --}}

            <div class="flex items-center justify-end mt-4">
                {{-- @if (Route::has('password.request')) --}}
                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('forgot_password') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                {{-- @endif --}}

                <x-jet-button class="ml-4">
                    {{ __('Login') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
