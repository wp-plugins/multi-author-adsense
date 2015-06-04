<?php

global $MAA;
global $MAA_vars;
include('admin_functions.php');

if (!isset($MultiAuthorAdSense) || !is_object(@$MultiAuthorAdSense)) {


	$MultiAuthorAdSense = new MultiAuthorAdSense;
	$MAA_vars = $MultiAuthorAdSense->get_vars();
}


?>
<div class="wrap">

	<form method="post" action="options.php" id="<?php echo $MAA_vars['OPTIONS_ID']; ?>_options_form" name="<?php echo $MAA_vars['OPTIONS_ID']; ?>_options_form">

		<?php 

			settings_fields($MAA_vars['OPTIONS_ID']); 
			$maa_settings = $GLOBALS['maa_settings'];

			if (isset($_GET['maa_reset_all'])) {
				unset($GLOBALS['maa_settings']);
				if (delete_option( 'maa_settings' )) echo "Options Deleted";
				$reload_url = str_replace( "&maa_reset_all", '', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] );
				//echo __('All settings were reset. Reloading settings page');
				echo "<script>window.location.href = '//{$reload_url}';</script>";
				exit;
			} elseif(empty($maa_settings)) {
				echo "<script>window.location.href = 'admin.php?page=multi-author-adsense';</script>";
				exit;
			}

		?>


		<h2><img class="maalogo" src="<?php echo plugins_url( '/images/maa64.png', __FILE__ ); ?>" /> Multi-Author AdSense v<?php echo $MAA_vars['VERSION']; ?> &raquo; Settings</h2>
		<div id="information">&nbsp;</div>
		<Br>
		<a href='http://consultingwp.com?so=maa' target="_blank"><span style='background-color:yellow;  padding: 5px 15px;margin-top: 10px;display: inline-block;border: 1px solid yellowgreen;color:black;font-weight:bold'>Need a WordPress coder?</span></a> <a href='http://consultingwp.com?so=maa' target="_blank">Contact <span style="color:#d54e21">Consulting WP</span> about custom wordpress projects or error fixes. Pay per 15 minutes worked.</a><br>
		<br>
		<table class="widefat mma_table" style="width:initial;margin-bottom: 20px;margin-top:15px;">
			<thead>
					<th colspan="2"  style="width:100%;height: 23px;">
						<input type="submit" id="submit" name="submit" value="Save Settings" class="button-primary" />
					</th>
			   </tr>
			</thead>
			<tr style="vertical-align:top;">

			<!-- MAIN SETTINGS -->

				<td style="vertical-align:top;">
					<table>
						<tbody>
							<tr>
								<td colspan>
									<h3><?php _e("Administration Options"); ?></h3>
								</td>
								<td style="vertical-align: middle !important;">								
									<?php
										if (!isset($_GET['maa_debug'])) {
											?>
											<a id="reset_settings" href="admin.php?page=multi-author-adsense&maa_reset_all" style='color:#F00;float:right;margin-left:5px;'>Reset All Settings</a>							
											<a id="debug" href="admin.php?page=multi-author-adsense&maa_debug" style='color:#666;float:right'>Debug</a>
											<?php
										}
									?>
								</td>
							</tr>
							<tr>
								<td colspan=2>
									<?php

										if (isset($_GET['maa_debug'])) {
											echo "<h3>".__("MULTI-AUTHOR ADSENSE DEBUG SCREEN")."</h3>";
											_e("Click in the box below to select the text, then right-click the highlighted text and select copy."); echo "<br><br>";
											_e("The content is now pasteable into our support forum: "); echo "<a target='_blank' href='http://thepluginfactory.co/community/forum/plugin-specific/multi-author-adsense/'>Official Support Forum</a>\n\n";
											echo "<br><br>";
											_e("Once you're complete"); echo ": <a href='admin.php?page=multi-author-adsense'>Click here to exit debug mode</a>\n\n";
											echo "<br><br><textarea readonly onclick='select()' style='width:100%;height:700px'>";
												_e("MAA Settings Dump"); echo "\n";
												print_r( $maa_settings ); echo "\n\n";
											echo "</textarea>";
											exit;
										}
									?>
								</td>
							</tr>						
						<?php
						############################################
						# Select which ad modes to enable
						############################################
						?>
							<tr class='setting'>
								<td>
									<a href='#enabled_ad_modes_help' class='popup-with-zoom-anim'><?php _e('Enabled Ad Modes'); ?></a>
									<div class='help_content zoom-anim-dialog mfp-hide' id='enabled_ad_modes_help'>
										<h3><?php _e("Enabled Ad Modes"); ?></h3>
										<p><?php _e("Select which types of ad modes to enable for Authors."); ?></p>

											<h4><?php _e("Basic AdSense Mode"); ?></h4>
												<p><?php _e("Fully Responsive AdSense with NO TRACKING in your AdSense Dashboard."); ?></p>
												<p><?php _e("All you need to do here is insert your publisher ID, and check some boxes!"); ?></p>
												<p><?php _e("Check the boxes next to the size ad that you want to display."); ?></p>
												<p><?php _e("To insert an ad in your content, use the shortcode: "); ?><span style='white-space: pre;'>[maa id='basic']</span></p>
											<h4><?php _e("Standard Ad Mode"); ?></h4>
												<p><?php _e("Accepts standard/unmodified ad code. Not responsive."); ?></p>
												<p><?php _e("To insert an ad in your content, use the shortcode provided to the left of each code box on your profile settings page."); ?></p>
												<p><?php _e("Example: "); ?><span style='white-space: pre;'>[maa id='1']</span></p>
											<h4><?php _e("Advanced AdSense Mode"); ?></h4>
												<p><?php _e("Fully Responsive AdSense with tracking in your AdSense Dashboard."); ?></p>
												<p><?php _e("To insert an ad in your content, use the shortcode: "); ?><span style='white-space: pre;'>[maa id='advanced']</span></p>

									</div>
								</td>
								<td style="text-align:right;vertical-align:top !important;">
									<b><?php _e("Basic Mode"); ?></b> <input type="checkbox" name="maa_settings[enable_basic_ad_settings]" id="maa_settings_enable_basic_ad_settings" value="1" <?php checked( @$maa_settings["enable_basic_ad_settings"], 1 ); ?> /><br>
									<?php _e("Standard Mode"); ?> <input type="checkbox" name="maa_settings[enable_standard_ad_settings]" id="maa_settings_enable_standard_ad_settings" value="1" <?php checked( @$maa_settings["enable_standard_ad_settings"],1 ); ?> /><br>
									<?php _e("Advanced Mode"); ?> <input type="checkbox" name="maa_settings[enable_advanced_ad_settings]" id="maa_settings_enable_advanced_ad_settings" value="1" <?php checked( @$maa_settings["enable_advanced_ad_settings"], 1 ); ?> />
								</td>
							</tr>
						<?php
						############################################
						# Basic Mode Settings
						############################################
						?>
							<tr class='setting basic_mode_settings <?php if( $maa_settings["enable_basic_ad_settings"] != 1 ) echo "hidden"; ?>'>
								<td style="padding-left:30px;">
									<a href='#enable_basic_ad_settings' class='popup-with-zoom-anim'><?php _e('Basic Mode Settings'); ?></a>
									<div class='help_content zoom-anim-dialog mfp-hide' id='enable_basic_ad_settings'>
										<h3><?php _e("Basic Mode Settings"); ?></h3>
										<h4><?php _e("Standard"); ?></h4>
											<p><?php _e("Accepts ONLY the authors AdSense publisher ID."); ?></p>
											<p><?php _e("Multi-Author AdSense will automatically determine the largest ad size that will fit in their content to display."); ?></p>
										<h4><?php _e("Full"); ?></h4>
											<p><?php _e("Allow your authors to select which ad sizes they should have available to them."); ?></p>
											<p><?php _e("This means that an author can select any size AdSense ad to display within their content."); ?></p>
										<h4><?php _e("Limited"); ?></h4>
											<p><?php _e("Allow you (the admin) to specify which ad sizes the authors have available to them for selection."); ?></p>
											<p><?php _e("If you prefer not to allow wide banner ads in the content, then you can select to disable them here."); ?></p>
										
									</div>
								</td>
								<td style="text-align:right;vertical-align:top !important;">
									Standard <input class="limited_settings" type="radio" name="maa_settings[basic_mode]" value="standard" <?php checked( @$maa_settings['basic_mode'], 'standard') ?>><br>
									Full <input     class="limited_settings" type="radio" name="maa_settings[basic_mode]" value="full"     <?php checked( @$maa_settings['basic_mode'], 'full') ?>><br>
									Limited <input  class="limited_settings" type="radio" name="maa_settings[basic_mode]" value="limited"  <?php checked( @$maa_settings['basic_mode'], 'limited') ?>>
								</td>
							</tr>

						<?php
						############################################
						# Limited Basic Mode Settings
						############################################
						?>
							<tr class='setting limited_basic_mode_settings <?php if( $maa_settings["basic_mode"] != 'limited' ) echo "hidden"; ?>'>
								<td style="padding-left:30px;">

									<a href='#basic_mode' class='popup-with-zoom-anim'><?php _e('Allowed AdSense Sizes'); ?></a>
									<div class='help_content zoom-anim-dialog mfp-hide' id='basic_mode'>
										<h3><?php _e("Basic Mode: Allowed AdSense Sizes"); ?></h3>
										<h4><?php _e("Standard"); ?></h4>
											<p><?php _e("Accepts ONLY the authors AdSense publisher ID."); ?></p>
											<p><?php _e("Multi-Author AdSense will automatically determine the largest ad size that will fit in their content to display."); ?></p>
										<h4><?php _e("Full"); ?></h4>
											<p><?php _e("Allow your authors to select which ad sizes they should have available to them."); ?></p>
											<p><?php _e("This means that an author can select any size AdSense ad to display within their content."); ?></p>
										<h4><?php _e("Limited"); ?></h4>
											<p><?php _e("Allow you (the admin) to specify which ad sizes the authors have available to them for selection."); ?></p>
											<p><?php _e("If you prefer not to allow wide banner ads in the content, then you can select to disable them here."); ?></p>
										
									</div>
								</td>
								<td style="text-align:right;vertical-align:top !important;">
									<a href="#" id="limited_basic_toggle">Modify Allowed AdSense Sizes</a>
									<div id="limited_basic_sizes" class='hidden'>
										<?php
											$output = '';
											foreach ($GLOBALS["maa_adsense_sizes"] as $size => $desc) {
												
												$output .= '<label for="maa_settings[basic_mode_limited]['.$size.']">
																<span style="width:60px;display:inline-block;">'.$size.'</span> | <b>'.$desc.'</b>
															</label>
															<input type="checkbox" name="maa_settings[basic_mode_limited]['.$size.']" value="1"  '.checked( @$maa_settings["basic_mode_limited"][$size] , 1 , false).' style="margin:0px;" />
															<br>';
											}

											echo $output;

										?>
									</div>
								</td>
							</tr>

						<?php
						############################################
						# Select how many standard ad code blocks
						# an author can store in their profile
						############################################
						?>
							<tr class='setting standard_mode_settings <?php if ($maa_settings["enable_standard_ad_settings"] != 1) { echo "hidden"; } ?>'>
								<td>
									<a href='#enable_standard_ad_settings' class='popup-with-zoom-anim'><?php _e('Author Standard Mode Ad Storage Limit'); ?></a>
									<div class='help_content zoom-anim-dialog mfp-hide' id='enable_standard_ad_settings'>
										<h3><?php _e("Author Standard Mode Ad Storage Limit"); ?></h3>
										<p><?php _e("How many Ads ads can an author keep in their profile?"); ?></p>
										<p><?php _e("For example, you can allow an author to store 10 ads, but only insert 2 ads per post."); ?></p>
									</div>
								</td>
								<td style="text-align:center;vertical-align:middle !important;">
									<select class="fullwidth_select" name='maa_settings[user_how_many_adsense_ads_allowed]'>
										<?php
											$i = 0;
											while ($i <= 20) {
												echo "<option value='{$i}' ".selected( @$maa_settings["user_how_many_adsense_ads_allowed"], "$i" )." >{$i}</option>";
												$i++;
											}
										?>
									</select>
								</td>
							</tr>
						<?php
						############################################
						# Select how many AdSense ads an author
						# can post within their content
						############################################
						?>
							<tr class='setting'>
								<td>
									<a href='#user_how_many_adsense_ads_display_allowed' class='popup-with-zoom-anim'><?php _e('Author AdSense Ad Display Limit'); ?></a>
									<div class='help_content zoom-anim-dialog mfp-hide' id='user_how_many_adsense_ads_display_allowed'>
										<h3><?php _e("Author AdSense Ad Display Limit"); ?></h3>
										<p><?php _e("How many AdSense ads can an author display within their content?"); ?></p>
										<p><?php _e("Keep in mind, that you are limited to three AdSense ads per webpage total. This means that if you, the site owner, want to display a single AdSense ad on every page of your website, then you should limit your authors to 2 AdSense ads."); ?></p>
										<p><?php _e("Likewise, if you, want to display two of your own AdSense ads on every page of your website, then you should limit your authors to 1 AdSense ads."); ?></p>
									</div>
								</td>
								<td style="text-align:center;vertical-align:middle !important;">
									<select class="fullwidth_select" name='maa_settings[user_how_many_adsense_ads_display_allowed]'>
										<?php
											$i = 0;
											while ($i <= 10) {
												echo "<option value='{$i}' ".selected( @$maa_settings["user_how_many_adsense_ads_display_allowed"], "$i" )." >{$i}</option>";
												$i++;
											}
										?>
									</select>
								</td>
							</tr>
						<?php
						############################################
						# Select how many AdSense ads an author
						# can post within their content
						############################################
						?>
							<tr class='setting'>
								<td>
									<a href='#ad_supression_abilities' class='popup-with-zoom-anim'><?php _e('Ad Supression Capabilities'); ?></a>
									<div class='help_content zoom-anim-dialog mfp-hide' id='ad_supression_abilities'>
										<h3><?php _e("Ad Supression Capabilities"); ?></h3>
										<p><?php _e("When creating or editing content, there is a box avaliable to you on the post edit page which disables the output of ads on this single piece of content."); ?></p>
										<p><?php _e("Who should have the ability to disable ads on a single post?"); ?></p>
										<h4><?php _e("Admin Only"); ?></h4>
										<p><?php _e("Only the site administrators can see the checkboxes and select whether or not a single post should have ads on it."); ?></p>
										<h4><?php _e("Both Admin and User"); ?></h4>
										<p><?php _e("The admin staff, and the post author should both have the ability to hide ads on the single post."); ?></p>
									</div>
								</td>
								<td style="text-align:center;vertical-align:middle !important;">
									<select class="fullwidth_select" name='maa_settings[ad_supression_abilities]'>
										<option value='admin' <?php echo selected( @$maa_settings["ad_supression_abilities"], "admin" ) ?> ><?php _e('Admin Only') ?></option>
										<option value='both' <?php  echo selected( @$maa_settings["ad_supression_abilities"], "both" )  ?> ><?php _e('Both Admin and User') ?></option>
										<option value='none' <?php  echo selected( @$maa_settings["ad_supression_abilities"], "none" )  ?> ><?php _e('Disable Ad Supression Cababilities') ?></option>
									</select>
								</td>
							</tr>

						<?php
						############################################
						# Default Ad Alignment
						############################################
						?>
							<tr class='setting'>
								<td>
									<a href='#ad_alignment' class='popup-with-zoom-anim'><?php _e('Ad Alignment'); ?></a>
									<div class='help_content zoom-anim-dialog mfp-hide' id='ad_alignment'>
										<h3><?php _e("Ad Alignment"); ?></h3>
										<p><?php _e("When the ad is displayed in the content of the article, do you want it to float left of the text, right of the text, centered, or no text wrap?"); ?></p>
										<p><?php _e("If you select right or left, the text will wrap around the ad. If you select center or no text wrapping, the text will not wrap."); ?></p>
									</div>
								</td>
								<td style="text-align:center;vertical-align:middle !important;">
									<select class="fullwidth_select" name='maa_settings[ad_alignment]'>
										<option value='none' <?php echo selected( @$maa_settings["ad_alignment"], "none" ) ?> ><?php _e('No Text Wrapping') ?></option>
										<option value='left' <?php  echo selected( @$maa_settings["ad_alignment"], "left" )  ?> ><?php _e('Left Align') ?></option>
										<option value='right' <?php  echo selected( @$maa_settings["ad_alignment"], "right" )  ?> ><?php _e('Right Align') ?></option>
										<option value='center' <?php  echo selected( @$maa_settings["ad_alignment"], "center" )  ?> ><?php _e('Center Align') ?></option>
									</select>
								</td>
							</tr>
						<?php
						############################################
						# MAA Pro Settings
						############################################
							if (!$pro) {
								?>
								<tr>
									<td colspan=2>
										<?php
											$demo = false;
											if ( @$maa_settings["pro_demo"] == 1 ) {
												$demo = true;
											}
											if ( $demo === false ) {
												?><a href="#" id="pro_demo"><?php _e('Show Pro Settings Demo') ?></a><?php
											} else {
												?><a href="#" id="pro_demo"><?php _e('Hide Pro Settings Demo') ?></a><?php
											}
										?>
										<input type='checkbox' value='1' name="maa_settings[pro_demo]" id="maa_settings_pro_demo" <?php checked(@$maa_settings["pro_demo"],1); ?> class="hidden" />
									</td>
								</tr>
								<?php
							}
							?>
							<tr class='setting prosetting'>
								<td colspan=2>
									<h3><?php _e("Multi-Author AdSense Pro Settings"); ?></h3>
										<?php if (!$pro) {
												echo "<span style='color:red'>".__("<b>DEMO MODE:</b> Changes will be saved, but won't actually do anything. This is not trialware, but an example of the settings and options available by upgrading.")."</span>";
											  }
										?>
								</td>
							</tr>
							<?php
							echo $MultiAuthorAdSense->pro_settings($maa_settings);
						?>
						</tbody>
					</table>
				</td>

			<!-- SIDEBAR SETTINGS -->

				<td style="min-width: 275px;max-width: 445px;vertical-align:top;">
					<table>
						<tbody>
							<tr>
								<td>
									<a href="http://thepluginfactory.co/?so=MAA_tpf_logo" target="_blank" title="The Plugin Factory"><img src="<?php echo plugins_url( '/images/ThePluginFactoryLogo.png', __FILE__ ) ?>" style="width:100%;max-width:420px;" /></a><br>
									<?php if( !$pro ) { ?>
									<a class="button button-primary button-red full_wide" href="http://thepluginfactory.co/warehouse/multi-author-adsense-pro/?so=maa_go_pro_button" target="_blank">Get Multi-Author AdSense Pro Today!</a>
									<a href="http://thepluginfactory.co/warehouse/multi-author-adsense-pro/?so=maa_go_pro_belcher" target="_blank"><img src="<?php echo plugins_url( '/images/maa_pro.png', __FILE__ ) ?>" style="width:100%;max-width:420px;" /></a>
									<?php } ?>
									<br>
									<a class="button button-secondary full_wide" href="http://thepluginfactory.co/community/forum/plugin-specific/multi-author-adsense/?so=maa_official_support_link" target="_blank">Official Support Forum</a>
									<br>
									<a class="button button-secondary full_wide" href="http://thepluginfactory.co/warehouse/multi-author-adsense/?so=maa_official_website_link" target="_blank">Official Website</a>
									<?php if( !$pro ) { ?>
									<h4 style="text-align:center"><?php _e("Upgrade requests by users: "); echo intval( @$maa_settings["how_many_upgrade_requests"] ); ?></h4>
									<?php } ?>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tfoot>
			  <tr>
				<th colspan="2" ><input type="submit" id="submit" name="submit" value="Save Settings" class="button-primary" /></th>
			  </tr>
			</tfoot>
		</table>
	</form>

	<script type="text/javascript" src="<?php echo plugins_url('/js/stickUp.min.js',__FILE__) ?>"></script>
	<script type="text/javascript">
		jQuery(function ($) {
			$(document).ready(function() {

			// Sticky Help
				// $('#helpdiv').stickUp({
    //                   marginTop: '50px;'
    //                 });


				<?php
					if ($demo === true) {
						echo '$(".prosetting").show();';
					} 
				?>


				$("#maa_settings_enable_basic_ad_settings").click(function() {					
					var val = $(this).is(":checked");
					if (val) {
						$(".basic_mode_settings").removeClass( 'hidden' );
						$(".basic_mode_settings").show( "400" );
						var bas_mode = $("input[name='maa_settings[basic_mode]']:checked").val();
						if (bas_mode == 'limited') {
							$(".limited_basic_mode_settings").removeClass( 'hidden' );
							$('.limited_basic_mode_settings').show( "400" );

						};
					} else {
						$(".basic_mode_settings").hide( "400" );
						$(".basic_mode_settings").addClass( 'hidden' );
						$(".limited_basic_mode_settings").hide( "400" );
						$(".limited_basic_mode_settings").addClass( 'hidden' );
					}

				});


				$("#maa_settings_enable_standard_ad_settings").click(function() {					
					var val = $(this).is(":checked");
					if (val) {
						$(".standard_mode_settings").removeClass( 'hidden' );
						$(".standard_mode_settings").show( "400" );						
					} else {
						$(".standard_mode_settings").hide( "400" );
						$(".standard_mode_settings").addClass( 'hidden' );
					}

				});

				$(".limited_settings").click(function() {					
					var val = $(this).val();
					if (val == 'limited') {
						$(".limited_basic_mode_settings").removeClass( 'hidden' );
						$('.limited_basic_mode_settings').show( "400" );						
					} else {
						$(".limited_basic_mode_settings").addClass( 'hidden' );
						$('.limited_basic_mode_settings').hide( "400" );
					}
				});

				$("#limited_basic_toggle").click(function() {					
						$("#limited_basic_sizes").toggleClass( 'hidden' );
				});

				$("#pro_demo").click(function() {					
					var text = $(this).text();
					if (text == 'Show Pro Settings Demo') {
						$(".prosetting").show();
						$(this).text("Hide Pro Settings Demo");
						$("#maa_settings_pro_demo").prop("checked",true);
					} else {
						$(".prosetting").hide();
						$(this).text("Show Pro Settings Demo");
						$("#maa_settings_pro_demo").prop("checked",false);
					}

					return false;
				});

				$('#authors_dropdown').change(function() {
						var val = $(this).val();

						var orig = $('#blocked_authors').val();
						var orig_arr = orig.split(",");
						var in_array = $.inArray( val , orig_arr );

						if (in_array >= 0) {
							$('#authors_dropdown').val('_');
							// notify('<div class="notice red"><p>User <b>'+val+'</b> is already on your list</p></div>');
							return false;
						};

						if (orig.length <= 0) {
							var new_list = val;
						} else {
							var new_list = orig +','+ val;
						};

						$('#blocked_authors').val( new_list );
						$('#authors_dropdown').val('_');
						// notify('<div class="notice"><p>Ads will not be displayed on  <b>'+val+'\'s</b> content</p></div>');
						return false;

					});

					$('.post_tags').change(function() {
						var val = $(this).val();

						var orig = $('#blocked_tags').val();
						var orig_arr = orig.split(",");
						var in_array = $.inArray( val , orig_arr );

						if (in_array >= 0) {
							$('.post_tags').val('');
							notify('<div class="notice red"><p>Tag <b>'+val+'</b> is already on your list</p></div>');
							return false;
						};

						if (orig.length <= 0) {
							var new_list = val;
						} else {
							var new_list = orig +','+ val;
						};

						$('#blocked_tags').val( new_list );
						$('.post_tags').val('');
						// notify('<div class="notice"><p>Ads will not be displayed on content with the tag <b>'+val+'</b></p></div>');
						return false;

					});

					$('.post_categories').change(function() {
						var val = $(this).val();

						var orig = $('#blocked_categories').val();
						var orig_arr = orig.split(",");
						var in_array = $.inArray( val , orig_arr );

						if (in_array >= 0) {
							$('.post_categories').val('');
							// notify('<div class="notice red"><p>Category <b>'+val+'</b> is already on your list</p></div>');
							return false;
						};

						if (orig.length <= 0) {
							var new_list = val;
						} else {
							var new_list = orig +','+ val;
						};

						$('#blocked_categories').val( new_list );
						$('.post_categories').val('');
						// notify('<div class="notice"><p>Ads will not be displayed on content with the Category <b>'+val+'</b></p></div>');
						return false;

					});


					$('.allowed_content_types').change(function() {
						var val = $(this).val();

						var orig = $('#allowed_content_types').val();
						var orig_arr = orig.split(",");
						var in_array = $.inArray( val , orig_arr );

						if (in_array >= 0) {
							$('.allowed_content_types').val('');
							// notify('<div class="notice red"><p>Category <b>'+val+'</b> is already on your list</p></div>');
							return false;
						};

						if (orig.length <= 0) {
							var new_list = val;
						} else {
							var new_list = orig +','+ val;
						};

						$('#allowed_content_types').val( new_list );
						$('.allowed_content_types').val('');
						// notify('<div class="notice"><p>Ads will not be displayed on content with the Category <b>'+val+'</b></p></div>');
						return false;

					});


					function notify(message){

						$('#information')
							.html( message )
							.fadeIn();

						setTimeout(function(){
							remove_information();
						},15000);
					};


					function remove_information(){
						$('#information').fadeOut();
					};

			// MAIN SETTINGS

					$("#help_toggle").click(function() {
						$("#help_table tbody").fadeToggle();
						var val = $(this).val();
						if (val == 'Turn help off') {
							$(this).val("Turn help on");
							$("#maa_settings_help_toggle").prop("checked",false);
						} else {
							$(this).val("Turn help off");
							$("#maa_settings_help_toggle").prop("checked",true);
						}
						return false;
					});

					$('tr.setting').mouseenter(function() {
						var text = $(this).find('.mfp-hide').html();
						$("#helpbox").html(text);
						return false;
					});

					$(".maa_ad_type").click(function() {
						var type = $(this).attr("id");
						$(".maa_ad_type").removeClass('button-primary');
						$(".maa_ad_type").addClass('button-secondary');
						$(this).addClass('button-primary');
						$("#maa_settings_admin_ad_mode").val(type);
						$(".maa_setting").addClass('hidden');
						$("." + type).removeClass('hidden');
						return false;
					});

					$(".<?php echo $ad_mode; ?>").removeClass('hidden');

					$(".sc").click(function () {
						$(this).select();
					});


					$(document).on('click', '.sc', function() {
						$(this).select();
					});

					$(document).on('click', '.sc_description', function() {
						$(this).prev('input').select();
					});

					$("#reset_settings").click( function() {
						var r = confirm("<?php _e('This will reset all settings on this screen. It is irreversible!'); ?>");
						if (r == true) {
						} else {
							return false;
						}
					});

			// CONTROL-S = Save
				$(document).keydown(function(event) {
					//19 for Mac Command+S
					if (!( String.fromCharCode(event.which).toLowerCase() == 's' && event.ctrlKey) && !(event.which == 19)) return true;
					$("#submit").click();
					event.preventDefault();
					return false;
				});

			// Lightbox settings
				$(document).ready(function() {
					$('.popup-with-zoom-anim').magnificPopup({
						type: 'inline',

						fixedContentPos: false,
						fixedBgPos: true,

						overflowY: 'auto',

						closeBtnInside: true,
						preloader: false,

						midClick: true,
						removalDelay: 300,
						mainClass: 'my-mfp-zoom-in'
					});
				});




			});
		});
	</script>