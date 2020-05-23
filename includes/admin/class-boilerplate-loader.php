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
		 * @return array
		 */
		public function load_plugin_boilderplates() {
			$boilerplate_directory = untrailingslashit( FOOGEN_PATH ) . DIRECTORY_SEPARATOR . 'boilerplates';

			return $this->load_boilerplates_from_directory( $boilerplate_directory );
		}

		/**
		 * Loads all boilerplates from a specific directory
		 *
		 * @param $directory
		 *
		 * @return array
		 */
		public function load_boilerplates_from_directory( $directory ) {
			$boilerplates = array();

			//loop through all folders
			$root = new RecursiveDirectoryIterator( $directory );
			$iterator = new RecursiveIteratorIterator( $root );

			foreach ( $iterator as $file ) {
				if ( !$file->isDir() ) {

					$filename = $file->getPathname();

					if ( 'foogen_boilerplate.php' === basename( $file ) ) {
						$boilerplate_path = str_replace( '\foogen_boilerplate.php', '', $filename );
						$boilerplate = include_once $filename;
						$boilerplate['path'] = $boilerplate_path;
						$boilerplates[ $boilerplate['name'] ] = $boilerplate;
					}
				}
			}

			return $boilerplates;
		}
	}
}
