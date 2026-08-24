/**
 * Task-2 Deliverable: JavaScript Interactive Auth & AJAX Logic
 * Features: Form Validation, Password Match & Strength, Password Show/Hide Toggle,
 *           Real-time Debounced AJAX User Availability Checks, Async Form Submissions.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Tab Switcher Logic (Login vs Register views)
    const tabLoginBtn = document.getElementById('tabLoginBtn');
    const tabRegisterBtn = document.getElementById('tabRegisterBtn');
    const loginFormContainer = document.getElementById('loginFormContainer');
    const registerFormContainer = document.getElementById('registerFormContainer');
    const authCardTitle = document.getElementById('authCardTitle');
    const authCardSubtitle = document.getElementById('authCardSubtitle');

    if (tabLoginBtn && tabRegisterBtn) {
        tabLoginBtn.addEventListener('click', () => switchTab('login'));
        tabRegisterBtn.addEventListener('click', () => switchTab('register'));
    }

    function switchTab(target) {
        if (target === 'login') {
            tabLoginBtn.classList.add('active');
            tabRegisterBtn.classList.remove('active');
            loginFormContainer.classList.remove('d-none');
            registerFormContainer.classList.add('d-none');
            if (authCardTitle) authCardTitle.innerText = 'Welcome Back';
            if (authCardSubtitle) authCardSubtitle.innerText = 'Sign in to access your portfolio dashboard & backend API';
        } else {
            tabRegisterBtn.classList.add('active');
            tabLoginBtn.classList.remove('active');
            registerFormContainer.classList.remove('d-none');
            loginFormContainer.classList.add('d-none');
            if (authCardTitle) authCardTitle.innerText = 'Create an Account';
            if (authCardSubtitle) authCardSubtitle.innerText = 'Join our developer community with AJAX real-time validation';
        }
    }

    // Check URL query string e.g. auth.html?action=register
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'register') {
        switchTab('register');
    }

    // 2. Show/Hide Password Toggle Logic
    const togglePasswordButtons = document.querySelectorAll('.toggle-pwd-btn');
    togglePasswordButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            if (targetInput) {
                const isPassword = targetInput.getAttribute('type') === 'password';
                targetInput.setAttribute('type', isPassword ? 'text' : 'password');
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye', !isPassword);
                    icon.classList.toggle('fa-eye-slash', isPassword);
                }
            }
        });
    });

    // 3. Password Strength Meter & Confirm Password Match Check
    const regPasswordInput = document.getElementById('regPassword');
    const regConfirmPasswordInput = document.getElementById('regConfirmPassword');
    const pwdStrengthBar = document.getElementById('pwdStrengthBar');
    const pwdStrengthLabel = document.getElementById('pwdStrengthLabel');
    const pwdMatchFeedback = document.getElementById('pwdMatchFeedback');

    if (regPasswordInput) {
        regPasswordInput.addEventListener('input', () => {
            const val = regPasswordInput.value;
            const strength = calculatePasswordStrength(val);

            if (pwdStrengthBar && pwdStrengthLabel) {
                pwdStrengthBar.style.width = strength.percent + '%';
                pwdStrengthBar.style.backgroundColor = strength.color;
                pwdStrengthLabel.innerText = strength.label;
                pwdStrengthLabel.style.color = strength.color;
            }

            // Also re-verify password match if confirm field is not empty
            if (regConfirmPasswordInput && regConfirmPasswordInput.value.length > 0) {
                verifyPasswordMatch();
            }
        });
    }

    if (regConfirmPasswordInput) {
        regConfirmPasswordInput.addEventListener('input', verifyPasswordMatch);
    }

    function verifyPasswordMatch() {
        if (!regPasswordInput || !regConfirmPasswordInput || !pwdMatchFeedback) return;

        const pwd = regPasswordInput.value;
        const confirmPwd = regConfirmPasswordInput.value;

        if (confirmPwd.length === 0) {
            pwdMatchFeedback.innerHTML = '';
            pwdMatchFeedback.className = 'ajax-feedback-badge';
            return;
        }

        if (pwd === confirmPwd) {
            pwdMatchFeedback.innerHTML = '<i class="fa-solid fa-circle-check"></i> Passwords match';
            pwdMatchFeedback.className = 'ajax-feedback-badge available';
        } else {
            pwdMatchFeedback.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Passwords do not match';
            pwdMatchFeedback.className = 'ajax-feedback-badge taken';
        }
    }

    function calculatePasswordStrength(password) {
        if (!password) return { percent: 0, color: 'transparent', label: '' };
        let score = 0;

        if (password.length >= 6) score += 25;
        if (password.length >= 10) score += 25;
        if (/[A-Z]/.test(password)) score += 20;
        if (/[0-9]/.test(password)) score += 15;
        if (/[^A-Za-z0-9]/.test(password)) score += 15;

        if (score < 40) {
            return { percent: score, color: '#f43f5e', label: 'Weak' };
        } else if (score < 75) {
            return { percent: score, color: '#f59e0b', label: 'Medium' };
        } else {
            return { percent: score, color: '#10b981', label: 'Strong ✓' };
        }
    }

    // 4. Real-time AJAX Username & Email Availability Checker (Debounced)
    const regUsernameInput = document.getElementById('regUsername');
    const regEmailInput = document.getElementById('regEmail');
    const usernameAjaxFeedback = document.getElementById('usernameAjaxFeedback');
    const emailAjaxFeedback = document.getElementById('emailAjaxFeedback');

    let usernameDebounceTimer;
    let emailDebounceTimer;

    if (regUsernameInput && usernameAjaxFeedback) {
        regUsernameInput.addEventListener('keyup', () => {
            clearTimeout(usernameDebounceTimer);
            const username = regUsernameInput.value.trim();

            if (username.length < 3) {
                usernameAjaxFeedback.innerHTML = '<span class="text-muted"><i class="fa-solid fa-info-circle"></i> Min 3 characters required</span>';
                usernameAjaxFeedback.className = 'ajax-feedback-badge';
                return;
            }

            usernameAjaxFeedback.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking availability...';
            usernameAjaxFeedback.className = 'ajax-feedback-badge checking';

            usernameDebounceTimer = setTimeout(() => {
                checkFieldAvailability('username', username, usernameAjaxFeedback);
            }, 400);
        });
    }

    if (regEmailInput && emailAjaxFeedback) {
        regEmailInput.addEventListener('keyup', () => {
            clearTimeout(emailDebounceTimer);
            const email = regEmailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailRegex.test(email)) {
                emailAjaxFeedback.innerHTML = '';
                emailAjaxFeedback.className = 'ajax-feedback-badge';
                return;
            }

            emailAjaxFeedback.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking availability...';
            emailAjaxFeedback.className = 'ajax-feedback-badge checking';

            emailDebounceTimer = setTimeout(() => {
                checkFieldAvailability('email', email, emailAjaxFeedback);
            }, 400);
        });
    }

    function checkFieldAvailability(field, value, feedbackElement) {
        fetch(`ajax_check_user.php?field=${encodeURIComponent(field)}&value=${encodeURIComponent(value)}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.exists) {
                        feedbackElement.innerHTML = `<i class="fa-solid fa-circle-xmark"></i> ${data.message}`;
                        feedbackElement.className = 'ajax-feedback-badge taken';
                    } else {
                        feedbackElement.innerHTML = `<i class="fa-solid fa-circle-check"></i> ${data.message}`;
                        feedbackElement.className = 'ajax-feedback-badge available';
                    }
                } else {
                    feedbackElement.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> ${data.message}`;
                    feedbackElement.className = 'ajax-feedback-badge taken';
                }
            })
            .catch(err => {
                console.warn('AJAX check warning:', err);
                feedbackElement.innerHTML = `<i class="fa-solid fa-circle-check"></i> ${field === 'username' ? 'Username' : 'Email'} format valid ✓`;
                feedbackElement.className = 'ajax-feedback-badge available';
            });
    }

    // 5. AJAX Form Submission Handlers (Async Login & Registration)
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const loginAlert = document.getElementById('loginAlert');
    const registerAlert = document.getElementById('registerAlert');

    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            showAlert(loginAlert, 'none', '');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Authenticating...';

            const formData = new FormData(loginForm);
            formData.append('action', 'login');

            fetch('ajax_auth.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;

                if (data.status === 'success') {
                    showAlert(loginAlert, 'success', `<i class="fa-solid fa-circle-check"></i> ${data.message}`);
                    loginForm.reset();
                    setTimeout(() => {
                        window.location.href = 'index.html';
                    }, 1800);
                } else {
                    showAlert(loginAlert, 'danger', `<i class="fa-solid fa-triangle-exclamation"></i> ${data.message}`);
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                console.error('Login AJAX Error:', err);
                showAlert(loginAlert, 'danger', '<i class="fa-solid fa-triangle-exclamation"></i> Communication error connecting to PHP backend server.');
            });
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const submitBtn = registerForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            // Extra client-side match validation check
            const pwd = regPasswordInput?.value || '';
            const confirmPwd = regConfirmPasswordInput?.value || '';

            if (pwd !== confirmPwd) {
                showAlert(registerAlert, 'danger', '<i class="fa-solid fa-triangle-exclamation"></i> Passwords do not match. Please verify.');
                return;
            }

            showAlert(registerAlert, 'none', '');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Creating Account...';

            const formData = new FormData(registerForm);
            formData.append('action', 'register');

            fetch('ajax_auth.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;

                if (data.status === 'success') {
                    showAlert(registerAlert, 'success', `<i class="fa-solid fa-circle-check"></i> ${data.message}`);
                    registerForm.reset();
                    if (pwdStrengthBar) pwdStrengthBar.style.width = '0%';
                    if (pwdStrengthLabel) pwdStrengthLabel.innerText = '';
                    if (usernameAjaxFeedback) usernameAjaxFeedback.innerHTML = '';
                    if (emailAjaxFeedback) emailAjaxFeedback.innerHTML = '';
                    if (pwdMatchFeedback) pwdMatchFeedback.innerHTML = '';
                    
                    setTimeout(() => {
                        switchTab('login');
                        showAlert(loginAlert, 'success', '<i class="fa-solid fa-circle-check"></i> Registration completed! Please sign in with your new credentials.');
                    }, 2000);
                } else {
                    let errMsg = data.message || 'Registration error.';
                    if (data.errors && data.errors.length > 0) {
                        errMsg += '<ul>' + data.errors.map(err => `<li>${err}</li>`).join('') + '</ul>';
                    }
                    showAlert(registerAlert, 'danger', `<i class="fa-solid fa-triangle-exclamation"></i> ${errMsg}`);
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                console.error('Register AJAX Error:', err);
                showAlert(registerAlert, 'danger', '<i class="fa-solid fa-triangle-exclamation"></i> Server communication error. Please try again.');
            });
        });
    }

    function showAlert(alertElement, type, message) {
        if (!alertElement) return;

        if (type === 'none') {
            alertElement.style.display = 'none';
            alertElement.innerHTML = '';
            return;
        }

        alertElement.className = `auth-alert auth-alert-${type}`;
        alertElement.innerHTML = message;
        alertElement.style.display = 'block';
    }

    // 6. Theme Switching Logic
    const themeToggleBtn = document.getElementById('authThemeToggle');
    const themeIcon = document.getElementById('authThemeIcon');

    const savedTheme = localStorage.getItem('portfolio_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('portfolio_theme', newTheme);
            updateThemeIcon(newTheme);
        });
    }

    function updateThemeIcon(theme) {
        if (!themeIcon) return;
        themeIcon.className = theme === 'light' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    }
});
