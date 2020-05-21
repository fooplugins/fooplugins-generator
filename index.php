<?php
/*
Plugin Name: FooPlugins Plugin Generator
Description: Generate the code for a new WordPress plugin in seconds
Version:     1.0.1
Author:      FooPlugins
Plugin URI:  https://fooplugins.com/generator
Author URI:  https://fooplugins.com
Text Domain: foogen
License:     GPL-2.0+
Domain Path: /languages

 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//define some essentials
if ( !defined('FOOGEN_SLUG' ) ) {
	define( 'FOOGEN_SLUG', 'foogen' );
	define( 'FOOGEN_NAMESPACE', 'FooPlugins\\Generator' );
	define( 'FOOGEN_DIR', __DIR__ );
	define( 'FOOGEN_PATH', plugin_dir_path( __FILE__ ) );
	define( 'FOOGEN_URL', plugin_dir_url( __FILE__ ) );
	define( 'FOOGEN_FILE', __FILE__ );
	define( 'FOOGEN_VERSION', '1.0.1' );
	define( 'FOOGEN_MIN_PHP', '5.4.0' ); // Minimum of PHP 5.4 required for autoloading, namespaces, etc
	define( 'FOOGEN_MIN_WP', '5.0.0' );  // Minimum of WordPress 4.4 required
}

//include common global functions
require_once( FOOGEN_PATH . 'includes/functions.php' );

//do a check to see if either free/pro version of the plugin is already running
if ( function_exists( 'foogen_fs' ) ) {
	foogen_fs()->set_basename( true, __FILE__ );
} else {
	require_once( FOOGEN_PATH . 'includes/freemius.php' );
}

//check minimum requirements before loading the plugin
if ( require_once FOOGEN_PATH . 'includes/startup-checks.php' ) {

	//start autoloader
	require_once( FOOGEN_PATH . 'vendor/autoload.php' );

	spl_autoload_register( 'foogen_autoloader' );

	//hook in activation
	register_activation_hook( __FILE__, array( 'FooPlugins\Generator\Activation', 'activate' ) );

	//start the plugin!
	new FooPlugins\Generator\Init();
}
