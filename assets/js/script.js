/**
 * Task-1 Deliverable: JavaScript Interactive Logic
 * Topics: DOM Manipulation (getElementById, querySelector), Event Handling (click, keyup, change, submit),
 *         Theme Switching (LocalStorage), Client-Side Form Validation, Filter Categories.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Light/Dark Theme Switcher with LocalStorage
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const htmlElement = document.documentElement;

    // Load saved theme or default to dark
    const savedTheme = localStorage.getItem('portfolio_theme') || 'dark';
    htmlElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('portfolio_theme', newTheme);
            updateThemeIcon(newTheme);
        });
    }

    function updateThemeIcon(theme) {
        if (!themeIcon) return;
        if (theme === 'light') {
            themeIcon.className = 'fa-solid fa-sun';
        } else {
            themeIcon.className = 'fa-solid fa-moon';
        }
    }

    // 2. Mobile Navigation Toggle
    const mobileToggle = document.getElementById('mobileToggle');
    const mainNav = document.getElementById('mainNav');

    if (mobileToggle && mainNav) {
        mobileToggle.addEventListener('click', () => {
            mainNav.classList.toggle('open');
            const icon = mobileToggle.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-xmark');
            }
        });
    }

    // Close mobile nav when clicking nav link
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (mainNav && mainNav.classList.contains('open')) {
                mainNav.classList.remove('open');
                const icon = mobileToggle?.querySelector('i');
                if (icon) {
                    icon.className = 'fa-solid fa-bars';
                }
            }
        });
    });

    // 3. Interactive Projects Category Filter
    const filterBtns = document.querySelectorAll('.filter-btn');
    const projectCards = document.querySelectorAll('.project-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active class from all buttons
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filterValue = btn.getAttribute('data-filter');

            projectCards.forEach(card => {
                const category = card.getAttribute('data-category');
                if (filterValue === 'all' || filterValue === category) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // 4. Password Toggle & Password Strength Live Check (Task-1 Form Validation practice)
    const demoPassword = document.getElementById('demoPassword');
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const pwdStrengthMsg = document.getElementById('pwdStrengthMsg');

    if (togglePasswordBtn && demoPassword) {
        togglePasswordBtn.addEventListener('click', () => {
            const type = demoPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            demoPassword.setAttribute('type', type);
            const icon = togglePasswordBtn.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            }
        });
    }

    if (demoPassword && pwdStrengthMsg) {
        demoPassword.addEventListener('keyup', (e) => {
            const val = e.target.value;
            if (val.length === 0) {
                pwdStrengthMsg.innerText = '';
            } else if (val.length < 6) {
                pwdStrengthMsg.innerText = 'Weak (Password must be at least 6 characters)';
                pwdStrengthMsg.style.color = '#ef4444';
            } else if (val.length < 10) {
                pwdStrengthMsg.innerText = 'Medium strength';
                pwdStrengthMsg.style.color = '#f59e0b';
            } else {
                pwdStrengthMsg.innerText = 'Strong password ✓';
                pwdStrengthMsg.style.color = '#22c55e';
            }
        });
    }

    // 5. Client-Side Contact Form Validation
    const contactForm = document.getElementById('portfolioContactForm');
    const fullNameInput = document.getElementById('fullName');
    const emailInput = document.getElementById('emailAddress');
    const messageInput = document.getElementById('messageContent');
    const nameError = document.getElementById('nameError');
    const emailError = document.getElementById('emailError');
    const messageError = document.getElementById('messageError');
    const formStatusMsg = document.getElementById('formStatusMsg');

    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            let isValid = true;

            // Reset error messages
            if (nameError) nameError.innerText = '';
            if (emailError) emailError.innerText = '';
            if (messageError) messageError.innerText = '';
            if (formStatusMsg) {
                formStatusMsg.className = 'form-status';
                formStatusMsg.innerText = '';
            }

            // Name validation
            if (fullNameInput && fullNameInput.value.trim().length < 2) {
                if (nameError) nameError.innerText = 'Please enter a valid name (at least 2 characters).';
                isValid = false;
            }

            // Email validation using Regular Expression
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailInput && !emailRegex.test(emailInput.value.trim())) {
                if (emailError) emailError.innerText = 'Please enter a valid email address.';
                isValid = false;
            }

            // Message length validation
            if (messageInput && messageInput.value.trim().length < 10) {
                if (messageError) messageError.innerText = 'Message must be at least 10 characters long.';
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault(); // Stop form submission if invalid
                if (formStatusMsg) {
                    formStatusMsg.className = 'form-status error';
                    formStatusMsg.innerText = 'Please fix the errors above before submitting.';
                }
            }
        });
    }
});
