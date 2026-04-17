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

.sng-login * { font-family: 'Plus Jakarta Sans', sans-serif !important; box-sizing: border-box; }

.sng-login { 
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

.sc-login {
    background: var(--bg-card);
    border-radius: 30px;
    border: 1px solid rgba(255,255,255,0.8);
    box-shadow:
        20px 20px 40px var(--sh-dark),
        -20px -20px 40px var(--sh-light);
    padding: 25px;
    width: 400px;
    max-width: 95%; 
    position: relative;
    z-index: 10;
}

.si-input {
    background: var(--bg-inner);
    border-radius: 16px;
    border: none; 
    box-shadow:
        inset 5px 5px 10px var(--sh-dark),
        inset -5px -5px 10px var(--sh-light);
    padding: 15px 20px;
    width: 100%;
    color: var(--text-dark);
    outline: none;
    font-size: 15px;
}

.sbtn-primary {
    background: linear-gradient(135deg, var(--green-main), var(--green-mid));
    color: white;  
    border: none;
    border-radius: 16px;
    padding: 16px;
    font-weight: 800;
    width: 100%;
    letter-spacing: 1px;
    box-shadow: 0 10px 20px rgba(26, 158, 92, 0.2);
    cursor: pointer;
    margin-top: 10px;
}

.sico-btn {
    width: 50px; height: 50px; border-radius: 15px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 5px 5px 10px var(--sh-dark), -5px -5px 10px var(--sh-light);
    background: var(--bg-card);
    transition: 0.3s;
}
</style>

<div class="sng-login">
    <!-- Garis Dekorasi Latar Belakang -->
    <div class="line-decor" style="left: 15%;"></div>
    <div class="line-decor" style="right: 15%;"></div>
    
    <div class="sc-login">
        <!-- Logo -->
        <div class="text-center mb-10">
            <img src="{{ asset('assets/images/logo-sinergi.png') }}" alt="Sinergi" style="height: 45px; margin: 0 auto;">
        </div>

        <div class="text-center mb-12">
            <h2 style="color: var(--text-dark); font-weight: 800; font-size: 26px; margin-bottom: 8px;">Welcome Back!</h2>
            <p style="color: var(--text-body); font-size: 14px; opacity: 0.7;">Sign in to Sinergi Hotel & Villa</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label style="display:block; font-size:12px; font-weight:800; color:var(--text-body); margin-bottom:10px; text-transform:uppercase; letter-spacing:1px;">Username / Email</label>
                <input type="text" name="email" class="si-input" placeholder="Masukan Emailmu" required value="{{ old('email') }}">
            </div>

            <div class="mb-6">
                <label style="display:block; font-size:12px; font-weight:800; color:var(--text-body); margin-bottom:10px; text-transform:uppercase; letter-spacing:1px;">Password</label>
                <input type="password" name="password" class="si-input" placeholder="••••••••" required>
            </div>

            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:30px;">
                <label style="display:flex; align-items:center; gap:10px; font-size:13px; color:var(--text-body); cursor:pointer;">
                    <input type="checkbox" name="remember" style="width:18px; height:18px; accent-color: var(--green-main);"> Remember me
                </label>
                <a href="#" style="font-size:13px; color: var(--green-main); font-weight:700; text-decoration:none;">Forgot?</a>
            </div>

            <button type="submit" class="sbtn-primary">SIGN IN</button>

            <div style="margin: 35px 0; text-align: center; position: relative;">
                <span style="background: var(--bg-card); padding: 0 15px; font-size: 11px; font-weight: 800; color: var(--text-body); position: relative; z-index: 2; text-transform: uppercase;">Or Login With</span>
                <div style="position: absolute; top: 50%; width: 100%; border-top: 1px solid rgba(148,188,163,0.3); z-index: 1;"></div>
            </div>

            <div style="display:flex; justify-content:center; gap:20px;">
                <div class="sico-btn"><i data-lucide="mail" style="width:20px; color:var(--text-body)"></i></div>
                <div class="sico-btn"><i data-lucide="facebook" style="width:20px; color:var(--text-body)"></i></div>
                <div class="sico-btn"><i data-lucide="github" style="width:20px; color:var(--text-body)"></i></div>
            </div>

            <div class="text-center mt-12">
                <p style="font-size: 14px; color: var(--text-body);">
                    Tidak memiliki akun?
                    <a href="{{ route('register') }}" style="color: var(--green-main); font-weight: 800; text-decoration: none;">Sign Up</a>
                </p>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com"></script>
<script>
    lucide.createIcons();
</script>
@endsection
