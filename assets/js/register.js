// Multi-step form navigation
document.addEventListener('DOMContentLoaded', function() {
    const formSteps = document.querySelectorAll('.form-step');
    const stepDots = document.querySelectorAll('.step-dot');
    
    // Next buttons
    document.querySelectorAll('.next-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const nextStep = this.getAttribute('data-next');
            const currentStep = this.closest('.form-step');
            
            // Validate current step inputs
            const inputs = currentStep.querySelectorAll('input[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.parentElement.style.border = '2px solid #ef4444';
                    setTimeout(() => {
                        input.parentElement.style.border = '';
                    }, 2000);
                } else {
                    input.parentElement.style.border = '';
                }
            });
            
            if (isValid) {
                showStep(nextStep);
            }
        });
    });
    
    // Previous buttons
    document.querySelectorAll('.prev-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const prevStep = this.getAttribute('data-prev');
            showStep(prevStep);
        });
    });
    
    // Form submit
    const form = document.getElementById('registerForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const currentStep = document.querySelector('.form-step.active');
            const inputs = currentStep.querySelectorAll('input[required], select[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.parentElement.style.border = '2px solid #ef4444';
                    setTimeout(() => {
                        input.parentElement.style.border = '';
                    }, 2000);
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    function showStep(stepId) {
        // Hide all steps
        formSteps.forEach(step => {
            step.classList.remove('active');
        });
        
        // Show target step
        document.getElementById(stepId).classList.add('active');
        
        // Update step indicator
        const stepNumber = parseInt(stepId.replace('step', ''));
        stepDots.forEach((dot, index) => {
            if (index + 1 <= stepNumber) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});

const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirm_password");
const errorMsg = document.getElementById("password_error");

function validatePassword() {
    if (password.value !== confirmPassword.value) {
        errorMsg.style.display = "block";
        confirmPassword.setCustomValidity("Password tidak cocok");
    } else {
        errorMsg.style.display = "none";
        confirmPassword.setCustomValidity("");
    }
}

password.addEventListener("input", validatePassword);
confirmPassword.addEventListener("input", validatePassword);
