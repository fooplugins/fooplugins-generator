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
			$tabs = '';
			$tab_content = '';
			$tab_active = 'nav-tab-active';
			$tab_content_display = '';

			foreach ( $this->processed_files as $file_name => $content ) {
				$file_slug = sanitize_title( $file_name );
				$tabs .= '<a href="#' . $file_slug . '" data-tab="' . $file_slug . '" class="nav-tab ' . $tab_active . '">' . esc_html( $file_name ) . '</a>';
				$tab_content .= '<div class="foogen-tab-content" ' . $tab_content_display . ' data-tab="' . $file_slug . '"><pre>' . esc_html( $content ) . '</pre></div>';
				$tab_active = '';
				$tab_content_display = 'style="display:none"';
			}
			echo '<div class="foogen-tabs nav-tab-wrapper">';
			echo $tabs;
			echo '</div>';
			echo $tab_content;
		}
	}
}

