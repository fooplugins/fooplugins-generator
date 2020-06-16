<?php
namespace FooPlugins\Generator\Admin;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * Zip Manager Class for FooPlugins Code Generator
 */
if ( ! class_exists( 'FooPlugins\Generator\Admin\BoilerplateZipManager' ) ) {

	class BoilerplateZipManager {

		public $zip_temp_filename;
		public $zip_root_directory;

		function __construct( $zip_temp_filename, $zip_root_directory ) {
			$this->zip_root_directory = $zip_root_directory;
			$this->zip_temp_filename = $zip_temp_filename;
		}

		/**
		 * Creates the new zip file based on an array of processed files
		 *
		 * @param $processed_files
		 */
		function create_zip( $processed_files ) {
			$zip = new ZipArchive;

			$zip->open( $this->zip_temp_filename, ZipArchive::CREATE && ZipArchive::OVERWRITE );

			foreach ( $processed_files as $filename => $contents ) {
				$zip->addFromString( trailingslashit( $this->zip_root_directory ) . $filename, $contents );
			}

			do_action( "FooPlugins\Generator\Admin\BoilerplateZipManager\PostProcess", $zip, $this );

			$zip->close();
		}
	}
}
