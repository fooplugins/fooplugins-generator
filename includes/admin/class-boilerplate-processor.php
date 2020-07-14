<?php
namespace FooPlugins\Generator\Admin;
use FooPlugins\Generator\Dependencies\LightnCandy\LightnCandy;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * Processor Class for FooPlugins Code Generator
 */
if ( ! class_exists( 'FooPlugins\Generator\Admin\BoilerplateProcessor' ) ) {

	class BoilerplateProcessor {

		public $variables = array();

		function __construct( $boilerplate_state ) {
			//first, calculate all variables from the state
			foreach( $boilerplate_state as $key => $item ) {
				$this->variables[ $key ] = $item;
			}
		}

		/**
		 * Process a sting with the variables
		 * @param $string
		 *
		 * @return string|string[]|null
		 */
		function process_string( $string ) {
			foreach ( $this->variables as $key => $value ) {
				//find and replace all fields
				$string = preg_replace_callback(
					'/({' . $key . '(:.*)?})/',
					function ( $matches ) use( $value ) {
						if ( isset( $matches[2] ) ) {
							$function = trim( $matches[2], ':' );
							if ( function_exists( '__foogen_' . $function ) ) {
								return call_user_func( '__foogen_' . $function, $value );
							} else if ( function_exists( $function ) ) {
								return call_user_func( $function, $value );
							}
						}
						return $value;
					},
					$string );

				//do conditional replacements
				$regex = "{#if $key}(\n|\r\n)(.|\n|\r\n)*?({#endif $key}\n|{#endif $key}\r\n|{#endif $key})";

				if ( !empty( $value ) ) {
					$regex = "({#if $key}\n|{#if $key}\r\n|{#if $key}|{#endif $key}\n|{#endif $key}\r\n|{#endif $key})";
					$string = preg_replace( '/' . $regex . '/', '', $string );
				}

				$string = preg_replace( '/' . $regex . '/', '', $string );
			}

			return $string;
		}

		/**
		 * Process the name of a file
		 *
		 * @param $filename
		 *
		 * @return string|string[]
		 */
		function process_filename( $filename ) {

			//do replacements in the filename
			$filename = $this->process_string( $filename );

			//rename .php.txt files to .php
			$filename = str_replace( '.php.txt', '.php', $filename );

			$filename = apply_filters( 'FooPlugins\Generator\Admin\BoilerplateProcessor\ProcessFilename', $filename, $this );

			return $filename;
		}

		/**
		 * Get a field value from the state
		 *
		 * @param $key
		 *
		 * @return bool|mixed
		 */
		function get_field_value( $key ) {
			if ( isset( $this->variables[ $key ] ) ) {
				return $this->variables[ $key ];
			}

			return '';
		}
	}
}
