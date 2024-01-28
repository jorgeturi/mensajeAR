<?php
// agrega scripts y estilos
function mensajeAR_enqueue_scripts() {
    wp_enqueue_style('mensajeAR-styles', plugin_dir_url(__FILE__) . 'styles.css');
    wp_enqueue_script('mensajeAR-scripts', plugin_dir_url(__FILE__) . 'scripts.js', array('jquery'), null, true);
}

// Llama a la función en el gancho adecuado
add_action('wp_enqueue_scripts', 'mensajeAR_enqueue_scripts');


// Llama a la función para mostrar el HTML
add_action('wp_footer', 'mensajeAR_output_html_front');


// Función que muestra el chat
function mensajeAR_output_html_front() {
    ?>
    <!-- Icono flotante -->
    <div class="floating-icon" id="floatingIcon">💬</div>

    <!-- Pantalla emergente -->
    <div class="modal" id="myModal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            
            <div id="chat-container">
            <button id="btn-flotante">Dejar una consulta</button>
            <button id="btn-flotante2">Ver opciones</button>

            </div>
            <div class = "inferior" style="display: flex; align-items: center;">
            <input type="text" id="user-input" placeholder="Escribe un mensaje..." />
            <button id="send-button" onclick="enviarMensaje()">Enviar</button>
            <div>

        </div>
    </div>

    <script>
        var estadoConversacion = 0;

        document.addEventListener('DOMContentLoaded', function() {

            var floatingIcon = document.getElementById('floatingIcon');
            var modal = document.getElementById('myModal');
            var closeModal = document.getElementById('closeModal');

            // Mostrar chat
            floatingIcon.addEventListener('click', function() {
                modal.style.display = 'block';
            });

            // Cerrar chat
            closeModal.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            // Cerrar chat haciendo click afuera
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });


    


    <?php
    $options = get_option('mensajeAR_options');
    $preguntas_respuestas = $options['preguntas_respuestas'] ?? array();
    $preguntas_respuestas = array_values($preguntas_respuestas); // Asegurar que sea un array numérico

    $preguntas_respuestas = array_map(function ($pregRespuesta) {
        $pregRespuesta['respuesta'] = nl2br($pregRespuesta['respuesta']);
        return $pregRespuesta;
        
    }, $preguntas_respuestas);
    ?>
    
    var opcionesMensajeAR = <?php echo json_encode($preguntas_respuestas); ?>;
    










    function agregarRegistro() {
    // solicitud AJAX al servidor para agregar un registro
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: new URLSearchParams({
            'action': 'agregar_registro',
            'nombre': nombreUsuario,
            'celular':telefonoUsuario,
            'consulta':consultaUsuario
        }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                agregarMensaje("Bot","Tus datos fueron guardados, te contactará una persona luego. Gracias!")
            } else {
                agregarMensaje("Bot", "Hubo un error al guardar tus datos, reintenta o contactanos");
                console.error('Error al agregar registro:', data.error);
            }
        })
        .catch(error => console.error('Error al agregar registro:', error));
    }

    </script>
    <?php
}