<?php
namespace FooPlugins\Generator\Pro\Admin;

use FilesystemIterator;
use FooPlugins\Generator\Admin\BoilerplateZipGenerator;
use mysql_xdevapi\Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * Pro Admin Init Class
 * Runs all classes that need to run in the admin for PRO
 */

if ( !class_exists( 'FooPlugins\Generator\Pro\Admin\BoilerplateZipGeneratorIncludes' ) ) {

	class BoilerplateZipGeneratorIncludes {

		/**
		 * Init constructor.
		 */
		function __construct() {
			add_action( 'FooPlugins\Generator\Admin\BoilerplateZipGenerator\PreProcess', array(
				$this,
				'handle_includes'
			), 10, 3 );
		}

		/**
		 * @param $filename $source_path
		 * @param $zip ZipArchive
		 * @param $zip_generator BoilerplateZipGenerator
		 */
		public function handle_includes( $source_path, $zip, $zip_generator ) {
			$boilerplate = $zip_generator->options['boilerplate'];

			if ( !array_key_exists( 'includes', $boilerplate ) ) {
				return;
			}

			foreach ( $boilerplate['includes'] as $include ) {
				if ( $include['type'] === 'zip' ) {

					$this->handle_zip_include( $source_path, $include );

				} else if ( $include['type'] === 'freemius' ) {

					$include['zip_filename'] = 'freemius.zip';
					$this->handle_freemius_include( $source_path, $include );

				} else {
					do_action( "FooPlugins\Generator\Admin\BoilerplateZipGenerator\PreProcess\HandleInclude\\" . $include['type'], $source_path, $zip, $this );
				}
			}
		}

		public function handle_freemius_include( $source_path, $include ) {
			//first download and unzip the latest freemius SDK
			$this->handle_zip_include( $source_path, $include );

			$destination_directory = untrailingslashit( untrailingslashit( $source_path ) . DIRECTORY_SEPARATOR . $include['directory'] ) . DIRECTORY_SEPARATOR;
			$freemius_directory    = untrailingslashit( $this->get_first_directory( $destination_directory ) ) . DIRECTORY_SEPARATOR;

			//then move it all up a directory
			$this->copy_files( $freemius_directory, $destination_directory );

			//delete the original directory
			$this->delete_directory( $freemius_directory );
			rmdir( $freemius_directory );
		}

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

		public function delete_directory( $directory ) {
			$di = new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS );
			$ri = new RecursiveIteratorIterator( $di, RecursiveIteratorIterator::CHILD_FIRST );
			foreach ( $ri as $file ) {
				$file->isDir() ? rmdir( $file ) : unlink( $file );
			}
		}

		public function handle_zip_include( $source_path, $include ) {
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
