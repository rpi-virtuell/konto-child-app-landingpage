<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package App_Landing_Page
 */

    /**
     * After Content
     * 
     * @hooked app_landing_page_content_end - 20
    */
    do_action( 'app_landing_page_after_content' );
    

    /**
     * App Landing Page Footer
     * 
     * @hooked app_landing_page_footer_start  - 20
     * @hooked app_landing_page_footer_widgets   - 30
     * @hooked app_landing_page_footer_credit - 40
     * @hooked app_landing_page_footer_end    - 50
	  
	  do_action( 'app_landing_page_footer' ); 
    */
	app_landing_page_footer_start();
	app_landing_page_footer_widgets();
	do_action( 'rpi_page_footer' ); 
	app_landing_page_footer_end();
	
	
    
    /**
	 * After Footer
     * 
     * @hooked app_landing_page_page_end - 20
	 */
    do_action( 'app_landing_page_page_end' );
    

wp_footer(); ?>

</body>
</html>
