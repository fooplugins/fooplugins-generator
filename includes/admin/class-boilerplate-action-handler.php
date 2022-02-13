<?php
namespace FooPlugins\Generator\Admin;

/**
 * FooPlugins Generator Boilerplate Download Action Handler Class
 */

if ( ! class_exists( 'FooPlugins\Generator\Admin\BoilerplateActionHandler' ) ) {

	class BoilerplateActionHandler {

		function __construct() {
			add_action( 'admin_init', array( $this, 'listen_for_boilerplate_actions' ), 1 );
		}

		function listen_for_boilerplate_actions() {
			$nonce = foogen_safe_get_from_request( 'foogen_generate' );
			$action = foogen_safe_get_from_request( 'action' );

			if ( empty($nonce) || empty($action) ) {
				return;
			}

			if ( ! empty( $nonce ) && wp_verify_nonce( $nonce, 'foogen_generate' ) ) {

				$selected_boilerplate = foogen_safe_get_from_array( 'selected_boilerplate', $_POST, false );
				if ( $selected_boilerplate !== false ) {

					$boilerplate_object = foogen_get_all_boilerplates()[ $selected_boilerplate ];

					$boilerplate_data = foogen_safe_get_from_request( $selected_boilerplate );

					//get the form state
					$state_manager = new BoilerplateStateManager();
					$boilerplate_state = $state_manager->build_state( $boilerplate_object, $boilerplate_data );

					//validate the request
					if ( !$state_manager->has_errors() ) {

						//create the text processor
						$processor = new BoilerplateProcessor( $boilerplate_state );

						//we will always process the files
						$file_processor = new BoilerplateFileProcessor( $boilerplate_object , $processor );
						$processed_files = $file_processor->process_files();

						//process the boilerplate
						do_action( 'FooPlugins\Generator\Admin\BoilerplateActionHandler\\' . $action, $boilerplate_object, $boilerplate_state, $processor, $processed_files );

					} else {
						foreach ( $state_manager->errors as $error_field ) {
							foogen_add_global_message( sprintf( __( 'The field %s is required!', 'foogen' ) ,'<strong>' . $error_field['label'] . '</strong>' ), true );
						}
					}
				}
			}
		}

		/**
		 * Create a zip file
		 *
		 * @param $boilerplate_object array
		 * @param $boilerplate_state array
		 * @param $processor BoilerplateProcessor
		 * @param $processed_files array
		 *
		 * @return BoilerplateZipManager
		 */
		protected function create_zip( $boilerplate_object, $boilerplate_state, $processor, $processed_files ) {
			$upload_dir = wp_upload_dir();
			$zip_temp_directory = $upload_dir['path'];

			//The filename of the temp zip we will be creating
			$zip_temp_filename = trailingslashit( $zip_temp_directory ) . sprintf( '%s-%s.zip', $boilerplate_object['name'], md5( print_r( $boilerplate_state, true ) ) );
			$zip_temp_filename = $processor->process_filename( $zip_temp_filename );

			//The root directory used within the created zip
			$zip_root_directory = isset( $boilerplate_object['zip_root_directory'] ) ? $boilerplate_object['zip_root_directory'] : $boilerplate_object['name'];
			$zip_root_directory = $processor->process_string( $zip_root_directory );

			$zip_manager = new BoilerplateZipManager( $zip_temp_filename, $zip_root_directory );

			$zip_manager->create_zip( $processed_files );

			return $zip_manager;
		}
	}
}

