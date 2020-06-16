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
				$this->variables['{' . $key . '}'] = $item;
			}
		}

		/**
		 * Process a sting with the variables
		 * @param $string
		 *
		 * @return string|string[]|null
		 */
		function process_string( $string ) {
//			$phpStr = LightnCandy::compile( $string );
//
//			// Quick and deprecated way to get render function
//			$renderer = LightnCandy::prepare($phpStr);
//
//			$return = $renderer( $this->variables );
//
//			return $return;

			foreach ( $this->variables as $key => $value ) {
				$string = preg_replace( '/(' . $key . ')/', $value, $string );
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
	}
}
