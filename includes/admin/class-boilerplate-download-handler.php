<?php
namespace FooPlugins\Generator\Admin;


/**
 * FooPlugins Generator Boilerplate Download Handler Class
 */

if ( ! class_exists( 'FooPlugins\Generator\Admin\BoilerplateDownloadHandler' ) ) {

	class BoilerplateDownloadHandler {

		function __construct() {
			add_action( 'admin_init', array( $this, 'listen_for_boilerplate_download' ), 1 );
		}

		function listen_for_boilerplate_download() {
			$nonce = foogen_safe_get_from_request( 'foogen_generate' );
			$action = foogen_safe_get_from_request( 'action' );

			if ( empty($nonce) || empty($action) ) {
				return;
			}

			if ( ! empty( $nonce ) && wp_verify_nonce( $nonce, 'foogen_generate' ) ) {

				$selected_boilerplate = foogen_safe_get_from_array( 'selected_boilerplate', $_POST, false );
				if ( $selected_boilerplate !== false ) {
					if ( 'download' === $action ) {
						$this->run( $selected_boilerplate );
					}
				}
			}
		}

		function run( $boilerplate_name ) {
			$boilerplate_data = foogen_safe_get_from_array( $boilerplate_name, $_POST, array() );

			$boilerplate_object = foogen_get_all_boilerplates()[$boilerplate_name];

			$state_manager = new BoilerplateStateManager();

			$boilerplate_state = $state_manager->build_state( $boilerplate_object, $boilerplate_data );

			$variables = array();
			foreach( $boilerplate_state as $key => $item ) {
				$variables['{' . $key . '}'] = $item;
			}

			$upload_dir = wp_upload_dir();

			//create the generator
			$zip_generator = new BoilerplateZipGenerator( array(
				'name'                 => $boilerplate_name,
				'process_extensions'   => isset( $boilerplate['process_extensions'] ) ? $boilerplate['process_extensions'] : array( 'php', 'css', 'js', 'txt', ),
				'source_directory'     => $boilerplate_object['path'],
				'zip_root_directory'   => $boilerplate_object['zip_root_directory'],
				'download_filename'    => $boilerplate_object['download_filename'],
				'variables'            => $variables,
				'zip_temp_directory'   => $upload_dir['path'],
			) );

			//generate the zip file
			$zip_generator->generate();

			//download it to the client
			$zip_generator->send_download_headers();
			die();
		}
	}
}

