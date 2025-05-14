document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const form = document.getElementById('health-profile-form');
    const formSteps = document.querySelectorAll('.form-step');
    const progressBar = document.querySelector('.progress');
    const steps = document.querySelectorAll('.step');
    const nextButtons = document.querySelectorAll('.btn-next');
    const prevButtons = document.querySelectorAll('.btn-prev');
    
    let currentStep = 1;
    const totalSteps = formSteps.length;
    
    // Update progress function
    function updateProgress() {
        // Update progress bar
        const progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
        progressBar.style.width = `${progressPercentage}%`;
        
        // Update steps
        steps.forEach((step, index) => {
            const stepNum = index + 1;
            
            if (stepNum < currentStep) {
                step.classList.add('completed');
                step.classList.remove('active');
            } else if (stepNum === currentStep) {
                step.classList.add('active');
                step.classList.remove('completed');
            } else {
                step.classList.remove('active', 'completed');
            }
        });
        
        // Show/hide form steps
        formSteps.forEach((step, index) => {
            const stepNum = index + 1;
            
            if (stepNum === currentStep) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });
        
        // Scroll to top of form
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    // Validate form step
    function validateStep(step) {
        let isValid = true;
        const inputs = formSteps[step - 1].querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            if (input.type === 'radio' || input.type === 'checkbox') {
                // For radio and checkbox, check if any in the group is checked
                const name = input.name;
                const checked = document.querySelector(`input[name="${name}"]:checked`);
                
                if (!checked) {
                    isValid = false;
                    // Find the parent label group and add error class
                    const fieldset = input.closest('.radio-group, .checkbox-grid');
                    if (fieldset) {
                        fieldset.classList.add('error');
                    }
                }
            } else {
                // For other input types
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('error');
                }
            }
        });
        
        return isValid;
    }
    
    // Remove error indicators when user interacts with fields
    form.addEventListener('input', function(e) {
        if (e.target.classList.contains('error')) {
            e.target.classList.remove('error');
        }
        
        // For radio and checkboxes
        if (e.target.type === 'radio' || e.target.type === 'checkbox') {
            const fieldset = e.target.closest('.radio-group, .checkbox-grid');
            if (fieldset && fieldset.classList.contains('error')) {
                fieldset.classList.remove('error');
            }
        }
    });
    
    // Add this at the top of the file if not already present
    function showModal(message) {
        // If the modal is not present, fallback to alert
        var modal = document.getElementById('customModal');
        var msg = document.getElementById('modalMessage');
        if (modal && msg) {
            msg.innerText = message;
            modal.style.display = 'block';
        } else {
            alert(message);
        }
    }
    
    // Next button event listeners
    nextButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (validateStep(currentStep)) {
                currentStep = Math.min(currentStep + 1, totalSteps);
                updateProgress();
            } else {
                showModal('Please fill in all required fields before proceeding.');
            }
        });
    });
    
    // Previous button event listeners
    prevButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentStep = Math.max(currentStep - 1, 1);
            updateProgress();
        });
    });
    
    // Step indicator clicks
    steps.forEach((step, index) => {
        step.addEventListener('click', function() {
            const stepNum = index + 1;
            
            // Only allow going back to previous steps or current step
            if (stepNum <= currentStep) {
                currentStep = stepNum;
                updateProgress();
            }
        });
    });
    
    // Form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateStep(currentStep)) {
                e.preventDefault();
                showModal('Please fill in all required fields before submitting.');
            }
        });
    }
    
    // Height and weight unit changes
    const heightInput = document.getElementById('height');
    const heightUnit = document.querySelector('select[name="height_unit"]');
    const weightInput = document.getElementById('weight');
    const weightUnit = document.querySelector('select[name="weight_unit"]');
    
    if (heightInput && heightUnit) {
        heightUnit.addEventListener('change', function() {
            if (heightInput.value) {
                // Convert between cm and feet
                if (this.value === 'feet' && heightInput.value > 0) {
                    // Convert cm to feet (approx)
                    heightInput.value = (heightInput.value / 30.48).toFixed(1);
                } else if (this.value === 'cm' && heightInput.value > 0) {
                    // Convert feet to cm
                    heightInput.value = Math.round(heightInput.value * 30.48);
                }
            }
        });
    }
    
    if (weightInput && weightUnit) {
        weightUnit.addEventListener('change', function() {
            if (weightInput.value) {
                // Convert between kg and lb
                if (this.value === 'lb' && weightInput.value > 0) {
                    // Convert kg to lb
                    weightInput.value = Math.round(weightInput.value * 2.20462);
                } else if (this.value === 'kg' && weightInput.value > 0) {
                    // Convert lb to kg
                    weightInput.value = (weightInput.value / 2.20462).toFixed(1);
                }
            }
        });
    }
    
    // Initialize
    updateProgress();
});