@extends('layouts.auth')
@section('content')

<div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5">
                <div class="card auth-card">
                    <div class="card-body px-3 py-5">
                        <div class="mx-auto mb-4 text-center auth-logo">
                            <a href="{{ route('login') }}" class="logo-dark">
                                <img src="{{ url('logo.png') }}" height="100" alt="logo dark">
                            </a>

                            <a href="{{ route('login') }}" class="logo-light">
                                <img src="{{ url('logo.png') }}" height="100" alt="logo light">
                            </a>
                        </div>

                        <h2 class="fw-bold text-uppercase text-center fs-18">Sign In</h2>
                        <p class="text-muted text-center mt-1 mb-4">Enter your email address and password to access admin panel.</p>

                        <!-- Display Success Messages -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Display Status Messages -->
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="px-4">
                            <form action="" method="POST" class="authentication-form" id="loginForm">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email"
                                           id="email"
                                           name="email"
                                           class="form-control bg-light bg-opacity-50 border-light py-2 @error('email') is-invalid @enderror"
                                           placeholder="Enter your email"
                                           value="{{ old('email') }}"
                                           >
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                               <div class="mb-3">
    <label class="form-label" for="password">Password</label>
    <div class="input-group">
        <input type="password"
               id="password"
               name="password"
               class="form-control bg-light bg-opacity-50 border-light py-2 @error('password') is-invalid @enderror"
               placeholder="Enter your password"
               >
        <button class="btn btn-success" type="button" id="togglePassword" tabindex="-1" aria-label="Toggle password visibility">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
              <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8z"/>
              <path d="M8 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6z"/>
            </svg>
        </button>
    </div>
    @error('password')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

                                <div class="mb-1 text-center d-grid">
                                    <button class="btn btn-warning py-2 fw-medium" type="submit" id="loginButton">
                                        <span class="button-text">Sign In</span>
                                        <span class="button-spinner d-none">
                                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                            Signing In...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> <!-- end card -->
            </div>
        </div> <!-- end row -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const loginButton = document.getElementById('loginButton');
    const buttonText = loginButton.querySelector('.button-text');
    const buttonSpinner = loginButton.querySelector('.button-spinner');

    loginForm.addEventListener('submit', function(e) {
        // Show loading state
        loginButton.disabled = true;
        buttonText.classList.add('d-none');
        buttonSpinner.classList.remove('d-none');

        // Optional: Add some visual feedback to the button
        loginButton.classList.add('pe-none');
    });

    @if($errors->any())
        loginButton.disabled = false;
        buttonText.classList.remove('d-none');
        buttonSpinner.classList.add('d-none');
        loginButton.classList.remove('pe-none');
    @endif

   const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');

togglePassword.addEventListener('click', function () {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);

    // Toggle eye icon
    if(type === 'password') {
        togglePassword.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
              <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8z"/>
              <path d="M8 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6z"/>
            </svg>
        `;
    } else {
        togglePassword.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
              <path d="M13.359 11.238l1.387 1.387a.5.5 0 0 1-.708.708l-1.387-1.387a8.14 8.14 0 0 1-4.651 1.5C3 13.446 0 8 0 8s1.655-2.5 4.651-3.5l-.707-.707A.5.5 0 0 1 4.651 4.5l-.707-.707a.5.5 0 0 1 .708-.708l10 10a.5.5 0 0 1-.708.708l-1.387-1.387zM11.646 9.354a3 3 0 0 0-4.292-4.292l4.292 4.292z"/>
              <path d="M3.646 3.646a.5.5 0 0 1 .708 0l8 8a.5.5 0 0 1-.708.708l-8-8a.5.5 0 0 1 0-.708z"/>
            </svg>
        `;
    }
});

});
</script>

@endsection
