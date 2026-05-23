<?php 
    $page_title = "Register - Task 2";
    $use_bootstrap = true;
    include('header.php'); 
?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        margin: 0;
    }

    input::-ms-reveal,
    input::-ms-clear {
        display: none;
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .btn-primary {
        background-color: #4e73df;
        border: none;
        padding: 12px;
        font-weight: 600;
    }
    
    /* Ensure header sits above the vh-100 container nicely */
    .header-container {
        position: relative;
        z-index: 100;
    }
</style>

    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="row w-100 justify-content-center">
            
            <div class="col-11 col-md-6 col-lg-4">
                <div class="card p-4 shadow my-4">
                    <h2 class="text-center fw-bold mb-3">Sign Up</h2>
                    <p class="text-center text-muted mb-4 small">Create a new account.</p>
                    
                    <form id="registerForm" novalidate>
                        <div class="mb-3">
                            <label for="name" class="form-label small fw-bold">Full Name</label>
                            <input type="text" class="form-control" id="name" placeholder="John Doe" required>
                            <div class="invalid-feedback">Please enter your name.</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold">Email Address <span id="emailLoader" class="spinner-border spinner-border-sm text-primary d-none" role="status"></span></label>
                            <input type="email" class="form-control" id="email" placeholder="name@example.com" required>
                            <div class="invalid-feedback" id="emailFeedback">Please enter a valid email address.</div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label small fw-bold">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" placeholder="Enter password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    Show
                                </button>
                                <div class="invalid-feedback">Password must be at least 6 characters.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label small fw-bold">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm password" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    Show
                                </button>
                                <div class="invalid-feedback" id="confirmPasswordError">Passwords do not match.</div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill">Create Account</button>
                        </div>

                        <div class="text-center mt-3">
                            <span class="small text-muted">Already have an account? <a href="login.php" class="text-decoration-none fw-bold">Login</a></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/Hide Password functionality
        const setupToggle = (toggleId, inputId) => {
            const toggleBtn = document.getElementById(toggleId);
            const inputField = document.getElementById(inputId);
            if(toggleBtn && inputField) {
                toggleBtn.addEventListener('click', function () {
                    const type = inputField.getAttribute('type') === 'password' ? 'text' : 'password';
                    inputField.setAttribute('type', type);
                    this.textContent = type === 'password' ? 'Show' : 'Hide';
                });
            }
        };
        
        setupToggle('togglePassword', 'password');
        setupToggle('toggleConfirmPassword', 'confirmPassword');

        // AJAX Email Check
        const emailInput = document.getElementById('email');
        const emailFeedback = document.getElementById('emailFeedback');
        const emailLoader = document.getElementById('emailLoader');
        let isEmailTaken = false;

        emailInput.addEventListener('blur', async function() {
            const emailValue = this.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if(emailValue === '' || !emailRegex.test(emailValue)) {
                return; // Let standard validation handle empty/invalid format
            }

            // Show loading spinner
            emailLoader.classList.remove('d-none');
            
            try {
                const response = await fetch('check_email.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email: emailValue })
                });
                
                const data = await response.json();
                
                if(data.exists) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                    emailFeedback.innerText = 'This email is already registered!';
                    isEmailTaken = true;
                } else {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    isEmailTaken = false;
                }
            } catch (error) {
                console.error('Error checking email:', error);
            } finally {
                // Hide loading spinner
                emailLoader.classList.add('d-none');
            }
        });

        // Validation Logic
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');

            // Name validation
            if(name.value.trim() === '') {
                name.classList.add('is-invalid');
                isValid = false;
            } else {
                name.classList.remove('is-invalid');
            }

            // Email validation (simple regex & AJAX check)
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if(!emailRegex.test(email.value)) {
                email.classList.add('is-invalid');
                emailFeedback.innerText = 'Please enter a valid email address.';
                isValid = false;
            } else if (isEmailTaken) {
                email.classList.add('is-invalid');
                isValid = false;
            } else {
                email.classList.remove('is-invalid');
            }

            // Password validation (min 6 chars)
            if(password.value.length < 6) {
                password.classList.add('is-invalid');
                isValid = false;
            } else {
                password.classList.remove('is-invalid');
            }

            // Confirm Password validation (matching)
            if(confirmPassword.value !== password.value || confirmPassword.value === '') {
                confirmPassword.classList.add('is-invalid');
                isValid = false;
            } else {
                confirmPassword.classList.remove('is-invalid');
            }

            if(isValid) {
                alert('Registration validation passed!');
                // In a real scenario, this would submit to the server
                // e.g. this.submit();
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include('footer.php'); ?>