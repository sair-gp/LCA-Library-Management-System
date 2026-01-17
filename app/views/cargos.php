<?php
require_once "app/config/database.php";
$conexion = conexion();
?>

<div id="add-position-container">
    <h1 id="add-position-title">Agregar Nuevo Cargo</h1>
    <form id="add-position-form">
        <div class="form-group">
            <label for="position-name">Nombre del Cargo:</label>
            <input type="text" id="position-name" name="position-name" placeholder="Ejemplo: Bibliotecario" required />
        </div>
        <div class="form-group">
            <label>Permisos del Sistema:</label>
            <button type="button" id="toggle-permissions-button">Seleccionar Permisos</button>
            <div id="permissions-container" style="display: none;">
                <?php
                $query = "SELECT id, permiso FROM permisos";
                $result = mysqli_query($conexion, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="permission-item">';
                    echo '<input type="checkbox" id="permission-' . $row['id'] . '" name="permissions[]" value="' . $row['id'] . '" />';
                    echo '<label for="permission-' . $row['id'] . '">' . $row['permiso'] . '</label>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        <div id="form-actions">
            <button type="submit" id="submit-button">Guardar Cargo</button>
            <button type="reset" id="reset-button">Limpiar</button>
        </div>
    </form>
</div>

<script>
    document.getElementById("toggle-permissions-button").addEventListener("click", function() {
        const permissionsContainer = document.getElementById("permissions-container");
        if (permissionsContainer.style.display === "none" || permissionsContainer.style.display === "") {
            permissionsContainer.style.display = "grid";
        } else {
            permissionsContainer.style.display = "none";
        }
    });
</script>

<style>
    /* Contenedor principal */
    #add-position-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background: linear-gradient(135deg, #ffffff, #f3f3f3);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        font-family: 'Arial', sans-serif;
    }

    /* Título */
    #add-position-title {
        text-align: center;
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
    }

    /* Formulario */
    #add-position-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: bold;
        color: #555;
        margin-bottom: 5px;
    }

    .form-group input {
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 10px;
        font-size: 16px;
        transition: border-color 0.3s;
    }

    .form-group input:focus {
        border-color: #0078ff;
        outline: none;
    }

    #permissions-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
        margin-top: 10px;
    }

    .permission-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .permission-item label {
        font-size: 14px;
        color: #333;
    }

    #form-actions {
        display: flex;
        justify-content: space-between;
    }

    #submit-button,
    #reset-button {
        background: #0078ff;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s;
    }

    #reset-button {
        background: #ff4d4d;
    }

    #submit-button:hover {
        background: #005bb5;
    }

    #reset-button:hover {
        background: #cc0000;
    }
</style>