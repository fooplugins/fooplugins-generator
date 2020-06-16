<?php

return array(
	'name'               => 'fooplugin',
	'title'              => __( 'FooPlugin', 'foogen' ),
	'description'        => __( 'A FooPlugin', 'foogen' ),
	'actions'       => 'download install',
	'zip_root_directory' => '{filename}',
	'download_filename'  => '{filename}.zip',
	'process_extensions' => array( 'php', 'css', 'js', 'txt' ),
	'includes'           => array(
		array(
			'directory' => 'freemius',
			'type'      => 'freemius',
			'source'    => 'https://github.com/Freemius/wordpress-sdk/zipball/master'
		)
	),
	'fields'             => array(
		'name'                => array(
			'label'   => __( 'Plugin Name', 'foogen' ),
			'type'    => 'text',
			'default' => __( 'Cool Thing', 'foogen' )
		),
		'desc'                => array(
			'label'   => __( 'Plugin Description', 'foogen' ),
			'type'    => 'textarea',
			'default' => __( 'A cool description about what your cool thing can do', 'foogen' )
		),
		'namespace'           => array(
			'label' => __( 'Plugin Namespace', 'foogen' ),
			'type'  => 'text',
			'desc'  => __( 'The root namespace of the plugin', 'foogen' )
		),
		'freemius_id'         => array(
			'label' => __( 'Freemius Plugin ID', 'foogen' ),
			'type'  => 'text',
			'desc'  => __( 'The ID of your Freemius plugin. You can find this in the Freemius Dashbaord.', 'foogen' )
		),
		'freemius_public_key' => array(
			'label'   => __( 'Freemius Public Key', 'foogen' ),
			'type'    => 'text',
			'default' => 'pk_abcdefghijklm1234567890',
			'desc'    => __( 'The plugin\'s 32-digit Freemius Public Key. You can find this in the Freemius Dashboard under the settings menu for your plugin.', 'foogen' )
		),
		'author'              => array(
			'label'   => __( 'Author', 'foogen' ),
			'type'    => 'text',
			'default' => wp_get_current_user()->display_name
		),
		'author_url'          => array(
			'label'   => __( 'Author URL', 'foogen' ),
			'type'    => 'text',
			'default' => site_url()
		),
		'slug'                => array(
			'source'   => 'field',
			'field'    => 'name',
			'function' => '__foogen_slugify',
		),
		'filename'            => array(
			'source'   => 'field',
			'field'    => 'name',
			'function' => '__foogen_convert_to_filename',
		),
		'class'               => array(
			'source'   => 'field',
			'field'    => 'name',
			'function' => '__foogen_convert_to_class',
		),
		'function'               => array(
			'source'   => 'field',
			'field'    => 'name',
			'function' => '__foogen_convert_to_function',
		),
		'constant'            => array(
			'source'   => 'field',
			'field'    => 'name',
			'function' => '__foogen_convert_to_constant',
		),
		'freemius_function' => array(
			'source' => 'field',
			'field' => 'name',
			'function' => '__foogen_freemius_function'
		)
	)
);
