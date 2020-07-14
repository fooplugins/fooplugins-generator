<?php
namespace FooPlugins\Generator\Admin;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * File Processor Class for FooPlugins Code Generator
 */
if ( ! class_exists( 'FooPlugins\Generator\Admin\BoilerplateFileProcessor' ) ) {

	class BoilerplateFileProcessor {

		public $boilerplate = array();
		public $rules = null;

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

			$iterator = new RecursiveDirectoryIterator( $source_path, FilesystemIterator::SKIP_DOTS );
			foreach ( new RecursiveIteratorIterator( $iterator ) as $filename ) {

				$base_filename = basename( $filename );

				if ( strpos( $base_filename, 'foogen_' ) === 0) {
					//we are dealing with a special file e.g. "foogen_special.php"
					$special_file_type = basename( str_replace( 'foogen_', '', $base_filename ), '.php' );
					do_action( "FooPlugins\Generator\Admin\BoilerplateFileProcessor\ProcessSpecialFile\{$special_file_type}", $filename, $this );
				}

				//check if we need to exclude files
				if ( $this->should_exclude_file( $filename ) ) {
					continue;
				}

				//check if we need to exclude directories
				if ( $this->should_exclude_directory( $filename ) ) {
					continue;
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
		 * Determine if we should exclude a file
		 *
		 * @param $filename
		 *
		 * @return bool
		 */
		function should_exclude_file( $filename ) {
			$base_filename = basename( $filename );

			if ( in_array( $base_filename, $this->boilerplate['exclude_files'] ) ) {
				return true;
			}

			foreach ( $this->get_rules_for_current_state() as $rule ) {
				if ( isset( $rule['type'] ) && 'exclude_files' === $rule['type'] ) {
					if ( in_array( $base_filename, $rule['files'] ) ) {
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Determine if we should exclude a directory
		 *
		 * @param $filename
		 *
		 * @return bool
		 */
		function should_exclude_directory( $filename ) {
			$source_path = realpath( $this->boilerplate['path'] );

			$directory = ltrim( str_replace( $source_path, '', $filename->getPath() ), '\\' );

			if ( in_array( $directory, $this->boilerplate['exclude_directories'] ) ) {
				return true;
			}

			foreach ( $this->get_rules_for_current_state() as $rule ) {
				if ( isset( $rule['type'] ) && 'exclude_directories' === $rule['type'] ) {
					if ( in_array( $directory, $rule['directories'] ) ) {
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Build up a set of rules that are valid for the current state
		 *
		 * @return array
		 */
		function get_rules_for_current_state() {
			if ( !isset( $this->rules ) ) {
				$this->rules = array();

				if ( isset( $this->boilerplate['rules'] ) ) {
					foreach ( $this->boilerplate['rules'] as $rule ) {
						$field = $rule['field'];
						$expected_value = $rule['value'];
						$actual_value = $this->boilerplate_processor->get_field_value( $field );

						//check if the rule if valid and store it
						if ( $expected_value === $actual_value ) {
							$this->rules[] = $rule;
						}
					}
				}
			}

			return $this->rules;
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
