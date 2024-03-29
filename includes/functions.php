<?php
/**
 * Contains all the Global common functions used throughout the plugin
 */

use FooPlugins\Generator\Admin\BoilerplateLoader;

/**
 * Custom Autoloader used throughout plugin
 *
 * @param $class
 */
function foogen_autoloader( $class ) {
	/* Only autoload classes from this namespace */
	if ( false === strpos( $class, FOOGEN_NAMESPACE ) ) {
		return;
	}

	/* Remove namespace from class name */
	$class_file = str_replace( FOOGEN_NAMESPACE . '\\', '', $class );

	/* Convert sub-namespaces into directories */
	$class_path = explode( '\\', $class_file );
	$class_file = array_pop( $class_path );
	$class_path = strtolower( implode( '/', $class_path ) );

	/* Convert class name format to file name format */
	$class_file = foogen_uncamelize( $class_file );
	$class_file = str_replace( '_', '-', $class_file );
	$class_file = str_replace( '--', '-', $class_file );

	/* Load the class */
	require_once FOOGEN_DIR . '/includes/' . $class_path . '/class-' . $class_file . '.php';
}

/**
 * Convert a CamelCase string to camel_case
 *
 * @param $str
 *
 * @return string
 */
function foogen_uncamelize( $str ) {
	$str    = lcfirst( $str );
	$lc     = strtolower( $str );
	$result = '';
	$length = strlen( $str );
	for ( $i = 0; $i < $length; $i ++ ) {
		$result .= ( $str[ $i ] == $lc[ $i ] ? '' : '_' ) . $lc[ $i ];
	}

	return $result;
}

/**
 * Safe way to get value from an array
 *
 * @param $key
 * @param $array
 * @param $default
 *
 * @return mixed
 */
function foogen_safe_get_from_array( $key, $array, $default ) {
	if ( is_array( $array ) && array_key_exists( $key, $array ) ) {
		return $array[ $key ];
	} else if ( is_object( $array ) && property_exists( $array, $key ) ) {
		return $array->{$key};
	}

	return $default;
}

/**
 * Safe way to get value from the request object
 *
 * @param $key
 *
 * @return mixed
 */
function foogen_safe_get_from_request( $key ) {
	return foogen_safe_get_from_array( $key, $_REQUEST, null );
}

/**
 * Gets all boilerplates
 *
 * @return mixed|void
 */
function foogen_get_all_boilerplates() {
	global $foogen_boilerplates;

	if ( isset( $foogen_boilerplates ) ) {
		return $foogen_boilerplates;
	}

	//get the default boilerplates built into the plugin
	$loader = new BoilerplateLoader();
	$foogen_boilerplates = $loader->load_plugin_boilderplates();

	//TODO : get the boilerplates saved in the current theme location

	$foogen_boilerplates = apply_filters( 'FooPlugins\Generator\Admin\GetAllBoilerplates', $foogen_boilerplates );

	return $foogen_boilerplates;
}

/**
 * Converts a string to something that can be used safely for a function name
 *
 * @param $value
 *
 * @return string
 */
function __foogen_function( $value ) {
	return strtolower( trim( preg_replace( '/[^A-Za-z0-9-]+/', '_', $value ) ) );
}

/**
 * Converts a string to something that can be used safely for a file name
 *
 * @param $value
 *
 * @return string
 */
function __foogen_filename( $value ) {
	return strtolower( trim( preg_replace( '/[^A-Za-z0-9-]+/', '-', $value ) ) );
}

/**
 * Converts a string to something that can be used safely for a PHP constant name
 *
 * @param $value
 *
 * @return string
 */
function __foogen_constant( $value ) {
	return strtoupper( trim( preg_replace( '/[^A-Za-z0-9-]+/', '_', $value ) ) );
}

/**
 * Converts a string to something that can be used safely for a PHP class name
 *
 * @param $value
 *
 * @return string
 */
function __foogen_class( $value ) {
	return str_replace( ' ', '_', ucwords( str_replace( array('-', '_'), ' ', $value ) ) );
}

/**
 * Slugify a string
 *
 * @param $value
 *
 * @return string
 */
function __foogen_slugify( $value ) {
	return sanitize_title_with_dashes( $value );
}

/**
 * Uppercase a string
 *
 * @param $value
 *
 * @return string
 */
function __foogen_uppercase( $value ) {
	return strtoupper( $value );
}

/**
 * Lowercase a string
 *
 * @param $value
 *
 * @return string
 */
function __foogen_lowercase( $value ) {
	return strtolower( $value );
}

/**
 * Converts a string into a function name that can be used for the Freemius global function
 *
 * @param $value
 *
 * @return string
 */
function __foogen_freemius_function( $value ) {
	return __foogen_function( $value ) . '_fs';
}


/**
 * Adds a global message used on the generator form
 *
 * @param $message
 * @param bool $is_error
 */
function foogen_add_global_message( $message, $is_error = false ) {
	global $foogen_messages;

	if ( !isset( $foogen_messages ) ) {
		$foogen_messages = array();
	}

	$foogen_messages[] = array(
		'message' => $message,
		'error' => $is_error
	);
}

/**
 * Gets all global message used on the generator form
 */
function foogen_get_global_messages() {
	global $foogen_messages;

	if ( !isset( $foogen_messages ) ) {
		$foogen_messages = array();
	}

	return $foogen_messages;
}
