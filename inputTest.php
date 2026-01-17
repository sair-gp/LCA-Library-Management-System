<form id="myForm">
    <!-- Required text field -->
    <input type="text" class="required" placeholder="Name">
    
    <!-- Email field -->
    <input type="text" class="required email" placeholder="Email">
    
    <!-- Password field -->
    <input type="password" class="required password" placeholder="Password">
    
    <!-- Numeric field -->
    <input type="text" class="numeric" placeholder="Age">
    
    <!-- Field with custom pattern -->
    <input type="text" class="pattern-\d{3}-\d{3}-\d{4}" placeholder="Phone (123-456-7890)">
    
    <button type="submit">Submit</button>
</form>

<script>

function validateFormSequentially(formId) {
    const form = document.getElementById(formId);
    if (!form) {
        console.error('Form not found');
        return false;
    }

    // Get all input elements that need validation
    const inputs = Array.from(form.querySelectorAll('input[class], select[class], textarea[class]'));
    if (inputs.length === 0) {
        console.error('No inputs with validation classes found');
        return false;
    }

    let currentIndex = 0;
    let isFormValid = true;

    // Function to validate a single input
    function validateInput(input) {
        const value = input.value.trim();
        const classes = input.className.split(' ');

        // Check for required fields
        if (classes.includes('required') && value === '') {
            showError(input, 'This field is required');
            return false;
        }

        // Email validation
        if (classes.includes('email')) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                showError(input, 'Please enter a valid email address');
                return false;
            }
        }

        // Password validation
        if (classes.includes('password')) {
            if (value.length < 8) {
                showError(input, 'Password must be at least 8 characters');
                return false;
            }
        }

        // Numeric validation
        if (classes.includes('numeric')) {
            if (isNaN(value)) {
                showError(input, 'Please enter a valid number');
                return false;
            }
        }

        // Custom validation patterns
        const patternClass = classes.find(cls => cls.startsWith('pattern-'));
        if (patternClass) {
            const pattern = new RegExp(patternClass.replace('pattern-', ''));
            if (!pattern.test(value)) {
                showError(input, 'Input does not match required format');
                return false;
            }
        }

        clearError(input);
        return true;
    }

    // Helper function to show error
    function showError(input, message) {
        clearError(input);
        isFormValid = false;
        
        const errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        errorElement.textContent = message;
        errorElement.style.color = 'red';
        errorElement.style.fontSize = '0.8em';
        errorElement.style.marginTop = '5px';
        
        input.parentNode.appendChild(errorElement);
        input.classList.add('error');
        input.focus();
    }

    // Helper function to clear error
    function clearError(input) {
        const errorElement = input.parentNode.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
        }
        input.classList.remove('error');
    }

    // Focus on the first input initially
    inputs[0].focus();

    // Add event listeners to all inputs
    inputs.forEach((input, index) => {
        // When leaving an input, validate it
        input.addEventListener('blur', () => {
            if (validateInput(input)) {
                // Only move to next if current input is valid
                if (index === currentIndex) {
                    currentIndex = Math.min(index + 1, inputs.length - 1);
                    inputs[currentIndex].focus();
                }
            } else {
                currentIndex = index;
            }
        });

        // When modifying an already validated input, re-validate
        input.addEventListener('input', () => {
            if (index < currentIndex) {
                currentIndex = index;
                input.focus();
            }
        });
    });

    // Form submission handler
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Validate all inputs before submission
        isFormValid = true;
        inputs.forEach(input => {
            if (!validateInput(input)) {
                isFormValid = false;
            }
        });

        if (isFormValid) {
            form.submit();
        } else {
            // Focus on the first invalid input
            const firstInvalid = inputs.find(input => input.classList.contains('error'));
            if (firstInvalid) {
                firstInvalid.focus();
            }
        }
    });

    return true;
}


    validateFormSequentially('myForm');
</script>