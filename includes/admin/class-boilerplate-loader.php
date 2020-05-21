<?php
namespace FooPlugins\Generator\Admin;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * FooPlugins Generator Boilerplate Loader Class
 */

if ( ! class_exists( 'FooPlugins\Generator\Admin\BoilerplateLoader' ) ) {

	class BoilerplateLoader {

		public function load_plugin_boilderplates() {

			$boilerplate_root = FOOGEN_PATH . 'boilerplates';

			$boilerplates = array();

			//loop through all folders
			$root = new RecursiveDirectoryIterator( $boilerplate_root );
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