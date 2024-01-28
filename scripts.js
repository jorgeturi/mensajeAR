var userInput = document.getElementById('user-input');

    userInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Evitar que se inserte un salto de línea
            enviarMensaje();
        }
    });


    function enviarMensaje() {
        var userInput = document.getElementById('user-input');
        var mensaje = userInput.value; //agarro valor 
       
        if (mensaje.trim() !== '') {

            agregarMensaje('Usuario', mensaje); //muestro lo que mando usuario
            simularEscribiendo();

            // Envía el mensaje para su análisis aquí después de la pausa
            setTimeout(function() {
             analizarPalabraClave(mensaje);
            }, 1000);
            // Limpiar el cuadro de entrada
        userInput.value = '';
        }
    }

    //para disparadores que no sean por chat
    function opcionElegida(palabraClave){
        agregarMensaje('Usuario', palabraClave);
        simularEscribiendo();
        setTimeout(function() {
             analizarPalabraClave(palabraClave);
        }, 1000);
    }

    //para serparar mensajes
    function contarOtroMensaje(mensaje) {
        var contador = 0;
        contador = (mensaje.match(/\[otro mensaje\]/g) || []).length;
        return contador;
    }
    
    //agrega mensajes al chat
    function agregarMensaje(usuario, mensaje) {
        var submensajes =contarOtroMensaje(mensaje);
       
        var chatContainer = document.getElementById('chat-container');
        var nuevoMensaje = document.createElement('div');

        if(usuario != "Bot"){     
        nuevoMensaje.className = 'mensaje mensaje-enviado';
        }

        if(usuario == "Bot"){
        nuevoMensaje.className = 'mensaje mensaje-recibido';
        }

        if(submensajes>0){
             // Si hay submensajes, dividir el mensaje en partes
            var partesMensaje = mensaje.split('[otro mensaje]');

             // Crear un nuevo mensaje para cada parte
             partesMensaje.forEach(function (parte, index) {
             setTimeout(function () {
                var nuevoMensaje = document.createElement('div');   //crea contenedor
                nuevoMensaje.className = 'mensaje mensaje-recibido';  //asigna clase

                nuevoMensaje.innerHTML = `<strong>${"Bot"}:</strong> ${parte}`; //inserto
                chatContainer.appendChild(nuevoMensaje); 

                chatContainer.scrollTop = chatContainer.scrollHeight;
            }, index * 800); // retraso entre mensajes
        });
        }
        else{ //si no hay submensajes inserto
        nuevoMensaje.innerHTML = `<strong>${usuario}:</strong> ${mensaje}`;
        chatContainer.appendChild(nuevoMensaje);
        }
        
        chatContainer.scrollTop = chatContainer.scrollHeight; // Desplazarse hacia abajo para mostrar el último mensaje
    }



    function analizarPalabraClave(mensaje) {
        var contestacion = "";
        // Verificar si hay alguna coincidencia
        var hayCoincidencia = opcionesMensajeAR.some(function (pregRespuesta) {
        var preguntaAlmacenada = pregRespuesta.pregunta.toLowerCase().trim();
        contestacion = pregRespuesta.respuesta;
        return preguntaAlmacenada !== '' && mensaje.toLowerCase().includes(preguntaAlmacenada);
        });
    
        
        if(estadoConversacion !=0){  //si inicio el pedido de datos
            preguntar_datos(mensaje);
            return;
        }
    
        if (hayCoincidencia) { // Si hay coincidencia, puedes manejarlo aquí
            agregarMensaje("Bot", contestacion);
            return;
        } 
    
        if(mensaje == "dejar un mensaje"){
            preguntar_datos(mensaje);
        }
        else {
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

            // Establecer un manejador de eventos para el evento 'transitionend'
         nuevoMensaje.addEventListener('animationend', function() {
        // Remover el mensaje después de que termine la animación
        chatContainer.removeChild(nuevoMensaje);
            });
    
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

    


   
        var notificacion = 0;

        var nombreUsuario = "";
        var telefonoUsuario = "";
        var consultaUsuario = "";
    
        function preguntar_datos(mensaje) {
        switch (estadoConversacion) {
            case 0:
                agregarMensaje('Bot', 'Hola, ¿cómo te llamas?');
                estadoConversacion = 1;
                break;
    
            case 1:            
                nombreUsuario = mensaje;
                agregarMensaje('Bot', 'Mucho gusto, ' + nombreUsuario + '. ¿Cuál es tu número de teléfono?');
                estadoConversacion = 2;
                break;
    
            case 2:
                estadoConversacion =3;
                telefonoUsuario = mensaje;
                agregarMensaje('Bot', 'Gracias. Ahora, ¿cuál es el motivo de tu consulta?');
                break;
    
            case 3:
                consultaUsuario = mensaje;
                estadoConversacion = 0; // Reinicia el estado para futuras interacciones
                agregarRegistro(); //registro los datos
    
                var btnFlotante2 = document.getElementById('btn-flotante2'); 
                btnFlotante2.removeAttribute('disabled'); // Habilitar el botón

            
                break;
             }
        }   
        


        function asignarEventoClic() {
            // Desasignar el evento antes de asignarlo nuevamente
            var textoBoton = event.target.textContent;
        
            opcionElegida(textoBoton);
            }
            
            document.addEventListener('click', function(event) {
            if (event.target.classList.contains('btn_chat')) {
                asignarEventoClic();
            }
            });
        
        
        
            document.addEventListener('DOMContentLoaded', function () {
                
            var btnFlotante2 = document.getElementById('btn-flotante2'); //boton que desencadena accion de muestra
            
                document.getElementById('btn-flotante').addEventListener('click', function() { //clickeado este desactivo el otro
                
                    btnFlotante2.disabled = true;
                    btnFlotante2.setAttribute('disabled', 'true');
               
                analizarPalabraClave("dejar un mensaje")  //envio desencadente
        
                this.remove(); // Elimina el botón después de hacer clic
                 });
        
                btnFlotante2.addEventListener('click', function() {
                    
                    analizarPalabraClave("hola");
        
                    this.remove(); // Elimina el botón después de hacer clic
                 
                 });
            });
        