<?php
namespace FooPlugins\Generator\Admin;

/**
 * FooPlugins Generator Admin Settings Class
 */

if ( !class_exists( 'FooPlugins\Generator\Admin\Settings' ) ) {

	class Settings {

		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_stylesheet' ) );
		}

		/**
		 * Adds the admin stylesheet
		 */
		public function add_admin_stylesheet() {

		}

		public function add_settings_page() {
			add_options_page(
				__( 'FooPlugins Generator' , 'foogen' ),
				__( 'FooPlugins Generator' , 'foogen' ),
				'manage_options',
				'foogen-settings',
				array( $this, 'create_settings_page' )
			);
		}

		public function create_settings_page() {
			require_once FOOGEN_PATH . 'includes/admin/views/generator.php';
        }
	}
}