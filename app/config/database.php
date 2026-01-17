<?php

class Database
{
    private $conn;
    private static $instancia = null;

    // Constructor privado para evitar la creación de nuevas instancias desde fuera
    public function __construct()
{
    try {
        // Intenta establecer la conexión
        $this->conn = @mysqli_connect("localhost", "root", "", "db_caceresluisa2");
        
        // Verificar si la conexión falló
        if (!$this->conn || mysqli_connect_errno()) {
            throw new Exception("Error de conexión a la base de datos");
        }
        
    } catch (Exception $e) {
        // Limpia cualquier salida previa
        if (ob_get_length()) ob_clean();
        
        // Redirige al login con mensaje de error
        header("Location: ../../index.php?vista=login&alerta=!conexion");
        exit(); // Termina la ejecución
    }
}

    // Método para obtener la instancia de la base de datos (Singleton)
    public static function obtener()
{
    if (self::$instancia === null) {
        try {
            self::$instancia = new Database();
        } catch (Exception $e) {
            // Si falla la creación de la instancia, redirige al login
            if (ob_get_length()) ob_clean();
            header("Location: ../../index.php?vista=login&alerta=!conexion");
            exit();
        }
    }
    return self::$instancia->conn;
}

    // Método para preparar una consulta
    public function prepare($query)
    {
        return $this->conn->prepare($query);
    }

    // Método para cerrar la conexión
    public function close()
    {
        if ($this->conn) {
            mysqli_close($this->conn);
            $this->conn = null; // Asegurarse de que la conexión esté cerrada
        }
    }


    // Método para realizar un respaldo de la base de datos y ofrecerlo como descarga en un ZIP
    public function respaldo()
{
    // Configuración de la base de datos
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = ''; // Si no hay contraseña, déjalo vacío
    $db_name = 'db_caceresluisa2';

    // Nombre del archivo de respaldo SQL
    $backup_file = 'backup_' . $db_name . '_' . date("Y-m-d_H-i-s") . '.sql';


    // Ruta completa a mysqldump (Windows con XAMPP)
    $mysqldump_path = 'E:\programas\xampp\mysql\bin\mysqldump.exe';



    // Comando para hacer el respaldo
    $command = "$mysqldump_path --host=$db_host --user=$db_user --password=$db_pass $db_name > $backup_file 2>&1";

    // Ejecutar el comando y capturar la salida
    exec($command, $output, $return_var);

    // Verificar si el archivo de respaldo se creó correctamente
    if (!file_exists($backup_file)) {
        die("Error al crear el respaldo. Salida del comando: " . implode("\n", $output));
    }

    // Verificar si el archivo SQL está vacío
    if (filesize($backup_file) === 0) {
        unlink($backup_file); // Eliminar el archivo vacío
        die("El archivo SQL está vacío. Verifica que la base de datos tenga datos.");
    }

    // Crear un archivo ZIP usando PharData
    $zip_file = 'backup_' . $db_name . '_' . date("Y-m-d_H-i-s") . '.zip';
    $phar = new PharData($zip_file);

    // Agregar el archivo SQL al ZIP
    $phar->addFile($backup_file, $backup_file);

    // Cerrar el archivo ZIP
    unset($phar);

    // Eliminar el archivo SQL temporal
    unlink($backup_file);

    // Configurar las cabeceras para forzar la descarga
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zip_file) . '"');
    header('Content-Length: ' . filesize($zip_file));

    // Enviar el archivo ZIP al navegador
    readfile($zip_file);

    // Eliminar el archivo ZIP después de la descarga
    unlink($zip_file);

    exit; // Terminar la ejecución del script
}

    public function respaldoAutomatico($backupDir = 'backups')
{
    // Crear el directorio de respaldos si no existe
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }

    // Configuración de la base de datos
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = ''; // Si no hay contraseña, déjalo vacío
    $db_name = 'db_caceresluisa2';

    // Nombre del archivo de respaldo SQL
    $backup_file = $backupDir . '/backup_' . $db_name . '_' . date("Y-m-d_H-i-s") . '.sql';

    // Ruta completa a mysqldump (Windows con XAMPP)
    $mysqldump_path = 'E:\programas\xampp\mysql\bin\mysqldump.exe';

    // Comando para hacer el respaldo
    $command = "$mysqldump_path --host=$db_host --user=$db_user --password=$db_pass $db_name > $backup_file 2>&1";

    // Ejecutar el comando y capturar la salida
    exec($command, $output, $return_var);

    // Verificar si el archivo de respaldo se creó correctamente
    if (!file_exists($backup_file)) {
        die("Error al crear el respaldo. Salida del comando: " . implode("\n", $output));
    }

    // Verificar si el archivo SQL está vacío
    if (filesize($backup_file) === 0) {
        unlink($backup_file); // Eliminar el archivo vacío
        die("El archivo SQL está vacío. Verifica que la base de datos tenga datos.");
    }

    // Crear un archivo ZIP usando PharData
    $zip_file = $backupDir . '/backup_' . $db_name . '_' . date("Y-m-d_H-i-s") . '.zip';
    $phar = new PharData($zip_file);

    // Agregar el archivo SQL al ZIP
    $phar->addFile($backup_file, basename($backup_file));

    // Cerrar el archivo ZIP
    unset($phar);

    // Eliminar el archivo SQL temporal
    unlink($backup_file);

    echo "Respaldo generado correctamente: " . basename($zip_file);
}


    public function eliminarRespaldoAntiguos($backupDir, $dias = 7)
{
    $archivos = glob($backupDir . '/*.zip'); // Obtener todos los archivos ZIP

    foreach ($archivos as $archivo) {
        if (filemtime($archivo) < time() - ($dias * 86400)) { // 86400 segundos = 1 día
            unlink($archivo); // Eliminar el archivo
        }
    }
}


public function necesitaRespaldoDias($intervaloHoras =  24)
{
    // Obtener la fecha del último respaldo
    $query = "SELECT fecha_respaldo FROM respaldos ORDER BY fecha_respaldo DESC LIMIT 1";
    $resultado = $this->conn->query($query);

    if ($resultado && $resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $ultimoRespaldo = strtotime($fila['fecha_respaldo']);
        $ahora = time();

        // Verificar si ha pasado el intervalo de tiempo especificado
        return ($ahora - $ultimoRespaldo) >= ($intervaloHoras * 3600);
    }

    // Si no hay respaldos registrados, devolver true
    return true;
}

public function necesitaRespaldoSemanal()
{
    // Obtener la fecha del último respaldo
    $query = "SELECT fecha_respaldo FROM respaldos ORDER BY fecha_respaldo DESC LIMIT 1";
    $resultado = $this->conn->query($query);

    if ($resultado && $resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $ultimoRespaldo = strtotime($fila['fecha_respaldo']);
        $ahora = time();

        // Verificar si han pasado 7 días (604800 segundos = 7 días)
        return ($ahora - $ultimoRespaldo) >= 604800;
    }

    // Si no hay respaldos registrados, devolver true
    return true;
}

public function registrarRespaldo()
{
    // Registrar la fecha y hora del respaldo en la base de datos
    $query = "INSERT INTO respaldos (fecha_respaldo) VALUES (NOW())";
    $this->conn->query($query);
}


}
// Función para obtener la conexión (usando el patrón Singleton)
if (!function_exists('conexion')) { // Evitar redeclaración de la función
    function conexion()
    {
        return Database::obtener();
    }

}


/*

Metodos construct y obtener viejos

 public function __construct()
    {
        $this->conn = mysqli_connect("localhost", "root", "", "db_caceresluisa2");

        // Verificar si la conexión fue exitosa
        if (mysqli_connect_errno()) {
            die("Error al conectar a la base de datos: " . mysqli_connect_error());
        }
    }

    // Método para obtener la instancia de la base de datos (Singleton)
    
    public static function obtener()
    {
        if (self::$instancia === null) {
            self::$instancia = new Database();
        }
        return self::$instancia->conn;
    }

    */