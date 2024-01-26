<?php
add_action('admin_menu', 'mensajeAR_apartado_menu');
add_action('admin_init', 'mensajeAR_settings_init');

add_action('wp_ajax_obtener_datos', 'obtener_datos');
add_action('wp_ajax_nopriv_obtener_datos', 'obtener_datos');
function mensajeAR_apartado_menu() {

    add_menu_page(
        'mensajeAR',          // Título de la página
        'mensajeAR',          // Título del menú
        'manage_options',     // Capacidad requerida para acceder
        'mensajeAR_menu',     // ID único de la página
        'mensajeAR_pagina',    // Función que renderiza la página
        'dashicons-whatsapp'
    );
}



function mensajeAR_pagina() {
    ?>
    <div class="wrap">

        <h2 class="nav-tab-wrapper">
            <a href="#" class="nav-tab" id="tab-configuracion">Configuración</a>
            <a href="#" class="nav-tab" id="tab-personalizacion">Personalización</a>
            <a href="#" class="nav-tab" id="tab-respuestas">Respuestas</a>
        </h2>

        <div id="contenido-configuracion" class="contenido-tab">
            <!-- Contenido de la pestaña de configuracion -->
            <?php mostrar_formulario_datos(); ?>
        </div>

        <div id="contenido-personalizacion" class="contenido-tab" style="display:none;">
            <!-- Contenido de la pestaña de Personalización -->
            <?php mostrar_formulario_personalizacion(); ?>
        </div>

        <div id="contenido-respuestas" class="contenido-tab" style="display:none;">
            <!-- Contenido de la pestaña de Personalización -->
            <?php mostrar_respuestas(); ?>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();

                // Ocultar todos los contenidos
                $('.contenido-tab').hide();

                // Mostrar el contenido de la pestaña correspondiente
                var targetTab = $(this).attr('id').replace('tab-', '');
                $('#contenido-' + targetTab).show();
            });
        });
    </script>
    <?php
}


function mensajeAR_enqueue_styles() {
    wp_enqueue_style('mensajeAR-styles', plugin_dir_url(__FILE__) . 'styles.css');
}

// Llama a la función en el gancho adecuado (por ejemplo, admin_enqueue_scripts)
add_action('admin_enqueue_scripts', 'mensajeAR_enqueue_styles');



// Función que registra las opciones de configuración
function mensajeAR_settings_init() {
    add_settings_section('mensajeAR_section', 'Configuración de MensajeAR', '__return_false', 'mensajeAR_settings');

    add_settings_field('nombre', 'Nombre', 'mensajeAR_setting_nombre', 'mensajeAR_settings', 'mensajeAR_section');
    add_settings_field('celular', 'Celular', 'mensajeAR_setting_celular', 'mensajeAR_settings', 'mensajeAR_section');

    add_settings_field('preguntas_respuestas', 'Preguntas y Respuestas', 'mensajeAR_setting_preguntas_respuestas', 'mensajeAR_settings', 'mensajeAR_section');

    register_setting('mensajeAR_settings', 'mensajeAR_options');
}

// Función que muestra el formulario de configuración

function mostrar_formulario_datos() {
    ?>
    <div class="wrap">
        <h1>MensajeAR Configuración</h1>
        
        <?php settings_errors(); ?>

        <form method="post" action="options.php">
            <?php
            // Agrega las opciones de configuración y las secciones al formulario
            settings_fields('mensajeAR_settings');

            do_settings_sections('mensajeAR_settings');
            
            ?>

            <?php submit_button('Guardar'); ?>
        </form>
    </div>
    <?php
}

// Función que agrega el campo "Nombre" al formulario
function mensajeAR_setting_nombre() {
    $options = get_option('mensajeAR_options');
    if (!is_array($options)) {
        $options = array();
    }
    ?>
    <input type="text" class="input-error" name="mensajeAR_options[nombre]" value="<?php echo esc_attr($options['nombre'] ?? ''); ?>" />
    <?php
}

// Función que agrega el campo "Celular" al formulario
function mensajeAR_setting_celular() {
    $options = get_option('mensajeAR_options');
    error_log(print_r($options, true));

    $options_raw = get_option('mensajeAR_options');
$options = maybe_unserialize($options_raw);

if (!empty($options)) {
    error_log(print_r($options, true));
} else {
    error_log('La opción mensajeAR_options no pudo deserializarse correctamente.');
}



    if (!is_array($options)) {
        $options = array();
    }
    $celular_value = $options['celular'] ?? '';
    if (!is_numeric(sanitize_text_field($options['celular']))){
        add_settings_error('mensajeAR_settings', 'mensajeAR_celular_error', 'El campo "Celular" debe contener solo números.', 'error');
        $options['celular'] = "invalido";   
        
    }
    ?>
    <input type="text" name="mensajeAR_options[celular]" value="<?php echo esc_attr($options['celular'] ?? ''); ?>" class="<?php echo !is_numeric($celular_value) ? 'input-error' : 'input-bien'?>"  />
    <?php
}














// Función que agrega el campo "Preguntas y Respuestas" al formulario
function mensajeAR_setting_preguntas_respuestas() {
    $options = get_option('mensajeAR_options');
    $preguntas_respuestas = $options['preguntas_respuestas'] ?? array();

    ?>
    <div id="preguntas-respuestas-container">
        <?php
        // Mostrar campos existentes
        foreach ($preguntas_respuestas as $indice => $pregunta_respuesta) {
            ?>
            <div class="pregunta-respuesta">
                <label for="pregunta_<?php echo $indice; ?>">Pregunta:</label><br>
                <input type="text" name="mensajeAR_options[preguntas_respuestas][<?php echo $indice; ?>][pregunta]" value="<?php echo esc_attr($pregunta_respuesta['pregunta'] ?? ''); ?>" />
                <br><br><label for="respuesta_<?php echo $indice; ?>">Respuesta:</label>
                <?php wp_editor(
                 $pregunta_respuesta['respuesta'] ?? '', // Contenido inicial del editor
                'respuesta_' . $indice, // ID único del editor
                array(
               'textarea_name' => 'mensajeAR_options[preguntas_respuestas][' . $indice . '][respuesta]',
                   'textarea_rows' => 7, // Ajusta este valor según sea necesario
                 )
                ); ?>
                <br><br>
                <button class="eliminar-campo">Eliminar</button>
            </div>
            <?php
        }
        ?>
    </div>

    <button id="agregar-campo">Agregar Pregunta y Respuesta</button>

    <script>
        jQuery(document).ready(function($) {
            var container = $('#preguntas-respuestas-container');
            var agregarBoton = $('#agregar-campo');

            agregarBoton.click(function(e) {
                e.preventDefault();
                var index = container.children().length;
                var nuevoCampo = `
                    <div class="pregunta-respuesta">
                        <label for="pregunta_${index}">Pregunta:</label><br>
                        <input type="text" name="mensajeAR_options[preguntas_respuestas][${index}][pregunta]" value=""/>
                        <br><label for="respuesta_${index}">Respuesta:</label>
                        <br><textarea name="mensajeAR_options[preguntas_respuestas][${index}][respuesta]" rows="5"></textarea>
                        <br><button class="eliminar-campo">Eliminar</button>
                    </div>
                `;
                container.append(nuevoCampo);
            });

            container.on('click', '.eliminar-campo', function(e) {
                e.preventDefault();
                $(this).parent().remove();
            });
        });
    </script>
    <?php
}












/// FIN DATOSSSSSSSSSSSSSSS

function mostrar_formulario_personalizacion() {
    // Contenido del formulario de Personalización
}










function mostrar_respuestas() {

    ?>
    
    <h1>titulo</h1>
    <div id="tablaRespuestas"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            obtenerDatosYMostrarTabla();
        });

        function obtenerDatosYMostrarTabla() {
    // Realiza una solicitud AJAX al servidor
    fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=obtener_datos')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la solicitud AJAX: ' + response.statusText);
            }
            return response.json(); // Asumiendo que esperas una respuesta JSON
        })
        .then(data => mostrarTabla(data))
        .catch(error => {
            console.error('Error al obtener datos:', error);
            mostrarErrorEnTabla();
        });
    }

function mostrarErrorEnTabla() {
    var tablaRespuestas = document.getElementById('tablaRespuestas');
    tablaRespuestas.innerHTML = 'Error al obtener datos. Por favor, inténtalo de nuevo.';
}

        function mostrarTabla(datos) {
            
            // Crear la tabla HTML
            var tablaHTML = '<table class="wp-list-table widefat fixed striped">';
            tablaHTML += '<thead><tr><th style="width: 50px;">ID</th><th style="width: 150px;">nombre</th><th style="width: 100px;">numero</th><th style="width: 500px;">consulta</th><th style="width: 80px;">contactar</th><th style="width: 60px;">eliminar</th></tr></thead>';
            tablaHTML += '<tbody>';

            // Itera sobre los datos y agrega filas a la tabla
            datos.forEach(function (fila) {
                tablaHTML += '<tr>';
                tablaHTML += '<td>' + fila['id'] + '</td>';
                tablaHTML += '<td>' + fila['nombre'] + '</td>';
                tablaHTML += '<td>' + fila['numero'] + '</td>';
                tablaHTML += '<td>' + fila['consulta'] + '</td>';
                tablaHTML += '<td><a href="https://wa.me/549' + fila['numero'] + '">Contactar</a></td>';
                tablaHTML += '<td><button onclick="eliminarRegistro(' + fila['id'] + ')">Eliminar</button></td>';


                // Agrega más columnas según tu estructura de base de datos
                tablaHTML += '</tr>';
            });

            tablaHTML += '</tbody>';
            tablaHTML += '</table>';

            // Muestra la tabla en el elemento con el id 'tablaRespuestas'
            document.getElementById('tablaRespuestas').innerHTML = tablaHTML;
        }




        function eliminarRegistro(id) {
    // Realiza una solicitud AJAX al servidor para eliminar un registro
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: new URLSearchParams({
            'action': 'eliminar_registro',
            'id': id
        }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar la tabla después de eliminar el registro
                obtenerDatosYMostrarTabla();
            } else {
                console.error('Error al eliminar registro:', data.error);
            }
        })
        .catch(error => console.error('Error al eliminar registro:', error));
}


    </script>



    <?php
}


