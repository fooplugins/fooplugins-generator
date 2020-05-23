<?php
namespace FooPlugins\Generator\Admin;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * Zip File Generator Class for WordPress
 */
if ( ! class_exists( 'FooPlugins\Generator\Admin\BoilerplateZipGenerator' ) ) {

	class BoilerplateZipGenerator {

		var $options = array();

		function __construct( $boilerplate_object, $boilerplate_state, $zip_temp_directory = null) {
			//first, calculate all variables from the state
			$variables = array();
			foreach( $boilerplate_state as $key => $item ) {
				$variables['{' . $key . '}'] = $item;
			}
			$this->options['variables'] = $variables;

			if ( empty( $zip_temp_directory ) ) {
				$upload_dir = wp_upload_dir();
				$zip_temp_directory = $upload_dir['path'];
			}

			//The name of the file that is downloaded
			$download_filename = isset( $boilerplate_object['download_filename'] ) ? $boilerplate_object['download_filename'] : $boilerplate_object['name'] . '.zip';

			//The file extensions to process within the boilerplate directory
			$process_extensions = isset( $boilerplate_object['process_extensions'] ) ? $boilerplate_object['process_extensions'] : array( 'php', 'css', 'js', 'txt', 'md' );

			//The filename of the temp zip we will be creating
			$zip_temp_filename = trailingslashit( $zip_temp_directory ) . sprintf( '%s-%s.zip', $boilerplate_object['name'], md5( print_r( $variables, true ) ) );

			//The root directory used within the created zip
			$zip_root_directory = isset( $boilerplate_object['zip_root_directory'] ) ? $boilerplate_object['zip_root_directory'] : $boilerplate_object['name'];

			$this->options = array_merge( $this->options, array(
				'process_extensions'   => $process_extensions,
				'download_filename'    => $this->process_string( $download_filename ),
				'zip_root_directory'   => $this->process_string( $zip_root_directory ),
				'zip_temp_filename'    => $this->process_string( $zip_temp_filename ),
				'boilerplate'          => $boilerplate_object,
				'exclude_directories'  => array( '.git', '.svn', '.', '..', ),
				'exclude_files'        => array( '.git', '.svn', '.DS_Store', '.gitignore', '.', '..', 'foogen_boilerplate.php', 'foogen_include.php' ),
			) );
		}

		/**
		 * Creates the new zip file based on the boilerplate path
		 */
		function generate() {
			$zip = new ZipArchive;

			$zip->open( $this->options['zip_temp_filename'], ZipArchive::CREATE && ZipArchive::OVERWRITE );

			$source_path = realpath( $this->options['boilerplate']['path'] );

			do_action( "FooPlugins\Generator\Admin\BoilerplateZipGenerator\PreProcess", $source_path, $zip, $this );

			$iterator = new RecursiveDirectoryIterator( $source_path );
			foreach ( new RecursiveIteratorIterator( $iterator ) as $filename ) {

				$base_filename = basename( $filename );

				if ( strpos( $base_filename, 'foogen_' ) === 0) {
					//we are dealing with a special file e.g. "foogen_special.php"
					$special_file_type = basename( str_replace( 'foogen_', '', $base_filename ), '.php' );
					do_action( "FooPlugins\Generator\Admin\BoilerplateZipGenerator\ProcessSpecialFile\{$special_file_type}", $filename, $zip, $this );
				}

				//check if we need to exclude files
				if ( in_array( $base_filename, $this->options['exclude_files'] ) ) {
					continue;
				}

				//check if we need to exclude directories
				foreach ( $this->options['exclude_directories'] as $directory ) {
					if ( strstr( $filename, "/{$directory}/" ) ) {
						continue 2;// continue the parent foreach loop
					}
				}

				$zip_filepath = $filename->getRealPath();

				$zip_filename = ltrim( str_replace( $source_path, '', $zip_filepath ), '\\' );

				$zip_filename = $this->process_filename( $zip_filename );

				$contents = $this->process_file_contents( file_get_contents( $filename ), $base_filename );

				$zip->addFromString( trailingslashit( $this->options['zip_root_directory'] ) . $zip_filename, $contents );
			}

			do_action( "FooPlugins\Generator\Admin\BoilerplateZipGenerator\PostProcess", $zip, $this );

			$zip->close();
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
			$valid_extensions_regex = implode( '|', $this->options['process_extensions'] );
			if ( ! preg_match( "/\.({$valid_extensions_regex})$/", $filename ) ) {
				return $contents;
			}

			$contents = $this->process_string( $contents );

			$contents = apply_filters( 'FooPlugins\Generator\Admin\BoilerplateZipGenerator\ProcessFileContents', $contents, $filename, $this );

			return $contents;
		}

		/**
		 * Process a sting with the variables
		 * @param $string
		 *
		 * @return string|string[]|null
		 */
		function process_string( $string ) {
			foreach ( $this->options['variables'] as $key => $value ) {
				$string = preg_replace( '/(' . $key . ')/', $value, $string );
			}

			return $string;
		}

		/**
		 * Process the name of the zip file
		 *
		 * @param $filename
		 *
		 * @return string|string[]
		 */
		function process_filename( $filename ) {

			//do replacements in the filename
			$filename = $this->process_string( $filename );

			//rename php files
			$filename = str_replace( '.php.txt', '.php', $filename );

			$filename = apply_filters( 'FooPlugins\Generator\Admin\BoilerplateZipGenerator\ProcessFilename', $filename, $this );

			return $filename;
		}

		/**
		 * Send the download headers to the browser
		 * @param bool $delete
		 */
		function send_download_headers( $delete = true ) {
			header( 'Pragma: public' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			header( 'Cache-Control: public' );
			header( 'Content-Description: File Transfer' );
			header( 'Content-type: application/octet-stream' );
			header( sprintf( 'Content-Disposition: attachment; filename="%s"', $this->options['download_filename'] ) );
			header( 'Content-Transfer-Encoding: binary' );

			ob_clean();
			flush();

			@readfile( $this->options['zip_temp_filename'] );
			if ( $delete ) {
				@unlink( $this->options['zip_temp_filename'] );
			}
		}
	}
}
