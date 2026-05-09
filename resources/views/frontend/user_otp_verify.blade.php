@extends('frontend.layouts.app')

@section('content')
    <section class="gry-bg py-5">
        <div class="profile">
            <div class="container">
                <!-- <div class="row">
                    <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-8 mx-auto">
                        <div class="card shadow-lg">
                            <div class="text-center pt-4">
                                <h1 class="h4 fw-600">
                                    {{ translate('Login') }}
                                </h1>
                            </div>

                            <div class="px-4 py-3 py-lg-4">
                                <div class="">
                                    <form class="form-default" role="form" action="{{ route('user.user_otp_verify') }}" method="POST">
                                        @csrf
                                        <input type="hidden" id="mobile-input" name="mobile" value="{{ session('mobile') }}">
                                        <input type="hidden" name="otp" id="otp-value">


                                        <div class="form-group phone-form-group mb-1">
                                            <input type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input">
                                            <input type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input">
                                            <input type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input">
                                            <input type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input">
                                        </div>
                                        <div class="mb-5">
                                            <button type="submit" class="btn btn-primary btn-block fw-600 ">{{  translate('Login With OTP') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="row align-items-center otp_page">
                    <div class="col-12">
                        <div class="card shadow-lg margin-0">
                            <div class="card-body p-0">
                                <div class="row g-0">
                                    <!-- Left Side - Logo -->
                                    <div class="col-lg-6 d-flex align-items-center justify-content-center p-5" style="background-color: #f8f9fa;">
                                        <div class="text-center">
                                            <a  class="d-block py-15px mr-3 ml-0" href="{{ route('home') }}">
                                                @php
                                                    $header_logo = get_setting('header_logo');
                                                @endphp
                                                @if($header_logo != null)
                                                    <img src="{{ uploaded_asset($header_logo) }}" alt="{{ env('APP_NAME') }}" class="img-fluid" style="max-width: 300px;">
                                                @else
                                                    <img src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}" class="img-fluid" style="max-width: 300px;">
                                                @endif
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Right Side - OTP Form -->
                                    <div class="col-lg-6 d-flex align-items-center justify-content-center p-5">
                                        <div class="w-100" style="max-width: 450px;">
                                            <h4 class="fw-bold mb-2 text-center" style="color: #333;">OTP Verification</h4>
                                            <p class="text-muted mb-4 text-center">
                                                Enter the OTP you received at<br>
                                                <!-- <strong style="color: #333;">{{ session('mobile') }}</strong> -->
                                                 <strong style="color: #333;" id="otp-title-number"></strong>
                                            </p>

                                            <form class="form-default" role="form" action="{{ route('user.user_otp_verify') }}" method="POST">
                                                @csrf
                                                <input type="hidden" id="mobile-input" name="mobile" value="{{ session('mobile') }}">
                                                <input type="hidden" name="otp" id="otp-value">

                                                <!-- OTP Input Fields - 4 inputs -->
                                                <div class="form-group phone-form-group mb-1 d-flex gap-3 justify-content-center">
                                                    <input type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" 
                                                        class="otp-input form-control text-center fw-bold" 
                                                        style="width: 48px; height: 38px; font-size: 22px; border: 1px solid #ddd;margin-right:8px; border-radius: 7px;">
                                                    <input type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" 
                                                        class="otp-input form-control text-center fw-bold" 
                                                        style="width: 48px; height: 38px; font-size: 22px; border: 1px solid #ddd;margin-right:8px; border-radius: 7px;">
                                                    <input type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" 
                                                        class="otp-input form-control text-center fw-bold" 
                                                        style="width: 48px; height: 38px; font-size: 22px; border: 1px solid #ddd;margin-right:8px; border-radius: 7px;">
                                                    <input type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" 
                                                        class="otp-input form-control text-center fw-bold" 
                                                        style="width: 48px; height: 38px; font-size: 22px; border: 1px solid #ddd;margin-right:8px; border-radius: 7px;">
                                                </div>

                                                <!-- Resend OTP -->
                                                <div class="text-center mb-4 mt-3">
                                                    <a href="#" class="text-decoration-none fw-600" style="color: #377dff;" id="resend-otp">
                                                        Resend OTP (<span id="timer">28</span>)
                                                    </a>
                                                </div>

                                                <!-- Verify Button -->
                                                <div class="mb-5">
                                                    <button type="submit" class="btn btn-primary btn-block fw-600">
                                                        Verify
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            let mobile = "{{ session('mobile') }}";

            if (mobile) {
                localStorage.setItem('mobile', mobile);
            }

            let storedMobile = localStorage.getItem('mobile');

            document.getElementById('otp-title-number').innerText = storedMobile;
            document.getElementById('mobile-input').value = storedMobile;
        });

        const inputs = document.querySelectorAll('.otp-input');
        const otpField = document.getElementById('otp-value');

        inputs.forEach((input, index) => {
            // Auto-focus next input
            input.addEventListener('input', function() {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }

                let otp = '';
                inputs.forEach(i => otp += i.value);
                otpField.value = otp;
            });

            // Handle backspace
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '') {
                    if (index > 0) {
                        inputs[index - 1].focus();
                    }
                }
            });

            // Only allow numbers
            input.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });

            // Handle paste
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text');
                const digits = pastedData.replace(/\D/g, '').split('');
                
                digits.forEach((digit, i) => {
                    if (inputs[index + i]) {
                        inputs[index + i].value = digit;
                    }
                });
                
                const lastFilledIndex = Math.min(index + digits.length - 1, inputs.length - 1);
                inputs[lastFilledIndex].focus();
                
                let otp = '';
                inputs.forEach(i => otp += i.value);
                otpField.value = otp;
            });
        });
       
    </script>

    <script>
    let countdown = 120;
    const timerEl = document.getElementById("timer");
    const resendBtn = document.getElementById("resend-otp");

    // Disable click initially
    resendBtn.style.pointerEvents = "none";
    resendBtn.style.opacity = "0.6";

    const interval = setInterval(() => {
        countdown--;
        timerEl.textContent = countdown;

        if (countdown <= 0) {
            clearInterval(interval);
            timerEl.textContent = "0";
            resendBtn.style.pointerEvents = "auto";
            resendBtn.style.opacity = "1";
            resendBtn.innerHTML = "Resend OTP";
        }
    }, 1000);

    // Click event
    resendBtn.addEventListener("click", function (e) {
        e.preventDefault();

        // 🔥 Call your resend OTP API here
        console.log("OTP Resent");

        // Restart countdown
        countdown = 28;
        timerEl.textContent = countdown;
        resendBtn.innerHTML = `Resend OTP (<span id="timer">${countdown}</span>)`;
        resendBtn.style.pointerEvents = "none";
        resendBtn.style.opacity = "0.6";

        startCountdown();
    });

    function startCountdown() {
        const newTimerEl = document.getElementById("timer");
        const newInterval = setInterval(() => {
            countdown--;
            newTimerEl.textContent = countdown;

            if (countdown <= 0) {
                clearInterval(newInterval);
                resendBtn.style.pointerEvents = "auto";
                resendBtn.style.opacity = "1";
                resendBtn.innerHTML = "Resend OTP";
            }
        }, 1000);
    }
</script>

@endsection