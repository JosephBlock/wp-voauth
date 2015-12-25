<?php
/*
Plugin Name: V-Oauth
Description: V
Version: 1.4
Author: DisasterTrident
License: MIT
*/
session_start();

Class VOA
{
	const PLUGIN_VERSION = "1.3";
	protected static $instance = null;
	private $settings = array(
		'voa_show_login_messages'              => 0,
		'voa_login_redirect'                   => 'home_page',
		'voa_login_redirect_page'              => 0,
		'voa_login_redirect_url'               => '',
		'voa_logout_redirect'                  => 'home_page',
		'voa_logout_redirect_page'             => 0,
		'voa_logout_redirect_url'              => '',
		'voa_logout_inactive_users'            => 0,
		'voa_hide_wordpress_login_form'        => 0,
		'voa_logo_links_to_site'               => 0,
		'voa_logo_image'                       => '',
		'voa_bg_image'                         => '',
		'voa_login_form_show_login_screen'     => 'Login Screen',
		'voa_login_form_show_profile_page'     => 'Profile Page',
		'voa_login_form_show_comments_section' => 'None',
		'voa_login_form_designs'               => array(
			'Login Screen' => array(
				'icon_set'          => 'none',
				'layout'            => 'buttons-row',
				'align'             => 'center',
				'show_login'        => 'conditional',
				'show_logout'       => 'conditional',
				'button_prefix'     => 'Login with',
				'logged_out_title'  => 'Please login:',
				'logged_in_title'   => 'You are already logged in.',
				'logging_in_title'  => 'Logging in...',
				'logging_out_title' => 'Logging out...',
				'style'             => '',
				'class'             => '',
			),
			'Profile Page' => array(
				'icon_set'          => 'none',
				'layout'            => 'buttons-row',
				'align'             => 'left',
				'show_login'        => 'always',
				'show_logout'       => 'never',
				'button_prefix'     => 'Link',
				'logged_out_title'  => 'Select a provider:',
				'logged_in_title'   => 'Select a provider:',
				'logging_in_title'  => 'Authenticating...',
				'logging_out_title' => 'Logging out...',
				'style'             => '',
				'class'             => '',
			),
		),
		'voa_suppress_welcome_email'           => 1,
		'voa_new_user_role'                    => 'subscriber',
		'voa_v_api_enabled'                    => 0,
		'voa_v_api_id'                         => '',
		'voa_v_api_secret'                     => '',
		'voa_http_util'                        => 'curl',
		'voa_http_util_verify_ssl'             => 1,
		'voa_restore_default_settings'         => 0,
		'voa_delete_settings_on_uninstall'     => 0,
	);

	function __construct()
	{
		register_activation_hook(__FILE__, array($this, 'voa_activate'));
		register_deactivation_hook(__FILE__, array($this, 'voa_deactivate'));
		add_action('plugins_loaded', array($this, 'voa_update'));
		add_action('init', array($this, 'init'));

	}

	public static function get_instance()
	{
		null === self::$instance and self::$instance = new self;
		return self::$instance;
	}

	function voa_activate() { }

	function voa_deactivate() { }

	function voa_update()
	{
		$plugin_version = VOA::PLUGIN_VERSION;
		$installed_version = get_option("voa_plugin_version");
		if (!$installed_version || $installed_version <= 0 || $installed_version != $plugin_version) {
			$this->voa_add_missing_settings();
			update_option("voa_plugin_version", $plugin_version);
		}
	}

	function voa_add_missing_settings()
	{
		foreach ($this->settings as $setting_name => $default_value) {
			if (is_array($this->settings[$setting_name])) {
				$default_value = json_encode($default_value);
			}
			$added = add_option($setting_name, $default_value);
		}
	}

	function voa_restore_default_settings_notice()
	{
		$settings_link = "<a href='options-general.php?page=V-Oauth'>Settings Page</a>";
		?>
		<div class="updated">
			<p>The default settings have been restored. You may review the <?php echo $settings_link ?>.</p>
		</div>
		<?php
	}

	function init()
	{
		if (get_option("voa_restore_default_settings")) {
			$this->voa_restore_default_settings();
		}

		add_filter('query_vars', array($this, 'voa_qvar_triggers'));
		add_action('template_redirect', array($this, 'voa_qvar_handlers'));
		add_action('wp_enqueue_scripts', array($this, 'voa_init_frontend_scripts_styles'));
		add_action('admin_enqueue_scripts', array($this, 'voa_init_backend_scripts_styles'));
		add_action('admin_menu', array($this, 'voa_settings_page'));
		add_action('admin_init', array($this, 'voa_register_settings'));
		$plugin = plugin_basename(__FILE__);
		add_filter("plugin_action_links_$plugin", array($this, 'voa_settings_link'));
		add_action('login_enqueue_scripts', array($this, 'voa_init_login_scripts_styles'));
		if (get_option('voa_logo_links_to_site') == true) {
			add_filter('login_headerurl', array($this, 'voa_logo_link'));
		}
		add_filter('login_message', array($this, 'voa_customize_login_screen'));
		add_filter('comment_form_defaults', array($this, 'voa_customize_comment_form_fields'));
		add_filter('show_user_profile', array($this, 'show_v_info'));
		add_action('edit_user_profile', array($this, 'show_v_info'));
		add_action('show_user_profile', array($this, 'voa_linked_accounts'));
		add_filter('manage_users_columns', array($this, 'add_v_columns'));
		add_filter('manage_users_custom_column', array($this, 'add_v_column_data'), 10, 3);
		add_action('wp_logout', array($this, 'voa_end_logout'));
		add_action('wp_ajax_voa_logout', array($this, 'voa_logout_user'));
		add_action('wp_ajax_voa_unlink_account', array($this, 'voa_unlink_account'));
		add_action('wp_ajax_nopriv_voa_unlink_account', array($this, 'voa_unlink_account'));
		add_shortcode('voa_login_form', array($this, 'voa_login_form'));
		if (get_option('voa_show_login_messages') !== false) {
			add_action('wp_footer', array($this, 'voa_push_login_messages'));
			add_filter('admin_footer', array($this, 'voa_push_login_messages'));
			add_filter('login_footer', array($this, 'voa_push_login_messages'));
		}
	}

	function voa_restore_default_settings()
	{
		foreach ($this->settings as $setting_name => $default_value) {
			if (is_array($this->settings[$setting_name])) {
				$default_value = json_encode($default_value);
			}
			update_option($setting_name, $default_value);
		}
		add_action('admin_notices', array($this, 'voa_restore_default_settings_notice'));
	}

	function voa_init_frontend_scripts_styles()
	{
		$voa_jvars = array(
			'ajaxurl'               => admin_url('admin-ajax.php'),
			'template_directory'    => get_bloginfo('template_directory'),
			'stylesheet_directory'  => get_bloginfo('stylesheet_directory'),
			'plugins_url'           => plugins_url(),
			'plugin_dir_url'        => plugin_dir_url(__FILE__),
			'url'                   => get_bloginfo('url'),
			'logout_url'            => wp_logout_url(),
			'show_login_messages'   => get_option('voa_show_login_messages'),
			'logout_inactive_users' => get_option('voa_logout_inactive_users'),
			'logged_in'             => is_user_logged_in(),
		);
		wp_enqueue_script('voa-vars', plugins_url('/jvars.js', __FILE__));
		wp_localize_script('voa-vars', 'voa_jvars', $voa_jvars);
		wp_enqueue_script('jquery');
		wp_enqueue_script('voa-script', plugin_dir_url(__FILE__) . 'wp-voauth.js', array());
		wp_enqueue_style('voa-style', plugin_dir_url(__FILE__) . 'wp-voauth.css', array());
	}

	function voa_init_backend_scripts_styles()
	{
		$voa_jvars = array(
			'ajaxurl'               => admin_url('admin-ajax.php'),
			'template_directory'    => get_bloginfo('template_directory'),
			'stylesheet_directory'  => get_bloginfo('stylesheet_directory'),
			'plugins_url'           => plugins_url(),
			'plugin_dir_url'        => plugin_dir_url(__FILE__),
			'url'                   => get_bloginfo('url'),
			'show_login_messages'   => get_option('voa_show_login_messages'),
			'logout_inactive_users' => get_option('voa_logout_inactive_users'),
			'logged_in'             => is_user_logged_in(),
		);
		wp_enqueue_script('voa-vars', plugins_url('/jvars.js', __FILE__));
		wp_localize_script('voa-vars', 'voa_jvars', $voa_jvars);
		wp_enqueue_script('jquery');
		wp_enqueue_script('voa-script', plugin_dir_url(__FILE__) . 'wp-voauth.js', array());
		wp_enqueue_style('voa-style', plugin_dir_url(__FILE__) . 'wp-voauth.css', array());
		wp_enqueue_media();
	}

	function voa_init_login_scripts_styles()
	{
		$voa_jvars = array(
			// basic info:
			'ajaxurl'               => admin_url('admin-ajax.php'),
			'template_directory'    => get_bloginfo('template_directory'),
			'stylesheet_directory'  => get_bloginfo('stylesheet_directory'),
			'plugins_url'           => plugins_url(),
			'plugin_dir_url'        => plugin_dir_url(__FILE__),
			'url'                   => get_bloginfo('url'),
			'hide_login_form'       => get_option('voa_hide_wordpress_login_form'),
			'logo_image'            => get_option('voa_logo_image'),
			'bg_image'              => get_option('voa_bg_image'),
			'login_message'         => $_SESSION['VOA']['RESULT'],
			'show_login_messages'   => get_option('voa_show_login_messages'),
			'logout_inactive_users' => get_option('voa_logout_inactive_users'),
			'logged_in'             => is_user_logged_in(),
		);
		wp_enqueue_script('voa-vars', plugins_url('/jvars.js', __FILE__));
		wp_localize_script('voa-vars', 'voa_jvars', $voa_jvars);
		wp_enqueue_script('jquery');
		wp_enqueue_script('voa-script', plugin_dir_url(__FILE__) . 'wp-voauth.js', array());
		wp_enqueue_style('voa-style', plugin_dir_url(__FILE__) . 'wp-voauth.css', array());
	}

	function voa_settings_link($links)
	{
		$settings_link = "<a href='options-general.php?page=V-Oauth'>Settings</a>";
		array_unshift($links, $settings_link);
		return $links;
	}

	function voa_add_basic_auth($url, $username, $password)
	{
		$url = str_replace("https://", "", $url);
		$url = "https://" . $username . ":" . $password . "@" . $url;
		return $url;
	}

	function voa_qvar_triggers($vars)
	{
		$vars[] = 'connect';
		$vars[] = 'code';
		$vars[] = 'error_description';
		$vars[] = 'error_message';
		return $vars;
	}

	function voa_qvar_handlers()
	{
		if (get_query_var('connect')) {
			$provider = get_query_var('connect');
			$this->voa_include_connector($provider);
		} elseif (get_query_var('code')) {
			$provider = $_SESSION['VOA']['PROVIDER'];
			$this->voa_include_connector($provider);
		} elseif (get_query_var('error_description') || get_query_var('error_message')) {
			$provider = $_SESSION['VOA']['PROVIDER'];
			$this->voa_include_connector($provider);
		}
	}

	function voa_include_connector($provider)
	{
		$provider = strtolower($provider);
		$provider = str_replace(" ", "", $provider);
		$provider = str_replace(".", "", $provider);
		include 'login-' . $provider . '.php';
	}

	function voa_login_user($oauth_identity)
	{
		if ($oauth_identity['quarantine']) {
			$this->voa_end_login("Sorry, you have been quarantined");
		}
		if ($oauth_identity['blacklisted']) {
			$this->voa_end_login("Sorry, you have been blacklisted.");
		}
		if (!$oauth_identity['verified']) {
			$this->voa_end_login("Sorry, you are not verified yet.");
		}
		$_SESSION["VOA"]["USER_ID"] = $oauth_identity["id"];
		$_SESSION['VOA']['agent'] = $oauth_identity['agent'];
		$_SESSION['VOA']['vlevel'] = $oauth_identity['vlevel'];
		$_SESSION['VOA']['vpoints'] = $oauth_identity['vpoints'];

		$_SESSION['VOA']['quarantine'] = $oauth_identity['quarantine'];
		$_SESSION['VOA']['blacklisted'] = $oauth_identity['blacklisted'];
		$_SESSION['VOA']['verified'] = $oauth_identity['verified'];


		$matched_user = $this->voa_match_wordpress_user($oauth_identity);
		if ($matched_user) {
			$user_id = $matched_user->ID;
			$user_login = $matched_user->user_login;
			$this->voa_add_vlevel($user_id);
			$this->voa_add_vpoints($user_id);
			wp_set_current_user($user_id, $user_login);
			wp_set_auth_cookie($user_id);
			do_action('wp_login', $user_login, $matched_user);
			$this->voa_end_login("Logged in successfully!");
		}
		if (is_user_logged_in()) {
			global $current_user;
			get_currentuserinfo();
			$user_id = $current_user->ID;
			$this->voa_link_account($user_id);
			$this->voa_add_vlevel($user_id);
			$this->voa_add_vpoints($user_id);
			$this->voa_end_login("Your account was linked successfully with your third party authentication provider.");
		}
		if (!is_user_logged_in() && !$matched_user) {
			include 'register.php';
		}
		$this->voa_end_login("Sorry, we couldn't log you in. The login flow terminated in an unexpected way. Please notify the admin or try again later.");
	}

	function voa_match_wordpress_user($oauth_identity)
	{
		global $wpdb;
		$usermeta_table = $wpdb->usermeta;
		$query_string = "SELECT $usermeta_table.user_id FROM $usermeta_table WHERE $usermeta_table.meta_key = 'voa_identity' AND $usermeta_table.meta_value LIKE '%" . $oauth_identity['provider'] . "|" . $oauth_identity['id'] . "%'";
		$query_result = $wpdb->get_var($query_string);
		$user = get_user_by('id', $query_result);
		return $user;
	}

	function voa_end_login($msg)
	{
		$last_url = $_SESSION["VOA"]["LAST_URL"];
		unset($_SESSION["VOA"]["LAST_URL"]);
		$_SESSION["VOA"]["RESULT"] = $msg;
		$this->voa_clear_login_state();
		$redirect_method = get_option("voa_login_redirect");
		$redirect_url = "";
		switch ($redirect_method) {
			case "home_page":
				$redirect_url = site_url();
				break;
			case "last_page":
				$redirect_url = $last_url;
				break;
			case "specific_page":
				$redirect_url = get_permalink(get_option('voa_login_redirect_page'));
				break;
			case "admin_dashboard":
				$redirect_url = admin_url();
				break;
			case "user_profile":
				$redirect_url = get_edit_user_link();
				break;
			case "custom_url":
				$redirect_url = get_option('voa_login_redirect_url');
				break;
		}
		wp_safe_redirect($redirect_url);
		die();
	}

	function voa_clear_login_state()
	{
		unset($_SESSION["VOA"]["USER_ID"]);
		unset($_SESSION["VOA"]["USER_EMAIL"]);
		unset($_SESSION["VOA"]["ACCESS_TOKEN"]);
		unset($_SESSION["VOA"]["EXPIRES_IN"]);
		unset($_SESSION["VOA"]["EXPIRES_AT"]);
	}

	function voa_link_account($user_id)
	{
		if ($_SESSION['VOA']['USER_ID'] != '') {
			add_user_meta($user_id, 'voa_identity', $_SESSION['VOA']['PROVIDER'] . '|' . $_SESSION['VOA']['USER_ID'] . '|' . time());
		}
	}

	function voa_add_vlevel($user_id)
	{
		if ($_SESSION['VOA']['USER_ID'] != '') {
			update_user_meta($user_id, 'voa_vlevel', $_SESSION['VOA']['vlevel']);
		}
	}

	function voa_add_vpoints($user_id)
	{
		if ($_SESSION['VOA']['USER_ID'] != '') {
			update_user_meta($user_id, 'voa_vpoints', $_SESSION['VOA']['vpoints']);
		}
	}

	function voa_logout_user()
	{
		$user = null;
		session_destroy();
		wp_logout();
	}

	function voa_end_logout()
	{
		$_SESSION["VOA"]["RESULT"] = 'Logged out successfully.';
		if (is_user_logged_in()) {
			$last_url = $_SERVER['HTTP_REFERER'];
		} else {
			$last_url = strtok($_SERVER['HTTP_REFERER'], "?");
		}
		unset($_SESSION["VOA"]["LAST_URL"]);
		$this->voa_clear_login_state();
		$redirect_method = get_option("voa_logout_redirect");
		$redirect_url = "";
		switch ($redirect_method) {
			case "default_handling":
				return false;
			case "home_page":
				$redirect_url = site_url();
				break;
			case "last_page":
				$redirect_url = $last_url;
				break;
			case "specific_page":
				$redirect_url = get_permalink(get_option('voa_logout_redirect_page'));
				break;
			case "admin_dashboard":
				$redirect_url = admin_url();
				break;
			case "user_profile":
				$redirect_url = get_edit_user_link();
				break;
			case "custom_url":
				$redirect_url = get_option('voa_logout_redirect_url');
				break;
		}
		wp_safe_redirect($redirect_url);
		die();
	}

	function voa_unlink_account()
	{
		$voa_identity_row = $_POST['voa_identity_row'];
		global $current_user;
		get_currentuserinfo();
		$user_id = $current_user->ID;
		global $wpdb;
		$usermeta_table = $wpdb->usermeta;
		$query_string = $wpdb->prepare("DELETE FROM $usermeta_table WHERE $usermeta_table.user_id = $user_id AND $usermeta_table.meta_key = 'voa_identity' AND $usermeta_table.umeta_id = %d", $voa_identity_row);
		$query_result = $wpdb->query($query_string);
		if ($query_result) {
			echo json_encode(array('result' => 1));
		} else {
			echo json_encode(array('result' => 0));
		}
		die();
	}

	function voa_push_login_messages()
	{
		$result = $_SESSION['VOA']['RESULT'];
		$_SESSION['VOA']['RESULT'] = '';
		echo "<div id='voa-result'>" . $result . "</div>";
	}

	function voa_logo_link()
	{
		return get_bloginfo('url');
	}

	function voa_customize_login_screen()
	{
		$html = "";
		$design = get_option('voa_login_form_show_login_screen');
		if ($design != "None") {
			$html .= $this->voa_login_form_content($design, 'none', 'buttons-column', 'Connect with', 'center', 'conditional', 'conditional', 'Please login:', 'You are already logged in.', 'Logging in...', 'Logging out...');
		}
		echo $html;
	}

	function voa_login_form_content($design = '', $icon_set = 'icon_set', $layout = 'links-column', $button_prefix = '', $align = 'left', $show_login = 'conditional', $show_logout = 'conditional', $logged_out_title = 'Please login:', $logged_in_title = 'You are already logged in.', $logging_in_title = 'Logging in...', $logging_out_title = 'Logging out...', $style = '', $class = '')
	{
		if ($design != '' && VOA::voa_login_form_design_exists($design)) {
			$a = VOA::voa_get_login_form_design($design);
			$icon_set = $a['icon_set'];
			$layout = $a['layout'];
			$button_prefix = $a['button_prefix'];
			$align = $a['align'];
			$show_login = $a['show_login'];
			$show_logout = $a['show_logout'];
			$logged_out_title = $a['logged_out_title'];
			$logged_in_title = $a['logged_in_title'];
			$logging_in_title = $a['logging_in_title'];
			$logging_out_title = $a['logging_out_title'];
			$style = $a['style'];
			$class = $a['class'];
		}
		$html = "";
		$html .= "<div class='voa-login-form voa-layout-$layout voa-layout-align-$align $class' style='$style' data-logging-in-title='$logging_in_title' data-logging-out-title='$logging_out_title'>";
		$html .= "<nav>";
		if (is_user_logged_in()) {
			if ($logged_in_title) {
				$html .= "<p id='voa-title'>" . $logged_in_title . "</p>";
			}
			if ($show_login == 'always') {
				$html .= $this->voa_login_buttons($icon_set, $button_prefix);
			}
			if ($show_logout == 'always' || $show_logout == 'conditional') {
				$html .= "<a class='voa-logout-button' href='" . wp_logout_url() . "' title='Logout'>Logout</a>";
			}
		} else {
			if ($logged_out_title) {
				$html .= "<p id='voa-title'>" . $logged_out_title . "</p>";
			}
			if ($show_login == 'always' || $show_login == 'conditional') {
				$html .= $this->voa_login_buttons($icon_set, $button_prefix);
			}
			if ($show_logout == 'always') {
				$html .= "<a class='voa-logout-button' href='" . wp_logout_url() . "' title='Logout'>Logout</a>";
			}
		}
		$html .= "</nav>";
		$html .= "</div>";
		return $html;
	}

	// show a custom login form at the top of the default comment form:

	function voa_login_form_design_exists($design_name)
	{
		return false;
	}

	function voa_get_login_form_design($design_name, $as_string = false)
	{
		$designs_json = get_option('voa_login_form_designs');
		$designs_array = json_decode($designs_json, true);
		foreach ($designs_array as $key => $val) {
			if ($design_name == $key) {
				$found = $val;
				break;
			}
		}
		$atts = "";
		if ($found) {
			if ($as_string) {
				$atts = json_encode($found);
			} else {
				$atts = $found;
			}
		}
		return $atts;
	}

	function voa_login_buttons($icon_set, $button_prefix)
	{
		$site_url = get_bloginfo('url');
		$redirect_to = urlencode($_GET['redirect_to']);
		if ($redirect_to) {
			$redirect_to = "&redirect_to=" . $redirect_to;
		}
		$icon_set_path = plugins_url('icons/' . $icon_set . '/', __FILE__);
		$atts = array(
			'site_url'      => $site_url,
			'redirect_to'   => $redirect_to,
			'icon_set'      => $icon_set,
			'icon_set_path' => $icon_set_path,
			'button_prefix' => $button_prefix,
		);
		$html = "";
		$html .= $this->voa_login_button("v", "V", $atts);
		if ($html == '') {
			$html .= 'Sorry, no login providers have been enabled.';
		}
		return $html;
	}

	function voa_login_button($provider, $display_name, $atts)
	{
		$html = "";
		if (get_option("voa_" . $provider . "_api_enabled")) {
			$html .= "<a id='voa-login-" . $provider . "' class='voa-login-button' href='" . $atts['site_url'] . "?connect=" . $provider . $atts['redirect_to'] . "'>";
			if ($atts['icon_set'] != 'none') {
				$html .= "<img src='" . $atts['icon_set_path'] . $provider . ".png' alt='" . $display_name . "' class='icon'></img>";
			}
			$html .= $atts['button_prefix'] . " " . $display_name;
			$html .= "</a>";
		}
		return $html;
	}

	function voa_customize_comment_form_fields($fields)
	{
		$html = "";
		$design = get_option('voa_login_form_show_comments_section');
		if ($design != "None") {
			$html .= $this->voa_login_form_content($design, 'none', 'buttons-column', 'Connect with', 'center', 'conditional', 'conditional', 'Please login:', 'You are already logged in.', 'Logging in...', 'Logging out...');
			$fields['logged_in_as'] = $html;
		}
		return $fields;
	}

	function voa_customize_comment_form()
	{
		$html = "";
		$design = get_option('voa_login_form_show_comments_section');
		if ($design != "None") {
			$html .= $this->voa_login_form_content($design, 'none', 'buttons-column', 'Connect with', 'center', 'conditional', 'conditional', 'Please login:', 'You are already logged in.', 'Logging in...', 'Logging out...');
		}
		echo $html;
	}

	function voa_login_form($atts)
	{
		$a = shortcode_atts(array(
			'design'            => '',
			'icon_set'          => 'none',
			'button_prefix'     => '',
			'layout'            => 'links-column',
			'align'             => 'left',
			'show_login'        => 'conditional',
			'show_logout'       => 'conditional',
			'logged_out_title'  => 'Please login:',
			'logged_in_title'   => 'You are already logged in.',
			'logging_in_title'  => 'Logging in...',
			'logging_out_title' => 'Logging out...',
			'style'             => '',
			'class'             => '',
		), $atts);
		$html = $this->voa_login_form_content($a['design'], $a['icon_set'], $a['layout'], $a['button_prefix'], $a['align'], $a['show_login'], $a['show_logout'], $a['logged_out_title'], $a['logged_in_title'], $a['logging_in_title'], $a['logging_out_title'], $a['style'], $a['class']);
		return $html;
	}

	function voa_login_form_designs_selector($id = '', $master = false)
	{
		$html = "";
		$designs_json = get_option('voa_login_form_designs');
		$designs_array = json_decode($designs_json, true);
		$name = str_replace('-', '_', $id);
		$html .= "<select id='" . $id . "' name='" . $name . "'>";
		if ($master == true) {
			foreach ($designs_array as $key => $val) {
				$html .= "<option value=''>" . $key . "</option>";
			}
			$html .= "</select>";
			$html .= "<input type='hidden' id='voa-login-form-designs' name='voa_login_form_designs' value='" . $designs_json . "'>";
		} else {
			$html .= "<option value='None'>" . 'None' . "</option>";
			foreach ($designs_array as $key => $val) {
				$html .= "<option value='" . $key . "' " . selected(get_option($name), $key, false) . ">" . $key . "</option>";
			}
			$html .= "</select>";
		}
		return $html;
	}

	function add_v_columns($column)
	{
		$column['vlevel'] = 'V Level';
		$column['vpoints'] = 'V Points';
		return $column;
	}

	function add_v_column_data($val, $column_name, $user_id)
	{


		$output = "";
		if ('vlevel' == $column_name) {
			$vlevel = get_user_meta($user_id, "voa_vlevel", true);
			$output .= ($vlevel);
		}
		if ('vpoints' == $column_name) {
			$vpoints = get_user_meta($user_id, "voa_vpoints", true);
			$output .= ($vpoints);
		}
		return $output;

	}

	function show_v_info()
	{
		global $current_user;
		get_currentuserinfo();
		$user_id = $current_user->ID;
		?>

		<h3>V Info</h3>

		<table class="form-table">

			<tr>
				<td>
					V Level: <?php echo get_user_meta($user_id, "voa_vlevel", true); ?>
				</td>
			</tr>
			<tr>
				<td>
					V Points: <?php echo get_user_meta($user_id, "voa_vpoints", true); ?>
				</td>
			</tr>

		</table>
	<?php }

	function voa_linked_accounts()
	{
		global $current_user;
		get_currentuserinfo();
		$user_id = $current_user->ID;
		global $wpdb;
		$usermeta_table = $wpdb->usermeta;
		$query_string = "SELECT * FROM $usermeta_table WHERE $user_id = $usermeta_table.user_id AND $usermeta_table.meta_key = 'voa_identity'";
		$query_result = $wpdb->get_results($query_string);
		echo "<div id='voa-linked-accounts'>";
		echo "<h3>Linked Accounts</h3>";
		echo "<p>Manage the linked accounts which you have previously authorized to be used for logging into this website.</p>";
		echo "<table class='form-table'>";
		echo "<tr valign='top'>";
		echo "<th scope='row'>Your Linked Providers</th>";
		echo "<td>";
		if (count($query_result) == 0) {
			echo "<p>You currently don't have any accounts linked.</p>";
		}
		echo "<div class='voa-linked-accounts'>";
		foreach ($query_result as $voa_row) {
			$voa_identity_parts = explode('|', $voa_row->meta_value);
			$oauth_provider = $voa_identity_parts[0];
			$oauth_id = $voa_identity_parts[1]; // keep this private, don't send to client
			$time_linked = $voa_identity_parts[2];
			$local_time = strtotime("-" . $_COOKIE['gmtoffset'] . ' hours', $time_linked);
			echo "<div>" . $oauth_provider . " on " . date('F d, Y h:i A', $local_time) . " <a class='voa-unlink-account' data-voa-identity-row='" . $voa_row->umeta_id . "' href='#'>Unlink</a></div>";
		}
		echo "</div>";
		echo "</td>";
		echo "</tr>";
		echo "<tr valign='top'>";
		echo "<th scope='row'>Link Another Provider</th>";
		echo "<td>";
		$design = get_option('voa_login_form_show_profile_page');
		if ($design != "None") {
			echo $this->voa_login_form_content($design, 'none', 'buttons-row', 'Link', 'left', 'always', 'never', 'Select a provider:', 'Select a provider:', 'Authenticating...', '');
		}
		echo "</div>";
		echo "</td>";
		echo "</td>";
		echo "</table>";
	}

	function voa_register_settings()
	{
		foreach ($this->settings as $setting_name => $default_value) {
			register_setting('voa_settings', $setting_name);
		}
	}

	function voa_settings_page()
	{
		add_options_page('V-Oauth Options', 'V-Oauth', 'manage_options', 'V-Oauth', array($this, 'voa_settings_page_content'));
	}

	function voa_settings_page_content()
	{
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		$blog_url = rtrim(site_url(), "/") . "/";
		include 'wp-voauth-settings.php';
	}

}

VOA::get_instance();