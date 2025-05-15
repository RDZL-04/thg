<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Input Password</title>
<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <img src="{{url('asset/icon/thg-logo-new.png')}}" class="card-logo" style="width:200px"/>
        </x-slot>
        <x-jet-validation-errors class="mb-4" />
        @include('/flash-message-pass')
        <form method="POST" action="{{ route('reset_password') }}">
            @csrf
            
            <input type="hidden" name="token" value="{{ $data['token'] }}">
            <input type="hidden" name="email" value="{{ $data['email'] }}">

            <div class="mt-4">
                <x-jet-label for="password" value="{{ __('Password') }}" />
                <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-jet-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-jet-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-jet-button>
                    {{ __('Reset Password') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
