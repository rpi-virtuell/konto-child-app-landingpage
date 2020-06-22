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

function rw_check_requirements() {
	$class = 'notice notice-error';
	$message = '';

	if ( !is_plugin_active( 'advanced-nocaptcha-recaptcha/advanced-nocaptcha-recaptcha.php' ) ) {
		$message .= 'Das <a href="plugins.php?s=advanced-nocaptcha-recaptcha">PluginAdvanced noCaptcha & invisible Captcha</a> ist nicht aktiv!<br>';
	}

	if ( !is_plugin_active( 'dsgvo-all-in-one-for-wp-pro/dsgvo_all_in_one_wp_pro.php' ) ) {
		$message .= 'Das Plugin <a href="plugins.php?s=dsgvo-all-in-one-for-wp-pro">DSGVO All in one PRO for WP</a> ist nicht aktiv!<br>';
	}

	if ( !is_plugin_active( 'wp-user-manager/wp-user-manager.php' ) ) {
		$message .= 'Das Plugin <a href="plugins.php?s=wp-user-manager">WP User Manager</a> ist nicht aktiv!<br>';
	}

	if(!empty($message))

		printf( '<div class="%1$s">
				<h3><strong>System-Sicherheit gefährdet! Fehlende Abhängigkeiten:</strong></h3>
				<p>%2$s</p>
				</div>', esc_attr( $class ),  $message ) ;
}
add_action( 'admin_notices', 'rw_check_requirements' );


// recatcha in das Login und Registrierungsformular von WP Usermanager integrieren
add_action('wpum_before_submit_button_login_form', 'rw_display_google_recaptcha', 20);
add_action('wpum_before_submit_button_registration_form', 'rw_display_google_recaptcha');

function rw_display_google_recaptcha(){
	echo '<div id="custom_google_recaptcha" style="hight:78px;overflow:hidden;">';
	echo do_shortcode( '[anr-captcha]' );
	echo '</div>';
}

// recatcha ausblenden wenn $_COOKIE ["privacy_allow_recaptcha"] nicht gesetzt wurde
add_filter( 'anr_get_option', 'rw_check_option_against_privacy', 10,4);
function rw_check_option_against_privacy($value, $option, $default, $is_default ){
	if($option == 'captcha_version'	 &&  $value == 'v2_checkbox'  && !isset($_COOKIE ["privacy_allow_recaptcha"]) ){
		return false;
	}
	return $value;
}
//funktioniert nicht, weil eine leere Rückgabe erfolgt. Plugin Bug?
function manipulate_get_service_police(){

	if( isset($_POST['action']) && $_POST['action']=='dsgvoaio_get_service_policy' && isset($_POST['key']) && $_POST['key'] == 'customservice1' ){
		$_POST['key'] = 'recaptcha';
	}
}
add_action('admin_init', 'manipulate_get_service_police');


add_filter( 'submit_wpum_form_validate_fields' , 'rw_captcha_verify', 1,5);

function rw_captcha_verify($is_valid, $wpum_fields, $values, $form_name, $wpum_obj){

	if(!anr_verify_captcha() && ( isset($_POST["submit_login"]) || isset($_POST["submit_registration"]) )){

		return new WP_Error( 'recaptcha-validation-error', rw_get_google_recaptia_allowness() );
		return false;
	}
	return $is_valid;
}

function rw_get_google_recaptia_allowness(){
	return 'Um sich anmelden oder registrieren zu können, müssen Sie der Verwendung des <a href="https://konto.rpi-virtuell.de/datenschutz/#recaptcha">Google reCaptcha</a> Dienstes <a class="actionbutton" id="customservice1" onclick="tarteaucitron.userInterface.respond(this, true); setTimeout(function(){location.reload()},1000);">akzeptieren</a>.';
}

function rw_print_remember_terms(){

	echo '<p>Mit der Anmeldung bei rpi-virtuell haben Sie sich mit den <a href="/nutzungsbedingungen">Nutzungsbestimmungen</a> 
			einverstanden erklärt und die <a href="/datenschutz">Datenschutzerklärung</a> akzeptiert.</p>';
}
add_action('wpum_before_submit_button_login_form', 'rw_print_remember_terms');

//Ausgabe des Privacy Policy Textes in den Serviceeinstellungen von DSGVO All in One
add_filter('option_dsdvo_recaptcha_policy', 'rw_get_dsdvo_recaptcha_policy');
function rw_get_dsdvo_recaptcha_policy(){
	return '
		<h3>Google reCAPTCHA</h3>
		<p>Um sich anmelden oder registrieren zu können, müssen Sie der Verwendung des reCaptcha Dienstes akzeptieren. Der Service reCAPTCHA von Google Inc dient vor allem zur Unterscheidung, ob die Eingabe in Formulare durch eine natürliche Person erfolgt oder missbräuchlich durch maschinelle und automatisierte Verarbeitung.</p>
		<p> Welche Daten von Google erfasst werden und wofür diese Daten verwendet werden, können Sie auf <a href="https://policies.google.com/privacy?hl=de-DE" target="_blank" rel="noopener nofollow" class="external">hier</a> nachlesen.<br />
		Weitere Informationen finden Sie in den <a href="https://policies.google.com/terms?hl=de-DE" target="_blank" rel="noopener nofollow" class="external">Nutzungsbedingungen für Dienste und Produkte von Google</a>.</p>
		</p>
		<b>Cookies von Google reCAPTCHA</b>
		<table style="width:100%">
		  <tr>
			<th>Name</th>
			<th>Zweck</th>
			<th>Gültigkeit</th>
		  </tr>
		  <tr>
			<td>NID</td>
			<td>Diese Cookies werden verwendet, um zwischen Menschen und Bots zu unterscheiden.</td>
			<td>variabel</td>
		  </tr>
		  <tr>
			<th colspan="3">Cookie für Datenschutzeinstellung von '. get_bloginfo( 'name' ).'</th>
		  </tr>
		  <tr>
			<td>privacy_allow_recaptcha</td>
			<td>Dieses Cookie wird gesetzt, sobald die Verwendung des Google reCaptcha Dienstes akzeptiert wird</td>
			<td>variabel</td>
		  </tr>
		</table>
		
		';

}

add_action('wp_head', 'rw_add_some_javascripts_for_handle_google_recaptcha_privacy');

function rw_add_some_javascripts_for_handle_google_recaptcha_privacy(){?>
	<script>
        function setPrivacyCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            var expires = "expires="+ d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function removeGoogleRecaptcha(){
            jQuery(document).ready( function($){

                $('.grecaptcha-badge').remove();

                var deactivated = '<div id="recaptcha_deactivated"><div>' +
                    '<b>Google reCaptcha</b> ist deaktiviert. ' +
                    '<br>' +
                    '<a class="actionbutton" id="customservice1" onclick="tarteaucitron.userInterface.respond(this, true); setTimeout(function(){location.reload()},1000);">Nutzung akzeptieren</a>' +
                    '<em>  ' +
                    '<a href="#recaptcha"  onclick="tarteaucitron.userInterface.openPanel();tarteaucitron.userInterface.dsgvoaio_open_details(\'customservice1\');" >Datenschutzinfos</a>' +
                    '</em>' +
                    '</div></div>';

                $('#custom_google_recaptcha').html(deactivated);

                $('#node_recaptcha_login').html(deactivated);
                $('#node_recaptcha_register').html(deactivated);
                $('#node_recaptcha_login').css('display', 'block');
                $('#node_recaptcha_register').css('display', 'block');

                $('.g-recaptcha').html(deactivated);
                $('.g-recaptcha').removeClass('g-recaptcha')
                setPrivacyCookie('privacy_allow_recaptcha', '',-100);


                $('#submit_registration').attr( "disabled", true);
                $('input[name="submit_login"]').attr( "disabled", true );
                $('input[name="submit_login"]').css( "background-color", '#ccc' );
                $('input[name="submit_registration"]').attr( "disabled", true );
                $('input[name="submit_registration"]').css( "background-color", '#ccc' );



            });

        }


        jQuery(document).ready( function($){

            function loadTarteaucitron(){

                var waitForLoad = function () {
                    if (typeof tarteaucitron != "undefined") {
                        console.log("tarteaucitron loaded..", tarteaucitron.reloadThePage);
                        $('.switch_customservice1').on('change', function(){
                            console.log("reloadThePage = true");
                            tarteaucitron.reloadThePage = true;
                        })
                    } else {
                        console.log("tarteaucitron not loaded..");
                        window.setTimeout(waitForLoad, 500);
                    }
                };
                window.setTimeout(waitForLoad, 500);
            };
            loadTarteaucitron();
        })
	</script>

	<?php
}
add_action('delete_user', 'rw_do_not_delete_user',10,2);
function rw_do_not_delete_user($id, $reassign){

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
		wp_redirect($register_page);
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
		$_SESSION['service'] = $_GET['ref_service'];
	}
	if (!isset($_SESSION['redirect_to']) && isset($_GET['redirect_to'])) {


		$_SESSION['redirect_to'] = $_GET['redirect_to'];



	}
	//die($_SESSION['redirect_to']);
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
	$item->classes = array();
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

        $_SESSION['service_link']='<p>Zurück zu <a href="'.$host.'">'.$host.'</a>';

		// only if frontpage
        if(! is_admin() ){
	        $items[] = _konto_nav_menu_item( $host , ''.$_SESSION['service'], 1 );
        }


	}

	return $items;
}



function wpum_username_check($pass, $fields, $values, $form ) {
	if( $form == 'login' || $form == 'profile' ) {
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
add_filter( 'submit_wpum_form_validate_fields', 'wpum_username_check', 10, 4 );

