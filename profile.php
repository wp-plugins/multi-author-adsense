<?php

function MAA_extra_user_profile_fields( $user ) {

	## SETUP

		ob_start();
		?>
		<div id="MAA">&nbsp;</div>
		<style type="text/css">
			.warning {
				width:initial;
				border:none;
				border-left: 4px solid #dd3d36;
				background: #fff;
				-webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
				box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
				padding: 1px 12px;
			}
			.warning p {
				margin: 12px;
				font-size: 18px;
			}
			.hidden {display:none !important;}
			.hidden1 {background:yellow !important; padding:15px !important;}
			.warning.blue {border-left: 4px solid #365EDD;}
			.admin_note {background: #FFFFDF;padding: 5px;width:100%;display: block;}

			.button-red {
				color: #FFF;
				border-color: #DB4F4F;
				background: #DF5656;
				-webkit-box-shadow: inset 0 1px 0 #EB5B5B,0 1px 0 rgba(0,0,0,.08);
				box-shadow: inset 0 1px 0 #EB5B5B,0 1px 0 rgba(0,0,0,.08);
				vertical-align: top;
			}

			.button-red:hover {
				color: #FFF;
				background: #CA3C3C;
			}

			.button-green {
				color: #FFF;
				border-color: #219B2B;
				background: #59BD55;
				-webkit-box-shadow: inset 0 1px 0 #5BEB72,0 1px 0 rgba(0,0,0,.08);
				box-shadow: inset 0 1px 0 #5BEB72,0 1px 0 rgba(0,0,0,.08);
				vertical-align: top;
			}

			.button-green:hover {
				color: #FFF;
				background: #33A22F;33A22F
			}

			.help_mode, #maa_settings {
				display: inline-block;
				text-decoration: none;
				margin: 0;
				cursor: pointer;
				border-width: 1px;
				border-style: solid;
				-webkit-border-radius: 3px;
				-webkit-appearance: none;
				border-radius: 3px;
				white-space: nowrap;
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
				height: 24px;
				line-height: 22px;
				padding: 0 8px 1px;
				font-size: 11px;
			}
		</style>
		<?php
		global $MAA_vars;
		global $MAA;
		$pro = false;
		$primary = false;
		if ( $GLOBALS['maa_pro'] === true )
			$pro = true;

		$user_login                  = $user->user_login;
		$maa_settings                = $GLOBALS['maa_settings'];
		$ad_count                    = @$GLOBALS['maa_settings']["user_how_many_adsense_ads_allowed"];
		$allowed_type                = @$GLOBALS['maa_settings']["adsense_only"];
		
		$enable_advanced_ad_settings = @$GLOBALS['maa_settings']["enable_advanced_ad_settings"];
		$enable_standard_ad_settings = @$GLOBALS['maa_settings']["enable_standard_ad_settings"];
		$enable_basic_ad_settings    = @$GLOBALS['maa_settings']["enable_basic_ad_settings"];
		
		if ( $enable_basic_ad_settings == '1' )
			$basic_mode              = @$GLOBALS['maa_settings']["basic_mode"];

		$maa_setting                 = get_user_meta( $user->ID , 'maa_settings' , true );
		$ad_mode                     = esc_attr( @$maa_setting['ad_mode'] );
		$help_mode                   = esc_attr( @$maa_setting['help_mode'] );
		$auto_insert_limit           = @$GLOBALS['maa_settings']["user_how_many_auto_insert_ads"];
		//$maa_settings                = '';
		
		$ad_mode_none                = 'display:none';

		if ($enable_advanced_ad_settings == "1") {
			$adv_button                  = 'primary';
			$advanced_adsense            = '';
			$primary                     = true;
		} else {
			$advanced_adsense            = 'hidden';
			$adv_button                  = '  hidden';
		}

		if ($enable_basic_ad_settings == "1") {
			if ( $primary === true ) {
				$bas_button = 'secondary'; 
				$basic_adsense = 'hidden'; 
			} else {
				$bas_button = 'primary';
				$basic_adsense = '';
				$primary       = true;
			}
		} else {
			$basic_adsense               = 'hidden';
			$bas_button                  = ' hidden';
		}

		if ($enable_standard_ad_settings == "1") {
			if ( $primary === true ) {
				$std_button = 'secondary'; 
				$standard_ad = 'hidden'; 
			} else {
				$std_button = 'primary';
				$standard_ad = '';
				$primary       = true;
			}
		} else {
			$standard_ad               = 'hidden';
			$std_button                  = ' hidden';
		}

	## MAA heading information

		?>
		<h2 style="margin-top: 20px;">
			<img style="vertical-align:bottom" src="<?php echo plugins_url( '/images/maa32.png', __FILE__ ); ?>" />
			<?php _e("Multi-Author AdSense", "blank"); ?>
		</h2>
		<?php

	## DEBUGGING 

		if ( isset($_GET['maa_debug']) ) {
			$this_url    = str_replace( "&maa_debug", '', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] );
			$this_url    = str_replace( "?maa_debug&", '?', $this_url );
			$this_url    = str_replace( "?maa_debug", '', $this_url );

			echo "<div class='warning' style='width:inherit'>";
			_e("Instructions: Click in the box below to select the text, then right-click the highlighted text and select copy. ");
			_e("The content is now pasteable into our support forums: "); echo "<a target='_blank' href='http://thepluginfactory.co/community/forum/plugin-specific/multi-author-adsense/'>http://thepluginfactory.co/community/forum/plugin-specific/multi-author-adsense/</a>"; echo "<br><br>";
			echo "<b>";_e("Once you're complete"); echo ": <a href='//{$this_url}#MAA'>Click here to exit debug mode</a></b>";
			echo "<br><br><textarea readonly onclick='select()' style='width:100%;height:1250px'>";
				
				_e("MAA Settings Dump"); echo "\n";
				print_r( @$maa_setting ); echo "\n\n";

				// _e("Full User Meta Dump"); echo "\n";
				// print_r( $meta, false );

			echo "</textarea></div>";
			return;
		}

	## Reset all settings

		if (isset($_GET['maa_reset_all_settings'])) {

			$reload_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$reload_url = str_replace("maa_reset_all_settings", '#MAA', $reload_url );
			if( delete_user_meta($user->ID, 'maa_settings') ) {
				echo "Deleted all settings";
			}

			// sample link http://dev.thepluginfactory.co/wp-admin/user-edit.php?user_id=2&updated=1&maa_reset_all_settings
		}


	## Email Administration

		if (isset($_GET['email_admin']) && !isset( $_GET['maa_message'])) {

			# VARS
				$site_name   = get_bloginfo( 'name' );
				$admin_email = get_bloginfo( 'admin_email' );
				$site_url    = get_bloginfo( 'wpurl' );
				$from_name   = $user->display_name.' ('.$user->user_login.')';
				$from_email  = $user->user_email;
				$from_link   = "<a href='".admin_url( 'user-edit.php?user_id='.$user->ID  )."'>$from_name</a>";
				$to          = $admin_email;
				$headers[]   = 'Content-type: text/html';
				$headers[]   = "From: $from_name <$from_email>";
				$requests    = intval( @$GLOBALS['maa_settings']['how_many_upgrade_requests'] ) + 1;

			if ($_GET['email_admin'] == "1") {
				$subject = __('Multi-Author AdSense: An author on your site ').$site_name.__(' has requested additional functionality.');
				$message = __("Hello from Multi-Author AdSense,
							<br><br>
							An author <b>{$from_link}</b> on your site <b>").$site_url.__("</b> has requested the ability <b>to store and use Non-AdSense ads for their content.</b>
							<br><br>
							The free version of Multi-Author AdSense only supports using AdSense ads. In order to use Non-AdSense ads, you need to ");
				$message .= "<a href='http://thepluginfactory.co/warehouse/multi-author-adsense-pro/?so=author_request' target='_blank'>".__('upgrade to Multi-Author AdSense Pro')."</a>.";
				$message .= "<br>";
				$message .= "<br>";
				$message .= __("So far there have been <b>{$requests}</b> requests on your website for an upgrade to Multi-Author AdSense Pro.");
				$message .= "<br>";
				$message .= "<h3><a href='http://thepluginfactory.co/warehouse/multi-author-adsense-pro/?so=author_request' target='_blank'>".__('Multi-Author AdSense Pro')."</a>";
				$message .= __(' offers the following feature enhancements:');
				$message .= "</h3>";
				$message .= "<ul>
								<li><b>Allow both AdSense & Non-AdSense Ads.</b></li>
								<li><b>Revenue Sharing.</b> (Generates income for you by splitting ad output between you and the authors)</li>
								<li><b>Show ads after minimum article count.</b> (Generates more articles for you)</li>
								<li><b>Show ads only on articles of a specific length.</b> (Generates longer articles for you)</li>
								<li><b>Show/Hide ads only on articles of a specific category.</b> (Keeps ads off of your special pages)</li>
								<li><b>Show/Hide ads only on articles with a specific tag.</b> (Keeps ads restricted to certain areas)</li>
								<li><b>Restrict specific authors from using Multi-Author AdSense.</b> (Ban trouble posters from using ads)</li>
								<li><b>Restricts ads from the home page, search results, or archives.</b> (Keep ads off of index type pages, so your ads can be shown instead)</li>
							 </ul>";
				$message .= "<br><br>";
				$message .= __('<b>To reply to this author, simply reply to this email.</b>');
				$message .= "<br><br>";
				$message .= "<a href='http://thepluginfactory.co/' target='_blank'>The Plugin Factory</a><br>
							Creating, Reviewing, and Distributing WordPress Plugins";
				
				// $sent = wp_mail( $to, $subject, $message, $headers );
				if( $sent ) {
					$maa_set = get_option( 'maa_settings' );
					$maa_set["how_many_upgrade_requests"]++;
					update_option( 'maa_settings', $maa_set );
					$reload_url = $_SERVER['HTTP_HOST'].strtok($_SERVER["REQUEST_URI"],'?').'?maa_message=2#MAA';	
					
				} else {
					$reload_url = $_SERVER['HTTP_HOST'].strtok($_SERVER["REQUEST_URI"],'?').'?maa_message=3#MAA';	
					$reload_url = str_replace( "&email_admin=1", '', $reload_url );
				}
			} else if ($_GET['email_admin'] == "4") {
				$subject = __('Multi-Author AdSense: An author on your site ').$site_name.__(' has requested additional functionality.');
				$message = __("Hello from Multi-Author AdSense,
							<br><br>
							An author <b>{$from_link}</b> on your site <b>").$site_url.__("</b> has requested the ability to <b>automatically insert ads in their content.</b>
							<br><br>
							The free version of Multi-Author AdSense does not support this. In order to enable the auto insertion ads, you need to ");
				$message .= "<a href='http://thepluginfactory.co/warehouse/multi-author-adsense-pro/?so=author_request' target='_blank'>".__('upgrade to Multi-Author AdSense Pro')."</a>.";
				$message .= "<br>";
				$message .= "<br>";
				$message .= __("So far there have been <b>{$requests}</b> requests on your website for an upgrade to Multi-Author AdSense Pro.");
				$message .= "<br>";
				$message .= "<h3><a href='http://thepluginfactory.co/warehouse/multi-author-adsense-pro/?so=author_request' target='_blank'>".__('Multi-Author AdSense Pro')."</a>";
				$message .= __(' offers the following feature enhancements:');
				$message .= "</h3>";
				$message .= "<ul>
								<li><b>Allow both AdSense & Non-AdSense Ads.</b></li>
								<li><b>Revenue Sharing.</b> (Generates income for you by splitting ad output between you and the authors)</li>
								<li><b>Show ads after minimum article count.</b> (Generates more articles for you)</li>
								<li><b>Show ads only on articles of a specific length.</b> (Generates longer articles for you)</li>
								<li><b>Show/Hide ads only on articles of a specific category.</b> (Keeps ads off of your special pages)</li>
								<li><b>Show/Hide ads only on articles with a specific tag.</b> (Keeps ads restricted to certain areas)</li>
								<li><b>Restrict specific authors from using Multi-Author AdSense.</b> (Ban trouble posters from using ads)</li>
								<li><b>Restricts ads from the home page, search results, or archives.</b> (Keep ads off of index type pages, so your ads can be shown instead)</li>
							 </ul>";
				$message .= "<br><br>";
				$message .= __('<b>To reply to this author, simply reply to this email.</b>');
				$message .= "<br><br>";
				$message .= "<a href='http://thepluginfactory.co/' target='_blank'>The Plugin Factory</a><br>
							Creating, Reviewing, and Distributing WordPress Plugins";
				
				// $sent = wp_mail( $to, $subject, $message, $headers );
				if( $sent ) {
					$maa_set = get_option( 'maa_settings' );
					$maa_set["how_many_upgrade_requests"]++;
					update_option( 'maa_settings', $maa_set );
					$reload_url = $_SERVER['HTTP_HOST'].strtok($_SERVER["REQUEST_URI"],'?').'?maa_message=4#MAA';	
					$reload_url = str_replace( "&email_admin=1", '', $reload_url );
				} else {
					$reload_url = $_SERVER['HTTP_HOST'].strtok($_SERVER["REQUEST_URI"],'?').'?maa_message=3#MAA';	
					$reload_url = str_replace( "&email_admin=1", '', $reload_url );
				}
			}


		}


	## Check if user is banned from MAA

		if ( function_exists('MAAPRO_user_check') && MAAPRO_user_check($user_login) == 'skip' ) {
			echo "<div class='warning'><p>";
				_e("Account Disabled by Administration");
			echo "</p></div>";
			return;
		}


	## Display Message

		if ( isset($_GET['maa_message']) ) {
			$message = $_GET['maa_message'];
			if ($message == "1") {
				echo "<div class='warning blue'><p>";
					_e("Settings have been reset.");
				echo "</p></div>";
			} elseif ($message == "2") {
				echo "<div class='warning blue'><p>";
					_e("An email has been sent to the administration on your behalf requesting the ability to use Non-AdSense ads.");
				echo "</p></div>";
			} elseif ($message == "3") {
				echo "<div class='warning red'><p>";
					_e("Could not send an email to the administration.");
				echo "</p></div>";
			}elseif ($message == "4") {
				echo "<div class='warning blue'><p>";
					_e("An email has been sent to the administration on your behalf requesting the ability to auto insert ads.");
				echo "</p></div>";
			} 
		}

	## Show Settings

		?>
		<table class="form-table">
		<?php

		$help        = false;
		$help_button = 'primary';
		$help_text   = 'Turn Help Off';
		$help_css    = '';

		if ($help_mode == '1') {	
			$help_button = 'secondary';
			$help_text = 'Turn Help On';
			$help_css = 'hidden';
			$help = true;
		}
		?>
			<tr>
				<th><label for="adsense_mode"><?php _e("Ad Mode"); ?></label></th>
				<td>
					<input class="advanced_adsense button button-<?php echo $adv_button; ?> button-small maa_ad_type" type="button" id="advanced_adsense" value="Advanced Mode" />
					<input class="basic_adsense button button-<?php echo $bas_button; ?> button-small maa_ad_type" type="button" id="basic_adsense" value="Basic Mode" />
					<input class="standard_ad button button-<?php echo $std_button; ?> button-small maa_ad_type" type="button" id="standard_ad" value="Standard Mode" />
					<input class="button-green" type="button" id="maa_settings" value="Settings" />
					<input class="help_mode button-red " type="button" value="<?php echo $help_text; ?>" />
					<input type="text" name="ad_mode" id="ad_mode" class="hidden" value="<?php echo esc_attr( @$maa_setting['ad_mode'] ); ?>" /><br />
					<input type="checkbox" name="help_mode" id="help_mode" class="hidden" value="1" <?php checked( esc_attr( @$maa_setting['help_mode'] ),1 ); ?>/><br />
				</td>
			</tr>

			<tr class='advanced_adsense advanced_adsense_help help <?php echo $advanced_adsense.' '.$help_css ?>'>
				<th><?php _e("Advanced AdSense Mode"); ?></th>
				<td>
					<p><?php _e("Fully Responsive AdSense with tracking in your AdSense Dashboard."); ?></p><br>
					<p><?php _e("The fields here are looking for your ad slug/slot (Google changed the term from slug to slot)."); ?></p><br>
					<p><?php _e("The ad slot is a 10 digit number located within your ad code."); ?></p><br>
					<p><?php _e("Some examples of ad slots are: data-ad-slot='9999894561' OR google_ad_slot = '9999894561'"); ?></p><br>
					<p><b><?php _e("ONLY put the 10 digit slot number in the box, not any of the surrounding code."); ?></b></p><br>
					<p><?php _e("To insert an ad in your content, use the shortcode: "); ?><span style='white-space: pre;'>[maa id='advanced']</span></p>
					<?php if ( is_super_admin() ) { ?>
						<p><span class="description admin_note"><?php _e('ADMINISTRATION NOTE: CSS Class: .maa_advanced'); ?></span></p>
					<?php } ?>
				</td>
			</tr>
			<tr class='basic_adsense basic_adsense_help help <?php echo $basic_adsense.' '.$help_css ?>'>
				<th><?php _e("Basic AdSense Mode"); ?></th>
				<td>
					<p><?php _e("Fully Responsive AdSense with NO TRACKING in your AdSense Dashboard."); ?></p><br>
					<p><?php _e("All you need to do here is insert your publisher ID, and check some boxes!"); ?></p><br>
					<p><?php _e("Check the boxes next to the size ad that you want to display."); ?></p><br>
					<p><?php _e("If two ads of the same width are checked, the first ad on the list will be used."); ?></p><br>
					<p><b><?php _e("Be sure to include some larger ads for big monitors, and some smaller sizes for mobile devices."); ?></b></p><br>
					<p><?php _e("To insert an ad in your content, use the shortcode: "); ?><span style='white-space: pre;'>[maa id='basic']</span></p>
					<?php if ( is_super_admin() ) { ?>
						<p><span class="description admin_note"><?php _e('ADMINISTRATION NOTE: CSS Class: .maa_basic'); ?></span></p>
					<?php } ?>
				</td>
			</tr>
			<tr class='standard_ad standard_ad_help help <?php echo $standard_ad.' '.$help_css ?>'>
				<th><label for="adsense_mode"><?php _e("Standard Ad Mode"); ?></label></th>
				<td>
					<p><?php _e("Accepts standard ad code. Not responsive."); ?></p><br>
					<p><?php _e("To insert an ad in your content, use the shortcode provided to the left of each code box."); ?></p>
					<br>
					<p><b><?php _e("You can always insert Standard ads in your content, even if you have Advanced or Basic modes selected."); ?></b></p>
					<br>
					<p><?php _e("To do so, simply enter the shortcode listed next to your Standard ad that you want displayed."); ?></p>
					<?php if ( is_super_admin() ) { ?>
						<p><span class="description admin_note"><?php _e('ADMINISTRATION NOTE: CSS Class: .maa_standard'); ?></span></p>
					<?php } ?>
				</td>
			</tr>

			<tr class='ad_mode_none' style='<?php echo $ad_mode_none ?>'>
				<th><?php _e("Select your Ad Mode"); ?></th>
				<td>
					<p><?php _e("Select the type of AdSense setup mode you'd like to use based on the buttons above."); ?></p>
				</td>
			</tr>
		<?php
		##################
		# Advanced Mode
		##################
		?>
			<tr class="advanced_adsense maa_setting <?php echo $advanced_adsense ?>">
				<th><label for="publisherid_1"><?php _e("Publisher ID"); ?></label></th>
				<td>
					<input type="text" name="publisherid_1" id="publisherid_1" value="<?php echo esc_attr( @$maa_setting['publisherid_1'] ); ?>" class="regular-text" /><br />
					<span class="description advanced_adsense_help help <?php echo $advanced_adsense.' '.$help_css ?>"><?php _e("Example: pub-0173921673266718"); ?></span>
				</td>
			</tr>
		<?php
		foreach ($GLOBALS['maa_adsense_sizes'] as $size => $description) {
			$setting = $size;
			$display_name = $size.' | '.$description;
			?>
			<tr class="advanced_adsense maa_setting <?php echo $advanced_adsense ?>">
				<th><label for="<?php echo $setting; ?>"><?php echo $display_name; ?></label></th>
				<td>
					<input type="text" name="<?php echo $setting; ?>" id="<?php echo $setting; ?>" value="<?php echo esc_attr( @$maa_setting["{$setting}"] ); ?>" class="regular-text" /><br />
					<span class="description advanced_adsense_help help <?php echo $advanced_adsense.' '.$help_css ?>"><?php _e("Please enter your ad slug if you would like a $display_name ad to display."); ?></span>
				</td>
			</tr>
			<?php
		}

		##################
		# Basic AdSense Mode
		##################
		// TODO: Make admin option to hide all checkboxes, and make all checked by default.
		if ($basic_mode == 'full') {
			?>
				<tr class="basic_adsense maa_setting <?php echo $basic_adsense ?>">
					<th><label for="publisherid_2"><?php _e("Publisher ID"); ?></label></th>
					<td>
						<input type="text" name="publisherid_2" id="publisherid_2" value="<?php echo esc_attr( @$maa_setting['publisherid_2'] ); ?>" class="regular-text" /><br />
						<span class="description basic_adsense_help help <?php echo $basic_adsense.' '.$help_css ?>"><?php _e("Example: pub-0173921673266718"); ?></span>
					</td>
				</tr>
			<?php
			foreach ($GLOBALS['maa_adsense_sizes'] as $size => $description) {
				$setting = $size.'_basic';
				$display_name = $size.' | '.$description;
				?>
				<tr class="basic_adsense maa_setting <?php echo $basic_adsense ?>">
					<th><label for="<?php echo $setting; ?>"><?php echo $display_name; ?></label></th>
					<td>
						<input type="checkbox" name="<?php echo $setting; ?>" id="<?php echo $setting; ?>" value="1" <?php checked( @$maa_setting["{$setting}"], 1 ); ?> /><br />
					</td>
				</tr>
				<?php
			}
		} elseif ($basic_mode == 'standard') {
			?>
				<tr class="basic_adsense maa_setting <?php echo $basic_adsense ?>">
					<th><label for="publisherid_2"><?php _e("Publisher ID"); ?></label></th>
					<td>
						<input type="text" name="publisherid_2" id="publisherid_2" value="<?php echo esc_attr( @$maa_setting['publisherid_2'] ); ?>" class="regular-text" /><br />
						<span class="description basic_adsense_help help <?php echo $basic_adsense.' '.$help_css ?>"><?php _e("Example: pub-0173921673266718"); ?></span>
					</td>
				</tr>
			<?php
		} elseif ($basic_mode == 'limited' && is_array( @$GLOBALS['maa_settings']['basic_mode_limited'] ) ) {
			?>
				<tr class="basic_adsense maa_setting <?php echo $basic_adsense ?>">
					<th><label for="publisherid_2"><?php _e("Publisher ID"); ?></label></th>
					<td>
						<input type="text" name="publisherid_2" id="publisherid_2" value="<?php echo esc_attr( @$maa_setting['publisherid_2'] ); ?>" class="regular-text" /><br />
						<span class="description basic_adsense_help help <?php echo $basic_adsense.' '.$help_css ?>"><?php _e("Example: pub-0173921673266718"); ?></span>
					</td>
				</tr>
				<?php
				foreach ( $GLOBALS['maa_settings']['basic_mode_limited'] as $size => $checked ) {
					$setting = $size.'_basic';
					$display_name = $size.' | '.$GLOBALS['maa_adsense_sizes'][$size];
					?>
					<tr class="basic_adsense maa_setting <?php echo $basic_adsense ?>">
						<th><label for="<?php echo $setting; ?>"><?php echo $display_name; ?></label></th>
						<td>
							<input type="checkbox" name="<?php echo $setting; ?>" id="<?php echo $setting; ?>" value="1" <?php checked( @$maa_setting["{$setting}"], 1 ); ?> /><br />
						</td>
					</tr>
					<?php
				}
		} 

		##################
		# Standard Mode
		##################

			$i = 1;
			while ($i <= $ad_count) {
				$content = esc_attr( @$maa_setting['ad_code_slot_'.$i] );
				$adsense = stripos($content, 'googlesyndication.com');
				?>
				<tr class='standard_ad maa_setting <?php echo $standard_ad; ?>'>
					<th><label for="ad_code_slot_<?php echo $i; ?>">
					<?php _e("Ad Code Slot $i"); ?>
					</label><br>
					<input style="width: 115px;background:white" type="text" class="sc" value="[MAA id='<?php echo $i; ?>']" readonly="readonly">
					<br><span class="description"><?php _e('Click to select & Control-C to copy'); ?></span><br>
					<?php if ( is_super_admin() ) { ?>
						<p><span class="description admin_note"><?php _e('ADMINISTRATION NOTE: CSS Class: .maa_standard_'.$i); ?></span></p>
					<?php } ?>
					<span class="description standard_ad_help help <?php echo $standard_ad.' '.$help_css ?>">
						<?php _e("Enter the shortcode <b>[MAA id='{$i}']</b> in your articles where you would like this ad to appear."); ?></span>
					</th>
					<td>
						<textarea name="ad_code_slot_<?php echo $i; ?>" id="ad_code_slot_<?php echo $i; ?>" rows="12" cols="30"><?php echo $content; ?></textarea>
						<br>
						<?php if ( !empty($content) && !$pro && !$adsense ) { ?>
								<br>
								<div class="warning">
								<p>
									<?php _e("This is not an AdSense and and will not be displayed."); ?><br><br>
									<?php 

										$reload_url_link = $_SERVER["HTTP_HOST"].strtok($_SERVER["REQUEST_URI"],'?').'?email_admin=1';
										// if (strpos($reload_url_link, '?') >= 5) {
										// 	$reload_url_link = $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].'&email_admin=1'; 
										// } else {
										// 	$reload_url_link = $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].'?email_admin=1'; 
										// }
										// $reload_url_link = str_replace( "&maa_message", '&trash', $reload_url_link ); 
									?>
									<a href="//<?php echo $reload_url_link ?>"><b><?php _e("Click here to email the site administration"); ?></b></a>
									<?php _e(" and let them know that you'd like to be able to use Non-AdSense ads with your articles.");?>
							
								</p>
							</div>
						<?php } elseif( !empty($content) && $pro ) { 
								 if ( !$adsense && $allowed_type != 'other' && $allowed_type != 'both') {
								 	 ?>
										<br>
										<div class="warning">
											<p>
												<?php _e("This is a Non-AdSense and and will not be displayed."); ?><br><br>									
												<?php _e("Non-AdSense ad code not allowed by the Administration.");?>
											</p>
										</div>
									<?php
								 } elseif ( $adsense && $allowed_type != 'adsense' && $allowed_type != 'both') {
								 	 ?>
										<br>
										<div class="warning">
											<p>
												<?php _e("This is an AdSense and and will not be displayed."); ?><br><br>									
												<?php _e("AdSense ad code not allowed by the Administration.");?>
											</p>
										</div>
									<?php
								 }
							} 
						?>
					</td>
				</tr>
				<?php
				$i++;

			}


		##################
		# Settings
		##################
			// TODO: UPDATE HELP ON THE AUTO INSERT WHEN DIALOGE
			if ($GLOBALS['maa_pro']) {
				if (@$maa_settings['allow_auto_insert'] == 'true') {
					?>

					<tr class="maa_settings maa_setting <?php echo @$maa_settings ?> hidden">
						<th><label for="maa_auto_insert_when"><?php _e("Auto Insert Type"); ?></label></th>
						<td>
							<select name='maa_auto_insert_when'>
								<option value='never' <?php selected( @$maa_setting['maa_auto_insert_when'], 'never' ) ?>>Never automatically insert ads</option>
								<option value='only' <?php selected( @$maa_setting['maa_auto_insert_when'], 'only' ) ?>>Only insert when content does not contain [maa] ads</option>
								<option value='always' <?php selected( @$maa_setting['maa_auto_insert_when'], 'always' ) ?>>Always insert, even if content contains [maa] ads already</option>
							</select><br>
							<span class="description maa_settings_help help <?php echo @$maa_settings.' '.$help_css ?>">
								<?php _e("Select when ads should be automatically inserted."); ?><br>
								<?php _e("This only counts ads inserted by the shortcode [maa and not other types of advertising."); ?>
							</span>
						</td>
					</tr>

					<?php

					$i = 1;
					while ($auto_insert_limit >= $i) {
						?>
						<tr class="maa_settings maa_setting <?php echo @$maa_settings ?> hidden">
							<th><label for="auto_insert_<?php echo $i; ?>"><?php _e("Auto Insert Ad "); echo $i; ?></label></th>
							<td>
								<select name='auto_insert_<?php echo $i; ?>_code'>
									<option value='never' <?php selected( @$maa_setting['auto_insert_'.$i.'_code'], 'advanced' ) ?>>Do Not Insert</option>
									<option value='advanced' <?php selected( @$maa_setting['auto_insert_'.$i.'_code'], 'advanced' ) ?>>[maa id="advanced"]</option>
									<option value='basic' <?php selected( @$maa_setting['auto_insert_'.$i.'_code'], 'basic' ) ?>>[maa id="basic"]</option>
									<?php
										$i2 = 1;
										while ($i2 <= $ad_count) {
											echo "<option value='{$i2}' ".selected( @$maa_setting['auto_insert_'.$i.'_code'], $i2, false ).">[maa id=\"{$i2}\"]</option>";
											$i2++;
										}
									?>
								</select>

								<select name='auto_insert_<?php echo $i; ?>_where'>
									<option value='never' <?php selected( @$maa_setting['auto_insert_'.$i.'_where'], 'advanced' ) ?>>Do Not Insert</option>
									<option value='above' <?php selected( @$maa_setting['auto_insert_'.$i.'_where'], 'above' ) ?>><?php _e("Above the article"); ?></option>
									<option value='below' <?php selected( @$maa_setting['auto_insert_'.$i.'_where'], 'below' ) ?>><?php _e("Below the article"); ?></option>
									<option value='middle' <?php selected( @$maa_setting['auto_insert_'.$i.'_where'], 'middle' ) ?>><?php _e("In the middle of the article"); ?></option>
									<option value='random' <?php selected( @$maa_setting['auto_insert_'.$i.'_where'], 'random' ) ?>><?php _e("After a random paragraph"); ?></option>
									<?php
										$i2 = 1;
										while ($i2 <= 25) {
											echo "<option value='{$i2}' ".selected( @$maa_setting['auto_insert_'.$i.'_where'], $i2, false ).">After paragraph {$i2}</option>";
											$i2++;
										}
									?>
								</select><br>
								<span class="description maa_settings_help help <?php echo @$maa_settings.' '.$help_css ?>">
									<?php _e("Select when and where ads should automatically be inserted in your content."); ?>
								</span>
							</td>
						</tr>
						<?php
						$i++;
					}
				}
			} else {
				?>

				<tr class="maa_settings maa_setting <?php echo @$maa_settings ?> hidden">
					<th><?php _e("Auto Insert Settings"); ?></th>
					<td>
						<?php _e("Auto insert not available."); ?><br><br>
						<?php 
							$reload_url_link = $_SERVER["HTTP_HOST"].strtok($_SERVER["REQUEST_URI"],'?').'?email_admin=4';
						?>

						<a href="//<?php echo $reload_url_link ?>"><b><?php _e("Click here to email the site administration"); ?></b></a>
						<?php _e(" and let them know that you'd like to be able to auto insert ads with your articles.");?>
				
					</td>
				</tr>

				<?php

			}
		?>

			<tr class="maa_settings maa_setting <?php echo @$maa_settings ?> hidden">
				<th><label for="maa_below"><?php _e("Debug"); ?></label></th>
				<td>
					
					<span class="description  <?php echo @$maa_settings ?>">
						<?php _e("Show debug screen, for troubleshooting."); ?><Br>
						<a href="#" id="show_debug">Show Debug Screen</a>
					</span>
				</td>
			</tr>

			<tr class="maa_settings maa_setting <?php echo @$maa_settings ?> hidden">
				<th><label for="maa_below"><?php _e("Reset ALL Settings"); ?></label></th>
				<td>
					
					<span class="description  <?php echo @$maa_settings ?>">
						<?php _e("This removes every setting from your profile page for Multi-Author AdSense."); ?><Br>
						<?php _e("Think of this as a reset, and you're going to have to re-setup all your ads."); ?><br>
						<a href="#" id="full_reset">Yes, I understand. Reset everything.</a>
					</span>
				</td>
			</tr>


		<?php /* TEMPLATE ?>
			<tr>
				<th><label for="city"><?php _e("City"); ?></label></th>
				<td>
					<input type="text" name="city" id="city" value="<?php echo esc_attr( @$maa_setting['city'] ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e("Please enter your city."); ?></span>
				</td>
			</tr>
		<?php */ // TEMPLATE ?>

		</table>
		
		<?php 
			if ( isset( $reload_url  ) ) {
				echo "<script>window.location.href = '//{$reload_url}';</script>";
				die("<h2>RELOADING</h2>");
			}
		?>
		<?php // if ( isset($_GET['maa_message']) ) {die("MESSAGE SET");} ?>
		<script type="text/javascript">

			jQuery(function ($) {
				$(document).ready(function() {

					<?php						
						if (empty($ad_mode)) {
								if ( $enable_advanced_ad_settings == "1" ) {
									?>$("#ad_mode").val('advanced_adsense');$("#submit").click();<?php
								} elseif ( $enable_basic_ad_settings == "1" ) {
									?>$("#ad_mode").val('basic_adsense');$("#submit").click();<?php	
								} elseif ( $enable_standard_ad_settings == "1" ) {
									?>$("#ad_mode").val('standard_ad');$("#submit").click();<?php
								}

						}
					?>


					function reset_confirmation() {
						var answer = confirm("Are you sure you want to reset all your settings?")
						if (answer){
							<?php 
								$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
								if ( stripos($url, "?") ) {
									$url = '//'.$url.'&maa_reset_all_settings#MAA';
								} elseif ( !stripos($url, "?") ) {
									$url = '//'.$url.'?maa_reset_all_settings#MAA';
								} 
							?>
							window.location = "<?php echo $url; ?>";
						}
					}

					$("#full_reset").click(function () {
						reset_confirmation();
						return false;
					});

					$("#show_debug").click(function () {
						<?php 
							$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
							if ( stripos($url, "?") ) {
								$url = '//'.$url.'&maa_debug#MAA';
							} elseif ( !stripos($url, "?") ) {
								$url = '//'.$url.'?maa_debug#MAA';
							} 
						?>
						window.location = "<?php echo $url; ?>";
						return false;
					});

					$(".sc").click(function () {
						$(this).select();
					});

					$(document).keydown(function(event) {
						//19 for Mac Command+S
						if (!( String.fromCharCode(event.which).toLowerCase() == 's' && event.ctrlKey) && !(event.which == 19)) return true;
						$("#submit").click();
						event.preventDefault();
						return false;
					});

					$(".maa_ad_type").click(function() {
						var type = $(this).attr("id");
						var help_mode = $("#help_mode").attr("checked");
						$(".maa_ad_type").removeClass('button-primary');
						$(".maa_ad_type").addClass('button-secondary');
						$(this).addClass('button-primary');
						if (type != 'maa_settings') {
							$("#ad_mode").val(type);
						};
						$(".maa_setting").addClass('hidden');
						$("." + type).removeClass('hidden');
						$("."+type+">_help").removeClass("hidden");
						$(".help").addClass("hidden");
						if (help_mode != "checked") {
							$("."+type+"_help").removeClass("hidden");
						};
						$(".ad_mode_none").hide();
						return false;
					});


					$("#maa_settings").click(function() {
						var type = $(this).attr("id");
						var help_mode = $("#help_mode").attr("checked");
						$(".maa_setting").addClass('hidden');
						$(".maa_settings").removeClass('hidden');
						$(".help").addClass("hidden");
						if (help_mode != "checked") {
							$("."+type+"_help").removeClass("hidden");
						};
						$(".ad_mode_none").hide();
						return false;
					});

					$(".help_mode").click(function() {
						var val = $("#help_mode").attr("checked");
						var ad_mode = $(this).prevAll('.button-primary').attr("id");
						var text = $(".help_mode").attr("value");
						if(val == "checked" || text == 'Turn Help On') {
							// alert("Help was off, now we are turning it on.");
							$(".help_mode").attr("value","Turn Help Off");
							$(".help").addClass("hidden");
							$("#help_mode").prop("checked",false);
							$("."+ad_mode+"_help").removeClass("hidden");
						} else {
							// alert("Help was on, now we are turning it off.");
							$(".help_mode").attr("value","Turn Help On");
							$("#help_mode").prop("checked",true);
							$("."+ad_mode+"_help").addClass("hidden");
						}

						return false;
					});


				});
			});
		</script>
		<?php
	echo ob_get_clean();
}

########################################################################################################################
# SAVE THE SETTINGS
########################################################################################################################

	function MAA_save_extra_user_profile_fields( $user_id ) {

		if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
		global $MAA_vars;
		global $MAA;
		$maa_settings = $GLOBALS['maa_settings'];

		$user_info    = get_userdata($user_id);
		$user_login   = $user_info->user_login;

		if ( function_exists('MAAPRO_user_check') && MAAPRO_user_check($user_login) == 'skip' ) { return false; }
		$ad_count = @$GLOBALS['maa_settings']["user_how_many_adsense_ads_allowed"];
		$auto_insert_limit = @$GLOBALS['maa_settings']["user_how_many_auto_insert_ads"];

		# Backup settings
			$maa_setting = get_user_meta( $user_id , 'maa_settings' , true );
			update_user_meta( $user_id, 'maa_settings_backup', @$maa_setting );

		$settings = array (
			'ad_mode' => '',
			'help_mode' => '',
			'publisherid_1' => '',
			'publisherid_2' => '',
			'maa_auto_insert_when' => '',
		);

		foreach ($settings as $key => $value) {
			if ( isset($_POST["$key"]) ) {
				$settings["$key"] = $_POST["$key"];
			} else {
				$settings["$key"] = '';
			}
		}


		# Advanced Settings
			if ( @$maa_settings["enable_advanced_ad_settings"] == '1' ) {
				foreach($GLOBALS['maa_adsense_sizes'] as $size => $description) {
					$settings["$size"] = $_POST["$size"];				
				}
			}

		# Basic Settings
			if ( @$maa_settings["enable_basic_ad_settings"] == '1' )
				$basic_mode = @$maa_settings["basic_mode"];

			if ($basic_mode == 'full') {
				foreach ($GLOBALS['maa_adsense_sizes'] as $size => $description) {
					$settings[$size] = $_POST["{$size}"];
					if (isset($_POST["{$size}_basic"])) {
						$settings[$size.'_basic'] = $_POST["{$size}_basic"];
					} else {
						$settings[$size.'_basic'] = '';
					}
				}
			} elseif ($basic_mode == 'standard') {
				
			} elseif ($basic_mode == 'limited' && is_array( @$GLOBALS['maa_settings']['basic_mode_limited'] ) ) {
				foreach ( $GLOBALS['maa_settings']['basic_mode_limited'] as $size => $checked) {
					$settings[$size] = $_POST["{$size}"];
					if (isset($_POST["{$size}_basic"])) {
						$settings[$size.'_basic'] = $_POST["{$size}_basic"];
					} else {
						$settings[$size.'_basic'] = '';
					}
				}
			} 

		$i = 1;
		while ($i <= $ad_count) {
			$settings['ad_code_slot_'.$i] = $_POST['ad_code_slot_'.$i];
			$i++;
		}

		$i = 1;
		while ($auto_insert_limit >= $i) {
			$settings['auto_insert_'.$i.'_code'] = $_POST['auto_insert_'.$i.'_code'];
			$settings['auto_insert_'.$i.'_where'] = $_POST['auto_insert_'.$i.'_where'];
			$i++;
		}

		update_user_meta( $user_id, 'maa_settings', $settings );

	}

########################################################################################################################
# REGISTER THE ACTIONS
########################################################################################################################

	add_action( 'show_user_profile', 'MAA_extra_user_profile_fields' );
	add_action( 'edit_user_profile', 'MAA_extra_user_profile_fields' );

	add_action( 'personal_options_update', 'MAA_save_extra_user_profile_fields' );
	add_action( 'edit_user_profile_update', 'MAA_save_extra_user_profile_fields' );

?>