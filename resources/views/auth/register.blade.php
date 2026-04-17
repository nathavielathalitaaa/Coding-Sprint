@extends('layouts.app')
@section('content')

<style>
@import url('https://googleapis.com');

:root {
    --bg-base:    #f8faf9;
    --bg-card:    #f0f7f2;
    --bg-inner:   #ebf5ee;
    --sh-light:   rgba(255,255,255,0.95);
    --sh-dark:    rgba(148,188,163,0.35);
    --green-main: #1a9e5c;
    --green-mid:  #2db870;
    --text-dark:  #14321f;
    --text-body:  #3d6650;
}

.sng-regis * { font-family: 'Plus Jakarta Sans', sans-serif !important; box-sizing: border-box; }   

.sng-regis { 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    width: 100vw;
    position: relative;
}


.bg-lines {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-image: radial-gradient(var(--green-lite) 1px, transparent 0);
    background-size: 40px 40px;
    opacity: 0.1;
    z-index: 1;
}

.line-decor {
    position: absolute;
    width: 2px; height: 100%;
    background: linear-gradient(to bottom, transparent, var(--green-lite), transparent);
    opacity: 0.2;
    z-index: 1;
}

.sc-regis {
    background: var(--bg-card);
    border-radius: 30px;
    border: 1px solid rgba(255,255,255,0.8);
    box-shadow:
        20px 20px 40px var(--sh-dark),
        -20px -20px 40px var(--sh-light);
    padding: 40px;
    width: 450px;
    max-width: 95%; 
    position: relative;
    z-index: 10;
}

.si-input {
    background: var(--bg-inner);
    border: 1px solid rgba(255,255,255,0.8);
    box-shadow:
        inset 4px 4px 8px var(--sh-dark),
        inset -4px -4px 8px var(--sh-light);
    border-radius: 15px;
    padding: 12px 20px;
    width: 100%;
    color: var(--text-dark);
    font-size: 16px;
    transition: all 0.2s ease-in-out;
}

.sbtn-primary {
    background: var(--green-main);
    border: none;
    color: white;
    padding: 12px 20px;
    width: 100%;
    border-radius: 15px;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}

.sico-btn {
    background: var(--green-mid);
    border: none;
    color: white;
    padding: 10px;
    border-radius: 50%;
    width: 40px; height: 40px;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s ease-in-out;
}

</style>
<div class="sng-regis">
    <div class="bg-lines"></div>
    <div class="line-decor" style="left: 15%;"></div>
    <div class="line-decor" style="right: 15%;"></div>

    <div class="sc-regis">
        <div class="text-center mb-10">
            <img src="{{ asset('assets/images/logo-sinergi.png') }}" alt="Sinergi" style="height: 45px; margin: 0 auto;">
            
            <div class="text-center mb-10">
                <h4 class="mb-1 text-custom-500">Buat akun</h4>
            </div>

            <form action="{{ route('register') }}" class="mt-10" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="username-field" class="inline-block mb-2 text-base font-medium">Name</label>
                    <input type="text" name="name" id="username-field" class="si-input" placeholder="Masukan Nama Lengkap">
                </div>
                <div class="mb-3">
                    <label for="email-field" class="inline-block mb-2 text-base font-medium">Email</label>
                    <input type="text" name="email" id="email-field" class="si-input" placeholder="Masukan Emailmu">
                </div>
                <div class="mb-3">
                    <label for="password" class="inline-block mb-2 text-base font-medium">Password</label>
                    <input type="password" name="password" id="password" class="si-input" placeholder="Masukan password">
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="inline-block mb-2 text-base font-medium">Password Confirmation</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="si-input" placeholder="Masukan konfirmasi password">
                </div>
                <div class="mt-10">
                    <button type="submit" class="w-full text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Sign Up</button>
                </div>

                <div class="mt-10 text-center">
                    <p class="mb-0 text-slate-500 dark:text-zink-200">Telah memiliki akun?
                        <a href="{{ route('login') }}" class="font-semibold underline transition-all duration-150 ease-linear text-slate-500 dark:text-zink-200 hover:text-custom-500 dark:hover:text-custom-500">Login</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    @section('script')
       
    @endsection
@endsection
