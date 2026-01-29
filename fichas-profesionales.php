<?php
/**
 * Plugin Name: Fichas Profesionales
 * Description: Directorio de profesionales de las artes escénicas con membresías de pago mediante WooCommerce.
 * Version: 0.1.0
 * Author: Sergi
 * Text Domain: fichas-profesionales
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

defined( 'ABSPATH' ) || exit;

function fichas_profesionales_activate() {
}

register_activation_hook( __FILE__, 'fichas_profesionales_activate' );

function fichas_profesionales_init() {
}

add_action( 'plugins_loaded', 'fichas_profesionales_init' );
