<?php
class User
{

    private $permisos;
    private $nombreCompleto;
    private $apellidoCompleto;
    private $rol;
    private $fotoPerfil;

    public function __construct() {
        $this->permisos = $_SESSION['permisos'] ?? [];
        $this->nombreCompleto = $_SESSION['nombre'] ?? '';
        $this->apellidoCompleto = $_SESSION['apellido'] ?? '';
        $this->rol = $_SESSION['rol'] ?? '';
        $this->fotoPerfil = $_SESSION['fotoPerfil'] ?? null;
    }

    // Métodos para nombres
    private function obtenerPrimerNombre($nombre) {
        $partesNombre = explode(" ", trim($nombre));
        return $partesNombre[0];
    }

    private function obtenerPrimerApellido($apellido) {
        $partesApellido = explode(" ", trim($apellido));
        $conectores = ['de', 'la', 'del', 'las', 'los', 'el', 'san', 'santa', "santisima"];
        $primerApellido = [];
        $encontradoNoConector = false;

        foreach ($partesApellido as $parte) {
            if (!$encontradoNoConector && (in_array(strtolower($parte), $conectores) || empty($primerApellido))) {
                $primerApellido[] = $parte;
            } else {
                $primerApellido[] = $parte;
                $encontradoNoConector = true;
                break;
            }
        }

        return implode(" ", $primerApellido);
    }

    // Getters
    public function getPrimerNombre() {
        return $this->obtenerPrimerNombre($this->nombreCompleto);
    }

    public function getPrimerApellido() {
        return $this->obtenerPrimerApellido($this->apellidoCompleto);
    }

    public function getNombreCompletoFormateado() {
        return $this->getPrimerNombre() . " " . $this->getPrimerApellido();
    }

    public function getRol() {
        return $this->rol;
    }

    public function getFotoPerfil() {
        return $this->fotoPerfil ? base64_encode($this->fotoPerfil) : null;
    }

    // Métodos de permisos
    public function tienePermiso($permiso) {
        return in_array($permiso, $this->permisos);
    }

    // Método para generar el menú
    public function generarMenu() {
        $menuItems = [
            'Inicio' => ['icon' => 'bi bi-grid-fill', 'link' => 'index.php?vista=home'],
            'Catalogación' => [
                'icon' => 'bi bi-collection',
                'submenus' => [
                    ['permiso' => 'libros', 'label' => 'Libros', 'icon' => 'bi bi-book-half', 'link' => 'index.php?vista=libros&pagina=1'],
                    ['permiso' => 'autores', 'label' => 'Autores', 'icon' => 'bi bi-pen', 'link' => 'index.php?vista=autores&pagina=1'],
                    ['permiso' => 'estanterias', 'label' => 'Estanterias', 'icon' => 'bi bi-bookshelf', 'link' => 'index.php?vista=estanterias'],
                    ['permiso' => 'editoriales', 'label' => 'Editoriales', 'icon' => 'bi bi-building', 'link' => 'index.php?vista=editoriales&pagina=1'],
                    ['permiso' => 'categorias', 'label' => 'Categorías', 'icon' => 'bi bi-list', 'link' => 'index.php?vista=categorias&pagina=1'],
                ],
            ],
            'Prestamos' => ['permiso' => 'prestamos', 'icon' => 'bi bi-hourglass-split', 'link' => 'index.php?vista=prestamos'],
            'Administración' => [
                'icon' => 'bi bi-clipboard-data',
                'submenus' => [
                    ['permiso' => 'visitantes', 'label' => 'Visitantes', 'icon' => 'bi bi-person-walking', 'link' => 'index.php?vista=visitantes&pagina=1'],
                    ['permiso' => 'actividades', 'label' => 'Actividades', 'icon' => 'bi bi-calendar-event', 'link' => 'index.php?vista=actividades&pagina=1'],
                    ['permiso' => 'reportes', 'label' => 'Reportes', 'icon' => 'bi bi-file-earmark-text"', 'link' => 'index.php?vista=reportes'],
                    ['permiso' => 'multas', 'label' => 'Multas', 'icon' => 'bi bi-receipt"', 'link' => 'index.php?vista=multas'],
                ],
            ],
            'Usuarios' => ['permiso' => 'usuarios', 'icon' => 'bi bi-person-circle', 'link' => 'index.php?vista=usuarios&pagina=1'],
            'Historial' => ['permiso' => 'historial', 'icon' => 'bi bi-stopwatch', 'link' => 'index.php?vista=historial&pagina=1'],
        ];

        $menuFiltrado = [];

        foreach ($menuItems as $key => $item) {
            if (isset($item['permiso'])) {
                if ($this->tienePermiso($item['permiso'])) {
                    $menuFiltrado[$key] = $item;
                }
            } elseif (isset($item['submenus'])) {
                $submenusFiltrados = [];
                foreach ($item['submenus'] as $submenu) {
                    if ($this->tienePermiso($submenu['permiso'])) {
                        $submenusFiltrados[] = $submenu;
                    }
                }
                if (!empty($submenusFiltrados)) {
                    $item['submenus'] = $submenusFiltrados;
                    $menuFiltrado[$key] = $item;
                }
            } else {
                // Ítems sin requisito de permiso (como Inicio)
                $menuFiltrado[$key] = $item;
            }
        }

        return $menuFiltrado;
    }

    public function userExists($cedula, $pass, $conn)
    {
        try {
            // Preparar la consulta SQL para obtener la contraseña hasheada
            $stmt = $conn->prepare("SELECT clave FROM usuarios WHERE cedula = ?");
            $stmt->bind_param("s", $cedula);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            // Verificar si se encontró un usuario
            if ($result->num_rows > 0) {
                $hashedPassword = $row['clave'];

                // Verificar la contraseña ingresada con la hasheada
                if (password_verify($pass, $hashedPassword)) {
                    $_SESSION["contrasena"] = $pass;
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            // Manejar errores
            echo "Error al verificar usuario: " . $e->getMessage();
            return false;
        }
    }


    public function addUser($cedula, $nombre, $apellido, $fecha_nac, $direccion, $telefono, $correo, $sexo, $rol, $clave, $permisos, $conn)
    {
        try {
            // Hashear la contraseña usando password_hash
            $hashedPassword = password_hash($clave, PASSWORD_BCRYPT);

            // Preparando la sentencia SQL para insertar el usuario
            $stmt = $conn->prepare("INSERT INTO `usuarios`(`cedula`, `nombre`, `apellido`, `fecha_nacimiento`, `direccion`, `telefono`, `correo`, `sexo`, `rol`, `clave`, `estado`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("sssssssiis", $cedula, $nombre, $apellido, $fecha_nac, $direccion, $telefono, $correo, $sexo, $rol, $hashedPassword);
            $stmt->execute();

            // Obtener el ID del usuario recién insertado
            $userId = $cedula;

            // Preparando la sentencia SQL para insertar los permisos
            $stmt2 = $conn->prepare("INSERT INTO `usuario_permisos`(`id_usuario`, `id_permiso`) VALUES (?, ?)");

            // Iterar sobre los permisos y ejecutar la sentencia
            foreach ($permisos as $permisoId) {
                $stmt2->bind_param("ii", $userId, $permisoId);
                $stmt2->execute();
            }

            // Cerrar las sentencias
            $stmt->close();
            $stmt2->close();

            return true;
        } catch (Exception $e) {
            // Manejar la excepción (loggear, enviar notificación, etc.)
            echo "Error al agregar usuario: " . $e->getMessage();
            return false;
        }
    }


    public function registrar_accion($usuario_responsable, $detalles, $fecha, $conn, $accion)
    {

        // Preparando la sentencia SQL con marcadores de posición
        $stmt = $conn->prepare("INSERT INTO `historial`(`cedula_responsable`, `accion_id`, `fecha`, `detalles`) VALUES(?,?,?,?)");

        // Enlazando los parámetros con los marcadores de posición
        $stmt->bind_param("siss", $usuario_responsable, $accion, $fecha,  $detalles,);

        // Ejecutando la sentencia
        $stmt->execute();

        // Verificando si la inserción fue exitosa
        if ($stmt->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }



    public function setUser($cedula, $conn)
    {
        $stmt = $conn->prepare("SELECT u.cedula, u.foto_perfil, u.nombre, u.apellido, u.fecha_nacimiento, u.direccion, u.telefono, u.correo, CASE WHEN u.sexo = 1 THEN 'Masculino' WHEN u.sexo = 2 THEN 'Femenino' ELSE 'Otro' END AS sexo_descripcion, r.nombre AS rol_nombre, YEAR(CURRENT_DATE) - YEAR(u.fecha_nacimiento) - (RIGHT(CURRENT_DATE,5) < RIGHT(u.fecha_nacimiento,5)) AS edad FROM usuarios AS u JOIN rol AS r ON u.rol = r.id_rol WHERE u.cedula = ? AND u.estado = 1");
        $stmt->bind_param("s", $cedula);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['apellido'] = $row['apellido'];
            $_SESSION['cedula'] = $row['cedula'];
            $_SESSION['rol'] = $row['rol_nombre'];
            $_SESSION["fotoPerfil"] = "data:image/jpeg;base64," . base64_encode($row['foto_perfil']);
        }

        // Obtener los permisos del usuario
        $stmt_permisos = $conn->prepare("SELECT p.permiso FROM usuario_permisos up JOIN permisos p ON up.id_permiso = p.id WHERE up.id_usuario = ?");
        $stmt_permisos->bind_param("s", $cedula); // Suponiendo que id_usuario es un entero
        $stmt_permisos->execute();

        $result_permisos = $stmt_permisos->get_result();
        $permisos = [];
        while ($row_permiso = $result_permisos->fetch_assoc()) {
            $permisos[] = $row_permiso['permiso'];
        }

        // Almacenar los permisos en la sesión
        $_SESSION['permisos'] = $permisos;
    }

    public function getNombre()
    {
        return $_SESSION['nombre'];
    }
}
