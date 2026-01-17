<style>
    /* General styling for the modal */
    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.6);
        /* Background overlay */
        animation: fadeIn 0.3s ease-in-out;
    }

    /* Modal dialog styling */
    .modal-dialog {
        max-width: 500px;
        width: 90%;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.4s ease-out;
    }

    /* Modal content */
    .modal-content {
        background: #ffffff;
        border: none;
        padding: 1.5rem;
    }

    /* Header */
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eaeaea;
    }

    .modal-title {
        font-size: 1.5rem;
        color: #0d6efd;
        font-weight: bold;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        color: #6c757d;
        transition: color 0.3s ease;
    }

    .btn-close:hover {
        color: #dc3545;
    }

    /* Body */
    .modal-body {
        padding: 1rem 0;
    }

    .form-check-label {
        font-size: 1rem;
        color: #495057;
    }

    .form-check-input {
        width: 2rem;
        height: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }

    /* Footer */
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #eaeaea;
    }

    .modal .btn {
        padding: 0.5rem 1.5rem;
        font-size: 1rem;
        border-radius: 5px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-success {
        background-color: #198754;
        color: #ffffff;
    }

    .btn-success:hover {
        background-color: #157347;
    }

    .btn-danger {
        background-color: #dc3545;
        color: #ffffff;
    }

    .btn-danger:hover {
        background-color: #bb2d3b;
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>


<div class="modal fade" id="editarPermisos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 text-primary" id="exampleModalLabel">Editar Permisos</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <form action="app/controller/usuarios/c_editarPermisos.php" method="POST" id="formPermisos">



            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Actualizar Permisos</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>