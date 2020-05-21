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
		var $slug = '';

		function __construct( $args = null ) {

			$defaults = array(
				'name'                 => '',
				'source_directory'     => '',
				'process_extensions'   => array( 'php', 'css', 'js', 'txt', 'md', ),
				'zip_root_directory'   => '',
				'zip_temp_directory'   => null,
				'download_filename'    => '',
				'exclude_directories'  => array( '.git', '.svn', '.', '..', ),
				'exclude_files'        => array( '.git', '.svn', '.DS_Store', '.gitignore', '.', '..', 'foogen_boilerplate.php', 'foogen_include.php' ),
				'filename_filter'      => null,
				'file_contents_filter' => null,
				'post_process_action'  => null,
				'variables'            => array(),
			);

			$this->options = wp_parse_args( $args, $defaults );

			//check required args
			if ( empty($this->options['name']) ) {
				throw new Exception( 'BoilerplateZipGenerator class requires a name in order to function!' );
			}

			$this->slug = sanitize_title_with_dashes( $this->options['name'] );

			$this->options['zip_root_directory'] = $this->process_string( $this->options['zip_root_directory'] );

			$this->options['download_filename'] = empty($this->options['download_filename']) ? "{$this->slug}.zip" : $this->options['download_filename'];
			$this->options['download_filename'] = $this->process_string( $this->options['download_filename'] );

			$this->options['zip_temp_filename'] = trailingslashit( $this->options['zip_temp_directory'] ) . sprintf( '%s-%s.zip', $this->slug, md5( print_r( $this->options['variables'], true ) ) );
			$this->options['zip_temp_filename'] = $this->process_string( $this->options['zip_temp_filename'] );
		}

		/**
		 * Creates the new zip file based on the source_directory
		 */
		function generate() {
			$zip = new ZipArchive;

			$zip->open( $this->options['zip_temp_filename'], ZipArchive::CREATE && ZipArchive::OVERWRITE );

			$source_path = realpath( $this->options['source_directory'] );

			$iterator = new RecursiveDirectoryIterator( $source_path );
			foreach ( new RecursiveIteratorIterator( $iterator ) as $filename ) {

				$base_filename = basename( $filename );

				if ( strpos( $base_filename, 'foogen_' ) === 0) {
					//we are dealing with a special file e.g. "foogen_include.php"
					$special_file_type = basename( str_replace( 'foogen_', '', $base_filename ), '.php' );
					do_action( "FooPlugins\Generator\Admin\BoilerplateZipGenerator\ProcessSpecialFile\{$special_file_type}", $filename, $zip, $this );
				}

				if ( in_array( $base_filename, $this->options['exclude_files'] ) ) {
					continue;
				}

				foreach ( $this->options['exclude_directories'] as $directory ) {
					if ( strstr( $filename, "/{$directory}/" ) ) {
						continue 2;
					}
				} // continue the parent foreach loop

				$zip_filepath = $filename->getRealPath();

				$zip_filename = ltrim( str_replace( $source_path, '', $zip_filepath ), '\\' );

				$zip_filename = $this->process_filename( $zip_filename );

				$contents = $this->process_file_contents( file_get_contents( $filename ), $base_filename );

				$zip->addFromString( trailingslashit( $this->options['zip_root_directory'] ) . $zip_filename, $contents );
			}

			do_action( "FooPlugins\Generator\Admin\BoilerplateZipGenerator\PostProcess\{$this->slug}", $zip, $this );

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

			$contents = apply_filters( "FooPlugins\Generator\Admin\BoilerplateZipGenerator\ProcessFileContents\{$this->slug}", $contents, $filename, $this );

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

			$filename = apply_filters( "FooPlugins\Generator\Admin\BoilerplateZipGenerator\ProcessFilename\{$this->slug}", $filename, $this );

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
