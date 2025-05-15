<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forgot Password</title>
<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <img src="{{url('asset/icon/thg-logo-new.png')}}" class="card-logo" style="width:200px"/>
        </x-slot>
         {{-- @include('/flash-message') --}}

        <div class="mb-4 text-sm text-gray-600">
            {{ __('passwords.header-title') }}
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        
        <form method="POST" action="{{ route('send_email') }}">
            @csrf
            @if(session('error'))
            <div class="block">
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-jet-input id="email" class="w-full is-invalid" type="email" name="email" :value="old('email')" required autofocus />
            </div>
            @include('/flash-message-pass')
            @else
            <div class="block">
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            </div>
            @endif
            <div class="flex items-center justify-end mt-4">
                <x-jet-button>
                    {{ __('SUBMIT') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
