<x-auth-layout title="Patient Registration">
    <div class="text-center mb-4">
         <div class="icon-circle-sm bg-primary bg-opacity-10 text-primary mx-auto mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/></svg>
        </div>
        <h3 class="fw-bold">Patient Registration</h3>
        <p class="text-muted">Create your patient account</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="row g-3">
            <!-- Full Name -->
            <div class="col-12">
                <label for="name" class="form-label">Full Name</label>
                <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required autofocus />
            </div>

            <!-- Email Address -->
            <div class="col-12">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required />
            </div>
            
            <!-- Password -->
            <div class="col-md-6">
                <label for="password" class="form-label">Password</label>
                <input id="password" class="form-control" type="password" name="password" required />
            </div>

            <!-- Confirm Password -->
            <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required />
            </div>

            <!-- Phone Number -->
            <div class="col-md-6">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input id="phone_number" class="form-control" type="tel" name="phone_number" value="{{ old('phone_number') }}" required />
            </div>

            <!-- Date of Birth -->
            <div class="col-md-6">
                <label for="date_of_birth" class="form-label">Date of Birth</label>
                <input id="date_of_birth" class="form-control" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required />
            </div>

            <!-- Gender -->
            <div class="col-md-6">
                <label for="gender" class="form-label">Gender</label>
                <select id="gender" name="gender" class="form-select" required>
                    <option value="" disabled selected>Select gender</option>
                    <option value="male" @selected(old('gender') == 'male')>Male</option>
                    <option value="female" @selected(old('gender') == 'female')>Female</option>
                    <option value="other" @selected(old('gender') == 'other')>Other</option>
                </select>
            </div>

            <!-- Address -->
            <div class="col-12">
                <label for="address" class="form-label">Address</label>
                <textarea id="address" name="address" class="form-control" rows="2" required>{{ old('address') }}</textarea>
            </div>

            @if ($errors->any())
                <div class="col-12 mt-3">
                    <div class="alert alert-danger py-2">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary w-100 py-2">Create Account</button>
            </div>
        </div>
    </form>
    <div class="text-center mt-4">
        <p class="text-muted">Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
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