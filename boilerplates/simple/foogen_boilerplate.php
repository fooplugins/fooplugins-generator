<?php

return array(
	'name'        => 'simple',
	'title'       => __( 'Simple Plugin', 'foogen' ),
	'description' => __( 'A simple WordPress plugin including the bare minimum files in order to run. No pre-requisites needed to run before activation', 'foogen' ),
	'zip_root_directory' => '{filename}',
	'download_filename' => '{filename}.zip',
	'process_extensions' => array( 'php', 'css', 'js', 'txt' ),
	'fields'      => array(
		'name'       => array(
			'label' => __( 'Plugin Name', 'foogen' ),
			'type'  => 'text',
			'default' => __( 'Cool Thing', 'foogen' )
		),
		'desc'       => array(
			'label' => __( 'Plugin Description', 'foogen' ),
			'type'  => 'textarea',
			'default' => __( 'A cool description about what your cool thing can do', 'foogen' )
		),
		'author'     => array(
			'label' => __( 'Author', 'foogen' ),
			'type'  => 'text',
			'default' => wp_get_current_user()->display_name
		),
		'author_url' => array(
			'label' => __( 'Author URL', 'foogen' ),
			'type'  => 'text',
			'default' => site_url()
		),
		'slug' => array(
			'source' => 'field',
			'field' => 'name',
			'function' => '__foogen_slugify',
		),
		'filename' => array(
			'source' => 'field',
			'field' => 'name',
			'function' => '__foogen_convert_to_filename',
		),
		'class' => array(
			'source' => 'field',
			'field' => 'name',
			'function' => '__foogen_convert_to_class',
		),
		'constant' => array(
			'source' => 'field',
			'field' => 'name',
			'function' => '__foogen_convert_to_constant',
		)
	)
);