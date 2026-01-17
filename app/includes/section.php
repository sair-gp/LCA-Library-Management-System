<?php

if (session_status() == PHP_SESSION_NONE) {
  // La sesión no está activa, podemos iniciarla
  session_start();
  //echo "Sesión iniciada correctamente.";
} else if (session_status() == PHP_SESSION_ACTIVE) {
  // La sesión ya está activa, no es necesario iniciarla
 // echo "La sesión ya está activa.";
} else if (session_status() == PHP_SESSION_DISABLED){
  //echo "Las sesiones estan deshabilitadas";
}

$rol = $_SESSION["rol"];
$displayNone = $rol !== 'Admin' ? 'style="display: none"' : "";

?>

<section class="fixed-navigation">
  <nav class="navbar">
    <!-- Dropdown User Options -->
    <div class="dropdown user-options">
      <button class="btn user-options-btn" type="button" id="userOptionsToggle" aria-expanded="false">
        <i class="bi bi-gear-wide-connected icon-white"></i>
      </button>
      <ul class="dropdown-menu user-options-menu" aria-labelledby="userOptionsToggle">
        <li class="user-option-item">
          <a href="#" class="dropdown-item user-details-btn" data-bs-toggle="modal" data-bs-target="#userConfigModal">
            <i class="bi bi-person-bounding-box"></i> Detalles de usuario
          </a>
        </li>
        <!--li class="user-option-item" <?= $displayNone ?>>
          <a href="#" class="dropdown-item user-details-btn" onclick="window.location.href='index.php?vista=reportes'">
          <i class="bi bi-file-earmark-bar-graph-fill"></i> Reportes
          </a>
        </li-->
        <?php if ($_SESSION["rol"] == "Admin"): ?>
        <li class="user-option-item" <?= $displayNone ?>>
          <a href="#" class="dropdown-item user-details-btn" onclick="window.location.href='index.php?vista=configuracion'">
            <i class="bi bi-gear"></i> Configuración
          </a>
        </li>
        <?php endif; ?>
      </ul>
    </div>

    <!-- Notification Bell -->
    <div class="notification-bell">
      <img
        src="public/img/campana.png"
        alt="Campana" />
      <span class="notification-badge" id="notification-count">0</span>
    </div>

    <!-- Notification Popup -->
    <div class="popup" id="popup">
      <h3>Notificaciones Recientes</h3>
      <ul id="notification-list">
        <!-- Notificaciones dinámicas -->
      </ul>
    </div>
  </nav>
</section>



<style>
  /* Campana de notificaciones */
  .notification-bell {
    position: relative;
    cursor: pointer;
    margin-left: 20px;
  }

  .notification-bell img {
    width: 30px;
    height: 30px;
  }

  .notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: #ff5733;
    color: #fff;
    border-radius: 50%;
    padding: 3px 6px;
    font-size: 10px;
    line-height: 1;
    font-weight: bold;
  }

  /* Popup de notificaciones */
  .popup {
    display: none;
    position: absolute;
    top: 40px;
    right: 0;
    width: 350px;
    max-height: 480px;
    /* Limitar la altura del popup */
    overflow-y: auto;
    /* Habilitar scroll vertical */
    background-color: #222;
    border: 1px solid #444;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    padding: 10px;
    border-radius: 5px;
  }

  .popup h3 {
    margin: 0 0 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #444;
    color: #fff;
    font-size: 1rem;
  }

  .popup ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
  }

  .popup ul li {
    padding: 10px;
    margin-bottom: 5px;
    background: #333;
    border-radius: 4px;
    color: #ccc;
    font-size: 0.9rem;
    transition: background-color 0.3s, color 0.3s;
  }

  .popup ul li:hover {
    background-color: #444;
    color: #fff;
    cursor: pointer;
  }

  /* Scroll elegante */
  .popup::-webkit-scrollbar {
    width: 8px;
  }

  .popup::-webkit-scrollbar-thumb {
    background: #444;
    border-radius: 4px;
  }

  .popup::-webkit-scrollbar-thumb:hover {
    background: #555;
  }

  .popup::-webkit-scrollbar-track {
    background: #222;
  }

  #notification-list a {
    color: #fff;
    text-decoration: none;
    margin: 0 15px;
    font-size: 1.1em;
  }

  #notification-list a:hover {
    color: #00f260;
  }


</style>