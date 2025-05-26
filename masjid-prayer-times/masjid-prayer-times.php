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

// Include the widget classes.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-masjid-prayer-times-widget.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-masjid-nisab-widget.php';

// Register the widgets.
function masjid_prayer_times_register_widgets() { // Renamed for clarity as it now registers multiple widgets
    register_widget( 'Masjid_Prayer_Times_Widget' );
    register_widget( 'Masjid_Nisab_Widget' );
}
add_action( 'widgets_init', 'masjid_prayer_times_register_widgets' );

// Enqueue plugin stylesheet.
// No changes needed here for widget registration.
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
