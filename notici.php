<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package Notici
 * @link    https://losol.no
 * @since   1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Notici
 * Plugin URI:        https://losol.io/notici
 * Description:       Notice listing made fun.
 * Version:           1.0.3
 * Author:            Ole Kristian Losvik
 * Author URI:        https://losol.no
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       notici
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'NOTICI_VERSION', '1.0.2' );

require plugin_dir_path( __FILE__ ) . 'includes/class-notici.php';
new Notici();
