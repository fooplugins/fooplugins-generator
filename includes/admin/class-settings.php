<?php
namespace FooPlugins\Generator\Admin;

/**
 * FooPlugins Generator Admin Settings Class
 */

if ( !class_exists( 'FooPlugins\Generator\Admin\Settings' ) ) {

	class Settings {

		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_stylesheet' ) );
		}

		/**
		 * Adds the admin stylesheet
		 */
		public function add_admin_stylesheet() {

		}

		/**
		 * Add menu to the tools menu
		 */
		public function add_menu() {
			add_management_page(
				__( 'FooPlugins Generator' , 'foogen' ),
				__( 'FooPlugins Generator' , 'foogen' ),
				'manage_options',
				'foogen-settings',
				array( $this, 'render_generator_page' )
			);
		}

		/**
		 * Renders the contents for the generator page
		 */
		public function render_generator_page() {
			require_once FOOGEN_PATH . 'includes/admin/views/generator.php';
        }
	}
}
