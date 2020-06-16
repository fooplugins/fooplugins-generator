<?php
namespace FooPlugins\Generator\Admin;

/**
 * FooPlugins Generator Boilerplate Download Action Handler Class
 */

if ( ! class_exists( 'FooPlugins\Generator\Admin\BoilerplateActionHandlerShow' ) ) {

	class BoilerplateActionHandlerShow extends BoilerplateActionHandler {

		private $processed_files;

		function __construct() {
			add_action( 'FooPlugins\Generator\Admin\BoilerplateActionHandler\show', array( $this, 'handle_show'), 10, 4 );
		}

		/**
		 * Handle showing the file contents
		 *
		 * @param $boilerplate_object
		 * @param $boilerplate_state
		 * @param $processor
		 * @param $processed_files
		 */
		public function handle_show( $boilerplate_object, $boilerplate_state, $processor, $processed_files ) {
			$this->processed_files = $processed_files;
			add_action( 'FooPlugins\Generator\Admin\AfterForm', array( $this, 'show_files_after_form' ) );
		}

		public function show_files_after_form() {
			foreach ( $this->processed_files as $file_name => $content ) {
				echo '<div>';
				echo '<h3>' . $file_name . '</h3>';
				echo '<pre>';
				echo  esc_html( $content );
				echo '</pre>';
				echo '</div>';
			}
		}
	}
}

