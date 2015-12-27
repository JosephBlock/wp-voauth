<div class='wrap voa-settings'>
	<div id="voa-settings-meta">Toggle tips:
		<ul>
			<li><a id="voa-settings-tips-on" href="#">On</a></li>
			<li><a id="voa-settings-tips-off" href="#">Off</a></li>
		</ul>
		<div class="nav-splitter"></div>
		Toggle sections:
		<ul>
			<li><a id="voa-settings-sections-on" href="#">On</a></li>
			<li><a id="voa-settings-sections-off" href="#">Off</a></li>
		</ul>
	</div>
	<h2>V-OAuth Settings</h2>
	<div id="voa-settings-header"></div>
	<div id="voa-settings-body">
		<div id="voa-settings-col1" class="voa-settings-column">
			<form method='post' action='options.php'>
				<?php settings_fields('voa_settings'); ?>
				<?php do_settings_sections('voa_settings'); ?>
				<div id="voa-settings-section-general-settings" class="voa-settings-section">
					<h3>General Settings</h3>
					<div class='form-padding'>
						<table class='form-table'>

							<tr valign='top' class='has-tip' class="has-tip">
								<th scope='row'>Always allow OAuth to register: <a href="#" class="tip-button">[?]</a>
								</th>
								<td>
									<input type='checkbox' name='voa_allow_oauth_to_register_always'
									       value='1' <?php checked(get_option('voa_allow_oauth_to_register_always') == 1); ?> />
									<p class="tip-message">Allows registration even when Wordpress option "Anyone can
										register" is disabled</p>
								</td>
							</tr>
							<tr valign='top' class='has-tip' class="has-tip">
								<th scope='row'>Show login messages: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<input type='checkbox' name='voa_show_login_messages'
									       value='1' <?php checked(get_option('voa_show_login_messages') == 1); ?> />
									<p class="tip-message">Shows a short-lived notification message to the user which
										indicates whether or not the login was successful, and if there was an
										error.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Login redirects to: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<select name='voa_login_redirect'>
										<option
											value='home_page' <?php selected(get_option('voa_login_redirect'), 'home_page'); ?>>
											Home Page
										</option>
										<option
											value='last_page' <?php selected(get_option('voa_login_redirect'), 'last_page'); ?>>
											Last Page
										</option>
										<option
											value='specific_page' <?php selected(get_option('voa_login_redirect'), 'specific_page'); ?>>
											Specific Page
										</option>
										<option
											value='admin_dashboard' <?php selected(get_option('voa_login_redirect'), 'admin_dashboard'); ?>>
											Admin Dashboard
										</option>
										<option
											value='user_profile' <?php selected(get_option('voa_login_redirect'), 'user_profile'); ?>>
											User's Profile Page
										</option>
										<option
											value='custom_url' <?php selected(get_option('voa_login_redirect'), 'custom_url'); ?>>
											Custom URL
										</option>
									</select>
									<?php wp_dropdown_pages(array("id" => "voa_login_redirect_page", "name" => "voa_login_redirect_page", "selected" => get_option('voa_login_redirect_page'))); ?>
									<input type="text" name="voa_login_redirect_url"
									       value="<?php echo get_option('voa_login_redirect_url'); ?>"
									       style="display:none;"/>
									<p class="tip-message">Specifies where to redirect a user after they log in.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Logout redirects to: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<select name='voa_logout_redirect'>
										<option
											value='default_handling' <?php selected(get_option('voa_logout_redirect'), 'default_handling'); ?>>
											Let WordPress handle it
										</option>
										<option
											value='home_page' <?php selected(get_option('voa_logout_redirect'), 'home_page'); ?>>
											Home Page
										</option>
										<option
											value='last_page' <?php selected(get_option('voa_logout_redirect'), 'last_page'); ?>>
											Last Page
										</option>
										<option
											value='specific_page' <?php selected(get_option('voa_logout_redirect'), 'specific_page'); ?>>
											Specific Page
										</option>
										<option
											value='admin_dashboard' <?php selected(get_option('voa_logout_redirect'), 'admin_dashboard'); ?>>
											Admin Dashboard
										</option>
										<option
											value='user_profile' <?php selected(get_option('voa_logout_redirect'), 'user_profile'); ?>>
											User's Profile Page
										</option>
										<option
											value='custom_url' <?php selected(get_option('voa_logout_redirect'), 'custom_url'); ?>>
											Custom URL
										</option>
									</select>
									<?php wp_dropdown_pages(array("id" => "voa_logout_redirect_page", "name" => "voa_logout_redirect_page", "selected" => get_option('voa_logout_redirect_page'))); ?>
									<input type="text" name="voa_logout_redirect_url"
									       value="<?php echo get_option('voa_logout_redirect_url'); ?>"
									       style="display:none;"/>
									<p class="tip-message">Specifies where to redirect a user after they log out.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Automatically logout inactive users: <a href="#"
								                                                        class="tip-button">[?]</a></th>
								<td>
									<select name='voa_logout_inactive_users'>
										<option
											value='0' <?php selected(get_option('voa_logout_inactive_users'), '0'); ?>>
											Never
										</option>
										<option
											value='1' <?php selected(get_option('voa_logout_inactive_users'), '1'); ?>>
											After 1 minute
										</option>
										<option
											value='5' <?php selected(get_option('voa_logout_inactive_users'), '5'); ?>>
											After 5 minutes
										</option>
										<option
											value='15' <?php selected(get_option('voa_logout_inactive_users'), '15'); ?>>
											After 15 minutes
										</option>
										<option
											value='30' <?php selected(get_option('voa_logout_inactive_users'), '30'); ?>>
											After 30 minutes
										</option>
										<option
											value='60' <?php selected(get_option('voa_logout_inactive_users'), '60'); ?>>
											After 1 hour
										</option>
										<option
											value='120' <?php selected(get_option('voa_logout_inactive_users'), '120'); ?>>
											After 2 hours
										</option>
										<option
											value='240' <?php selected(get_option('voa_logout_inactive_users'), '240'); ?>>
											After 4 hours
										</option>
									</select>
									<p class="tip-message">Specifies whether to log out users automatically after a
										period of inactivity.</p>
									<p class="tip-message tip-warning"><strong>Warning:</strong> When a user logs out of
										WordPress, they will remain logged into their third-party provider until they
										close their browser. Logging out of WordPress DOES NOT log you out of Google,
										Facebook, LinkedIn, etc...</p>
								</td>
							</tr>
						</table>
						<?php submit_button('Save all settings'); ?>
					</div>
				</div>
				<div id="voa-settings-section-login-with-v" class="voa-settings-section">
					<h3>Login with V</h3>
					<div class='form-padding'>
						<table class='form-table'>
							<tr valign='top'>
								<th scope='row'>Enabled:</th>
								<td>
									<input type='checkbox' name='voa_v_api_enabled'
									       value='1' <?php checked(get_option('voa_v_api_enabled') == 1); ?> />
								</td>
							</tr>

							<tr valign='top'>
								<th scope='row'>Client ID:</th>
								<td>
									<input type='text' name='voa_v_api_id'
									       value='<?php echo get_option('voa_v_api_id'); ?>'/>
								</td>
							</tr>

							<tr valign='top'>
								<th scope='row'>Client Secret:</th>
								<td>
									<input type='text' name='voa_v_api_secret'
									       value='<?php echo get_option('voa_v_api_secret'); ?>'/>
								</td>
							</tr>
						</table>
						<p>
							<a href="https://v.enl.one/oauth/clients">https://v.enl.one/oauth/clients</a> to add client.
							In url field put http://&lt;your domain&gt;/ or https://&lt;your domain&gt;/ depending on
							ssl or not <b>(trailing "/" is required on domain)</b>
						</p>
						<?php submit_button('Save all settings'); ?>
					</div>
				</div>
				<div id="voa-settings-section-login-forms" class="voa-settings-section">
					<h3>Login Forms</h3>
					<div class='form-padding'>
						<table class='form-table'>


							<tr valign='top' class="has-tip">
								<th scope='row'>Hide the WordPress login form: <a href="#" class="tip-button">[?]</a>
								</th>
								<td>
									<input type='checkbox' name='voa_hide_wordpress_login_form'
									       value='1' <?php checked(get_option('voa_hide_wordpress_login_form') == 1); ?> />
									<p class="tip-message">Use this to hide the WordPress username/password login form
										that is shown by default on the Login Screen and Login Popup.</p>
									<p class="tip-message tip-warning"><strong>Warning: </strong>Hiding the WordPress
										login form may prevent you from being able to login. If you normally rely on
										this method, DO NOT enable this setting. Furthermore, please make sure your
										login provider(s) are active and working BEFORE enabling this setting.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Logo links to site: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<input type='checkbox' name='voa_logo_links_to_site'
									       value='1' <?php checked(get_option('voa_logo_links_to_site') == 1); ?> />
									<p class="tip-message">Forces the logo image on the login form to link to your site
										instead of WordPress.org.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Logo image: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<p>
										<input id='voa_logo_image' type='text' size='' name='voa_logo_image'
										       value="<?php echo get_option('voa_logo_image'); ?>"/>
										<input id='voa_logo_image_button' type='button' class='button' value='Select'/>
									</p>
									<p class="tip-message">Changes the default WordPress logo on the login form to an
										image of your choice. You may select an image from the Media Library, or specify
										a custom URL.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Background image: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<p>
										<input id='voa_bg_image' type='text' size='' name='voa_bg_image'
										       value="<?php echo get_option('voa_bg_image'); ?>"/>
										<input id='voa_bg_image_button' type='button' class='button' value='Select'/>
									</p>
									<p class="tip-message">Changes the background on the login form to an image of your
										choice. You may select an image from the Media Library, or specify a custom
										URL.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Custom form to show on the login screen: <a href="#" class="tip-button">[?]</a>
								</th>
								<td>
									<?php echo VOA::voa_login_form_designs_selector('voa-login-form-show-login-screen'); ?>
									<p class="tip-message">Create or manage these login form designs in the CUSTOM LOGIN
										FORM DESIGNS section.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Custom form to show on the user's profile page: <a href="#"
								                                                                   class="tip-button">[?]</a>
								</th>
								<td>
									<?php echo VOA::voa_login_form_designs_selector('voa-login-form-show-profile-page'); ?>
									<p class="tip-message">Create or manage these login form designs in the CUSTOM LOGIN
										FORM DESIGNS section.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Custom form to show in the comments section: <a href="#"
								                                                                class="tip-button">[?]</a>
								</th>
								<td>
									<?php echo VOA::voa_login_form_designs_selector('voa-login-form-show-comments-section'); ?>
									<p class="tip-message">Create or manage these login form designs in the CUSTOM LOGIN
										FORM DESIGNS section.</p>
								</td>
							</tr>
						</table>
						<?php submit_button('Save all settings'); ?>
					</div>
				</div>
				<!-- END Login Page & Form Customization section -->

				<!-- START Custom Login Form Designs section -->
				<div id="voa-settings-section-custom-login-form-designs" class="voa-settings-section">
					<h3>Custom Login Form Designs</h3>
					<div class='form-padding'>
						<p>You may create multiple login form <strong><em>designs</em></strong> and use them throughout
							your site. A design is essentially a re-usable <em>shortcode preset</em>. Instead of writing
							out the login form shortcode ad-hoc each time you want to use it, you can build a design
							here, save it, and then specify that design in the shortcode's <em>design</em> attribute.
							For example:
						<pre><code>[voa_login_form design='CustomDesign1']</code></pre>
						</p>
						<table class='form-table'>
							<tr valign='top' class="has-tip">
								<th scope='row'>Design: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<?php echo VOA::voa_login_form_designs_selector('voa-login-form-design', true); ?>
									<p>
										<input type="button" id="voa-login-form-new" class="button" value="New">
										<input type="button" id="voa-login-form-edit" class="button" value="Edit">
										<input type="button" id="voa-login-form-delete" class="button" value="Delete">
									</p>
									<p class="tip-message">Here you may create a new design, select an existing design
										to edit, or delete an existing design.</p>
									<p class="tip-message tip-info"><strong>Tip: </strong>Make sure to click the <em>Save
											all settings</em> button after making changes here.</p>
								</td>
							</tr>
						</table>

						<table class="form-table" id="voa-login-form-design-form">

							<tr valign='top' class="has-tip">
								<th scope='row'>Design name: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<input id='voa-login-form-design-name' type='text' size='36'
									       name='voa_login_form_design_name' value=""/>
									<p class="tip-message">Sets the name to use for this design.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Icon set: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<select name='voa_login_form_icon_set'>
										<option value='none'>None</option>
										<option value='hex'>Hex</option>
									</select>
									<p class="tip-message">Specifies which icon set to use for displaying provider icons
										on the login buttons.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Show login buttons: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<select name='voa_login_form_show_login'>
										<option value='always'>Always</option>
										<option value='conditional'>Conditional</option>
										<option value='never'>Never</option>
									</select>
									<p class="tip-message">Determines when the login buttons should be shown.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Show logout button: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<select name='voa_login_form_show_logout'>
										<option value='always'>Always</option>
										<option value='conditional'>Conditional</option>
										<option value='never'>Never</option>
									</select>
									<p class="tip-message">Determines when the logout button should be shown.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Layout: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<select name='voa_login_form_layout'>
										<option value='links-row'>Links Row</option>
										<option value='links-column'>Links Column</option>
										<option value='buttons-row'>Buttons Row</option>
										<option value='buttons-column'>Buttons Column</option>
									</select>
									<p class="tip-message">Sets vertical or horizontal layout for the buttons.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Login button prefix: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<input id='voa_login_form_button_prefix' type='text' size='36'
									       name='voa_login_form_button_prefix' value=""/>
									<p class="tip-message">Sets the text prefix to be displayed on the social login
										buttons.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Logged out title: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<input id='voa_login_form_logged_out_title' type='text' size='36'
									       name='voa_login_form_logged_out_title' value=""/>
									<p class="tip-message">Sets the text to be displayed above the login form for logged
										out users.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Logged in title: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<input id='voa_login_form_logged_in_title' type='text' size='36'
									       name='voa_login_form_logged_in_title' value=""/>
									<p class="tip-message">Sets the text to be displayed above the login form for logged
										in users.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Logging in title: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<input id='voa_login_form_logging_in_title' type='text' size='36'
									       name='voa_login_form_logging_in_title' value=""/>
									<p class="tip-message">Sets the text to be displayed above the login form for users
										who are logging in.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Logging out title: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<input id='voa_login_form_logging_out_title' type='text' size='36'
									       name='voa_login_form_logging_out_title' value=""/>
									<p class="tip-message">Sets the text to be displayed above the login form for users
										who are logging out.</p>
								</td>
							</tr>

							<tr valign='top' id='voa-login-form-actions'>
								<th scope='row'>
									<input type="button" id="voa-login-form-ok" name="voa_login_form_ok" class="button"
									       value="OK">
									<input type="button" id="voa-login-form-cancel" name="voa_login_form_cancel"
									       class="button" value="Cancel">
								</th>
								<td>

								</td>
							</tr>
						</table>
						<?php submit_button('Save all settings'); ?>
					</div>
				</div>
				<!-- END Login Buttons section -->

				<!-- START User Registration section -->
				<div id="voa-settings-section-user-registration" class="voa-settings-section">
					<h3>User Registration</h3>
					<div class='form-padding'>
						<table class='form-table'>
							<tr valign='top' class="has-tip">
								<th scope='row'>Suppress default welcome email: <a href="#" class="tip-button">[?]</a>
								</th>
								<td>
									<input type='checkbox' name='voa_suppress_welcome_email'
									       value='1' <?php checked(get_option('voa_suppress_welcome_email') == 1); ?> />
									<p class="tip-message">Prevents WordPress from sending an email to newly registered
										users by default, which contains their username and password.</p>
								</td>
							</tr>

							<tr valign='top' class="has-tip">
								<th scope='row'>Assign new users to the following role: <a href="#" class="tip-button">[?]</a>
								</th>
								<td>
									<select
										name="voa_new_user_role"><?php wp_dropdown_roles(get_option('voa_new_user_role')); ?></select>
									<p class="tip-message">Specifies what user role will be assigned to newly registered
										users.</p>
								</td>
							</tr>
						</table>
						<?php submit_button('Save all settings'); ?>
					</div>
				</div>

				<div id="voa-settings-section-maintenance-troubleshooting" class="voa-settings-section">
					<h3>Maintenance & Troubleshooting</h3>
					<div class='form-padding'>
						<table class='form-table'>
							<tr valign='top' class="has-tip">
								<th scope='row'>Restore default settings: <a href="#" class="tip-button">[?]</a></th>
								<td>
									<input type='checkbox' name='voa_restore_default_settings'
									       value='1' <?php checked(get_option('voa_restore_default_settings') == 1); ?> />
									<p class="tip-message"><strong>Instructions:</strong> Check the box above, click the
										Save all settings button, and the settings will be restored to default.</p>
									<p class="tip-message tip-warning"><strong>Warning:</strong> This will restore the
										default settings, erasing any API keys/secrets that you may have entered above.
									</p>
								</td>
							</tr>
							<tr valign='top' class="has-tip">
								<th scope='row'>Delete settings on uninstall: <a href="#" class="tip-button">[?]</a>
								</th>
								<td>
									<input type='checkbox' name='voa_delete_settings_on_uninstall'
									       value='1' <?php checked(get_option('voa_delete_settings_on_uninstall') == 1); ?> />
									<p class="tip-message"><strong>Instructions:</strong> Check the box above, click the
										Save all settings button, then uninstall this plugin as normal from the Plugins
										page.</p>
									<p class="tip-message tip-warning"><strong>Warning:</strong> This will delete all
										settings that may have been created in your database by this plugin, including
										all linked third-party login providers. This will not delete any WordPress user
										accounts, but users who may have registered with or relied upon their
										third-party login providers may have trouble logging into your site. Make
										absolutely sure you won't need the values on this page any time in the future,
										because they will be deleted permanently.</p>
								</td>
							</tr>
						</table>
						<?php submit_button('Save all settings'); ?>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>