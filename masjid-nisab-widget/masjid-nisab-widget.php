<?php
/**
 * Plugin Name:       Masjid Nisab Widget
 * Plugin URI:        
 * Description:       Displays Mahr Fatimi, Minimum Mahr, and Zakaah Nisab based on live silver prices from Lear Capital.
 * Version:           1.0.0
 * Author:            AI Agent
 * Author URI:        
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       masjid-nisab-widget
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Include API functions.
require_once plugin_dir_path( __FILE__ ) . 'includes/api-functions.php';

// Include the widget class.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-masjid-nisab-widget.php';

// Function to register the widget
function masjid_nisab_register_widget() {
    register_widget( 'Masjid_Nisab_Widget' );
}
add_action( 'widgets_init', 'masjid_nisab_register_widget' );

// Function to enqueue styles
function masjid_nisab_enqueue_styles() {
    wp_enqueue_style(
        'masjid-nisab-widget-style', // Handle
        plugin_dir_url( __FILE__ ) . 'css/masjid-nisab-widget.css', // Source
        array(), // Dependencies
        '1.0.0' // Version
    );
}
add_action( 'wp_enqueue_scripts', 'masjid_nisab_enqueue_styles' );
?>
