<?php
include 'conn.php';

if (isset($_GET['accion'])) {
    $accion = $_GET['accion'];

    if ($accion == 'guardarEvento') {
        guardarEvento($_POST);
    } elseif ($accion == 'eliminarEvento') {
        eliminarEvento($_GET['id']);
    } elseif ($accion == 'guardarOrganizador') {
        guardarOrganizador($_POST);
    } elseif ($accion == 'eliminarOrganizador') {
        eliminarOrganizador($_GET['id']);
    }
}

function guardarEvento($data)
{
    $conexion = conn();
    $id = $data['id'] ?? null;
    $nombre_evento = $data['nombre_evento'];
    $tipo_deporte = $data['tipo_deporte'];
    $fecha = $data['fecha'];
    $hora = $data['hora'];
    $ubicacion = $data['ubicacion'];
    $id_organizador = $data['id_organizador'];

    if ($id) { // Editar
        $query = "UPDATE eventos SET nombre_evento='$nombre_evento', tipo_deporte='$tipo_deporte', fecha='$fecha', hora='$hora', ubicacion='$ubicacion', id_organizador=$id_organizador WHERE id=$id";
    } else { // Crear
        $query = "INSERT INTO eventos (nombre_evento, tipo_deporte, fecha, hora, ubicacion, id_organizador) VALUES ('$nombre_evento', '$tipo_deporte', '$fecha', '$hora', '$ubicacion', $id_organizador)";
    }
    mysqli_query($conexion, $query);
    mysqli_close($conexion);
    echo '<script>';
    echo 'window.location.href = "listarEventos.php";';
    echo '</script>';
}

function eliminarEvento($id)
{
    $conexion = conn();
    mysqli_query($conexion, "DELETE FROM eventos WHERE id=$id");
    mysqli_close($conexion);
    echo '<script>';
    echo 'window.location.href = "listarEventos.php";';
    echo '</script>';
}

function guardarOrganizador($data)
{
    $conexion = conn();
    $id = $data['id'] ?? null;
    $nombre = $data['nombre'];
    $email = $data['email'];
    $telefono = $data['telefono'];

    if ($id) { // Editar
        $query = "UPDATE organizadores SET nombre='$nombre', email='$email', telefono='$telefono' WHERE id=$id";
    } else { // Crear
        $query = "INSERT INTO organizadores (nombre, email, telefono) VALUES ('$nombre', '$email', '$telefono')";
    }
    mysqli_query($conexion, $query);
    mysqli_close($conexion);
    echo '<script>';
    echo 'window.location.href = "listarOrganizadores.php";';
    echo '</script>';

}

function eliminarOrganizador($id)
{
    $conexion = conn();
    $result = mysqli_query($conexion, "SELECT COUNT(*) AS conteo FROM eventos WHERE id_organizador = $id");
    $organizadorAsociado = mysqli_fetch_assoc($result);
    echo '<script>';
    echo 'window.location.href = "listarOrganizadores.php";';
    echo '</script>';

    if ($organizadorAsociado['conteo'] > 0) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>';
        echo 'document.addEventListener("DOMContentLoaded", () => {';
        echo '  Swal.fire("Error", "El organizador tiene eventos asociados.", "error").then(() => {';
        echo '    window.location.href = "listarOrganizadores.php";';
        echo '  });';
        echo '});';
        echo '</script>';
        return;
    }

    mysqli_query($conexion, "DELETE FROM organizadores WHERE id=$id");
    mysqli_close($conexion);
}

// Función para obtener eventos desde la base de datos con filtros, búsqueda, paginación y ordenación
function obtenerEventos($busqueda = null, $orden = 'nombre_evento', $direccion = 'ASC', $limite = 3, $pagina = 1) {
    $conexion = conn();
    $offset = ($pagina - 1) * $limite; // Calcula el desplazamiento para la paginación.

    // Consulta base para obtener los eventos junto con el nombre del organizador
    $sql = "SELECT eventos.*, organizadores.nombre AS organizador_nombre 
            FROM eventos 
            JOIN organizadores ON eventos.id_organizador = organizadores.id";

    // Si se proporciona un término de búsqueda, se añade a la consulta un filtro con "LIKE"
    if ($busqueda) {
        $busqueda = mysqli_real_escape_string($conexion, $busqueda);
        $sql .= " WHERE eventos.nombre_evento LIKE '%$busqueda%' 
                  OR eventos.tipo_deporte LIKE '%$busqueda%' 
                  OR eventos.ubicacion LIKE '%$busqueda%' 
                  OR organizadores.nombre LIKE '%$busqueda%'"; // Busca en varias columnas
    }

  
    $orden = mysqli_real_escape_string($conexion, $orden);
    $direccion = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC'; // Solo permite "ASC" o "DESC"

    // Lista de columnas válidas para la ordenación
    $columnasValidas = ['nombre_evento', 'tipo_deporte', 'fecha', 'hora', 'ubicacion', 'organizador_nombre'];
    if (!in_array($orden, $columnasValidas)) {
        $orden = 'nombre_evento'; // Si no es válida, usa el orden por defecto
    }

    // Función auxiliar para alternar la dirección de orden
    function alternarDireccion($columnaActual, $columnaOrdenada, $direccionActual) {
        if ($columnaActual === $columnaOrdenada) {
            return $direccionActual === 'ASC' ? 'DESC' : 'ASC';
        }
        return 'ASC'; // Por defecto, empieza en ascendente
    }

    // Se completa la consulta con la cláusula ORDER BY y los límites para la paginación
    $sql .= " ORDER BY $orden $direccion LIMIT $limite OFFSET $offset";

    
    $result = mysqli_query($conexion, $sql); // Ejecuta la consulta
    $eventos = []; // Array para almacenar los eventos
    while ($fila = mysqli_fetch_assoc($result)) { // Recorre los resultados y los añade al array
        $eventos[] = $fila;
    }
    mysqli_close($conexion);
    return $eventos;
}

// Función para contar el total de eventos, incluyendo un filtro de búsqueda si se proporciona
function contarEventos($busqueda = null) {
    $conexion = conn();
    // Consulta base para contar el número de registros en la tabla de eventos
    $sql = "SELECT COUNT(*) AS total FROM eventos 
            JOIN organizadores ON eventos.id_organizador = organizadores.id";
    // Si se proporciona un término de búsqueda, se añade a la consulta un filtro con "LIKE"
    if ($busqueda) {
        $busqueda = mysqli_real_escape_string($conexion, $busqueda);
        $sql .= " WHERE eventos.nombre_evento LIKE '%$busqueda%' 
                  OR eventos.tipo_deporte LIKE '%$busqueda%' 
                  OR eventos.ubicacion LIKE '%$busqueda%' 
                  OR organizadores.nombre LIKE '%$busqueda%'";
    }

    $result = mysqli_query($conexion, $sql); // Ejecuta la consulta
    $total = mysqli_fetch_assoc($result)['total']; // Obtiene el valor del total desde el resultado
    mysqli_close($conexion);
    return $total; // Devuelve el total de registros
}


function obtenerOrganizadores($busqueda = null)
{
    $conexion = conn();
    $sql = "SELECT * FROM organizadores";

    // Agregar filtro de búsqueda si se proporciona una palabra clave
    if ($busqueda) {
        $busqueda = mysqli_real_escape_string($conexion, $busqueda);
        $sql .= " WHERE organizadores.nombre LIKE '%$busqueda%'
                  OR organizadores.email LIKE '%$busqueda%'
                  OR organizadores.telefono LIKE '%$busqueda'";
    }

    $result = mysqli_query($conexion, $sql );
    $organizadores = [];
    while ($fila = mysqli_fetch_assoc($result)) {
        $organizadores[] = $fila;
    }
    mysqli_close($conexion);
    return $organizadores;
}
// Función para obtener un evento por su ID
function obtenerEventoPorId($id)
{
    $conexion = conn();  // Usamos la función de conexión a la base de datos
    $query = "SELECT eventos.id, eventos.nombre_evento, eventos.tipo_deporte, eventos.fecha, eventos.hora, eventos.ubicacion, eventos.id_organizador, organizadores.nombre AS organizador_nombre 
              FROM eventos 
              JOIN organizadores ON eventos.id_organizador = organizadores.id
              WHERE eventos.id = $id";  // Usamos el ID directamente en la consulta

    $result = $conexion->query($query);  // Ejecutar la consulta
    $evento = $result->fetch_assoc();  // Obtener el evento como un arreglo asociativo
    $conexion->close();  // Cerrar la conexión

    return $evento;  // Devolver el evento
}
// Función para obtener un organizador por su ID
function obtenerOrganizadorPorId($id)
{
    $conexion = conn();  // Usamos la función de conexión a la base de datos
    $query = "SELECT id, nombre, email, telefono FROM organizadores WHERE id = $id";  // Usamos el ID directamente en la consulta

    $result = $conexion->query($query);  // Ejecutar la consulta
    $organizador = $result->fetch_assoc();  // Obtener el organizador como un arreglo asociativo
    $conexion->close();  // Cerrar la conexión

    return $organizador;  // Devolver el organizador
}


$ruta_csv = "C:/wamp64/www/deportes/eventos.csv";
// Verificar si el archivo existe
if (!file_exists($ruta_csv)) {
    die("El archivo no existe en la ruta especificada.");
}
$handle = fopen($ruta_csv, "r");
// Leer la primera línea (encabezados)
$encabezados = fgetcsv($handle, 1000, ",");
// Leer las filas y almacenarlas en un array
$filas = [];
while (($fila = fgetcsv($handle, 1000, ",")) !== false) {
    $filas[] = array_combine($encabezados, $fila); // Asocia los encabezados con los valores
}
fclose($handle);
// Convertir a JSON y enviarlo al navegador
echo "<script>";
echo "const datosCSV = " . json_encode($filas) . ";";
echo "console.log('Datos cargados desde el CSV:', datosCSV);";
echo "</script>";

?>