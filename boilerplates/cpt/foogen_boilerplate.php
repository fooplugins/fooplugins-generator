<?php

return array(
	'name'        => 'cpt',
	'title'       => __( 'Custom Post Type Class Generator', 'foogen' ),
	'description' => __( 'Generates a class for a Custom Post Type readme.txt', 'foogen' ),
	'actions'     => 'show',
	'fields'      => array(
		'slug'         => array(
			'label'    => __( 'CPT Slug', 'foogen' ),
			'type'     => 'text',
			'default'  => __( 'product', 'foogen' ),
			'required' => true
		),
		'name'         => array(
			'label'    => __( 'CPT Name (Singular)', 'foogen' ),
			'type'     => 'text',
			'default'  => __( 'Product', 'foogen' ),
			'required' => true
		),
		'plural'         => array(
			'label'    => __( 'CPT Name (Plural)', 'foogen' ),
			'type'     => 'text',
			'default'  => __( 'Products', 'foogen' ),
			'required' => true
		),
		'namespace'         => array(
			'label'    => __( 'Plugin Namespace', 'foogen' ),
			'type'     => 'text',
			'default'  => __( 'FooPlugins\CoolThing', 'foogen' ),
			'desc'     => __( 'The base namespace for the class.', 'foogen' ),
			'required' => true
		),
		'textdomain'    => array(
			'label'    => __( 'Text Domain', 'foogen' ),
			'type'     => 'text',
			'desc'  => __( 'The text domain used in your plugin', 'foogen' ),
			'required' => true
		),
		'filename' => array(
			'source' => 'field',
			'field' => 'slug',
			'function' => '__foogen_filename',
		),
	)
);
