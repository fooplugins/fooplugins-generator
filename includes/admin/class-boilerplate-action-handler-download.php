<?php
namespace FooPlugins\Generator\Admin;


use Plugin_Upgrader;

/**
 * FooPlugins Generator Boilerplate Download Action Handler Class
 */

if ( ! class_exists( 'FooPlugins\Generator\Admin\BoilerplateActionHandlerDownload' ) ) {

	class BoilerplateActionHandlerDownload extends BoilerplateActionHandler {

		function __construct() {
			add_action( 'FooPlugins\Generator\Admin\BoilerplateActionHandler\download', array( $this, 'handle_download'), 10, 4 );
		}

		/**
		 * Handle a zip download
		 *
		 * @param $boilerplate_object
		 * @param $boilerplate_state
		 * @param $processor
		 * @param $processed_files
		 */
		public function handle_download( $boilerplate_object, $boilerplate_state, $processor, $processed_files ) {
			//The name of the file that is downloaded
			$download_filename = isset( $boilerplate_object['download_filename'] ) ? $boilerplate_object['download_filename'] : $boilerplate_object['name'] . '.zip';
			$download_filename = $processor->process_filename( $download_filename );

			//create the zip file
			$zip_manager = $this->create_zip( $boilerplate_object, $boilerplate_state, $processor, $processed_files );

			//download it to the client
			$this->send_download_headers(
				$zip_manager->zip_temp_filename,
				$download_filename,
				true
			);

			die();
		}


		/**
		 * Send the download headers to the browser
		 *
		 * @param bool $delete
		 */
		function send_download_headers( $zip_temp_filename, $download_filename, $delete = true ) {
			header( 'Pragma: public' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			header( 'Cache-Control: public' );
			header( 'Content-Description: File Transfer' );
			header( 'Content-type: application/octet-stream' );
			header( sprintf( 'Content-Disposition: attachment; filename="%s"', $download_filename ) );
			header( 'Content-Transfer-Encoding: binary' );

			ob_clean();
			flush();

			@readfile( $zip_temp_filename );
			if ( $delete ) {
				@unlink( $zip_temp_filename );
			}
		}
	}
}

