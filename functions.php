<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
	function chld_thm_cfg_locale_css( $uri ){
		if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
			$uri = get_template_directory_uri() . '/rtl.css';
		return $uri;
	}
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
	function chld_thm_cfg_parent_css() {
		wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'animate.light' ) );
	}
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );

// END ENQUEUE PARENT ACTION
/**
 * footer anpassen
 */
function rpi_footer_credit(){

	$copyright_text = get_theme_mod( 'app_landing_page_footer_copyright_text' );
	echo '<div class="site-info">';
	if( $copyright_text ){
		echo wp_kses_post( $copyright_text );
		echo ' &nbsp; | &nbsp; ';
	}

	echo '<a href="'.home_url('/impressum/').'">Impressum</a>';
	echo ' &nbsp; | &nbsp; ';
	if ( function_exists( 'the_privacy_policy_link' ) ) {
		the_privacy_policy_link();
	}
	echo ' &nbsp; | &nbsp; ';
	echo '<a href="'.home_url('/cookies/').'">Cookie Richtlinie</a>';
	echo '</div>';
}
add_action('rpi_page_footer','rpi_footer_credit' );
/**
 * nervige Werbung des parenthemes abchalten
 */
add_filter( 'tgmpa_load', function(){return false;} );

/**
 * Die folgenden Funktionen verhindern das Laden von Google recaptcha
 * Es benötigt die aktivierten Plugins:
 * https://de.wordpress.org/plugins/wp-user-manager/
 * https://wordpress.org/plugins/advanced-nocaptcha-recaptcha/
 * DSGVO All in one PRO for WP (https://dsgvo-for-wp.com/)
 *   dort muss  unter Eigene Dienste der erste #1 (customservice1)
 *   unter der Categorie API
 *   recaptcha (Google reCaptcha) angelegt sein
 *
 */




add_action('delete_user', 'rw_do_not_delete_user',10,2);
function rw_do_not_delete_user($id, $reassign){

    if(current_user_can('manage_options')){
       return;
    }
	//h$EnB&7^rl2CRz@RsozHf6Fw

	$user_data = wp_update_user( array( 'ID' => $id, 'user_email' => 'deleted_'.$id.'@deleted.del', 'user_pass' => wp_generate_password( 20, true, true ) ) );
	if ( is_wp_error( $user_data ) ) {
		// There was an error; possibly this user doesn't exist.
		echo 'Error.';
	} else {
		// Success!
		echo 'Der User wurde erfolgreich gelöscht.';
	}
	$reassign = 'yes';
	$id =0;
	wp_logout() ;
	wp_redirect( '/' );
	exit;

}

add_action ('wp_head', 'rw_check_user_role');
function rw_check_user_role(){


	$user = wp_get_current_user();

	if($user->ID > 0 && count($user->roles)<1){

		$user->set_role('subscriber');
	}

}


add_action('init','rw_redirect_login_page');
function rw_redirect_login_page() {


	$login_page  = home_url('/anmelden/');
	$register_page = home_url('/registrieren/');
	$page_viewed = basename($_SERVER['REQUEST_URI']);
	if($_REQUEST['action'] == 'register'){
		$querystring = '';
		if(isset($_GET['ref_service'])){
			$querystring = '?ref_service='. $_GET['ref_service'];
		}

		wp_redirect($register_page.$querystring);
		exit;
	}

	if($page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET' ) {


		$url = isset($_GET['redirect_to'])?$_GET['redirect_to']:false;
		if($url !==false ){

			$url= urldecode($url);

			$uri = parse_url($url);

			if (
				$uri['host'] == $_SERVER['HTTP_HOST'] &&
				strpos($_SERVER['QUERY_STRING'],'https://'.$uri['host'].'/wp-cas/')>0
			)
			{
				return;
			}
		}
		wp_redirect($login_page);
		exit;
	}
}
add_action('init','konto_rw_start_session');
add_action('login_form_top','konto_rw_start_session');

function konto_rw_start_session() {

	// Check for an active session: if there is none, start one.
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	// Check for referrer and store in session.
	// If there is no referrer, assign to user zero.
	if (!isset($_SESSION['ref_service']) && isset($_GET['ref_service'])) {

		$_SESSION['service'] = urldecode($_GET['ref_service']);
	}
	if (!isset($_SESSION['redirect_to']) && isset($_GET['redirect_to'])) {

		$_SESSION['redirect_to'] = urldecode($_GET['redirect_to']);
	}


	//die($_SESSION['redirect_to']);
}

add_action('login_init','login_form_add_design_tweaks');

function login_form_add_design_tweaks(){
	wp_enqueue_style('loginstyles', get_stylesheet_directory_uri().'/css/login.css' );
}


add_action('user_register', 'konto_rw_update_user_referrer', 10, 1);

function konto_rw_update_user_referrer($user_id){

	$url = wp_parse_url( $_SESSION['service']);
	update_user_meta($user_id, 'konto_register_referrer_host', $url['host']);


}
add_action('wp_login', 'konto_rw_update_user_redircet', 10, 2);

function konto_rw_update_user_redircet( $user_login, $user){

	$user_id = $user->ID;

	$split = explode('/wp-cas/login?service=', $_SESSION['redirect_to']);

	if(isset($split[1])){
	    $url = urldecode($split[1]);
	}

	$parsed = wp_parse_url($url);

	$myhosts = get_user_meta($user_id, 'konto_hosts' , true);

	if($myhosts){
		$myhosts = json_decode($myhosts,true);
    }else{
		$myhosts = array();
    }

	$myhosts[$parsed['host']]='x';



	update_user_meta($user_id, 'konto_last_redir' ,  $url );

	update_user_meta($user_id, 'konto_hosts' ,  json_encode($myhosts) );

	unset($_SESSION['redirect_to']);

}


/**
 * Simple helper function for make menu item objects
 *
 * @param $title      - menu item title
 * @param $url        - menu item url
 * @param $order      - where the item should appear in the menu
 * @param int $parent - the item's parent item
 * @return \stdClass
 */
function _konto_nav_menu_item( $title, $url, $order, $parent = 0 ){
	$item = new stdClass();
	$item->ID = 1000000 + $order + parent;
	$item->db_id = $item->ID;
	$item->title = $title;
	$item->url = $url;
	$item->menu_order = $order;
	$item->menu_item_parent = $parent;
	$item->type = '';
	$item->object = '';
	$item->object_id = '';
	$item->classes = array('current_service','current_page_ancestor');
	$item->target = '';
	$item->attr_title = '';
	$item->description = '';
	$item->xfn = '';
	$item->status = '';
	return $item;
}
add_filter( 'wp_get_nav_menu_items', 'konto_nav_menu_items', 20, 2 );

function konto_nav_menu_items( $items, $menu ){

        // only add item to a specific menu
	if ( $menu->slug == 'main'  && isset($_SESSION['service'])){
		$url_parse = wp_parse_url($_SESSION['service']);


		$hostparts = explode('.', $url_parse['host'] );
		array_pop($hostparts);
		$m = count($hostparts)-1;
		$domain = $hostparts[$m];
		array_pop($hostparts);
		$name = ucfirst(implode('.' ,$hostparts));
        $host = $domain .' '.$name;

		$host = apply_filters('konto_refservice_name',$host);


		$_SESSION['service_link']='<p>Zurück zu <a href="'.$_SESSION['service'].'">'.$host.'</a>';



		// only if frontpage
        if(! is_admin() ){
	        $items[] = _konto_nav_menu_item( $host , ''.$_SESSION['service'], 1 );
        }


	}

	return $items;
}

add_filter('konto_refservice_name', function ($host){
	if($host == 'relilab My'){
		$host = 'relilab OER Werkstatt';
	}
	return $host;
});

function wpum_username_check($pass, $fields, $values, $form ) {

	//var_dump($form);

	if($form == 'login' || $form == 'profile' || $form == 'password-recovery'  || $form == 'password' ){
		return $pass;
	}
	$username = $values['register'][ 'username' ];

	if(preg_match('/[^a-z_\-0-9]/', $username)) {
			return new WP_Error( 'nickname-validation-error', __( 'This username cannot be used.', 'wp-user-manager' ) );
	}
	if ( strlen($username) < 5  ) {
		return new WP_Error( 'nickname-validation-error', __( 'Der Nutzername muss zwischen 5 und 25 Zeichen lang sein.', 'wp-user-manager' ) );
	}
	if ( strlen( $username) > 25 ) {
		return new WP_Error( 'nickname-validation-error', __( 'Der Nutzername muss zwischen 5 und 25 Zeichen lang sein.', 'wp-user-manager' ) );
	}
	return $pass;

}
//add_filter( 'submit_wpum_form_validate_fields', 'wpum_username_check', 10, 4 );

add_action("gform_user_registration_validation", "ignore_already_registered_error", 10, 3);
function ignore_already_registered_error($form, $config, $pagenum){



	// Make sure we only run this code on the specified form ID
	if($form['title'] != 'Registrierung') {
		return $form;
	}


	// Get the ID of the username field from the User Registration config
	//$email_id = $config['meta']['email'];
	$user_id = $config['meta']['username'];


	// Loop through the current form fields
	foreach($form['fields'] as &$field) {


		if($field->id == $user_id ){
			$entry = GFFormsModel::get_current_lead();
			$meta = rgar( $config, 'meta' );
			$username       = gf_user_registration()->get_meta_value( 'username', $meta, $form, $entry );

			$wp_user = get_user_by('login', $username);

			if($wp_user){
				$field->validation_message = "Dieser Username existiert bereits. Wähle bitte einen anderen.";
				$field->failed_validation = true;
            }

		    //$field->validation_message == 'This username is already registered')

           // var_dump($field->validation_message);


		}

	}

	return $form;

}

add_shortcode('password_forgotten_form',function ($atts){
	ob_start();
	include 'sections/password-forgotten.php';
	return ob_get_clean();
});
