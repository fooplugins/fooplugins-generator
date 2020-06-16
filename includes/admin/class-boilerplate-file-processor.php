<?php
namespace FooPlugins\Generator\Admin;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * File Processor Class for FooPlugins Code Generator
 */
if ( ! class_exists( 'FooPlugins\Generator\Admin\BoilerplateFileProcessor' ) ) {

	class BoilerplateFileProcessor {

		public $boilerplate = array();

		/**
		 * @var BoilerplateProcessor
		 */
		public $boilerplate_processor;

		function __construct( $boilerplate_object, $boilerplate_processor ) {
			$this->boilerplate = $boilerplate_object;
			$this->boilerplate_processor = $boilerplate_processor;
		}

		/**
		 * Creates an array of processed files
		 */
		function process_files() {
			$source_path = realpath( $this->boilerplate['path'] );

			do_action( "FooPlugins\Generator\Admin\BoilerplateFileProcessor\PreProcess", $source_path, $this );

			$processed = array();

			$iterator = new RecursiveDirectoryIterator( $source_path );
			foreach ( new RecursiveIteratorIterator( $iterator ) as $filename ) {

				$base_filename = basename( $filename );

				if ( strpos( $base_filename, 'foogen_' ) === 0) {
					//we are dealing with a special file e.g. "foogen_special.php"
					$special_file_type = basename( str_replace( 'foogen_', '', $base_filename ), '.php' );
					do_action( "FooPlugins\Generator\Admin\BoilerplateFileProcessor\ProcessSpecialFile\{$special_file_type}", $filename, $this );
				}

				//check if we need to exclude files
				if ( in_array( $base_filename, $this->boilerplate['exclude_files'] ) ) {
					continue;
				}

				//check if we need to exclude directories
				foreach ( $this->boilerplate['exclude_directories'] as $directory ) {
					if ( strstr( $filename, "/{$directory}/" ) ) {
						continue 2;// continue the parent foreach loop
					}
				}

				$dest_filename = ltrim( str_replace( $source_path, '', $filename->getRealPath() ), '\\' );

				$dest_filename = $this->boilerplate_processor->process_filename( $dest_filename );

				$contents = $this->process_file_contents( file_get_contents( $filename ), $base_filename );

				$contents = apply_filters( "FooPlugins\Generator\Admin\BoilerplateFileProcessor\ProcessFile", $contents, $filename, $dest_filename, $this );

				$processed[$dest_filename] = $contents;
			}

			$processed = apply_filters( "FooPlugins\Generator\Admin\BoilerplateCodeGenerator\PostProcess", $processed, $this );

			return $processed;
		}

		/**
		 * Process the contents of an individual file
		 * @param string $contents
		 * @param string $filename
		 *
		 * @return string
		 */
		function process_file_contents( $contents, $filename ) {
			// Replace only files we care about
			$valid_extensions_regex = implode( '|', $this->boilerplate['process_extensions'] );
			if ( ! preg_match( "/\.({$valid_extensions_regex})$/", $filename ) ) {
				return $contents;
			}

			$contents = $this->boilerplate_processor->process_string( $contents );

			$contents = apply_filters( 'FooPlugins\Generator\Admin\BoilerplateFileProcessor\ProcessFileContents', $contents, $filename, $this );

			return $contents;
		}
	}
}
