<?php
function crear_tabla() {
    global $wpdb;
    $tabla_nombre = $wpdb->prefix . 'mensajeAR_tabla';

    // Verificar si la tabla ya existe
    if ($wpdb->get_var("SHOW TABLES LIKE '$tabla_nombre'") != $tabla_nombre) {
        // Crear la tabla
        $sql = "CREATE TABLE $tabla_nombre (
            id INT NOT NULL AUTO_INCREMENT,
            nombre VARCHAR(100) NOT NULL,
            numero VARCHAR(20) NOT NULL, 
            consulta TEXT,
            PRIMARY KEY (id)
        )";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

function borrar_tabla(){
    global $wpdb;
    $tabla_nombre = $wpdb->prefix . 'mensajeAR_tabla';

    // Verifica si la tabla existe antes de intentar borrarla
    if ($wpdb->get_var("SHOW TABLES LIKE '$tabla_nombre'") == $tabla_nombre) {
        // Borra la tabla
        $wpdb->query("DROP TABLE IF EXISTS $tabla_nombre");
    }

}



function obtener_datos() {
    global $wpdb;
    
    $tabla_nombre = $wpdb->prefix . 'mensajeAR_tabla';

    try {
        // Realiza una consulta para obtener los datos
        $query = "SELECT * FROM $tabla_nombre";
        $resultado = $wpdb->get_results($query, ARRAY_A);

        if ($resultado) {
            wp_send_json_success($resultado);
        } else {
            wp_send_json_error(array('message' => 'No se encontraron datos.'));
        }
    } catch (Exception $e) {
        wp_send_json_error(array('message' => 'Error interno en el servidor.'));
    }

wp_die();
}





function eliminar_registro() {
    if (isset($_POST['action']) && $_POST['action'] === 'eliminar_registro') {
        // Conexión a la base de datos global de WordPress
        global $wpdb;

        // Obtener el ID del registro a eliminar
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id > 0) {
            // Eliminar el registro de la base de datos
            $tabla_nombre = $wpdb->prefix . 'mensajeAR_tabla';
            $wpdb->delete($tabla_nombre, array('id' => $id), array('%d'));

            // Responder con JSON
            header('Content-Type: application/json');
            echo json_encode(array('success' => true));
            exit;
        }
    }

    // Si la acción no es válida o falta el ID, responder con error
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'Invalid request'));
    exit;
}


function agregar_registro() {
    error_log(isset($_POST['nombre']));
    error_log(isset($_POST['celular']));

    if (isset($_POST['action']) && $_POST['action'] === 'agregar_registro') {
        // Obtener datos de la solicitud POST
        $nombre = isset($_POST['nombre']) ? sanitize_text_field($_POST['nombre']) : '';
        $celular = isset($_POST['celular']) ? sanitize_text_field($_POST['celular']) : '';
        $consulta = isset($_POST['consulta']) ? sanitize_text_field($_POST['consulta']) : '';

        // Validar y agregar los datos a la base de datos
        if (!empty($nombre) && !empty($celular) && !empty($consulta)) {
            global $wpdb;

            $tabla_nombre = $wpdb->prefix . 'mensajeAR_tabla';
            $wpdb->insert($tabla_nombre, array('nombre' => $nombre, 'numero' => $celular, 'consulta' => $consulta), array('%s', '%s', '%s'));

            // Responder con JSON
            header('Content-Type: application/json');
            echo json_encode(array('success' => true));
            exit;
        } else {
            // Responder con JSON si faltan datos
            header('Content-Type: application/json');
            echo json_encode(array('error' => 'Missing data'));
            exit;
        }
    }

    // Si la acción no es válida, responder con error
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'Invalid request'));
    exit;
}



// Llama a las funciones según la acción proporcionada
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'eliminar_registro':
            eliminar_registro();
            break;
        case 'agregar_registro':
             agregar_registro();
            break; 
    }
}