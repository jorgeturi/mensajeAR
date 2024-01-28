<?php
/*
Plugin Name: MensajeAR
Description: Plugin para crear disparadores y respuestas
Version: 1.0
Author: Tujo
Author URI: https://tujo.com.ar
License: GPL-2.0+
Instructions: Enlace a la documentación del plugin
Screenshots: URL de las capturas de pantalla
Support: desarrollostujo@gmail.com
*/

// Incluyo archivos adicionales
include_once(plugin_dir_path(__FILE__) . 'tabla.php');
include_once(plugin_dir_path(__FILE__) . 'menu.php');
include_once(plugin_dir_path(__FILE__) . 'front.php');

// Función que se ejecuta al activar el plugin
function mensajeAR_activado() {
    crear_tabla(); 
}

// Función que se ejecuta al desactivar el plugin
function mensajeAR_desactivado() {
    // Puedes agregar código aquí al desactivar el plugin
}

function mensajeAR_desinstalar() {
    borrar_tabla();
    delete_option('mensajeAR_options');
}

register_activation_hook(__FILE__, 'mensajeAR_activado');
register_deactivation_hook(__FILE__, 'mensajeAR_desactivado');
register_uninstall_hook(__FILE__, 'mensajeAR_desinstalar');

