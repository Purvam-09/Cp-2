document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });
    }
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (navLinks && navLinks.classList.contains('active') && !event.target.closest('.nav-links') && !event.target.closest('.menu-toggle')) {
            navLinks.classList.remove('active');
        }
    });
    
    // Header scroll effect
    const header = document.getElementById('header');
    
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }
    
    // Animate on scroll elements
    const animateElements = document.querySelectorAll('.animate-on-scroll');
    
    if (animateElements.length) {
        const animateOnScroll = function() {
            animateElements.forEach(element => {
                const elementPosition = element.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                
                if (elementPosition < windowHeight - 100) {
                    element.classList.add('visible');
                }
            });
        };
        
        // Initial check
        animateOnScroll();
        
        // Check on scroll
        window.addEventListener('scroll', animateOnScroll);
    }
    
    // Password visibility toggle
    const passwordToggles = document.querySelectorAll('.password-toggle');
    
    if (passwordToggles.length) {
        passwordToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const passwordField = this.previousElementSibling;
                const icon = this.querySelector('i');
                
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordField.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    }
});