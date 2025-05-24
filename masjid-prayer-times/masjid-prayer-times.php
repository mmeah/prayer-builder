<?php
/**
 * Plugin Name: Masjid Prayer Times
 * Plugin URI:
 * Description: A plugin to display prayer times from masjidal.com.
 * Version: 1.0.0
 * Author: AI Agent
 * Author URI:
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: masjid-prayer-times
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Include API functions.
require_once plugin_dir_path( __FILE__ ) . 'includes/api-functions.php';

// Include the widget class.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-masjid-prayer-times-widget.php';

// Register the widget.
function register_masjid_prayer_times_widget() {
    register_widget( 'Masjid_Prayer_Times_Widget' );
}
add_action( 'widgets_init', 'register_masjid_prayer_times_widget' );

// Enqueue plugin stylesheet.
function masjid_prayer_times_enqueue_styles() {
    wp_enqueue_style(
        'masjid-prayer-times-widget-style', // Handle
        plugin_dir_url( __FILE__ ) . 'css/masjid-prayer-times-widget.css', // Source
        array(), // Dependencies
        '1.0.0' // Version
    );
}
add_action( 'wp_enqueue_scripts', 'masjid_prayer_times_enqueue_styles' );

?>
