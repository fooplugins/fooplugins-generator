<?php
namespace FooPlugins\Generator\Admin;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * FooPlugins Generator Boilerplate Loader Class
 */

if ( ! class_exists( 'FooPlugins\Generator\Admin\BoilerplateLoader' ) ) {

	class BoilerplateLoader {

		/**
		 * Loads all the default boilerplates
		 *
		 * @return array
		 */
		public function load_plugin_boilderplates() {
			$boilerplate_directory = untrailingslashit( FOOGEN_PATH ) . DIRECTORY_SEPARATOR . 'boilerplates';

			return $this->load_boilerplates_from_directory( $boilerplate_directory );
		}

		/**
		 * Loads all boilerplates from a specific directory
		 *
		 * @param string $directory Path used for loading boilerplaets.
		 *
		 * @return array
		 */
		public function load_boilerplates_from_directory( $directory ) {
			$boilerplates = array();

			// Loop through all folders.
			$root     = new RecursiveDirectoryIterator( $directory );
			$iterator = new RecursiveIteratorIterator( $root );

			foreach ( $iterator as $file ) {
				if ( ! $file->isDir() ) {

					$filename = $file->getPathname();

					if ( 'foogen_boilerplate.php' === basename( $file ) ) {
						$boilerplate_path = str_replace( '\foogen_boilerplate.php', '', $filename );
						$boilerplate = include $filename;
						$boilerplate['path'] = $boilerplate_path;
						$boilerplates[ $boilerplate['name'] ] = $this->build_boilerplate( $boilerplate );
					}
				}
			}

			return $boilerplates;
		}

		/**
		 * Sets some defaults for the boilerplate
		 *
		 * @param array $boilerplate The boilerplate array.
		 *
		 * @return array
		 */
		private function build_boilerplate( $boilerplate ) {
			if ( ! isset( $boilerplate['process_extensions'] ) ) {
				$boilerplate['process_extensions'] = array( 'php', 'css', 'js', 'txt', 'md' );
			}
			if ( ! isset( $boilerplate['exclude_files'] ) ) {
				$boilerplate['exclude_files'] = array( '.git', '.svn', '.DS_Store', '.gitignore', '.', '..', 'foogen_boilerplate.php', 'foogen_include.php' );
			}
			if ( ! isset( $boilerplate['exclude_directories'] ) ) {
				$boilerplate['exclude_directories'] = array( '.git', '.svn', '.', '..' );
			}

			return $boilerplate;
		}
	}
}
