<?php
/**
 * Banner Section
 * 
 * @package App_Landing_Page
 */
$app_landing_page_banner_button_one     = get_theme_mod( 'app_landing_page_banner_button_one' );
$app_landing_page_banner_button_one_url = get_theme_mod( 'app_landing_page_banner_button_one_url' );
$app_landing_page_banner_button_two     = get_theme_mod( 'app_landing_page_banner_button_two' );
$app_landing_page_banner_button_two_url = get_theme_mod( 'app_landing_page_banner_button_two_url' );
$app_landing_page_banner_button_text    = get_theme_mod( 'app_landing_page_banner_button_text', __( 'Download Now', 'app-landing-page' ) );
$app_landing_page_banner_button_url     = get_theme_mod( 'app_landing_page_banner_button_url', '#' );
if( have_posts() ){                
    while( have_posts() ){
?>
	<section class="banner" id="banner" <?php if( has_post_thumbnail() ){ ?> style="background: url(' <?php the_post_thumbnail_url(); ?> ')no-repeat; background-size: cover; background-position: center;" <?php } ?> >
		<div class="container">
			<div class="banner-text">
				<?php 
				     the_post();
				     the_title( '<strong class="title" itemprop="name">', '</strong>' ); 
				     the_content();
		    	?>
		     
				<div class="appstrore-holder">
					<?php 
					if( $app_landing_page_banner_button_one_url ) echo '<a href="' . esc_url( $app_landing_page_banner_button_one_url ) . '" class="app-store" target="_blank"><img src="' . esc_html( $app_landing_page_banner_button_one ) . '" alt="" ></a>';
					if( $app_landing_page_banner_button_two_url ) echo '<a href="' . esc_url( $app_landing_page_banner_button_two_url ) . '" class="android-market" target="_blank"><img src="' . esc_html( $app_landing_page_banner_button_two ). '" alt="" ></a>';
					?>
				</div>

				<?php
					if( $app_landing_page_banner_button_url ) echo '<a href="' . esc_url( $app_landing_page_banner_button_url ) . '" class="btn-download" target="_blank">'. esc_html( $app_landing_page_banner_button_text ) . '</a>';
				?>
			</div>
		</div>
	</section>
<?php
	}
} 
echo '<div class="site-content home-site-content" id="content">';