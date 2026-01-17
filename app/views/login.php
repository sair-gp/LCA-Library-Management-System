<?php
session_destroy();
?>

<div class="body">
    <div class="container">
        <div>
            <img src="public/img/HEADER-LOGIN.png">
        </div>
        <form id="formulario" tabindex="500" action="app/controller/c_login.php" method="POST">
            <center>

                <label for="nombre">CEDULA DE IDENTIDAD:</label>
                <input type="text" id="nombre" name="cedula" class="val-cedula val-requerido" required> <br>

                <label for="contrasena">CONTRASEÑA:</label>
                <input type="password" id="contrasena" name="contrasena" required><br>

                <button type="submit">Iniciar sesión</button>

            </center>

        </form>

    </div>
    <footer>
        <p>Biblioteca Publica Luisa Caceres de Arismendi <br>
            Cumaná, Edo. Sucre | <a class="maps" href="https://maps.app.goo.gl/7q7fXzge7NF7LSXb6" target="_blank">Ubicacion via Maps</a> </p>

    </footer>
</div>
<?php


echo '<input type="hidden" id="alertaDulce" value="' . (isset($_GET["alerta"]) ? $_GET["alerta"] : "") . '">';

?>
<style>
    /* styles.css */
    .body {
        font-family: Arial, sans-serif;
        background-image: url('public/img/bg_login.jpg'), linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5));
        background-size: cover;
        background-blend-mode: multiply;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .container {
        width: 35%;
        height: 100vh;
        background-color: rgba(255, 255, 255, 0.8);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        margin-left: 65%
    }


    form {
        max-width: 400px;
        margin: 0 auto;
        margin-top: 5%;
        padding: 20px;
        background-color:
            #fff;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }

    label {
        display: block;
        margin-bottom:
            5px;
    }

    input[type="text"],
    input[type="password"] {
        width: 70%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
    }

    button[type="submit"] {
        background-color: #4CAF50;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    button[type="submit"]:hover {
        background-color: #3e8e41;

    }

    a {

        color: #fff;
    }

    img {
        margin-top: 10px;
        margin-left: 10%;
        width: 400px;
        height: 300px;
    }

    p {

        margin-bottom: 5px;
    }

    footer {
        color: white;
        position: absolute;
        bottom: 10px;
        left: 32.5%;
        transform: translateX(-50%);
        text-align: center;

    }
</style>

<style>
    .input-error {
    border-color: #ff0000;
    box-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
}
</style>

<script>


document.addEventListener('DOMContentLoaded', function() {
    // Definimos los tipos de validación
    const validators = {
        'val-numero': {
            regex: /^\d+$/,
            error: 'Solo se permiten números',
            format: value => value.replace(/\D/g, '')
        },
        'val-cedula': {
            regex: /^\d{8}$/,
            error: 'La cédula debe tener 8 dígitos',
            format: value => value.replace(/\D/g, '').slice(0, 8)
        },
        'val-email': {
            regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            error: 'Ingrese un email válido'
        },
        'val-texto': {
            regex: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/,
            error: 'Solo se permiten letras'
        },
        'val-requerido': {
            regex: /\S+/,
            error: 'Este campo es obligatorio'
        }
    };

    // Función optimizada para validar un input
    function validateInput(input) {
        const validatorClasses = Array.from(input.classList)
            .filter(className => validators.hasOwnProperty(className));
        
        if (validatorClasses.length === 0) return { isValid: true };
        
        for (const validatorClass of validatorClasses) {
            const validator = validators[validatorClass];
            if (!validator.regex.test(input.value)) {
                return { isValid: false, error: validator.error };
            }
        }
        
        return { isValid: true };
    }

    // Aplicar a todos los formularios
    document.querySelectorAll('form').forEach(form => {
        const inputs = Array.from(form.querySelectorAll('input, select, textarea'))
            .filter(input => Array.from(input.classList).some(className => validators.hasOwnProperty(className)));
        
        if (inputs.length === 0) return;

        // Enfocar automáticamente el primer input
        inputs[0].focus();
        
        // Deshabilitar otros inputs inicialmente
        inputs.slice(1).forEach(input => {
            input.disabled = true;
            input.tabIndex = -1;
        });
        
        // Configurar eventos para cada input
        inputs.forEach((input, index) => {
            // Referencia al posible span existente
            let existingSpan = input.nextElementSibling?.classList?.contains('span-message') 
                ? input.nextElementSibling 
                : null;
            
            // Función unificada para manejar validación
            const handleValidation = () => {
                if (input.value.trim() === '') {
                    crearSpan(input, existingSpan, '', '', true);
                    return false;
                }
                
                const { isValid, error } = validateInput(input);
                
                if (!isValid) {
                    crearSpan(input, existingSpan, error, 'red');
                    return false;
                } else {
                    crearSpan(input, existingSpan, '', '', true);
                    
                    // Habilitar siguiente input si existe
                    if (index < inputs.length - 1) {
                        inputs[index + 1].disabled = false;
                        inputs[index + 1].tabIndex = 0;
                    }
                    return true;
                }
            };
            
            // Eventos de interacción
            input.addEventListener('input', function() {
                // Aplicar formato si existe
                const validatorClass = Array.from(this.classList)
                    .find(className => validators.hasOwnProperty(className) && validators[className].format);
                
                if (validatorClass) {
                    this.value = validators[validatorClass].format(this.value);
                }
                
                handleValidation();
                
                // Actualizar referencia al span por si acaso
                existingSpan = input.nextElementSibling?.classList?.contains('span-message') 
                    ? input.nextElementSibling 
                    : null;
            });
            
            input.addEventListener('blur', function() {
                if (!handleValidation()) {
                    this.focus();
                }
            });
            
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Tab') {
                    if (!handleValidation()) {
                        e.preventDefault();
                    }
                }
            });
        });
        
        // Validación al enviar el formulario
        form.addEventListener('submit', function(e) {
            let formIsValid = true;
            
            for (const input of inputs) {
                const existingSpan = input.nextElementSibling?.classList?.contains('span-message') 
                    ? input.nextElementSibling 
                    : null;
                
                if (input.value.trim() === '') {
                    crearSpan(input, existingSpan, 'Este campo es obligatorio', 'red');
                    if (formIsValid) {
                        input.focus();
                        formIsValid = false;
                    }
                    continue;
                }
                
                const { isValid, error } = validateInput(input);
                
                if (!isValid) {
                    crearSpan(input, existingSpan, error, 'red');
                    if (formIsValid) {
                        input.focus();
                        formIsValid = false;
                    }
                } else {
                    crearSpan(input, existingSpan, '', '', true);
                }
            }
            
            if (!formIsValid) {
                e.preventDefault();
            }
        });
    });
});


</script>







<!--script>
    document.addEventListener('DOMContentLoaded', function() {
    const formulario = document.getElementById('formulario');
    const inputCedula = document.getElementById('nombre');
    const inputContrasena = document.getElementById('contrasena');

    // Validar que solo sean números y máximo 8 dígitos
    inputCedula.addEventListener('input', function(e) {
        // Eliminar cualquier caracter que no sea número
        this.value = this.value.replace(/\D/g, '');
        
        // Limitar a 8 dígitos
        if (this.value.length > 8) {
            this.value = this.value.substring(0, 8);
        }
    });

    // Validar antes de enviar el formulario
    formulario.addEventListener('submit', function(e) {
        // Validar que la cédula tenga exactamente 8 dígitos
        if (inputCedula.value.length !== 8) {
            e.preventDefault(); // Evitar envío del formulario
            alert('La cédula debe tener exactamente 8 dígitos');
            inputCedula.focus(); // Mantener el foco en el campo
        }
    });

    // Evitar que el foco salga del campo si no tiene 8 dígitos
    inputCedula.addEventListener('blur', function() {
        if (this.value.length !== 8) {
            this.focus();
        }
    });
});
</script-->