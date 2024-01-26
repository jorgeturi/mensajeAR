<?php
// Función que agrega scripts y estilos
function mensajeAR_enqueue_scripts() {
    wp_enqueue_style('mensajeAR-styles', plugin_dir_url(__FILE__) . 'styles.css');
    wp_enqueue_script('mensajeAR-scripts', plugin_dir_url(__FILE__) . 'scripts.js', array('jquery'), null, true);
}

// Llama a la función en el gancho adecuado
add_action('wp_enqueue_scripts', 'mensajeAR_enqueue_scripts');

// Función que muestra el icono flotante y la pantalla emergente
function mensajeAR_output_html() {
    ?>
    <!-- Icono flotante -->
    <div class="floating-icon" id="floatingIcon">&#9432;</div>

    <!-- Pantalla emergente -->
    <div class="modal" id="myModal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Nombre de la Persona</h2>
            <p>Preguntas Frecuentes:</p>
            <ul>
                <li>Pregunta 1</li>
                <li>Pregunta 2</li>
                <!-- Agrega más preguntas según sea necesario -->
            </ul>
        </div>
    </div>
    <?php
}

// Llama a la función para mostrar el HTML
add_action('wp_footer', 'mensajeAR_output_html_front');



// Función que muestra el icono flotante y la pantalla emergente
function mensajeAR_output_html_front() {
    ?>
    <!-- Icono flotante -->
    <div class="floating-icon" id="floatingIcon">💬</div>

    <!-- Pantalla emergente -->
    <div class="modal" id="myModal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <?php
            /*$options = get_option('mensajeAR_options');
            ?>
            <h2>Nombre: <?php echo esc_html($options['nombre'] ?? ''); ?></h2>
            <p>Preguntas Frecuentes:</p>
            <ul>
                <?php
                $preguntas_respuestas = $options['preguntas_respuestas'] ?? array();

                foreach ($preguntas_respuestas as $indice => $pregunta_respuesta) : ?>
                    <div class="pregunta-respuesta" data-indice="<?php echo esc_attr($indice); ?>">
                        <strong class="pregunta">Pregunta:</strong> <?php echo esc_html($pregunta_respuesta['pregunta'] ?? ''); ?><br>
                        <div class="respuesta" style="display: none;">
                            <strong>Respuesta:</strong> <?php echo wp_kses_post($pregunta_respuesta['respuesta'] ?? ''); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </ul>


            agregOOOOO ESTO ABAJO... BORRAR SI SACO COMENTARIO*/
             ?>


            <div id="chat-container"></div>
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

            // Mostrar la pantalla emergente al hacer clic en el icono
            floatingIcon.addEventListener('click', function() {
                modal.style.display = 'block';
            });

            // Cerrar la pantalla emergente al hacer clic en la "X"
            closeModal.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            // Cerrar la pantalla emergente al hacer clic fuera de ella
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });

        jQuery(document).ready(function($) {
        $('.pregunta-respuesta .pregunta').on('click', function() {
            var indice = $(this).closest('.pregunta-respuesta').data('indice');
            $('.pregunta-respuesta[data-indice="' + indice + '"] .respuesta').toggle();
        });
    });




    var userInput = document.getElementById('user-input');

    userInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Evitar que se inserte un salto de línea
            enviarMensaje();
        }
    });

    function enviarMensaje() {
        var userInput = document.getElementById('user-input');
        var mensaje = userInput.value;
       
        if (mensaje.trim() !== '') {
            

            agregarMensaje('Usuario', mensaje);

             // Simula que el bot está escribiendo
             simularEscribiendo();

            // Envía el mensaje para su análisis aquí después de la pausa
            setTimeout(function() {
             analizarPalabraClave(mensaje);
            }, 1000);
// Limpiar el cuadro de entrada
userInput.value = '';
        }
    }


    
    function agregarMensaje(usuario, mensaje) {
        console.log("agrego mensaje, de "+usuario+" pongo"+mensaje);

        var chatContainer = document.getElementById('chat-container');
        var nuevoMensaje = document.createElement('div');
        if(usuario != "Bot"){
        nuevoMensaje.className = 'mensaje mensaje-enviado';
        }

        if(usuario == "Bot"){
        nuevoMensaje.className = 'mensaje mensaje-recibido';
        }
        
        nuevoMensaje.innerHTML = `<strong>${usuario}:</strong> ${mensaje}`;
        chatContainer.appendChild(nuevoMensaje);

        // Desplazarse hacia abajo para mostrar el último mensaje
        chatContainer.scrollTop = chatContainer.scrollHeight;

    }

    <?php
    $options = get_option('mensajeAR_options');
    $preguntas_respuestas = $options['preguntas_respuestas'] ?? array();
    $preguntas_respuestas = array_values($preguntas_respuestas); // Asegurar que sea un array numérico
    ?>
    var opcionesMensajeAR = <?php echo json_encode($preguntas_respuestas); ?>;
    
    function analizarPalabraClave(mensaje) {
    console.log("me estoy metiendo en analizar");

    var contestacion = "";
    // Verificar si hay alguna coincidencia
    var hayCoincidencia = opcionesMensajeAR.some(function (pregRespuesta) {
    var preguntaAlmacenada = pregRespuesta.pregunta.toLowerCase().trim();
    contestacion = pregRespuesta.respuesta;
    return preguntaAlmacenada !== '' && mensaje.toLowerCase().includes(preguntaAlmacenada);
    });


    console.log("viendo la logica, voy a pasar al for si estadoconv != 0 y vale "+estadoConversacion);
    if(estadoConversacion !=0){
        preguntar_datos(mensaje);
        console.log("el break me manda aca?");
        return;
    }
    if (hayCoincidencia) {
        // Si hay coincidencia, puedes manejarlo aquí
        agregarMensaje("Bot", contestacion);
    } 
    if(mensaje == "un mensaje"){
        preguntar_datos(mensaje);
    }else {
        console.log("me estoy metiendo aca y estado vale "+estadoConversacion);

        // Si no hay coincidencia, puedes manejarlo aquí
        contestacion = "No soy capaz de comprender la pregunta, intenta de otra manera";
        agregarMensaje("Bot", contestacion);
    }
    }


    function simularEscribiendo() {
        var chatContainer = document.getElementById('chat-container');
        var nuevoMensaje = document.createElement('div');
        nuevoMensaje.className = 'escribiendo';
        nuevoMensaje.innerHTML = 'escribiendo...';
        chatContainer.appendChild(nuevoMensaje);

        // Desplazarse hacia abajo para mostrar el mensaje de escritura
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }







    var nombreUsuario = "";
    var telefonoUsuario = "";
    var consultaUsuario = "";

function preguntar_datos(mensaje) {
    switch (estadoConversacion) {
        case 0:
            // Estado inicial, preguntar nombre y pasar al siguiente estado
            agregarMensaje('Bot', 'Hola, ¿cómo te llamas?');
            estadoConversacion = 1;
            
            break;
        case 1:
            // Estado para obtener el nombre, almacenar y pasar al siguiente estado
            
            nombreUsuario = mensaje;
            agregarMensaje('Bot', 'Mucho gusto, ' + nombreUsuario + '. ¿Cuál es tu número de teléfono?');
            estadoConversacion = 2;
            console.log("estoy en el case 1 termianndo");
            break;
        case 2:
            // Estado para obtener el teléfono, almacenar y pasar al siguiente estado
            estadoConversacion =3;

            telefonoUsuario = mensaje;
            agregarMensaje('Bot', 'Gracias. Ahora, ¿cuál es el motivo de tu consulta?');
            break;
        case 3:
            // Estado para obtener la consulta, almacenar y finalizar la conversación
            consultaUsuario = mensaje;
            estadoConversacion = 0; // Reiniciar el estado para futuras interacciones

            agregarRegistro();
            agregarMensaje('Bot', 'Gracias por tu consulta. Fin de la conversación.');
            
            // Ahora, puedes almacenar las variables (nombreUsuario, telefonoUsuario, consultaUsuario) en tu base de datos.
            // Puedes utilizar AJAX para enviar estos datos al servidor y procesarlos en el lado del servidor (por ejemplo, PHP).
            // Ejemplo de envío mediante AJAX:
           // enviarDatosAlServidor(nombreUsuario, telefonoUsuario, consultaUsuario);
            break;
        // Puedes agregar más casos para solicitar información adicional en otros estados
    }
}
    






function agregarRegistro() {
    console.log('nombreUsuario:', nombreUsuario);
console.log('telefonoUsuario:', telefonoUsuario);
console.log('consultaUsuario:', consultaUsuario);
    // Realiza una solicitud AJAX al servidor para eliminar un registro
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
                
            } else {
                console.error('Error al agregar registro:', data.error);
            }
        })
        .catch(error => console.error('Error al agregar registro:', error));
}







    </script>
    <?php
}