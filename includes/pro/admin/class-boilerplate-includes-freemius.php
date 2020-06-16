<?php /** @noinspection PhpUnusedParameterInspection */

namespace FooPlugins\Generator\Pro\Admin;

use FooPlugins\Generator\Admin\BoilerplateIncludes;

/**
 * Pro Class for handling Freemius includes
 */

if ( !class_exists( 'FooPlugins\Generator\Pro\Admin\BoilerplateIncludesFreemius' ) ) {

	class BoilerplateIncludesFreemius extends BoilerplateIncludes {

		/**
		 * Init constructor.
		 */
		function __construct() {
			add_action( 'FooPlugins\Generator\Admin\BoilerplateIncludes\HandleInclude\freemius', array(
				$this,
				'handle_freemius_include'
			), 10, 3 );
		}

		/**
		 * Handles including latest Freemius SDK from github
		 *
		 * @param $include array
		 * @param $source_path string
		 */
		public function handle_freemius_include( $include, $source_path ) {
			//force the zip_filename
			$include['zip_filename'] = 'freemius.zip';

			//first download and unzip the latest freemius SDK
			$this->handle_zip_include( $include, $source_path );

			$destination_directory = untrailingslashit( untrailingslashit( $source_path ) . DIRECTORY_SEPARATOR . $include['directory'] ) . DIRECTORY_SEPARATOR;
			$freemius_directory    = untrailingslashit( $this->get_first_directory( $destination_directory ) ) . DIRECTORY_SEPARATOR;

			//then move it all up a directory
			$this->copy_files( $freemius_directory, $destination_directory );

			//delete the original directory
			$this->delete_directory( $freemius_directory );
			rmdir( $freemius_directory );
		}
	}
}
