<?php
namespace FooPlugins\Generator\Admin;

use FilesystemIterator;
use FooPlugins\Generator\Admin\BoilerplateZipGenerator;
use mysql_xdevapi\Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * Runs all classes that need to run in the admin for PRO
 */

if ( !class_exists( 'FooPlugins\Generator\Admin\BoilerplateIncludes' ) ) {

	class BoilerplateIncludes {

		/**
		 * Init constructor.
		 */
		function __construct() {
			add_action( 'FooPlugins\Generator\Admin\BoilerplateFileProcessor\PreProcess', array(
				$this,
				'handle_includes'
			), 10, 2 );

			add_action( 'FooPlugins\Generator\Admin\BoilerplateIncludes\HandleInclude\zip', array(
				$this,
				'handle_zip_include'
			), 10, 2 );
		}

		/**
		 * Handle any includes included in the boilerplate
		 *
		 * @param $source_path
		 * @param $file_processor BoilerplateFileProcessor
		 */
		public function handle_includes( $source_path, $file_processor ) {
			$boilerplate = $file_processor->boilerplate;

			if ( !array_key_exists( 'includes', $boilerplate ) ) {
				return;
			}

			foreach ( $boilerplate['includes'] as $include ) {
				do_action( "FooPlugins\Generator\Admin\BoilerplateIncludes\HandleInclude\\" . $include['type'], $include, $source_path );
			}
		}

		/**
		 * Copy files from one directory to another
		 *
		 * @param $source_directory
		 * @param $target_directory
		 */
		public function copy_files( $source_directory, $target_directory ) {
			$di = new RecursiveDirectoryIterator( $source_directory, FilesystemIterator::SKIP_DOTS );
			$ri = new RecursiveIteratorIterator( $di, RecursiveIteratorIterator::CHILD_FIRST );

			$directories = array();
			$files = array();

			foreach ( $ri as $file ) {
				$new_path = untrailingslashit( $target_directory ) . DIRECTORY_SEPARATOR . $ri->getSubPathName();

				if ($file->isDir()) {
					$directories[] = $new_path;
				} else {
					$files[ $file->getPathname() ] = $new_path;
				}
			}

			foreach ( $directories as $directory ) {
				wp_mkdir_p( $directory );
			}

			foreach ( $files as $source => $target ) {
				copy( $source, $target );
			}
		}

		/**
		 * Get the name of the first directory
		 *
		 * @param $directory
		 *
		 * @return string|null
		 */
		public function get_first_directory( $directory ) {
			$di = new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS );
			$ri = new RecursiveIteratorIterator( $di, RecursiveIteratorIterator::SELF_FIRST );
			foreach ( $ri as $file ) {
				if ( $file->isDir() ) {
					return strval( $file );
				}
			}
			return null;
		}

		/**
		 * Delete a directory
		 *
		 * @param $directory
		 */
		public function delete_directory( $directory ) {
			$di = new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS );
			$ri = new RecursiveIteratorIterator( $di, RecursiveIteratorIterator::CHILD_FIRST );
			foreach ( $ri as $file ) {
				$file->isDir() ? rmdir( $file ) : unlink( $file );
			}
		}

		/**
		 * Handle zip includes
		 *
		 * @param $include array
		 * @param $source_path string
		 */
		public function handle_zip_include( $include, $source_path ) {
			//get zip filename
			$source = $include['source'];

			//define where the file will be unzipped
			$destination_directory = untrailingslashit( $source_path ) . DIRECTORY_SEPARATOR . $include['directory'];

			$destination_zip_file = isset( $include['zip_filename'] ) ? $include['zip_filename'] : basename( parse_url( $source, PHP_URL_PATH ) );

			$destination_full_path = $destination_directory . $destination_zip_file;

			//create the directory
			if ( wp_mkdir_p( $destination_directory ) ) {

				//delete all files and folders in the directory before we download zip and unzip
				$this->delete_directory( $destination_directory );

				//download zip
				if ( copy( $source, $destination_full_path ) ) {

					//unzip the zip file
					$include_zip = new ZipArchive();
					if ( $include_zip->open($destination_full_path ) ) {
						for ( $i = 0; $i < $include_zip->numFiles; $i++ ) {
							$include_zip->extractTo( $destination_directory, array( $include_zip->getNameIndex( $i ) ) );
						}
						$include_zip->close();

						//delete the zip from local storage:
						unlink( $destination_full_path );
					}
				}
			}
		}
	}
}
