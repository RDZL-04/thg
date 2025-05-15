<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.bundle.min.js"></script>
<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <img src="{{url('asset/icon/thg-logo-new.png')}}" class="card-logo" style="width:200px"/>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('passwords.verification-code-title-1') }} <strong>{{ $email }}</strong>. {{ __('passwords.verification-code-title-2') }} 
        </div>

        @if (session('status')) 
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('check_otp') }}">
            @csrf
            <input type="hidden" id="email" name="email" value="{{ $email }}">
            <input type="hidden" id="next_send" name="next_send" value="{{ $next_send }}">
            @if(!empty(session('error')))
            <div class="block">
                <x-jet-label for="otp" value="{{ __('Verification Code') }}" />
                <x-jet-input id="otp" class="w-full is-invalid" type="text" name="otp" :value="old('otp')" required autofocus />
            </div>
            @include('/flash-message-pass')
            @else
            <div class="block">
                <x-jet-label for="otp" value="{{ __('Verification Code') }}" />
                <x-jet-input id="otp" class="block mt-1 w-full" type="otp" name="otp" :value="old('otp')" required autofocus />
            </div>
            @endif
            <div class="mb-4 text-sm text-gray-60" id="message-show">
                <span>If somehow, you did not recieve the verification email then resend the verification email in 
                <span id="time"></span></span>
            </div>
            <!-- edit : arka.moharifrifai 2022-06-03 -->
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="javascript:;" rel="noopener noreferrer">
                <div id="message-send">Resend Email</div>
            </a>
            <div class="fade show" role="alert" style="color: red" id="message-error">
                
            </div>
            <!-- edit : arka.moharifrifai 2022-06-03 -->
            <div class="flex items-center justify-end mt-4">
                <x-jet-button>
                    {{ __('Verify') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
<script>
    var email
    function startTimer(duration, display) {
        var timer = duration, minutes, seconds;
        var interv;
        interv = setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;

            if (--timer < 0) {
                clearInterval(interv);
                display.textContent = "";
                display = document.querySelector('#message-show');
                message = document.querySelector('#message-send');
                time = document.querySelector('#time');
                time.style.display = "block";
                display.style.display = "none";
                message.style.display = "block";
            }
        }, 1000);
    
    }

    window.onload = function () {
        // var minutes = 25,
        // edit : arka.moharifrifai 2022-06-03
        var minutes = $("#next_send").val();
        // edit : arka.moharifrifai 2022-06-03
        display = document.querySelector('#time');
        startTimer(minutes, display);
        display = document.querySelector('#message-show');
        display.style.display = "block";
        time = document.querySelector('#time');
        time.style.display = "block";
        message = document.querySelector('#message-send');
        message.style.display = "none";
        email = $('#email').val();
    };

    $('#message-send').click(function(){

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var data = new FormData();
        data.append('email', email);
        // console.log(data);
        $.ajax({
                type: 'POST',
                url: '/forgot_pass_resend_email',
                data: data,
                contentType: false,
                processData: false,
                success:function(request)
                {
                    if(request.status == true){             
                        
                        display = document.querySelector('#message-show');
                        message = document.querySelector('#message-send');
                        time = document.querySelector('#time');
                        
                        display.style.display = "block";
                        message.style.display = "none";
                        time.style.display = "block";
                        startTimer(request.data['next_send'], time);
                    }else{
                        $("#message-error").html(request.message);
                        $("#message-send").hide()
                    }
                },
                error: function(xhr, status, error){

                }
        });
    });


</script>
