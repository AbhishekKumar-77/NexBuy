@extends('layouts.app')
@section('title', 'Create Account — NexBuy')

@section('content')
<div style="display: flex; align-items: center; justify-content: center; min-height: 70vh;">
    <div class="card fade-up" style="width: 100%; max-width: 440px; padding: 3rem;">
        <div style="text-align: center; margin-bottom: 2.5rem;">
            <div class="logo" style="justify-content: center; margin-bottom: 1rem; font-size: 2rem;">
                <i class="ph-fill ph-planet"></i> NexBuy
            </div>
            <h1 class="font-display" style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">Create Account</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Join the procurement intelligence network</p>
        </div>

        @if($errors->any())
        <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: var(--radius-sm); padding: 0.75rem 1rem; margin-bottom: 1.5rem; font-size: 0.9rem; color: #EF4444;">
            <ul style="list-style: none; padding: 0; margin: 0;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div style="margin-bottom: 1.25rem;">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Rajesh Kumar" required autofocus>
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="officer@gov.in" required>
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label class="form-label">Department (Optional)</label>
                <input type="text" name="department" class="form-control" value="{{ old('department') }}" placeholder="Ministry of Electronics & IT">
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
            </div>
            <div style="margin-bottom: 2rem;">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Re-enter password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.9rem; font-size: 1rem;">
                <i class="ph-fill ph-user-plus"></i> Create Account
            </button>
        </form>

        <div style="text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--glass-border);">
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Already have an account? <a href="{{ route('login') }}" style="color: #A78BFA; text-decoration: none; font-weight: 600;">Sign In</a>
            </p>
        </div>
    </div>
</div>
@endsection
