<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package App_Landing_Page
 */

	/**
     * Doctype Hook
     * 
     * @hooked app_landing_page_doctype_cb
    */
    do_action( 'app_landing_page_doctype' );
?>

<head>

<?php 
    /**
     * Before wp_head
     * 
     * @hooked app_landing_page_head
    */
    do_action( 'app_landing_page_before_wp_head' );

    wp_head(); 
?>
</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

<?php
    wp_body_open();
    
    /**
     * Before Header
     * 
     * @hooked app_landing_page_page_start - 20 
    */
    do_action( 'app_landing_page_before_header' );
    
    /**
     * app Landing Page Header
     * 
     * @hooked app_landing_page_header_cb  - 20  
    */
    do_action( 'app_landing_page_header' );
    
    /**
     * Before Content
     * 
     * @hooked app_landing_page_page_header - 20
    */
    do_action( 'app_landing_page_before_content' );
    
			   
