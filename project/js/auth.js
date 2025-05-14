document.addEventListener('DOMContentLoaded', function() {
    // Profile image upload preview
    const profileInput = document.getElementById('profile-image');
    const profilePreview = document.querySelector('.profile-upload .preview');
    const profileIcon = document.querySelector('.profile-upload i');
    
    if (profileInput && profilePreview) {
        profileInput.addEventListener('change', function() {
            const file = this.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.addEventListener('load', function() {
                    profilePreview.src = reader.result;
                    profilePreview.style.display = 'block';
                    
                    if (profileIcon) {
                        profileIcon.style.display = 'none';
                    }
                });
                
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Password validation
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm-password');
    const signupForm = document.querySelector('form[action="../php/signup.php"]');
    
    if (signupForm && passwordField && confirmPasswordField) {
        signupForm.addEventListener('submit', function(e) {
            // Check if passwords match
            if (passwordField.value !== confirmPasswordField.value) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            // Check password strength
            if (passwordField.value.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
                return false;
            }
        });
    }
});