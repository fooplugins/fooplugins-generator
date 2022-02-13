<?php

return array(
	'name'        => 'cpt',
	'title'       => __( 'Custom Post Type Class Generator', 'foogen' ),
	'description' => __( 'Generates a class for a Custom Post Type', 'foogen' ),
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
		'plural'       => array(
			'label'    => __( 'CPT Name (Plural)', 'foogen' ),
			'type'     => 'text',
			'default'  => __( 'Products', 'foogen' ),
			'required' => true
		),
		'namespace'         => array(
			'label'    => __( 'Plugin Namespace', 'foogen' ),
			'type'     => 'text',
			'default'  => __( 'CoolPlugins\\CoolThing', 'foogen' ),
			'desc'     => __( 'What is the base namespace for the class? By default the class will use a namespace or CoolPlugins\\CoolThing\\PostTypes\\Product', 'foogen' ),
			'required' => true
		),
		'textdomain'   => array(
			'label'    => __( 'Text Domain', 'foogen' ),
			'type'     => 'text',
			'desc'     => __( 'The text domain used in your plugin.', 'foogen' ),
			'default'  => __( 'coolthing', 'foogen' ),
			'required' => true
		),
		'filename' => array(
			'source' => 'field',
			'field' => 'slug',
			'function' => '__foogen_filename',
		),
	)
);
