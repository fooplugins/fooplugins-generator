<?php
namespace FooPlugins\Generator\Admin;


use Plugin_Upgrader;

/**
 * FooPlugins Generator Boilerplate Download Action Handler Class
 */

if ( ! class_exists( 'FooPlugins\Generator\Admin\BoilerplateActionHandlerInstall' ) ) {

	class BoilerplateActionHandlerInstall extends BoilerplateActionHandler {

		function __construct() {
			add_action( 'FooPlugins\Generator\Admin\BoilerplateActionHandler\install', array( $this, 'handle_install'), 10, 4 );
		}

		/**
		 * Handle a direct zip plugin install
		 *
		 * @param $boilerplate_object
		 * @param $boilerplate_state
		 * @param $processor
		 * @param $processed_files
		 */
		public function handle_install( $boilerplate_object, $boilerplate_state, $processor, $processed_files ) {
			//create the zip file
			$zip_manager = $this->create_zip( $boilerplate_object, $boilerplate_state, $processor, $processed_files );

			if ( $this->install( $zip_manager->zip_temp_filename ) ) {
				foogen_add_global_message( __( 'Plugin installed successfully! Head over to the plugin page to activate it.', 'foogen' ) );
			}
		}

		/**
		 * Install the plugin
		 *
		 * @param $filename
		 *
		 * @return bool
		 */
		public function install( $filename ) {

			//we need some files!
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // plugins_api calls
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; // Plugin_Upgrader class

			$skin = new namespace\SilentInstallerSkin();

			//instantiate Plugin_Upgrader
			$upgrader = new Plugin_Upgrader( $skin );

			$upgrader->install( $filename );

			if ( 'process_failed' === $skin->feedback ) {

				//we had an error along the way
				foogen_add_global_message( __('The plugin could not be installed!', 'foogen' ), true );
				return false;

			} else {
				return true;
			}
		}
	}
}

