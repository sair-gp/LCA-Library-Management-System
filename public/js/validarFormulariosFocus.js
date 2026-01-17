document.addEventListener('DOMContentLoaded', function() {
    // Definimos los tipos de validación
    const validators = {
        'val-numero': {
            regex: /^\d+$/,
            error: 'Solo se permiten números',
            format: value => value.replace(/\D/g, ''),
            preventInvalid: true
        },
        'val-telefono': {
            regex: /^\d{7}$/,
            error: 'El teléfono debe tener 7 dígitos',
            format: value => value.replace(/\D/g, '').slice(0, 7),
            preventInvalid: false,
            validateOnlyWhenComplete: true
        },
        'val-cedula': {
            regex: /^\d{8}$/,
            error: 'La cédula debe tener 8 dígitos',
            format: value => value.replace(/\D/g, '').slice(0, 8),
            preventInvalid: false,
            ajax: true
        },
        'val-email': {
            regex: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9-]+\.(com|net|org|edu|gov|mil|int)$/,
            error: 'Ingrese un email válido',
            preventInvalid: /^[a-zA-Z0-9._%+-@]*$/,
            validateComplex: true
        },
        'val-texto': {
            regex: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/,
            error: 'Solo se permiten letras',
            preventInvalid: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]*$/,
            format: value => value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')
        },
        'val-requerido': {
            regex: /\S+/,
            error: 'Este campo es obligatorio'
        },
        'val-select': {
            validate: select => select.value !== '',
            error: 'Debe seleccionar una opción'
        }
    };

    // Función para validar email complejo
    async function validateComplexEmail(input) {
        const email = input.value;
        const existingSpan = input.nextElementSibling?.classList?.contains('span-message') 
            ? input.nextElementSibling 
            : null;

        // Lista de dominios populares permitidos
        const allowedDomains = ["gmail", "yahoo", "outlook", "hotmail", "aol", "icloud", "protonmail", "yandex", "zoho", "mail"];
        const allowedExtensions = ["com", "net", "org", "edu", "gov", "mil", "int"];

        // Dividir el correo en partes
        let parts = email.split("@");

        // Validar múltiples @
        if (parts.length > 2) {
            input.value = email.slice(0, -1);
            crearSpan(input, existingSpan, "Formato incorrecto. Solo se permite un '@'.", "red");
            return { isValid: false };
        }

        let localPart = parts[0];
        let domainAndExtension = parts[1] || "";

        // Validar longitud nombre de usuario
        if (localPart.length > 64) {
            crearSpan(input, existingSpan, "Has alcanzado el límite de 64 caracteres para el nombre del correo.", "red");
            return { isValid: false };
        }

        if (domainAndExtension) {
            let domainParts = domainAndExtension.split(".");
            let domainName = domainParts[0] || "";
            let domainExtension = domainParts[1] || "";

            // Validar longitud dominio
            if (domainName.length > 255) {
                crearSpan(input, existingSpan, "Has alcanzado el límite de 255 caracteres para el nombre de dominio.", "red");
                return { isValid: false };
            }

            // Validar dominio permitido
            if (!allowedDomains.includes(domainName)) {
                crearSpan(input, existingSpan, "El dominio ingresado no está permitido. Usa un dominio popular como Gmail o Yahoo.", "red");
                return { isValid: false };
            }

            // Validar extensión
            if (domainExtension && !allowedExtensions.includes(domainExtension)) {
                crearSpan(input, existingSpan, "La extensión del dominio no es válida. Usa .com, .net, .org, etc.", "red");
                return { isValid: false };
            }
        }

        // Validación final con regex
        if (validators['val-email'].regex.test(email)) {
            crearSpan(input, existingSpan, "", "", true);
            return { isValid: true };
        } else {
            return { isValid: false };
        }
    }

    // Función para validar cédula con AJAX
    async function validateCedula(input) {
        const existingSpan = input.nextElementSibling?.classList?.contains('span-message') 
            ? input.nextElementSibling 
            : null;

        // Validación inicial del formato
        if (!validators['val-cedula'].regex.test(input.value)) {
            crearSpan(input, existingSpan, validators['val-cedula'].error, "red");
            return { isValid: false };
        }

        try {
            const response = await fetch("public/js/ajax/validarCamposUnicos.php", {
                method: "POST",
                headers: {"Content-type": "application/json"},
                body: JSON.stringify({validarCedulaVisitante: input.value})
            });

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            
            // Limpiar mensajes anteriores
            if (existingSpan) {
                crearSpan(input, existingSpan, "", "", true);
            }

            // Mostrar nuevo mensaje
            if (data.message) {
                crearSpan(input, existingSpan, data.message, data.class);
            }

            return { 
                isValid: data.class !== "red",
                isFinalValidation: true
            };
        } catch (error) {
            console.error("Error al validar la cédula:", error);
            return { isValid: false, error: "Error al validar la cédula" };
        }
    }

    // Función optimizada para validar un input
    async function validateInput(input) {
        // Validación especial para selects
        if (input.tagName === 'SELECT' && input.classList.contains('val-select')) {
            const isValid = validators['val-select'].validate(input);
            return {
                isValid,
                error: isValid ? '' : validators['val-select'].error
            };
        }

        const validatorClasses = Array.from(input.classList)
            .filter(className => validators.hasOwnProperty(className));
        
        if (validatorClasses.length === 0) return { isValid: true };
        
        // Validación especial para cédula
        if (validatorClasses.includes('val-cedula') && validators['val-cedula'].ajax) {
            return await validateCedula(input);
        }
        
        // Validación especial para email
        if (validatorClasses.includes('val-email') && validators['val-email'].validateComplex) {
            return await validateComplexEmail(input);
        }
        
        // Validación para teléfono (solo cuando está completo)
        if (validatorClasses.includes('val-telefono') && validators['val-telefono'].validateOnlyWhenComplete) {
            if (input.value.length < 7) {
                return { isValid: true, pending: true };
            }
        }
        
        // Otras validaciones
        for (const validatorClass of validatorClasses) {
            const validator = validators[validatorClass];
            if (validator.regex && !validator.regex.test(input.value)) {
                return { isValid: false, error: validator.error };
            }
        }
        
        return { isValid: true };
    }

    // Función para prevenir caracteres inválidos
    function preventInvalidCharacters(input, validator) {
        if (!validator.preventInvalid) return;

        const preventPattern = validator.preventInvalid === true ? 
            validator.regex : 
            validator.preventInvalid;

        input.addEventListener('keypress', function(e) {
            const char = String.fromCharCode(e.keyCode || e.which);
            const currentValue = this.value + char;
            
            if (!preventPattern.test(currentValue)) {
                e.preventDefault();
            }
        });
    }

    // Configuración de eventos del input
    function configureInputEvents(input, index, inputs, form) {
        let lastValidValue = '';
        let isValidating = false;
        const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');

        // Aplicar prevención de caracteres inválidos
        const validatorClass = Array.from(input.classList)
            .find(className => validators.hasOwnProperty(className) && 
                 validators[className].preventInvalid);
        
        if (validatorClass) {
            preventInvalidCharacters(input, validators[validatorClass]);
        }

        input.addEventListener('input', async function() {
            if (isValidating) return;
            isValidating = true;

            // Aplicar formato si es necesario
            const validatorClass = Array.from(input.classList)
                .find(className => validators.hasOwnProperty(className) && validators[className].format);
            
            if (validatorClass) {
                input.value = validators[validatorClass].format(input.value);
            }

            // Obtener referencia al span actual
            const currentSpan = input.nextElementSibling?.classList?.contains('span-message') 
                ? input.nextElementSibling 
                : null;

            // Solo validar si el valor cambió significativamente
            if (input.value !== lastValidValue) {
                const { isValid, error, pending } = await validateInput(input);

                if (!isValid) {
                    if (error) crearSpan(input, currentSpan, error, 'red');
                    // Bloquear siguientes inputs
                    inputs.slice(index + 1).forEach(nextInput => {
                        nextInput.disabled = true;
                        nextInput.tabIndex = -1;
                    });
                } else if (!pending) {
                    crearSpan(input, currentSpan, "", "", true);
                    // Habilitar siguiente input si es el último válido
                    if (index < inputs.length - 1 && inputs[index + 1].disabled) {
                        inputs[index + 1].disabled = false;
                        inputs[index + 1].tabIndex = 0;
                    }
                    lastValidValue = input.value;
                }

                // Actualizar estado del botón submit
                if (submitButton) {
                    const allValid = await checkAllValidations(inputs);
                    submitButton.disabled = !allValid;
                }
            }

            isValidating = false;
        });

        input.addEventListener('blur', async function() {
            const { isValid } = await validateInput(input);
            if (!isValid) {
                input.focus();
            }
        });

        // Para selects, validar cuando cambian
        if (input.tagName === 'SELECT') {
            input.addEventListener('change', async function() {
                const { isValid } = await validateInput(input);
                const currentSpan = input.nextElementSibling?.classList?.contains('span-message') 
                    ? input.nextElementSibling 
                    : null;
                
                if (!isValid) {
                    // Bloquear siguientes inputs
                    inputs.slice(index + 1).forEach(nextInput => {
                        nextInput.disabled = true;
                        nextInput.tabIndex = -1;
                    });
                } else {
                    // Habilitar siguiente input si es el último válido
                    if (index < inputs.length - 1 && inputs[index + 1].disabled) {
                        inputs[index + 1].disabled = false;
                        inputs[index + 1].tabIndex = 0;
                    }
                }
                
                // Actualizar estado del botón submit
                if (submitButton) {
                    const allValid = await checkAllValidations(inputs);
                    submitButton.disabled = !allValid;
                }
            });
        }
    }

    // Función para verificar todas las validaciones
    async function checkAllValidations(inputs) {
        const results = await Promise.all(inputs.map(validateInput));
        return results.every(r => r.isValid);
    }

    // Aplicar a todos los formularios
    document.querySelectorAll('form').forEach(form => {
        const inputs = Array.from(form.querySelectorAll('input, select, textarea'))
            .filter(input => {
                const classes = Array.from(input.classList);
                return classes.some(className => validators.hasOwnProperty(className)) ||
                       (input.tagName === 'SELECT' && classes.includes('val-select'));
            });
        
        if (inputs.length === 0) return;

        const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
        
        // Configuración inicial
        inputs[0].focus();
        inputs.slice(1).forEach(input => {
            input.disabled = true;
            input.tabIndex = -1;
        });
        
        // Configurar eventos para cada input
        inputs.forEach((input, index) => {
            configureInputEvents(input, index, inputs, form);
        });
        
        // Validación al enviar el formulario
        form.addEventListener('submit', async function(e) {
            let formIsValid = true;
            let firstInvalidInput = null;
            
            for (const input of inputs) {
                const currentSpan = input.nextElementSibling?.classList?.contains('span-message') 
                    ? input.nextElementSibling 
                    : null;
                
                const { isValid, error } = await validateInput(input);
                
                if (!isValid) {
                    if (error) crearSpan(input, currentSpan, error, 'red');
                    if (formIsValid) {
                        firstInvalidInput = input;
                        formIsValid = false;
                    }
                } else {
                    crearSpan(input, currentSpan, "", "", true);
                }
            }
            
            if (!formIsValid && firstInvalidInput) {
                e.preventDefault();
                firstInvalidInput.focus();
            }
        });

        // Validación inicial del botón submit
        if (submitButton) {
            checkAllValidations(inputs).then(allValid => {
                submitButton.disabled = !allValid;
            });
        }
    });
});