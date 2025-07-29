<x-auth-layout title="Patient Login">
    <div class="text-center mb-4">
        <div class="icon-circle-sm bg-primary bg-opacity-10 text-primary mx-auto mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/></svg>
        </div>
        <h3 class="fw-bold">Patient Login</h3>
        <p class="text-muted">Access your patient dashboard</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-floating mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
            <label for="email">Email Address</label>
            @error('email')
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-floating mb-4">
            <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
            <label for="password">Password</label>
             @error('password')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">Sign In</button>
    </form>

    <div class="text-center mt-4">
        <p class="text-muted">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
        <a href="/" class="text-muted small">← Back to Home</a>
    </div>
</x-auth-layout>
<style>
.icon-circle-sm {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>