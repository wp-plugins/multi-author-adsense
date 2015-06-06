<?php
/*
    Plugin Name: Multi-Author AdSense
    Plugin URI: http://thepluginfactory.co/warehouse/multi-author-adsense
	Description: Display AdSense Advertising for Multiple Authors on your Website Quickly and Easily!
	Version: 1.1
	Author: The Plugin Factory
	Author URI: http://thepluginfactory.co
*/

# SET UP

	# INTERNATIONALIZATION
	
		// add_action( 'init', array($MAA,'internationalization'));
	
	# DEBUGGING CHECK
		$GLOBALS['maa_debug'] = false;

		if (isset($_GET['MAA_DEBUG'])){
			$GLOBALS['maa_debug'] = true;
		}

	# CHECK ADSENSE AD OUTPUT COUNT FOR FRONTEND LIMITING OF ADS

		if (empty($GLOBALS["maa_adsense_ads_count"])){
			$GLOBALS["maa_adsense_ads_count"] = 0;
		}

	# CHECK NON ADSENSE AD OUTPUT COUNT ON PAGE

		if (empty($GLOBALS["maa_non_adsense_ads_count"])){
			$GLOBALS["maa_non_adsense_ads_count"] = 0;
		}

	# DEFINE MAA PRO AS FALSE UNLESS MAA PRO IS INSTALLED

		if( !isset( $GLOBALS['maa_pro'] ) ){
			$GLOBALS['maa_pro'] = false;
		}

	# CONFIGURE GOOGLE ADSENSE AD SIZES AVAILABLE TO MAA

		$GLOBALS["maa_adsense_sizes"] = array(
			#	Google AdSense Display and Text Unit Sizes
				'970x90'  => 'Large Leaderboard',
				'728x90'  => 'Leaderboard',
				'468x60'  => 'Banner ',
				'336x280' => 'Large Rectangle',
				'320x100' => 'Large Mobile Banner',
				'320x50'  => 'Mobile Banner',
				'300x600' => 'Large Skyscraper',
				'300x250' => 'Medium Rectangle',
				'250x250' => 'Square ',
				'234x60'  => 'Half Banner',
				'200x200' => 'Small Square',
				'200x200' => 'Small Square',
				'180x150' => 'Small Rectangle',
				'160x600' => 'Wide Skyscraper',
				'125x125' => 'Button',
				'120x600' => 'Skyscraper',
				'120x240' => 'Vertical Banner',

			#	Google AdSense Link Unit Sizes
				'728x15'  => 'Displays 4 links',
				'468x15'  => 'Displays 4 links',
				'200x90'  => 'Displays 3 links',
				'180x90'  => 'Displays 3 links',
				'160x90'  => 'Displays 3 links',
				'120x90'  => 'Displays 3 links',
		);

	# LOAD ALL MAA SETTINGS INTO THE GLOBAL VARIABLE maa_settings FOR USE ANYWHERE

		$GLOBALS['maa_settings'] = get_option( 'maa_settings' );

	# IF MAA SETTINGS IS BLANK, SET UP SOME DEFAULT SETTING VALUES

		if (empty( $GLOBALS['maa_settings'] )) {
			$setup_defaults = array(
				# Standard Settings
					'admin_id'                                  => '1',
					'enable_standard_ad_settings'               => '0',
					'enable_advanced_ad_settings'               => '0',
					'enable_basic_ad_settings'                  => '1',
					'basic_mode'                                => 'standard',
					'user_how_many_adsense_ads_allowed'         => '2',
					'user_how_many_adsense_ads_display_allowed' => '2',
					'ad_supression_abilities'                   => 'admin',
				
				# Pro Settings
					'user_how_many_auto_insert_ads'             => '1',
					'show_ads_after_x_articles'                 => '0',
					'show_ads_if_post_is_x_words'               => '0',
					'user_how_many_non_adsense_ads_allowed'     => '1',
					'adsense_only'                              => 'both',
					'hide_ads_on_users'                         => '',
					'category_filter_mode'                      => 'show',
					'ads_on_categories'                         => '',
					'tag_filter_mode'                           => 'show',
					'ads_on_tags'                               => '',
					'enabled_post_types'                        => 'post',
					'show_ads_to_logged_in'                     => '1',
					'show_ads_on_archive'                       => '0',
					'show_ads_on_home'                          => '0',
					'show_ads_on_search'                        => '0',
					'revenue_sharing'                           => '0',
					'author_filtering_type'                     => 'none',
					// TODO: Make admin option to hide all checkboxes, and make all checked by default.
				
				# Admin Settings
					'how_many_upgrade_requests'                 => '0',
					'pro_demo'                                  => '0',
			);

			foreach ($GLOBALS['maa_adsense_sizes'] as $key => $value) {
				$setup_defaults["basic_mode_limited"][$key]             = "0";
			}

			update_option( 'maa_settings', $setup_defaults );
		} else {
	# OTHERWISE, IF THE SETTINGS ARE NOT EMPTY, CHECK THE LICENCE KEY STATUS
			$keyEntered = trim( get_option( 'multiauthoradsense_license_key' ) );
			$keyStatus 	= ucfirst( get_option( 'multiauthoradsense_license_status' ) );
			if ( $GLOBALS['maa_pro'] == true && empty( $keyEntered ) ) {
				$GLOBALS['maa_settings']['license_key_status'] = 'no license key entered yet<br><a href="admin.php?page=multi-author-adsense-license">click here to enter your key</a>';
			} else if ( $GLOBALS['maa_pro'] == false ) {
				$GLOBALS['maa_settings']['license_key_status'] = 'Pro version not installed.<br><a href="http://thepluginfactory.co/warehouse/multi-author-adsense-pro/?so=maa_get_license_key_link_settings" target="_blank"><b>Get a license key!</b></a>';
			} else {
				$GLOBALS['maa_settings']['license_key_status'] = "Key Entered.<br>";
				if ($keyStatus != 'Valid') {
					$GLOBALS['maa_settings']['license_key_status'] .= "<a href='admin.php?page=multi-author-adsense-license' style='text-decoration:underline;color:red;font-weight:bold;font-size:16px'>Status = $keyStatus</a>";
				} else {
					$GLOBALS['maa_settings']['license_key_status'] .= "<a href='admin.php?page=multi-author-adsense-license' style='text-decoration:underline;color:darkgreen'>Status = $keyStatus</a>";
				}
			}
		# ALSO, SET DEFAULT VALUES ONLY FOR THOSE SETTINGS WHICH ARE CURRENTLY EMPTY FROM THE LIST BELOW
			$checkboxes = array(
				'enable_standard_ad_settings'               => '0',
				'enable_advanced_ad_settings'               => '0',
				'enable_basic_ad_settings'                  => '0',
				'show_ads_to_logged_in'                     => '0',
				'show_ads_on_archive'                       => '0',
				'show_ads_on_home'                          => '0',
				'show_ads_on_search'                        => '0',
				'help_toggle'                               => '0',
				'pro_demo'                                  => '0',
			);
			foreach ($checkboxes as $setting => $value) {
				if ( empty($GLOBALS['maa_settings']["$setting"]) ) {
					$GLOBALS['maa_settings']["$setting"] = $value;
				}
			}
		}


# CLASS SETUP
	if (!class_exists('MultiAuthorAdSense')) {
		class MultiAuthorAdSense {

			############################################
			# Helper functions
			############################################

				function strtotitle($title){
					$smallwordsarray = array('of','a','the','and','an','or','nor','but','is','if','then','else','when','at','from','by','on','off','for','in','out','over','to','into','with');
					$title = strtolower($title);
					$words = explode(' ', $title);
					foreach ($words as $key => $word){
						if ($key == 0 or !in_array($word, $smallwordsarray)) {
							$words[$key] = ucwords($word);
						}
					}
					$newtitle = implode(' ', $words);
					return $newtitle;
				}

			############################################
			# Setup global variables
			############################################

				function get_vars() {

					# EDIT THESE
						$MAA_vars["UPPERCASE_SLUG"]	= 'MULTI_AUTHOR_ADSENSE'; // PLUGIN NAME
						$MAA_vars["PLUGIN_TITLE"] 	= 'Multi-Author AdSense'; // Plugin Name // OPTIONS_NICK
						$MAA_vars["VERSION"] 		= '1.1';

					# THESE ARE DYNAMIC
						$MAA_vars["LOWERCASE_SLUG_UNDERSCORE"] = strtolower( str_replace(" ", "_", $MAA_vars["UPPERCASE_SLUG"]) ); // plugin_name //OPTIONS_URL_CODE
						$MAA_vars["LOWERCASE_SLUG_DASH"] = strtolower( str_replace("_", "-", $MAA_vars["UPPERCASE_SLUG"]) ); // plugin-name //OPTIONS_URL_CODE
						$MAA_vars["OPTIONS_ID"] = $MAA_vars["UPPERCASE_SLUG"].'-options';
						$MAA_vars["OPTIONS"] = $MAA_vars["UPPERCASE_SLUG"].'_';

					return $MAA_vars;
				}

			############################################
			# Register all options with WordPress
			############################################

				function register()  {
					if (!current_user_can('manage_options')) { return; }
					global $MAA_vars;
					$options_id = $GLOBALS['MAA_vars']['OPTIONS_ID'];
					register_setting( $options_id, 'maa_settings' );
				}

			############################################
			# Register scripts and styles for frontend
			############################################

				function enqueue_frontend(){
					if (file_exists( plugins_url( '/style.css', __FILE__ ) )) {
						wp_register_style( 'maa-style', plugins_url( '/style.css', __FILE__ ) );
						wp_enqueue_style(  'maa-style' );
					}
					wp_enqueue_script( 'jquery' );
				}

			############################################
			# Set up internationalization
			############################################

				function internationalization() {

					load_plugin_textdomain('maa', false, basename( dirname( __FILE__ ) ) . '/languages' );
				}

			############################################
			# Menus for admin
			############################################

				function setup_menu($atts) {

					# Register scripts and styles for settings pages

						function enqueue_settings(){
							wp_register_style( 'maa-lightbox-style', plugins_url( 'css/magnific_popup.css?'.rand(999,9999), __FILE__ ) );
							wp_enqueue_style(  'maa-lightbox-style' );

							wp_register_script( 'maa-lightbox-js', plugins_url( 'js/magnific_popup.js', __FILE__ ) );
							wp_enqueue_script( 'maa-lightbox-js' );
						}

					function MAA_options() {
						enqueue_settings();
						include(dirname(__FILE__) . '/options.php');
					}

					function MAA_help() {
						include(dirname(__FILE__) . '/help.php');
					}



					global $MAA_vars;
					$page_title = $MAA_vars["PLUGIN_TITLE"];
					$menu_title = $MAA_vars["PLUGIN_TITLE"];
					$capability = 'manage_options';
					$menu_slug  = $MAA_vars["LOWERCASE_SLUG_DASH"];
					$function   = 'MAA_options';
					$icon_url   = plugins_url( '/images/maa16.png' , __FILE__ );
					$position   = 58;

					add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url/*, $position*/ );
					add_submenu_page( $menu_slug, 'Multi-Author AdSense Settings', 'Settings', $capability, $menu_slug, $function );
					add_submenu_page( $menu_slug, 'Multi-Author AdSense Help', 'Help', $capability, $menu_slug.'-help', 'MAA_help' );
					if (file_exists(dirname(dirname(__FILE__)) . '/multi-author-adsense-pro/index.php')) {
						// include(dirname(dirname(__FILE__)) . '/multi-author-adsense-pro/license.php');

						// function MAA_license() {
						// 	include(dirname(dirname(__FILE__)) . '/multi-author-adsense-pro/license_setup.php');
						// }

						// add_submenu_page( $menu_slug, 'Multi-Author AdSense Help',        'Help',        $capability, $menu_slug.'-help', 'MAA_help' );
						// add_submenu_page( $menu_slug, 'Multi-Author AdSense Pro License', 'Pro License', $capability, $menu_slug.'-license', 'multiauthoradsense_license_page' );
					}

					// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

					// if ( function_exists('MAAPRO_License') ) {
					// 	MAAPRO_License();
					// }
				}

			############################################
			# Profile page changes
			############################################

				function user_settings(){

					if (isset( $_GET['post'] )) {
						if (!function_exists('wp_get_current_user')) {
							require_once(ABSPATH.'/wp-includes/pluggable.php');
						}
						if ( (@$GLOBALS['maa_settings']["ad_supression_abilities"] == "admin" && is_super_admin()) || @$GLOBALS['maa_settings']["ad_supression_abilities"] == "both" ) {
							add_action( 'add_meta_boxes', array( $this, 'maa_checkboxes' ) );
							add_action( 'save_post', array( $this, 'maa_save_data' ) );
						} 
					}

						

					include(dirname(__FILE__) . '/profile.php');
				}

			############################################
			# Post Editing Screen Meta Boxes
			############################################

				## register the meta box
					function maa_checkboxes() {
						global $MAA_vars;
						$options = $MAA_vars["OPTIONS"];

						$id       = 'maa_post_settings';          															// this is HTML id of the box on edit screen
						$title    = '<img src="'.plugins_url( '/images/maa16.png' , __FILE__ ).'?v1"/> '.__('Multi-Author AdSense - Ad Supression'); 	// title of the box
						$callback = array( $this, 'maa_post_box_content' );   																// function to be called to display the checkboxes, see the function below
						$context  = 'normal';      																			// part of page where the box should appear
						$priority = 'default';     																			// priority of the box
						$callback_args = array();
						// $post_types = 'post';        // on which edit screen the box should appear
						// add_meta_box( $id, $title, $callback, $post_types, $context, $priority );

						$post_types = array( 'post', 'page');

						$args = array(
							'public'   => true,
							'_builtin' => false
						);

						$output = 'names'; // names or objects, note names is the default
						$operator = 'and'; // 'and' or 'or'

						$post_type_arr = get_post_types( $args, $output, $operator );

						$types = array('post','page');
						$post_type_arr = array_merge($types,$post_type_arr);

						foreach ( $post_type_arr  as $post_type ) {
							$post_types[$post_type] = get_option( $options.$post_type );
						}

						foreach ($post_types as $type => $value) {
							if ($value) {
								add_meta_box( $id, $title, $callback, $type /*, $context, $priority, $callback_args */);
							}
						}
					}

				## display the metabox
					function maa_post_box_content( $post ) {
						// nonce field for security check, you can  have the same
						// nonce field for all your meta boxes of same plugin
						wp_nonce_field( plugin_basename( __FILE__ ), 'maa_nonce' );

						$checkboxes = array(
							'maa_disable_display' => 'Do not show ads on this content',
							'maa_disable_auto_insert' => 'Do not auto insert ads on this content'
						);

						echo '<input type="checkbox" name="maa_disable_display" value="1" '.checked('maa_disable_display',1,false).' /> '.__('Do not show ads on this content').' <br />';
						
						if ($GLOBALS['maa_pro']) {
							echo '<input type="checkbox" name="maa_disable_auto_insert" value="1" '.checked('maa_disable_auto_insert',1,false).' /> '.__('Do not auto insert ads on this content').' <br />';
						}

						//print_r( get_post_meta($post) );
					}

				## save data from checkboxes
					function maa_save_data( $post ) {

						// check if this isn't an auto save
						if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
							return;

						// security check
						if ( isset($_POST['maa_nonce']) && !wp_verify_nonce( $_POST['maa_nonce'], plugin_basename( __FILE__ ) ) )
							return;

						$checkboxes = array('maa_disable_display','maa_disable_auto_insert');

						foreach ($checkboxes as $checkbox) {
							if ( isset( $_POST["$checkbox"] ) ) {
								update_post_meta( $post, $checkbox, 1 );
							} else {
								update_post_meta( $post, $checkbox, 0 );
							}
						}
					}

			############################################
			# Pro Settings Output
			############################################

				function pro_settings($maa_settings) {
					global $MAA;
					global $MAA_vars;

					ob_start();
					?>
						<tr class='setting prosetting'>
							<td>
								<a href='#license_key' class='popup-with-zoom-anim'><?php _e('Pro License Key Status'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='license_key'>
									<h3><?php _e("Pro License Key Status"); ?></h3>
									<p><?php _e("This shows the status of the upgrade <a href='http://thepluginfactory.co/warehouse/multi-author-adsense-pro/?so=maa_get_license_key_link_help_popup' target='_blank'>Multi-Author AdSense Pro</a>."); ?></p>
									<p><?php _e("Have you purchased Multi-Author AdSense Pro? If you have, please <a href='admin.php?page=multi-author-adsense-license'>click here to enter your key and activate it</a>."); ?></p>
									<p><?php _e("If you have not yet purchased a license key, you can <a href='http://thepluginfactory.co/warehouse/multi-author-adsense-pro/?so=maa_get_license_key_link_help_popup' target='_blank'><b>get a license key here</b></a>."); ?></p>
								</div>
							</td>
							<td style="text-align:center;vertical-align:middle !important;">
								<?php 
									if ($GLOBALS['maa_pro'] === false) {
										?>
										inactive, <a href="http://thepluginfactory.co/warehouse/multi-author-adsense-pro/?so=maa_get_license_key_link" target="_blank"><b>get a license key</b></a>
										<?php
									} else {
										echo @$maa_settings["license_key_status"]; 
									}
								?>
							</td>
						</tr>
						<tr class='setting prosetting'>
							<td>
								<a href='#user_how_many_auto_insert_ads' class='popup-with-zoom-anim'><?php _e('Author Auto Insert Ad Limit'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='user_how_many_auto_insert_ads'>
									<h3>Author Auto Insert Ad Limit</h3>
									<p><?php _e("How many ads should an author be able to automatically have inserted in their content?"); ?></p>
									<p><?php _e("Keep in mind, that you are limited to three AdSense ads per webpage total. This means that if you, the site owner, want to display a single AdSense ad on every page of your website, then you should limit your authors to 2 auto inserted ads."); ?></p>
								</div>
							</td>
							<td style="text-align:center;vertical-align:middle !important;">
								<select class="fullwidth_select" name='maa_settings[user_how_many_auto_insert_ads]'>
									<?php
										$i = 0;
										while ($i <= 5) {
											echo "<option value='{$i}' ".selected( @$maa_settings["user_how_many_auto_insert_ads"], "$i" )." >{$i}</option>";
											$i++;
										}
									?>
								</select>
							</td>
						</tr>
						<tr class='setting prosetting'>
							<td>
								<a href='#revenue_sharing' class='popup-with-zoom-anim'><?php _e('Revenue Sharing'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='revenue_sharing'>
									<h3>Revenue Sharing</h3>
									<p><?php _e("If you would like to enable the administrators ads to show on articles written by authors of the website, set this value to something higher than 0%"); ?></p>
									<p><?php _e("0% means that the authors ad shows 100% of time time, and the site administrators ads never show."); ?></p>
									<p><?php _e("50% means that the authors ad shows half of time time, and the site administrators ads show half of the time."); ?></p>
									<p><?php _e("For example, if you want to one out of every five ads to be an administrators ad, then select 20% here."); ?></p>
								</div>
							</td>
							<td style="text-align:right;vertical-align:middle;">
								<select name='maa_settings[revenue_sharing]'>
									<option value='0' <?php selected( @$maa_settings["revenue_sharing"], '0' ) ?>><?php _e('0% Admin / 100% Author'); ?></option>
									<option value='1' <?php selected( @$maa_settings["revenue_sharing"], '1' ) ?>><?php _e('10% Admin / 90% Author'); ?></option>
									<option value='2' <?php selected( @$maa_settings["revenue_sharing"], '2' ) ?>><?php _e('20% Admin / 80% Author'); ?></option>
									<option value='2.5' <?php selected( @$maa_settings["revenue_sharing"], '2.5' ) ?>><?php _e('25% Admin / 75% Author'); ?></option>
									<option value='3' <?php selected( @$maa_settings["revenue_sharing"], '3' ) ?>><?php _e('30% Admin / 70% Author'); ?></option>
									<option value='4' <?php selected( @$maa_settings["revenue_sharing"], '4' ) ?>><?php _e('40% Admin / 60% Author'); ?></option>
									<option value='5' <?php selected( @$maa_settings["revenue_sharing"], '5' ) ?>><?php _e('50% Admin / 50% Author'); ?></option>
									<option value='6' <?php selected( @$maa_settings["revenue_sharing"], '6' ) ?>><?php _e('60% Admin / 40% Author'); ?></option>
									<option value='7' <?php selected( @$maa_settings["revenue_sharing"], '7' ) ?>><?php _e('70% Admin / 30% Author'); ?></option>
									<option value='7.5' <?php selected( @$maa_settings["revenue_sharing"], '7.5' ) ?>><?php _e('75% Admin / 25% Author'); ?></option>
									<option value='8' <?php selected( @$maa_settings["revenue_sharing"], '8' ) ?>><?php _e('80% Admin / 20% Author'); ?></option>
									<option value='9' <?php selected( @$maa_settings["revenue_sharing"], '9' ) ?>><?php _e('90% Admin / 10% Author'); ?></option>
									<option value='10' <?php selected( @$maa_settings["revenue_sharing"], '10' ) ?>><?php _e('100% Admin / 0% Author'); ?></option>
								</select>
							</td>
						</tr>
						
						<tr class='setting prosetting'>
							<td width=350px>
								<a href='#admin_id' class='popup-with-zoom-anim'><?php _e('Fallback User Account'); ?></a>
								<?php
									global $wpdb;
									global $MAA_vars;
									$authors = $wpdb->get_results( "SELECT ID,user_login, display_name from $wpdb->users" );
									$admin_id = @$maa_settings['admin_id'];
									$output = "<select name='maa_settings[admin_id]' class='fullwidth_select' id='admin_author_id'>";
									$output .= '<option value="none" '.selected( $admin_id , 'none', false).'>No Fallback User</option>';
										foreach ($authors as $author) {
											$id = $author->ID;
											$login = $author->user_login;
											$displayname = $author->display_name;

											$selected = selected( $admin_id , $id, false );
											if (!empty($selected)) {
												$admin_id = $author->ID;
												$admin_login = $author->user_login;
												$admin_displayname = $author->display_name;
											}

											$output .= '<option value="'.$id.'" '.$selected.'>'.$login.' ('.$displayname.') (ID# '.$id.')</option>';
										}
									$output .= "</select>";
								?>
								<div class='help_content zoom-anim-dialog mfp-hide' id='admin_id'>
									<h3><?php _e("Fallback User Account"); ?></h3>
									<p><?php _e("If there is a post, and the author has NOT entered their own AdSense ads on the page, which user should we use to display ads instead?"); ?></p>
									<p><?php _e("For example, if the author <b>Bob</b> has not configured his AdSense settings on his profile yet, then we will show ads from the user selected here instead."); ?></p>
									<p><?php _e("The number of ads, and the position is managed at the fallback users profile settings page."); ?></p>
									<p><?php
										if(!empty( $admin_id ) && is_numeric($admin_id) ) {
											echo "<a class='button button-primary button-small' href='http://dev.thepluginfactory.co/wp-admin/user-edit.php?user_id={$admin_id}#MAA'>Manage Ad Settings for ".$admin_login." (".$admin_displayname.")</a>";
										}
									?></p>
								</div>
							</td>
							<td width=250px style="text-align:center;vertical-align:middle !important;">
									<?php
									echo $output;
									?>
							</td>
						</tr>

						<tr class='setting prosetting'>
							<td>
								<a href='#author_filtering_type' class='popup-with-zoom-anim'><?php _e('Fallback Mode'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='author_filtering_type'>
									<h3>Fallback Mode</h3>
									<p><?php _e("When an authors ads are not displayed, due to any of the <b>Filter Settings</b> below, what would you like to do?"); ?></p>
									<p><?php _e("If you want to block ALL ads from appearing in the post content, set the first dropdown to <b>Don't show any ads</b>."); ?></p>
									<p><?php _e("If you want to block the authors ads from appearing in the post content, and instead, insert the fallback users ads in the content, set the first dropdown to <b>Show ads from the Fallback User Account</b> ."); ?></p>
								</div>
							</td>
							<td style="text-align:right;width:220px;">
								<select name='maa_settings[author_filtering_type]'>
									<option value="none" <?php selected( @$maa_settings["author_filtering_type"], "none" ); ?>><?php _e('Don\'t show any ads'); ?></option>
									<option value="fallback" <?php selected( @$maa_settings["author_filtering_type"], "fallback" ); ?>><?php _e('Show ads from the Fallback User Account'); ?></option>
								</select>
							</td>
						</tr>

						<tr class='setting prosetting'>
							<td>
								<a href='#allow_auto_insert' class='popup-with-zoom-anim'><?php _e('Allow Auto Insertion'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='allow_auto_insert'>
									<h3>Allow Auto Insertion</h3>
									<p><?php _e("Would you like to give your users the ability to automatically insert ads into their content?"); ?></p>
									<p><?php _e("If you would, there is an option in their profile page, under the MAA settings. They can configure whether or not to auto insert ads, and pick which ads to insert."); ?></p>
									<p><?php _e("If not, then ads must either be hard coded by you, placed in a widget, or inserted manually by the users using the shortcode [maa]. More instructions on using the shortcode are on each users profile page, under the MAA settings."); ?></p>
								</div>
							</td>
							<td style="text-align:right;width:220px;">
								<select name='maa_settings[allow_auto_insert]'>
									<option value="false" <?php selected( @$maa_settings["allow_auto_insert"], "false" ); ?>><?php _e('Don\'t allow auto inserted ads'); ?></option>
									<option value="true"  <?php selected( @$maa_settings["allow_auto_insert"], "true"  ); ?>><?php _e('Auto inserted ads are allowed'); ?></option>
								</select>
							</td>
						</tr>

						<tr class='setting prosetting'>
							<td colspan=2>
								<h4><?php _e("Display Restrictions"); ?></h4>
							</td>
						</tr>

						<tr class='setting prosetting'>
							<td>
								<a href='#show_ads_after_x_articles' class='popup-with-zoom-anim'><?php _e('Show ads after a minimum article count?'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='show_ads_after_x_articles'>
									<h3>Show ads after a minimum article count?</h3>
									<p><?php _e("How many articles does an author have to write before ads are shown on their content?"); ?></p>
									<p><?php _e("This is retroactive, so once they hit the limit, all past articles automatically get ads inserted in them."); ?></p>
									<p><?php _e("For example, if you want to restrict ads until an author has contributed 5 articles, set this option to <b>5 Articles</b>. After your author publishes his 5th approved article, all 5 of his articles will instantly start displaying ads."); ?></p>
									<p><?php _e("<i>Note: You should regularly clear any content caching you have setup. If you do not have a schedule to clear your content cache, then the old articles will NOT begin showing ads until you clear the cache.</i>"); ?></p>
								</div>
							</td>
							<td style="text-align:right;vertical-align:middle;">
								<select name='maa_settings[show_ads_after_x_articles]'>
									<option value='0' <?php selected( @$maa_settings["show_ads_after_x_articles"], '0' ) ?>><?php _e('No Minimum'); ?></option>
									<option value='1' <?php selected( @$maa_settings["show_ads_after_x_articles"], '1' ) ?>><?php _e('1 Article'); ?></option>
									<?php
									$i = 2;
									while ($i <= 20) {$counts[] = $i;$i++;}
									foreach ($counts as $value) {echo "<option value='{$value}' ".selected( @$maa_settings["show_ads_after_x_articles"], $value,false).">{$value} Articles</option>";}
									?>
								</select>
							</td>
						</tr>

						<tr class='setting prosetting'>
							<td>
								<a href='#show_ads_if_post_is_x_words' class='popup-with-zoom-anim'><?php _e('Minimum Article Length'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='show_ads_if_post_is_x_words'>
									<h3>Minimum Article Length</h3>
									<p><?php _e("How many words does an article have to be in order to qualify to display advertisements?"); ?></p>
									<p><?php _e("For example, if you want to restrict ads if an article is less than 500 words, set this option to <b>500 Words</b>. This will only show ads on content from an author which if over 500 words."); ?></p>
								</div>
							</td>
							<td style="text-align:right;vertical-align:middle;">
								<select name='maa_settings[show_ads_if_post_is_x_words]'>
									<option value='0' <?php selected( @$maa_settings["show_ads_if_post_is_x_words"], '0' ) ?>><?php _e('Any Length'); ?></option>
									<option value='1' <?php selected( @$maa_settings["show_ads_if_post_is_x_words"], '1' ) ?>><?php _e('1 Word'); ?></option>
									<?php
									$i = 50;
									while ($i <= 3000) {$counts[] = $i;$i = $i + 50;}
									foreach ($counts as $value) {echo "<option value='{$value}' ".selected( @$maa_settings["show_ads_if_post_is_x_words"], $value,false).">{$value} Words</option>";}
									?>
								</select>
							</td>
						</tr>

						<tr class='setting prosetting'>
							<td>
								<a href='#user_how_many_non_adsense_ads_allowed' class='popup-with-zoom-anim'><?php _e('Author Non-AdSense Ad Display Limit'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='user_how_many_non_adsense_ads_allowed'>
									<h3>Author Non-AdSense Ad Display Limit</h3>
									<p><?php _e("How many Non-AdSense ads can an author display within their own content?"); ?></p>
									<p><?php _e("This can be any type of HTML/JavaScript combination."); ?></p>
								</div>
							</td>
							<td style="text-align:center;vertical-align:middle !important;">
								<select class="fullwidth_select" name='maa_settings[user_how_many_non_adsense_ads_allowed]'>
									<?php
										$i = 0;
										while ($i <= 10) {
											echo "<option value='{$i}' ".selected( @$maa_settings["user_how_many_non_adsense_ads_allowed"], "$i" )." >{$i}</option>";
											$i++;
										}
									?>
								</select>
							</td>
						</tr>

						<tr class='setting prosetting'>
							<td>
								<a href='#adsense_only' class='popup-with-zoom-anim'><?php _e('Allow Only AdSense Ads'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='adsense_only'>
									<h3>Allow only AdSense Ads</h3>
									<p><?php _e("This gives you the choice to allow your authors to either use only AdSense Ads, only Non-AdSense Ads, or a combination of both."); ?></p>
								</div>
							</td>
							<td style="text-align:center;vertical-align:middle !important;">
								<select name='maa_settings[adsense_only]'>
									<option value="adsense" <?php selected( @$maa_settings["adsense_only"], "adsense" ); ?>><?php _e('Allow AdSense Only'); ?></option>
									<option value="other" <?php selected( @$maa_settings["adsense_only"], "other" ); ?>><?php _e('Allow Non-AdSense Only'); ?></option>
									<option value="both" <?php selected( @$maa_settings["adsense_only"], "both" ); ?>><?php _e('Allow Both AdSense & Non-AdSense'); ?></option>
								</select>
							</td>
						</tr>

						<tr class='setting prosetting'>
							<td colspan=2>
								<h4><?php _e("Filter Settings"); ?></h4>
							</td>
						</tr>

						<tr class='setting prosetting'>
							<td>
								<a href='#hide_ads_on_users' class='popup-with-zoom-anim'><?php _e('Author Filtering'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='hide_ads_on_users'>
									<h3>Author Filtering</h3>
									<p><?php _e("By default all authors are allowed to display ads within their content."); ?></p>
									<p><?php _e("There are two ways to add authors to the list:<br>Either manually type a comma separated list in the large text box provided, OR select each user you want to disable from the drop-down titled <b>Add User</b>"); ?></p>
									<!-- <p><?php _e("If you attempt to add an author which is already on the list, you will receive a message stating <b>User Username is already on your list</b>."); ?></p> -->
									<p><?php _e("Be sure to save your settings after adding or removing authors from this list."); ?></p>
								</div>
							</td>
							<td style="text-align:right;width:220px;">
								<?php
									echo $this->authors_dropdown();
								?><br>
								<textarea id='blocked_authors' class='fullwidth_textarea' name='maa_settings[hide_ads_on_users]'><?php echo @$maa_settings["hide_ads_on_users"]; ?></textarea><br>

							</td>
						</tr>

						<tr class='setting prosetting'>
							<td>
								<a href='#ads_on_categories' class='popup-with-zoom-anim'><?php _e('Category Filtering'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='ads_on_categories'>
									<h3>Category Filtering</h3>
									<p><?php _e("Leave the list empty to show author ads on all content regardless of the categories."); ?></p>
									<h4>Block Ads On Content With Category</h4>
									<p><?php _e("If you would like to prevent ads from showing on content with specific categories, set the first drop down to <b>Hide Ads Only On These Categories</b>."); ?></p>
									<p><?php _e("Select categories to add to your list by using the second dropdown menu."); ?></p>
									<p><?php _e("If you know the ad slug, you can also enter it in the box directly as a comma separated list of slugs."); ?></p>
									<h4>Show Ads On Content With Category</h4>
									<p><?php _e("If you would like to only allow ads on content with specific categories, set the first drop down to <b>Show Ads Only On These Categories</b>."); ?></p>
									<p><?php _e("Select categories to add to your list by using the second dropdown menu."); ?></p>
									<p><?php _e("If you know the ad slug, you can also enter it in the box directly as a comma separated list of slugs."); ?></p>
								</div>
							</td>
							<td style="text-align:center;vertical-align:middle !important;">

								<select name='maa_settings[category_filter_mode]'>
									<option value="show" <?php selected( @$maa_settings["category_filter_mode"], "adsense" ); ?>><?php _e('Show Ads Only On These Categories'); ?></option>
									<option value="hide" <?php selected( @$maa_settings["category_filter_mode"], "hide" ); ?>><?php _e('Hide Ads Only On These Categories'); ?></option>
								</select>
							<?php
								$categories = get_categories();
								$html = '<select class="post_categories">';
								$html .= '<option value="">Select Category to Add</option>';
								foreach ( $categories as $category ) {
									$category_link = get_category_link( $category->term_id );

									$html .= "<option value='{$category->slug}'>{$category->name} ({$category->slug})</option>";
								}
								$html .= '</select>';
								echo $html;
							?>
							<textarea id='blocked_categories' class='fullwidth_textarea' name='maa_settings[ads_on_categories]'><?php echo @$maa_settings["ads_on_categories"]; ?></textarea>
							</td>
						</tr>

						<tr class='setting prosetting'>
							<td>
								<a href='#tag_filter_mode' class='popup-with-zoom-anim'><?php _e('Tag Filtering'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='tag_filter_mode'>
									<h3>Tag Filtering</h3>
									<p><?php _e("Leave the list empty to show author ads on all content regardless of the tags."); ?></p>
									<h4>Block Ads On Content With Tags</h4>
									<p><?php _e("If you would like to prevent ads from showing on content with specific tags, set the first drop down to <b>Hide Ads Only On These Tags</b>."); ?></p>
									<p><?php _e("Select tags to add to your list by using the second dropdown menu."); ?></p>
									<p><?php _e("If you know the ad slug, you can also enter it in the box directly as a comma separated list of slugs."); ?></p>
									<h4>Show Ads On Content With Tags</h4>
									<p><?php _e("If you would like to only allow ads on content with specific tags, set the first drop down to <b>Show Ads Only On These Tags</b>."); ?></p>
									<p><?php _e("Select tags to add to your list by using the second dropdown menu."); ?></p>
									<p><?php _e("If you know the ad slug, you can also enter it in the box directly as a comma separated list of slugs."); ?></p>
								</div>
							</td>
							<td style="text-align:center;vertical-align:middle !important;">

								<select name='maa_settings[tag_filter_mode]'>
									<option value="show" <?php selected( @$maa_settings["tag_filter_mode"], "adsense" ); ?>><?php _e('Show Ads Only On These Tags '); ?></option>
									<option value="hide" <?php selected( @$maa_settings["tag_filter_mode"], "hide" ); ?>><?php _e('Hide Ads Only On These Tags'); ?></option>
								</select>
							<?php
								$tags = get_tags();
								$html = '<select class="post_tags">';
								$html .= '<option value="">Select Tags to Add</option>';
								foreach ( $tags as $tag ) {
									$tag_link = get_tag_link( $tag->term_id );

									$html .= "<option value='{$tag->slug}'>{$tag->name} ({$tag->slug})</option>";
								}
								$html .= '</select>';
								echo $html;
							?>
							<textarea id='blocked_tags' class='fullwidth_textarea' name='maa_settings[ads_on_tags]'><?php echo @$maa_settings["ads_on_tags"]; ?></textarea>
							</td>
						</tr>

						<tr class='setting prosetting'>
							<td>
								<a href='#enabled_post_types' class='popup-with-zoom-anim'><?php _e('Content Type Filtering'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='enabled_post_types'>
									<h3>Content Type Filtering</h3>
									<p><?php _e("Select the content types to enable Multi-Author AdSense on."); ?></p>
									<p><?php _e("By default, only regular posts are enabled, select any other content types you'd like to enable Multi-Author AdSense for."); ?></p>
								</div>
							</td>
							<td style="text-align:center;vertical-align:middle !important;">

							<?php

							# Post Type Filtering
								$args = array(
									'public'   => true,
									'_builtin' => false
								);

								$output = 'names'; // names or objects, note names is the default
								$operator = 'and'; // 'and' or 'or'

								$post_types = get_post_types( $args, $output, $operator );

								$types = array('post','page');
								$post_types = array_merge($types,$post_types);
								$ckbx = array();

								$html = '<select class="allowed_content_types">';
								$html .= '<option value="">Select Allowed Content Types</option>';
								foreach ( $post_types  as $post_type ) {
									$html .= "<option value='{$post_type}'>{$post_type}</option>";
								}
								$html .= '</select>';
								echo $html;
							?>
							<textarea id='allowed_content_types' class='fullwidth_textarea' name='maa_settings[enabled_post_types]'><?php echo @$maa_settings["enabled_post_types"]; ?></textarea>
							</td>
						</tr>
					<!-- checkbox settings -->
						<tr class='setting prosetting'>
							<td>
								<a href='#show_ads_to_logged_in' class='popup-with-zoom-anim'><?php _e('Show Ads to logged in users'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='show_ads_to_logged_in'>
									<h3>Show Ads to logged in users</h3>
									<p><?php _e("Check this box to display ads to everyone."); ?></p>
									<p><?php _e("Uncheck this box to only show ads to people who are not logged in (visitors)."); ?></p>
								</div>
							</td>
							<td style="text-align:right;vertical-align:middle;">
								<input type="checkbox" name="maa_settings[show_ads_to_logged_in]" value="1" <?php checked( @$maa_settings["show_ads_to_logged_in"],1); ?> />
							</td>
						</tr>

						<tr class='setting prosetting'>
							<td>
								<a href='#show_ads_on_home' class='popup-with-zoom-anim'><?php _e('Show Ads on the Home Page'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='show_ads_on_home'>
									<h3>Show Ads on the Home Page</h3>
									<p><?php _e("Check this box to display user ads on the home page."); ?></p>
									<p><?php _e("Leaving this box unchecked will not show ads on the home page."); ?></p>
								</div>
							</td>
							<td style="text-align:right;vertical-align:middle;">
								<input type="checkbox" name="maa_settings[show_ads_on_home]" value="1" <?php checked( @$maa_settings["show_ads_on_home"],1); ?> />
							</td>
						</tr>

						<tr class='setting prosetting'>
							<td>
								<a href='#show_ads_on_archive' class='popup-with-zoom-anim'><?php _e('Show Ads on Archives'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='show_ads_on_archive'>
									<h3>Show Ads on Archives</h3>
									<p><?php _e("Check this box to display user ads on archive pages."); ?></p>
									<p><?php _e("Leaving this box unchecked will not show ads on archive pages."); ?></p>
								</div>
							</td>
							<td style="text-align:right;vertical-align:middle;">
								<input type="checkbox" name="maa_settings[show_ads_on_archive]" value="1" <?php checked( @$maa_settings["show_ads_on_archive"],1); ?> />
							</td>
						</tr>

						<tr class='setting prosetting'>
							<td>
								<a href='#show_ads_on_search' class='popup-with-zoom-anim'><?php _e('Show Ads on Search Results'); ?></a>
								<div class='help_content zoom-anim-dialog mfp-hide' id='show_ads_on_search'>
									<h3>Show Ads on Search Results</h3>
									<p><?php _e("Check this box to display user ads on search result pages."); ?></p>
									<p><?php _e("Leaving this box unchecked will not show ads on search result pages."); ?></p>
								</div>
							</td>
							<td style="text-align:right;vertical-align:middle;">
								<input type="checkbox" name="maa_settings[show_ads_on_search]" value="1" <?php checked( @$maa_settings["show_ads_on_search"],1); ?> />
							</td>
						</tr>

					<?php
					return ob_get_clean();
				}

				function authors_dropdown(){
					global $wpdb;
					$authors = $wpdb->get_results( "SELECT user_login, display_name from $wpdb->users" );
					$output = "<select id='authors_dropdown'>";
					$output .= '<option value="_">Select Users</option>';
						foreach ($authors as $author) {
							$login = $author->user_login;
							$displayname = $author->display_name;
							$output .= '<option value="'.$login.'">'.$login.' ('.$displayname.')</option>';
						}
					$output .= "</select>";
					return $output;
				}

			############################################
			# Shortcode
			############################################

				function maa($atts = '') {

					############################################
					## Set up variables to use
					############################################
						extract( shortcode_atts( array(
							'id' => 'undefined',
							'source' => '',
							'align' => ''
						), $atts ) );

					############################################
					## Skip if it's not a post, or we defined already to skip it
					############################################
						if (empty($GLOBALS['post']) || @$GLOBALS['maa_global_skip'])
							return;

					############################################
					## Set the author ID based on the author of the post
					############################################
						if ( !isset( $GLOBALS['maa_settings']['this_author_id'] ) ) {
							$author_id = $GLOBALS['post']->post_author;
						} else {
							$author_id = $GLOBALS['maa_settings']['this_author_id'];
						}

					############################################
					## Get user settings and ad settings
					############################################
						$user_info = get_userdata($author_id);
						if ($user_info != false) {
							$user_login = $user_info->data->user_login;
						}

					############################################
					## If the author is not set up, or the user is
					## on the banned list, use the admin account for ads
					############################################
						if ( function_exists('MAAPRO_user_check') && MAAPRO_user_check($user_login) == 'skip' && $GLOBALS['maa_settings']['author_filtering_type'] == 'none' ) {
							$GLOBALS['maa_global_skip'] = true;
						} else if ( function_exists('MAAPRO_user_check') && MAAPRO_user_check($user_login) == 'skip' && $GLOBALS['maa_settings']['author_filtering_type'] == 'fallback' ) {
							// $GLOBALS['maa_settings']['this_author_id'] = @$GLOBALS['maa_settings']['admin_id'];
							$author_id = @$GLOBALS['maa_settings']['admin_id'];
							$user_info = get_userdata($author_id);
							if ($user_info != false) {
								$user_login = $user_info->data->user_login;
							}
						}

					############################################
					## Revenue Sharing, set this_author_id to admin
					############################################
						if ( !isset($GLOBALS['maa_settings']['this_author_id']) && function_exists('MAAPRO_Revenue_Sharing') ) {
							$author_id = MAAPRO_Revenue_Sharing($author_id);
						}

					############################################
					## Settings
					############################################
						$GLOBALS['maa_settings']['this_author_id'] = $author_id;
						$options = $GLOBALS['MAA_vars']['OPTIONS'];
						$admin_id = get_option($options.'admin_id');
						$adsense_count_limit = $GLOBALS['maa_settings']['user_how_many_adsense_ads_display_allowed'];
						$maa_count_adsense = $GLOBALS["maa_adsense_ads_count"];

					############################################
					## If the post author ID wasn't set
					############################################
						if ( !is_numeric($author_id) || $author_id < 1 )
							return $this->skipped('Skipped due to post author ID not being set. ID # '.$author_id);

					############################################
					## Standard Ad Output Checks
					############################################
						$skip = $this->ad_filtering_checks($author_id,$id,$adsense_count_limit);
						if ( !empty($skip) && strlen($skip) >= 2 )
							return $this->skipped($skip);

					############################################
					## Pro Ad Output Checks
					############################################
						if ( function_exists('MAAPRO_Pro_Checks') ) {
							$Pro_Check = MAAPRO_Pro_Checks($author_id,$id);
							if ( $Pro_Check != false )
								return $this->skipped( $Pro_Check );
						}

					############################################
					## Increment the counters
					############################################
						

						if ($id == 'basic' || $id == 'advanced') {
							if ( $GLOBALS["maa_adsense_ads_count"] >= 3 ) {
								return $this->skipped( 'Skipped due to there being 3 AdSense ads on this page already' );
							} else {
								$GLOBALS["maa_adsense_ads_count"]++;
							}
						} elseif( is_numeric($id) ) {

							$maa_setting = get_user_meta( $author_id , 'maa_settings' , true );
							$code        = $maa_setting['ad_code_slot_'.$id];
							$adsense     = stripos($code, 'googlesyndication.com');

							if ( !$adsense ) {
								$GLOBALS["maa_non_adsense_ads_count"]++;
							} elseif ( $adsense ) {
								if ( $GLOBALS["maa_adsense_ads_count"] >= 3 ) {
									return $this->skipped( 'Skipped due to there being 3 AdSense ads on this page already' );
								} else {
									$GLOBALS["maa_adsense_ads_count"]++;
								}
							}
						}
			
					############################################
					## Pro Ad Output Checks
					############################################
						

					############################################
					## Start the output
					############################################
						ob_start();
						$output = $this->show_ads($author_id,$id,$align);

					############################################
					## Uncomment things here for debugging
					############################################
						//$output .= "<pre>".print_r($user_settings, false)."</pre><br>";

					############################################
					## return everything to the post content
					############################################
						echo $output;

						return ob_get_clean();
				}

				function ad_filtering_checks($user_id,$ad_id,$adsense_count_limit) {

					global $MAA_vars;
					global $post;
					$options_id                  = $MAA_vars['OPTIONS'];
					$skip                        = false;
					$maa_setting                 = get_user_meta( $user_id , 'maa_settings' , true );
					$enable_advanced_ad_settings = @$GLOBALS['maa_settings']['enable_advanced_ad_settings'];
					$enable_standard_ad_settings = @$GLOBALS['maa_settings']['enable_standard_ad_settings'];
					$enable_basic_ad_settings    = @$GLOBALS['maa_settings']['enable_basic_ad_settings'];
					$disable_maa_by_post         = get_post_meta( $post->ID, 'maa_disable_display' );


					############################################
					## Skip if ad mode not enabled by admin
					############################################

						if ( $enable_advanced_ad_settings != '1' && $ad_id == 'advanced' ) {
							$skip = __("Skipped due to admin restricted ad mode [{$ad_id}] not being enabled.");
						} elseif ( $enable_standard_ad_settings != '1'  && is_numeric($ad_id) && $ad_id >= 1) {
							$skip = __("Skipped due to admin restricted ad mode [standard] not being enabled.");
						} elseif ( $enable_basic_ad_settings != '1' && $ad_id == 'basic') {
							$skip = __("Skipped due to admin restricted ad mode [{$ad_id}] not being enabled.");
						}


					############################################
					## Skip if ad supression checked for this post
					############################################
						elseif ( $disable_maa_by_post['0'] == "1" ){
							$GLOBALS['maa_global_skip'] = true;
							$skip = __("Skipped due ad supression being checked on the post edit page.");
						
						} 

					############################################
					## Skip if ad mode not set in shortcode
					############################################
						elseif ( $ad_id == 'undefined' ) {
							$skip = __("Skipped due to ad mode not being set in shortcode.");
						} 

					############################################
					## Don't output if too many ads already shown
					############################################
						
						elseif ($ad_id == 'basic' || $ad_id == 'advanced') {
							if ( !empty($adsense_count_limit) && intval($GLOBALS["maa_adsense_ads_count"]) >= intval($adsense_count_limit)  ){
								$skip = __("Skipped due to too many AdSense ads already output on this page.\n\r[AdSense Count: {$GLOBALS['maa_adsense_ads_count']}]\n\r[AdSense Limit: {$adsense_count_limit}]");
							}
						} 

					############################################
					## Skip non AdSense ads
					############################################

						elseif ( is_numeric($ad_id) ) {
							if ( !isset( $maa_setting['ad_code_slot_'.$ad_id] ) ) {
								$skip = __("Skipped due to this not being an available standard ad slot. You selected ad slot $ad_id.");
							} elseif ( empty( $maa_setting['ad_code_slot_'.$ad_id] ) ) {
								$skip = __("Skipped due to [standard ad slot $ad_id] being empty for [user ID {$user_id}].");
							} else {
								$code = $maa_setting['ad_code_slot_'.$ad_id];
								if ( stripos($code, 'googlesyndication.com') == false && @$GLOBALS['maa_pro'] === false ) {
									$skip = __("Skipped due to this not being an AdSense ad. Ask the site owner to upgrade to Multi-Author AdSense Pro to enable Non-AdSense ads.");
								}
							}
						}

					############################################
					## Skip non defined author ID
					############################################

						elseif ( !is_numeric($user_id) || $user_id < 1 ) {
							$skip = __('Skipped due to post author ID not being set. ID # '.$user_id);
						}

					return $skip;					
				}

				function show_ads($author_id = '', $ad_id, $align = ''){
					$maa_setting = get_user_meta( $author_id , 'maa_settings' , true );

					if (empty($align)) {
						$align = @$GLOBALS['maa_settings']['ad_alignment'];
						if ($align == 'none' or empty($align)) {
							unset($align);
						}
					}

					if($align == 'center') {
						$align   = 'margin: 0 auto;float: none;display: block;text-align:center;';
						$float   = "";
						$margin  = "0px auto";
						$text    = "center";
						$display = 'block';
					} elseif($align == 'left') {
						$align   = 'margin: 0 10px 10px 0;float: left;display: block;';
						$float   = "left";
						$margin  = "0px 10px 10px 0px";
						$text    = "initial";
						$display = 'block';
					} elseif($align == 'right') {
						$align   = 'margin: 0 0 10px 10px;float: right;display: block;';
						$float   = "right";
						$margin  = "0px 0px 10px 10px";
						$text    = "initial";
						$display = 'block';
					} elseif (!isset($align) OR empty($align)) {
						$align   = 'width:100%;display:block;float:none;';
						$float   = "";
						$margin  = "";
						$text    = "initial";
						$display = 'block';
					}

					if ( is_numeric($ad_id) ) {
						$code = $maa_setting['ad_code_slot_'.$ad_id];
						$adsense = stripos($code, 'googlesyndication.com');

						$ad = '<div rel="maa_standard_mode author_id_'.$author_id.' adsense_count_'.$GLOBALS["maa_adsense_ads_count"].' non_adsense_count_'.$GLOBALS["maa_non_adsense_ads_count"].'" 
									style="'.$align.'" 
									class="MAA maa_standard maa_standard_'.$ad_id.'">';
						$ad .= $maa_setting['ad_code_slot_'.$ad_id];

						$ad .= '</div>';
					}
					elseif ( $ad_id == 'basic') {

						# Basic Settings
							if ( @$GLOBALS['maa_settings']["enable_basic_ad_settings"] == '1' ) {
								$basic_mode = @$GLOBALS['maa_settings']["basic_mode"];
							} else {
								return $this->skipped('Skipped due to [basic mode] not being enabled by admin '.$author_id);
							}


							$size_array = array();
							if ($basic_mode == 'standard') { 
								// loop through all ad sizes and show all sizes
								foreach ($GLOBALS['maa_adsense_sizes'] as $size => $desc) {
									$size_array[] = $size;
								}						
							} elseif ($basic_mode == 'limited' && is_array( @$GLOBALS['maa_settings']['basic_mode_limited'] ) ) {
								// Loop through selected user sizes
								$skip = true;
								foreach ( $GLOBALS['maa_settings']['basic_mode_limited'] as $size => $allowed) {
									if ($allowed && @$maa_setting[$size.'_basic']) {
										$size_array[] = $size;
										$skip = false;
									} 
								}
								if ($skip) {
									return $this->skipped('Skipped due to [basic mode] not not having ads setup for [author id '.$author_id.']');
								}
							} elseif ($basic_mode == 'full') {
								// Loop through user selected sizes	

								foreach ($GLOBALS['maa_adsense_sizes'] as $size => $desc) {
									if ( $maa_settings[$size.'_basic'] ) {
										$size_array[] = $size;
									}
								}

							} 

						$pub_id = esc_attr( $maa_setting["publisherid_2"] );
						$num = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 5);

						$google_color_border = str_replace("#", '', '#FFFFFF' );
						$google_color_bg = str_replace("#", '', '#FFFFFF' );
						$google_color_link = str_replace("#", '', '#0000FF' );
						$google_color_url = str_replace("#", '', '#008000' );
						$google_color_text = str_replace("#", '', '#000000' );
						$google_ui_features = '0';

						$link_units = array(
							'728x15' => '728x15',
							'468x15' => '468x15',
							'200x90' => '200x90',
							'180x90' => '180x90',
							'160x90' => '160x90',
							'120x90' => '120x90',
							);

						$ad =	'
						<div id="google-ads-'.$num.'_container"  rel="maa_basic_mode author_id_'.$author_id.' adsense_count_'.$GLOBALS["maa_adsense_ads_count"].' non_adsense_count_'.$GLOBALS["maa_non_adsense_ads_count"].'" class="MAA maa_basic" >
						<div id="google-ads-'.$num.'" style="'.$align.'">
						<script>
						adUnit = document.getElementById("google-ads-'.$num.'");
						adUnitContainer = document.getElementById("google-ads-'.$num.'_container");
						adWidth = adUnitContainer.offsetWidth;
						if(typeof ad_unit === "undefined"){ ad_unit = 0; }
						if(typeof large_skyscraper === "undefined"){ large_skyscraper = 0; }
						if(typeof link_unit === "undefined"){ link_unit = 0; }
						if ( adWidth >= 999999 ) {
						}';

						foreach($size_array as $key => $size) {

							$exp_size = explode('x', $size);
							$size1 = $exp_size["0"];
							$size2 = $exp_size["1"];
							
							$format_extension = '_as';

							$type = 'ad_unit';
							$google_ad_type = 'google_ad_type = "text_image";';

							if ( in_array($size, $link_units) ) {
								$format_extension = '_0ads_al';
								$type = 'link_unit';
							} elseif ($size == '300x600') {
								$type = 'large_skyscraper';
							}

							$tag = $type."_".$size;

							if ( $type == "large_skyscraper"  ) {
								// Show ads if there are less than 3 ad units, and less no large_skyscrapers
								$ad .= ' else if ( adWidth >= '.$size1.' && (( large_skyscraper < 1 && ad_unit < 3 )) ) { ';
							} elseif( $type == "link_unit" ) {
								$ad .= ' else if ( adWidth >= '.$size1.' && link_unit < 3 ) { ';
							} else {
								// Show ads if there are less than 3 ad units, and less no large_skyscrapers
								$ad .= ' else if ( adWidth >= '.$size1.' && (( large_skyscraper < 1 && ad_unit < 3 ) || ( large_skyscraper == 1 && ad_unit < 2 )) ) { ';
							}

						$ad .= '
								// alert("'.$size1.' x '.$size2.'");
								google_ad_width = "'.$size1.'";
								google_ad_height = "'.$size2.'";
								google_ad_format = "'.$size1.'x'.$size2.$format_extension.'";
								'.$google_ad_type.'
								google_ad_channel = "";
								'.$type.' = '.$type.' + 1;
						}';
						
						}

						$ad .= ' else {
							google_ad_slot = "0";
							adUnit.style.display = "none";
						}
						google_ad_client = "'.$pub_id.'";					
						google_color_border = "'.$google_color_border.'";
						google_color_bg = "'.$google_color_bg.'";
						google_color_link = "'.$google_color_link.'";
						google_color_url = "'.$google_color_url.'";
						google_color_text = "'.$google_color_text.'";
						google_ui_features = "rc:'.$google_ui_features.'";
						adUnit.style.cssFloat  = "'.$float.'";
						adUnit.style.styleFloat  = "'.$float.'";
						adUnit.style.margin = "'.$margin.'";
						adUnit.style.textAlign = "'.$text.'";
						</script>
						<script  src="//pagead2.googlesyndication.com/pagead/show_ads.js"></script>
						</div></div>';

					}
					elseif ( $ad_id == 'advanced') {

						//$GLOBALS["maa_adsense_ads_count"]++;

						$GLOBALS['maa_adsense_sizes'];
						$pub_id = esc_attr( $maa_setting["publisherid_1"] );
						$no_ad_skip = true;

						if (empty($pub_id)) 
							return $this->skipped('Skipped due to [advanced mode] not having a publisher ID set. [Publisher ID: '.$pub_id.'] for [Author ID: '.$author_id.']');

						foreach($GLOBALS['maa_adsense_sizes'] as $size => $description) {
							$code = esc_attr( @$maa_setting["$size"] );
							if (!empty($code)) {
								$no_ad_skip = false;
							}
						}

						if ( $no_ad_skip ) 
							return $this->skipped('Skipped due to [advanced mode] not having any ad slugs set. [Publisher ID: '.$pub_id.'] for [Author ID: '.$author_id.']');


						$num = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 5);

						$link_units = array(
							'728x15' => '728x15',
							'468x15' => '468x15',
							'200x90' => '200x90',
							'180x90' => '180x90',
							'160x90' => '160x90',
							'120x90' => '120x90',
							);

						$ad =	'
							<div rel="maa_advanced_mode author_id_'.$author_id.' adsense_count_'.$GLOBALS["maa_adsense_ads_count"].' non_adsense_count_'.$GLOBALS["maa_non_adsense_ads_count"].'" 
							class="MAA maa_advanced" id="google-ads-'.$num.'_container">
							<div id="google-ads-'.$num.'" style="'.$align.'">
							<script>
							google_ad_client = "'.$pub_id.'";
							adUnit = document.getElementById("google-ads-'.$num.'");
							adUnitContainer = document.getElementById("google-ads-'.$num.'_container");
							adWidth = adUnitContainer.offsetWidth;
							if(typeof ad_unit === "undefined"){ ad_unit = 0; }
							if(typeof large_skyscraper === "undefined"){ large_skyscraper = 0; }
							if(typeof link_unit === "undefined"){ link_unit = 0; }
							if ( adWidth >= 999999 ) {
							}';

							foreach($GLOBALS['maa_adsense_sizes'] as $size => $description) {
								$type = 'ad_unit';
								if ( in_array($size, $link_units) ) {
									$type = 'link_unit';
								} elseif ($size == '300x600') {
									$type = 'large_skyscraper';
								}

								$tag = $type."_".$size;
								$code = esc_attr( @$maa_setting["$size"] );
								$exp_size = explode('x', $size);
								$size1 = $exp_size[0];
								$size2 = $exp_size[1];

								if ( strlen($code) == '10' && is_numeric($code)) {
									
								if ( $type == "large_skyscraper"  ) {
									// Show ads if there are less than 3 ad units, and less no large_skyscrapers
									$ad .= ' else if ( adWidth >= '.$size1.' && (( large_skyscraper < 1 && ad_unit < 3 )) ) { ';
								} elseif( $type == "link_unit" ) {
									$ad .= ' else if ( adWidth >= '.$size1.' && link_unit < 3 ) { ';
								} else {
									// Show ads if there are less than 3 ad units, and less no large_skyscrapers
									$ad .= ' else if ( adWidth >= '.$size1.' && (( large_skyscraper < 1 && ad_unit < 3 ) || ( large_skyscraper == 1 && ad_unit < 2 )) ) { ';
								}
									$ad .= '
									google_ad_slot = "'.$code.'";
									google_ad_width = '.$size1.';
									google_ad_height = '.$size2.';
									// adUnit.style.cssFloat  = "'.$float.'";
									// adUnit.style.styleFloat  = "'.$float.'";
									// adUnit.style.margin = "'.$margin.'";
									// adUnit.style.textAlign = "'.$text.'";
									adcount = document.querySelectorAll(".'.$type.'").length;
									tag = "'.$tag.'_"+adcount;
									adUnit.className = adUnit.className + " '.$type.' " + tag;
									'.$type.' = '.$type.' + 1;
									// alert("'.$size1.' x '.$size2.'");
							}';
								}
							}

						$ad .= ' else if ( (large_skyscraper == 0 && ad_unit == 3 || large_skyscraper == 1 && ad_unit == 2 ) ) {
								google_ad_slot	 = "0";
								adUnit.style.display = "none";				
								adUnit.innerHTML = "<span style=\"display:none;\">Too many AdSense image/text ads on the page.</span>";
							}';

						$ad .= ' else {
								google_ad_slot	 = "0";
								adUnit.style.display = "none";
							}</script>
							<script src="//pagead2.googlesyndication.com/pagead/show_ads.js"></script></div></div>';
					} 
					elseif( $ad_id == 'undefined') {
						$ad = $this->skipped('Ad ID undefined.');
					}
					else {

						$ad = $this->skipped('Ad ID couldn\'t be determined.');
					}

					if ( !$GLOBALS["maa_debug"] ){
						// $ad = $this->compressJS($ad);
					}

					return $ad;

				}

			############################################
			# Custom actions & functions
			############################################

				function skipped($message){
					return "\n\r<!--googleoff: all-->\n\r<div class='hidden' rel='MAA Debug' style='display:none !important;'>\n\r========= Multi-Author AdSense =========\n\r\n\r{$message}\n\r\n\r========================================\n\r</div>\n\r<!--googleon: all-->\n\r";
				}

				function compressJS($js){

					$js = str_replace("\t", "", $js);
					$js = str_replace("\n", "", $js);
					$js = str_replace(" == "	, "==", 	$js);
					$js = str_replace(" >= "	, ">=", 	$js);
					$js = str_replace(" > "	, ">", 			$js);
					$js = str_replace(" = "		, "=", 		$js);
					$js = str_replace("} "		, "}", 		$js);
					$js = str_replace(" {"		, "{", 		$js);
					$js = str_replace(" }"		, "}", 		$js);
					$js = str_replace("{ "		, "{", 		$js);
					$js = str_replace("if "		, "if", 	$js);
					$js = str_replace(" else"	, "else", 	$js);
					$js = str_replace("if( "	, "if(", 	$js);
					$js = str_replace(" )"		, ")", 		$js);
					$js = str_replace(";}"		, "}", 		$js);
					$js = str_replace(" + "		, "+", 		$js);
					$js = str_replace(" ="		, "=", 		$js);
					$js = str_replace('/* GETTING THE FIRST IF OUT OF THE WAY */', "", $js);

					return $js;
				}

			############################################
			# Output main settings
			############################################

				function MAA_Show_Admin_AdSense_Settings() {
					ob_start();
					global $MAA_vars;
					$pub_id_value = esc_attr( @$GLOBALS['maa_settings']['admin_publisherid'] );
					$pub_id = $MAA_vars['OPTIONS'].'admin_publisherid';

					##################
					# Advanced AdSense Mode
					##################
						?>

						<tr class='advanced_adsense maa_setting hidden'>
							<td><label for="<?php echo $pub_id; ?>"><?php _e("Publisher ID"); ?></label></td>
							<td>
								<input type="text" name="<?php echo $pub_id; ?>" id="<?php echo $pub_id; ?>" value="<?php echo $pub_id_value; ?>"  /><br />
								<span class="description"><?php _e("Example: pub-1173921673266718"); ?></span>
							</td>
						</tr>

						<?php
						foreach ($GLOBALS['maa_adsense_sizes'] as $size => $description) {
							$setting = $MAA_vars['OPTIONS'].$size.'_admin_advanced';
							$display_name = $size.' | '.$description;
							?>
							<tr class='setting advanced_adsense maa_setting hidden'>
								<td><label for="<?php echo $setting; ?>"><?php echo $display_name; ?></label></td>
								<td>
									<input type="text" name="<?php echo $setting; ?>" id="<?php echo $setting; ?>" value="<?php echo esc_attr( get_option( $setting ) ); ?>"  /><br />
									<span class="help_content">
										<h4><?php echo $display_name ; _e("Settings"); ?></h4>
										<?php _e("Please enter your ad slug if you would like a $display_name ad to display."); ?></span>
								</td>
							</tr>
							<?php
						}

					##################
					# Basic AdSense Mode
					##################
						?>
							<tr class="basic_adsense maa_setting hidden">
								<td><label for="<?php echo $pub_id; ?>"><?php _e("Publisher ID"); ?></label></td>
								<td>
									<input type="text" name="<?php echo $pub_id; ?>" id="<?php echo $pub_id; ?>" value="<?php echo $pub_id_value; ?>"  /><br />
									<span class="description"><?php _e("Example: pub-1173921673266718"); ?></span>
								</td>
							</tr>
						<?php
						foreach ($GLOBALS['maa_adsense_sizes'] as $size => $description) {
							$setting = $MAA_vars['OPTIONS'].$size.'_admin_basic';
							$display_name = $size.' | '.$description;
							?>
							<tr class="basic_adsense maa_setting hidden">
								<td><label for="<?php echo $setting; ?>"><?php echo $display_name; ?></label></td>
								<td>
									<input type="checkbox" name="<?php echo $setting; ?>" id="<?php echo $setting; ?>" value="1" <?php checked( get_option( $setting, '' ), 1 ); ?> /><br />
								</td>
							</tr>
							<?php
						}

					##################
					# Standard Ad Settings
					##################

						$i = 1;
						while ($i <= 10) {
							$setting = $MAA_vars['OPTIONS'].'admin_standard_'.$i;
							$content = esc_attr( @$GLOBALS['maa_settings']['admin_standard_'.$i] );
							$adsense = stripos($content, 'googlesyndication.com');
							?>
							<tr class='setting standard_ad maa_setting <?php echo $standard_ad; ?>'>
								<td colspan=2>
									<label class='ad_clot_label' for="ad_code_slot_<?php echo $i; ?>">
									<?php
										if ( !function_exists('MAAPRO_Profile_Show_Settings')) {
											_e("AdSense Code Slot $i");
										} else {
											_e("Advertisement Code Slot $i");
										}
									?>
									</label>
									<span class="help_content">
										<h4><?php _e("AdSense Code Slot "); echo  $i; ?></h4>
										<p><?php _e("Paste your AdSense code for slot $i into this box."); ?></p>
										<p><?php _e("Enter the shortcode <b>[MAA id='{$i}']</b> in your articles where you would like this ad to appear."); ?></p>
										<p><input style="width: 100%;background:white" type="text" class="sc" value="[MAA id='<?php echo $i; ?>']" readonly="readonly">
										   <span class="sc_description description"><?php _e('Click to select & Control-C to copy'); ?></span>
										</p>
									</span><br>
									<textarea class="standard_ad_textarea" name="<?php echo $setting; ?>" id="<?php echo $setting; ?>" style="width:100%;height:50px"><?php echo $content; ?></textarea>
									<br>
									<?php if ( !function_exists('MAAPRO_Profile_Show_Settings') && !empty($content) && !$adsense ) { ?>
										<br>
										<div class="notice red">
											<p>
												<?php _e("This is not an AdSense and and will not be displayed."); ?><br><br>
												<?php _e("Upgrade to "); ?>
												<a href="http://thepluginfactory.co/warehouse/multi-author-adsense-pro/?so=profile_page" target="_blank"><?php _e("Multi-Author AdSense Pro"); ?></a>
												<?php _e(" in order to use your own custom ads.");?>
											</p>
										</div>
									<?php } ?>
								</td>
							</tr>
							<?php
							$i++;

						}

					return ob_get_clean();
				}

			############################################
			# Not yet organized
			############################################

			############################################
			# PRO FEATURES
			############################################


		}
	}


$MultiAuthorAdSense = new MultiAuthorAdSense;
$MAA_vars = $MultiAuthorAdSense->get_vars();

/** 
	End of classes and functions setup
*/


if ( is_admin() ) {
	add_action( 'admin_menu',  array($MultiAuthorAdSense,'setup_menu') );
	add_action( 'admin_init',  array($MultiAuthorAdSense,'register') );
	$MultiAuthorAdSense->user_settings();

} else {
	add_shortcode( 'maa', array($MultiAuthorAdSense,'MAA') );
	add_shortcode( 'MAA', array($MultiAuthorAdSense,'MAA') );

	if ( $GLOBALS['maa_pro'] &&  @$GLOBALS['maa_settings']['allow_auto_insert'] == 'true' ) {
		add_filter( 'the_content', 'MAA_auto_insert' );
	}
}