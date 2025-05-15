<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Input Password</title>
<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <img src="{{url('asset/icon/thg-logo-new.png')}}" class="card-logo" style="width:200px"/>
        </x-slot>
        @include('/flash-message-pass')
            @csrf
            <div class="mt-4">
            </div>

            <div class="flex items-center justify-end mt-4">
                <form action="{{ route('login_user') }}">
                    <x-jet-button>
                        {{ __('Login Page') }}
                    </x-jet-button>
                </form>
               
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
