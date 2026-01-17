<?php 
$registrosVariable = 10;
$consultaCount = "SELECT COUNT(*) AS total FROM editorial;";
$consultaPaginacion = "SELECT * FROM editorial LIMIT ?, ?";

include "app/controller/c_paginacion.php";
include "modal/agregarEditorial.php";
?>
<br><br>
<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0"><i class="bi bi-building me-2"></i>Gestión de Editoriales</h3>
                    <p class="text-muted mb-0">Total de registros: <?= $total_registros ?></p>
                </div>
                
                <div class="d-flex">
                    <div class="input-group me-3" style="width: 300px;">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="inputTermino" class="form-control" placeholder="Buscar editorial...">
                    </div>
                    <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#agregarEditorial">
                        <i class="bi bi-plus-circle me-1"></i> Nueva Editorial
                    </button>
                    <!--button type="button" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Exportar a PDF">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </button-->
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%; display: none;">ID</th>
                            <th style="width: 45%">Nombre</th>
                            <th style="width: 30%">Origen</th>
                            <th style="width: 20%; text-align: center;">Libros</th>
                        </tr>
                    </thead>
                    <tbody id="tablaEditoriales">
                        <?php if (isset($resultado)): ?>
                            <?php while ($row = mysqli_fetch_assoc($resultado)): 
                               
                            ?>
                                <tr>
                                    <td style="display: none;" class="notEditable"><?= $row['id'] ?></td>
                                    
                                    <td class="editable">
                                        <div class="fw-semibold"><?= $row['nombre'] ?></div>
                                        <small class="text-muted">ID: <?= $row['id'] ?></small>
                                    </td>
                                    
                                    <td class="editable">
                                        <?= $row['origen'] ?>
                                    </td>
                                    
                                    <td class="text-center">
                                        <button onclick="window.location.href='index.php?vista=librosEditorial&id=<?= $row['id'] ?>'"
                                                class="btn btn-sm btn-outline-success"
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top"
                                                title="Ver libros">
                                            <i class="bi bi-book me-1"></i> Libros
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-exclamation-circle text-muted" style="font-size: 2rem;"></i>
                                        <p class="text-muted mt-2 mb-0">No se encontraron editoriales registradas</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando <?= min($registros_por_pagina, $total_registros) ?> de <?= $total_registros ?> registros
                </div>
                <nav>
                    <?= $paginacion->generarPaginacion($pagina_actual, $total_paginas) ?>
                </nav>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(33, 40, 50, 0.125);
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .editable {
        vertical-align: middle;
    }
</style>