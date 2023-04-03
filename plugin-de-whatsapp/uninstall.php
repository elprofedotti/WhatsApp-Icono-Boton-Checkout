<?php
// Si uninstall no es llamado desde WordPress, salir
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Eliminar opciones del plugin de la base de datos
delete_option('mi_plugin_de_whatsapp_telefono');
delete_option('mi_plugin_de_whatsapp_retraso');
delete_option('mi_plugin_de_whatsapp_posicion');
delete_option('mi_plugin_de_whatsapp_mensaje');
delete_option('mi_plugin_de_whatsapp_boton_carrito');
