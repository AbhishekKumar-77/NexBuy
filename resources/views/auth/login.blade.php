@extends('layouts.app')
@section('title', 'Sign In — NexBuy')

@section('content')
<div style="display: flex; align-items: center; justify-content: center; min-height: 70vh;">
    <div class="card fade-up" style="width: 100%; max-width: 440px; padding: 3rem;">
        <div style="text-align: center; margin-bottom: 2.5rem;">
            <div class="logo" style="justify-content: center; margin-bottom: 1rem; font-size: 2rem;">
                <i class="ph-fill ph-planet"></i> NexBuy
            </div>
            <h1 class="font-display" style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">Welcome Back</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Sign in to access your procurement dashboard</p>
        </div>

        @if($errors->any())
        <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: var(--radius-sm); padding: 0.75rem 1rem; margin-bottom: 1.5rem; font-size: 0.9rem; color: #EF4444;">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div style="margin-bottom: 1.25rem;">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="officer@gov.in" required autofocus>
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem;">
                <input type="checkbox" name="remember" id="remember" style="accent-color: var(--primary);">
                <label for="remember" style="font-size: 0.9rem; color: var(--text-muted); cursor: pointer;">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.9rem; font-size: 1rem;">
                <i class="ph-fill ph-sign-in"></i> Access Dashboard
            </button>
        </form>

        <div style="text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--glass-border);">
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                New to NexBuy? <a href="{{ route('register') }}" style="color: #A78BFA; text-decoration: none; font-weight: 600;">Create Account</a>
            </p>
        </div>
    </div>
</div>
@endsection
