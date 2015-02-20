<?php

$GLOBALS['maa_pro'] = true;

############################################
# Profile Page Function
############################################

	function MAAPRO_Profile_Show_Settings($this_user) {
		global $maa_adsense_sizes;
	}

	function MAAPRO_ad_limit($ad_limit) {
		global $MAA_vars;
		$options_id = $MAA_vars["OPTIONS"];
		$non_adsense_limit = @$GLOBALS['maa_settings']['user_how_many_non_adsense_ads_allowed'];
		$ad_limit = intval($ad_limit) + intval($non_adsense_limit);
		return $ad_limit;
	}

	function MAAPRO_Profile_JS() {
		ob_start();
		?>
			$(".maa_ad_type").click(function() {
				var type = $(this).attr("id");
				$(".maa_ad_type").removeClass('button-primary');
				$(".maa_ad_type").addClass('button-secondary');
				$(this).addClass('button-primary');
				$("#ad_mode").val(type);
				$(".maa_setting").addClass('hidden');
				$("." + type).removeClass('hidden');
				return false;
			});
		<?php
		return ob_get_clean();
	}

############################################
# Frontend Ad Output Functions
############################################

	function MAAPRO_user_check($user){
		$blocked_users = @$GLOBALS['maa_settings']['hide_ads_on_users'];
			if (strpos($blocked_users, ",")) {
				$blocked_users = explode(",", @$blocked_users);
			} elseif (!empty($blocked_users)) {
				$blocked_users = array("$blocked_users");
			} else {
				return 'good';
			}
		if (in_array($user, $blocked_users)) {
			return 'skip';
		}
		return 'good';
	}

	function MAAPRO_Revenue_Sharing($author_id){
		if (!empty($GLOBALS['maa_settings']['revenue_sharing']) && $GLOBALS['maa_settings']['revenue_sharing'] >= 1 ) {
			$chance = rand(1,10);
			if ($chance <= $GLOBALS['maa_settings']['revenue_sharing']) {
				$author_id = @$GLOBALS['maa_settings']['admin_id'];
			}
		}
		return $author_id;
	}

	function MAAPRO_skipped($message) {

		return "\n\r <div class='hidden' rel='MAA Debug' style='display:none !important;'>{$message}</div>  \n\r";
	}

	function MAAPRO_Pro_Checks($author_id, $ad_id) {
		// Find more areas to skip from this list http://codex.wordpress.org/Conditional_Tags#Conditional_Tags_Index
		$skipped_reason                            = false;
		global $MAA_vars;
		global $post;
		
		$maa_setting                               = get_user_meta( $author_id , 'maa_settings' , true );
		
		$options_id                                = $MAA_vars["OPTIONS"];
		$show_ads_after_x_articles                 = @$GLOBALS['maa_settings']['show_ads_after_x_articles'];
		$user_how_many_non_adsense_ads_allowed     = @$GLOBALS['maa_settings']['user_how_many_non_adsense_ads_allowed'];
		$user_how_many_adsense_ads_display_allowed = @$GLOBALS['maa_settings']['user_how_many_adsense_ads_display_allowed'];
		$show_ads_to_logged_in                     = @$GLOBALS['maa_settings']['show_ads_to_logged_in'];
		$show_ads_on_archive                       = @$GLOBALS['maa_settings']['show_ads_on_archive'];
		$show_ads_on_home                          = @$GLOBALS['maa_settings']['show_ads_on_home'];
		$adsense_only                              = @$GLOBALS['maa_settings']['adsense_only'];
		$show_ads_if_post_is_x_words               = @$GLOBALS['maa_settings']['show_ads_if_post_is_x_words'];
		
		$author_article_count                      = count_user_posts( $author_id );
		$content                                   = $post->post_content;
		$special_chars                             = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ';
		$word_count                                = str_word_count($content, 0, $special_chars);
		$post_types                                = @$GLOBALS['maa_settings']['enabled_post_types'];
		if (stripos($post_types, ",")) {
			$post_types                            = explode(",",$post_types);
		} elseif (!empty($post_types)) {
			$post_types                            = array("$post_types");
		}
		$post_type                                 = $post->post_type;
		$disable_maa_by_post                       = get_post_meta( $post->ID, 'maa_disable_display' );

		if ( $ad_id >= 1 ) {
			$adsense = $maa_setting["ad_code_slot_".$ad_id];
			$adsense = stripos($adsense, 'googlesyndication.com');
		} elseif ( $ad_id == 'basic' || $ad_id == 'advanced' ) {
			$adsense = true;
		}

		$maa_non_adsense_ads_count = @$GLOBALS["maa_non_adsense_ads_count"];
		$maa_adsense_ads_count = @$GLOBALS["maa_adsense_ads_count"];

		if (!in_array($post_type, $post_types)) {
			$GLOBALS['maa_global_skip'] = true;
			$skipped_reason = __("Pro Feature: Skipped due to content type [").$post_type.__("] not being allowed to show ads.");

		} elseif ( $disable_maa_by_post['0'] == "1" ){
			$GLOBALS['maa_global_skip'] = true;
			$skipped_reason = __("Pro Feature: Skipped due author specifying on the post edit page that this content should not receive ads.");
		
		} elseif ( !empty($show_ads_after_x_articles) && $show_ads_after_x_articles > $author_article_count && @$GLOBALS['maa_settings']['admin_id'] != $author_id ) {
			$GLOBALS['maa_global_skip'] = true;
			$skipped_reason = __("Pro Feature: Skipped due to author not having enough posts. [Author ID: ".$author_id."] [Post Count: ").$author_article_count.__("] and [Required Post Count: ").$show_ads_after_x_articles.__("].");

		} elseif ( $show_ads_to_logged_in != '1' && is_user_logged_in() ) {
			$GLOBALS['maa_global_skip'] = true;
			$skipped_reason = __("Pro Feature: Skipped due to user being logged in. Log out to see ads. This setting is controlled by admin.");

		} elseif ( !empty($show_ads_on_archive) && $show_ads_on_archive == '0' && is_archive() ){
			$GLOBALS['maa_global_skip'] = true;
			$skipped_reason = __("Pro Feature: Skipped due to this being an archive page. Ads on archives are turned off by the admin.");

		} elseif ( $show_ads_on_home != '1' && is_home() ){
			$GLOBALS['maa_global_skip'] = true;
			$skipped_reason = __("Pro Feature: Skipped due to this being the home page. Ads on the home page are turned off by the admin.");

		} elseif ( !empty($show_ads_if_post_is_x_words) && $show_ads_if_post_is_x_words >= $word_count) {
			$GLOBALS['maa_global_skip'] = true;
			$skipped_reason = __("Pro Feature: Skipped due to article being less than {$show_ads_if_post_is_x_words} words. Article length is {$word_count} words.");

		} elseif ( !empty($adsense_only) && $ad_id >= 1 && !$adsense && $adsense_only == 'adsense' ) {
			$skipped_reason = __("Pro Feature: Skipped due to Non-AdSense ad. Admin requires only AdSense ads.");

		} elseif ( !empty($adsense_only) && $adsense && $adsense_only == 'other' ) {
			$skipped_reason = __("Pro Feature: Skipped due to AdSense ad. Admin requires only Non-AdSense ads.");

		} elseif ( !empty($user_how_many_non_adsense_ads_allowed) && $user_how_many_non_adsense_ads_allowed < $maa_non_adsense_ads_count ){
			$skipped_reason = __("Pro Feature: Skipped due to Non-AdSense ad limit of ").$user_how_many_non_adsense_ads_allowed.__(". There is/are already ").$maa_non_adsense_ads_count.__(" Non-AdSense ads on this page.");
		
		} elseif ( $user_how_many_adsense_ads_display_allowed == "0" && $adsense ){
			$skipped_reason = __("Pro Feature: Skipped due to AdSense ad limit of ").$user_how_many_adsense_ads_display_allowed.__(" set by administration.");

		} elseif ( $user_how_many_non_adsense_ads_allowed == "0" && !$adsense){
			$skipped_reason = __("Pro Feature: Skipped due to Non-AdSense ad limit of ").$user_how_many_non_adsense_ads_allowed.__(" set by administration.");
		
		} 

		// Tag Filtering
			$post_tags_object = wp_get_post_tags( $post->ID );
			$i = 0;
			foreach ($post_tags_object as $key => $value) {
				$post_tags[] = $post_tags_object[$i]->slug;
				$i++;
			}

			$tag_filter_mode = @$GLOBALS['maa_settings']['tag_filter_mode'];
			$ads_on_tags = @$GLOBALS['maa_settings']['ads_on_tags'];
			if (strpos($ads_on_tags,",")) {
				$ads_on_tags = explode(",", $ads_on_tags );
			} elseif (!empty($ads_on_tags)) {
				$ads_on_tags = array($ads_on_tags);
			}

			if ($tag_filter_mode == 'hide' && is_array($ads_on_tags)) {
				foreach ($post_tags as $tag ) {
					if ( in_array($tag, $ads_on_tags) ) {
						$GLOBALS['maa_global_skip'] = true;
						$skipped_reason = __("Pro Feature: Skipped due to tag ").$tag.__(" being on this article.");
					}
				}
			} elseif ($tag_filter_mode == 'show' && is_array($ads_on_tags)) {
				$show = false;
				foreach ($post_tags as $tag ) {
					if ( in_array($tag, $ads_on_tags) ) {
						$show = true;
						$showtag = $tag;
					}
				}
				if ($show == false) {
					$GLOBALS['maa_global_skip'] = true;
					$skipped_reason = __("Pro Feature: Skipped due to one of the following required tags not being on the article: ").implode(",", $ads_on_tags).".";
				}
			}

		// Category Filtering
			$post_categories_object = wp_get_post_categories( $post->ID );
			$i = 0;
			foreach ($post_categories_object as $value) {
				$cat = get_category( $value );
				$post_categories[] = $cat->slug;
				$i++;
			}

			$category_filter_mode = @$GLOBALS['maa_settings']['category_filter_mode'];
			$ads_on_categories = @$GLOBALS['maa_settings']['ads_on_categories'];
			if (strpos($ads_on_categories,",")) {
				$ads_on_categories = explode(",", $ads_on_categories );
			} elseif (!empty($ads_on_categories)) {
				$ads_on_categories = array($ads_on_categories);
			}

			if ($category_filter_mode == 'hide' && is_array($ads_on_categories)) {
				foreach ($post_categories as $category ) {
					if ( in_array($category, $ads_on_categories) ) {
						$GLOBALS['maa_global_skip'] = true;
						$skipped_reason = __("Pro Feature: Skipped due to category ").$category.__(" being on this article.");
						break;
					}
				}
			} elseif ($category_filter_mode == 'show' && is_array($ads_on_categories)) {
				$show = false;
				foreach ($post_categories as $category ) {
					if ( in_array($category, $ads_on_categories) ) {
						$show = true;
						$showcategory = $category;
					}
				}
				if ($show == false) {
					$GLOBALS['maa_global_skip'] = true;
					$skipped_reason = __("Pro Feature: Skipped due to one of the following required categories not being on the article: ").implode(",", $ads_on_categories).".";
				}
			}

		if ( isset($_GET['maa_debug_pro']) ) {
			$skipped_reason  = "Pro Feature: Nothing Skipped.\r\n";
			$skipped_reason .= "\$show_ads_after_x_articles = {$show_ads_after_x_articles}\r\n";
			$skipped_reason .= "\$user_how_many_non_adsense_ads_allowed = {$user_how_many_non_adsense_ads_allowed}\r\n";
			$skipped_reason .= "\$show_ads_to_logged_in = {$show_ads_to_logged_in}\r\n";
			$skipped_reason .= "\$show_ads_on_archive = {$show_ads_on_archive}\r\n";
			$skipped_reason .= "\$show_ads_on_home = {$show_ads_on_home}\r\n";
			$skipped_reason .= "\$author_article_count = {$author_article_count}\r\n";
			$skipped_reason .= "\$adsense_only = {$adsense_only}\r\n";
			$skipped_reason .= "\$tag_filter_mode = {$tag_filter_mode}\r\n";
			$skipped_reason .= "\$category_filter_mode = {$category_filter_mode}\r\n";
			$skipped_reason .= "\$show_ads_if_post_is_x_words = {$show_ads_if_post_is_x_words}\r\n";
			$skipped_reason .= "\$word_count = {$word_count}\r\n";
			$skipped_reason .= "\$author_id = {$author_id}\r\n";
			$skipped_reason .= "\$ad_id = {$ad_id}\r\n";
			$skipped_reason .= "\$maa_adsense_ads_count = {$maa_adsense_ads_count}\r\n";
			$skipped_reason .= "\$maa_non_adsense_ads_count = {$maa_non_adsense_ads_count}\r\n";
			$skipped_reason .= "\$disable_maa_by_post['0'] = {$disable_maa_by_post['0']}\r\n";
			$skipped_reason .= "\$ads_on_tags = ".print_r($ads_on_tags,true)."\r\n";
			$skipped_reason .= "\$ads_on_categories = ".print_r($ads_on_categories,true)."\r\n";
			$skipped_reason .= "\$post_categories = ".print_r($post_categories,true)."\r\n";
			$skipped_reason .= "\$post_tags = ".print_r($post_tags,true)."\n\r";
			$skipped_reason .= "\$maa_settings = ".print_r($GLOBALS['maa_settings'],true)."\r\n";
		}

		return $skipped_reason;
	}

	############################################
	# PRO Skipped
	############################################ 

		function MAAPRO_skipped_message($message){
			return "\n\r<div class='hidden' rel='MAA Debug' style='display:none !important;'>\n\r============ MAA SKIPPED ===============\n\r\n\r{$message}\n\r\n\r========================================\n\r</div>\n\r";
		}

	############################################
	# Modify the post content before output
	############################################

		function MAA_auto_insert($html) {

			/*
				Check if auto insert is enabled for this post
				Check if auto insert is enabled for this post type
				check if the author wants auto insert to insert ads
				check if should always be inserted, or only if no ads already
			*/

			global $MAA_vars;
			$has_ads = stripos($html, "[maa");

			## Skip if we defined already to skip it
				if (@$GLOBALS['maa_global_skip'] === true)
					return $html;

			############################################
			## Set the author ID for auto insert
			############################################
				$author_id = $GLOBALS['post']->post_author;

			############################################
			## Setup
			############################################
				$post_id                 = $GLOBALS['post']->ID;
				$post_type               = $GLOBALS['post']->post_type;
				$auto_insert             = false;
				$skip_insert             = false;
				$user_setting            = get_user_meta( $author_id , 'maa_settings' , true );
				$filter_type             = $GLOBALS['maa_settings']['author_filtering_type'];
					
			############################################
			## SKIP IF AUTHOR HAS DISABLED THIS SPECIFIC POST
			############################################
				$author_auto_insert_hide = get_post_meta( $post_id, 'maa_disable_auto_insert', true );
				if ($skip_insert == false && $author_auto_insert_hide == "1")
					$skip_insert = MAAPRO_skipped_message("Skipped due author specifying on the post edit page that this content should not have ads auto inserted.").$html;

			############################################
			# IF THE AUTHOR IS NOT SET UP, USE THE ADMIN ACCOUNT FOR ADS
			############################################
				$maa_auto_insert_when = @$user_setting['maa_auto_insert_when'];
				if ( $filter_type == 'fallback' && @$maa_auto_insert_when == 'never' || empty( $maa_auto_insert_when ) ) {
					// IF THE CONTENT DOES NOT HAVE ADS, AND THE AUTHOR IF THE POST HAS NOT SET UP AUTO INSERT YET
					
					$author_id = @$GLOBALS['maa_settings']['admin_id'];
					$user_info = get_userdata($author_id);
					if ($user_info != false) {
						$user_setting = get_user_meta( $author_id , 'maa_settings' , true );
						$maa_auto_insert_when = @$user_setting['maa_auto_insert_when'];
					} else {
						$skip_insert = MAAPRO_skipped_message("Author ID {$author_id} skipped auto insert.").$html;
					}
				}

				if ( $filter_type == 'none' && $skip_insert == false && @$maa_auto_insert_when == 'never' || empty( $maa_auto_insert_when ) ) {
					// IF THE CONTENT DOES NOT HAVE ADS, AND THE ADMIN/FALLBACK ID HAS NOT SET UP AUTO INSERT YET
					$skip_insert = MAAPRO_skipped_message("[Author ID ".$GLOBALS['post']->post_author."] has not setup Auto Insert fully, or has said to never auto insert.\r\nWhen to insert = [{$maa_auto_insert_when}]").$html;
				}

				if ( $filter_type == 'fallback' && $skip_insert == false && @$maa_auto_insert_when == 'never' || empty( $maa_auto_insert_when ) ) {
					// IF THE CONTENT DOES NOT HAVE ADS, AND THE ADMIN/FALLBACK ID HAS NOT SET UP AUTO INSERT YET
					$skip_insert = MAAPRO_skipped_message("Both the original [Author ID ".$GLOBALS['post']->post_author."] and the fallback [ID {$author_id}] have not setup Auto Insert fully, or they both have said to never auto insert.\r\nWhen to insert = [{$maa_auto_insert_when}]").$html;
				}


				// return MAAPRO_skipped_message("User ID {$author_id}\n\r".print_r($user_setting, true)).$html;

				if ( $skip_insert == false && $maa_auto_insert_when == 'never' || empty($maa_auto_insert_when) ) {
					$skip_insert =  MAAPRO_skipped_message("Skipped auto insert based on author ID {$author_id}'s settings.\r\nWhen to insert = [{$maa_auto_insert_when}]").$html;
				} elseif ( $skip_insert == false && $maa_auto_insert_when == 'only' && $has_ads != false ) {
					$skip_insert =  MAAPRO_skipped_message("Pro Feature: Skipped due to author [Author ID: {$author_id}] specifying not to insert ads if they are manually inserted. This content has ads already.").$html;
				} elseif ($skip_insert == false && $maa_auto_insert_when == 'always') {}

			$auto_insert_limit = @$GLOBALS['maa_settings']['user_how_many_auto_insert_ads'];
			$auto = array();
			$i = 1;
			while ($auto_insert_limit >= $i) {
				if ( $user_setting['auto_insert_'.$i.'_code'] != 'never'  && !empty($user_setting['auto_insert_'.$i.'_code'])  && 
					 $user_setting['auto_insert_'.$i.'_where'] != 'never' && !empty($user_setting['auto_insert_'.$i.'_where'])  ) {							
					$auto["$i"]["code"]  = $user_setting['auto_insert_'.$i.'_code'];
					$auto["$i"]["where"] = $user_setting['auto_insert_'.$i.'_where'];
				}
				$i++;
			}

			if (empty($auto)) 
				$skip_insert = "<span class='display:none !important' rel='Auto Insert has not been setup' />".$html;


			# If it should be skipped, then skip it!
				if ($skip_insert != false)
					return $skip_insert;

			$debugging = "<pre>".print_r($auto, true)."</pre>";		

			$delimiters = array('</p>','<br/><br/>','<br /><br />','<br><br>',"\r\n\r\n","\n\n\n\n","\n\r\n\r",'!!!THEREISNODELIMITERFOUND!!!');

			foreach ($delimiters as $del) {
				$para_count = substr_count($html, $del);
				if( $para_count >= 1 )
					break;
			}

			if ($del == '!!!THEREISNODELIMITERFOUND!!!')
				return "<span class='display:none !important' rel='NO DELIMITER FOUND' />".$html;

			$output_html = explode($del, $html);
			$paragraphs  = count($output_html) -1;
			foreach ($auto as $key => $value) {
				$id = $value['code'];
				$where = $value['where'];
				if ($where == 'above') {
					$output_html["0"] = "<span class='display:none !important' rel='MAA Auto Insert ad ID $id position $where' />[maa id={$id}]".$output_html["0"];
				} elseif ($where == 'below') {
					$output_html["$paragraphs"] .= "<span class='display:none !important' rel='MAA Auto Insert ad ID $id position $where' />[maa id={$id}]";
				} elseif ($where == 'random') {
					$random = rand(0,$paragraphs);
					$output_html["$random"] .= "<span class='display:none !important' rel='MAA Auto Insert ad ID $id position $where' />[maa id={$id}]";
					# TO DO: ADD CHECKS TO INSURE WE DONT INSERT ADS IN THE SAME PLACE MORE THAN ONCE
				} elseif( is_numeric($where) ){
					$where--;
					$output_html["$where"] .= "<span class='display:none !important' rel='MAA Auto Insert ad ID $id position $where' />[maa id={$id}]";
				}
			}
			
			$final = implode($del, $output_html);
		
			//return $debugging."Not Skipped $author_auto_insert_hide -".$html;
				
			return $final;
		}

############################################
# Post Editing Screen Meta Boxes
############################################

	## register the meta box
		function maa_checkboxes() {
			global $MAA_vars;
			$options = $MAA_vars["OPTIONS"];

			$id       = 'maa_post_settings';          															// this is HTML id of the box on edit screen
			$title    = '<img src="'.plugins_url( '/images/maa16.png' , __FILE__ ).'"/> Multi-Author AdSense'; 	// title of the box
			$callback = 'maa_post_box_content';   																// function to be called to display the checkboxes, see the function below
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
		add_action( 'add_meta_boxes', 'maa_checkboxes' );

	## display the metabox
		function maa_post_box_content( $post ) {
			// nonce field for security check, you can  have the same
			// nonce field for all your meta boxes of same plugin
			wp_nonce_field( plugin_basename( __FILE__ ), 'maa_nonce' );

			$checkboxes = array(
				'maa_disable_display' => 'Do not show ads on this content',
				'maa_disable_auto_insert' => 'Do not auto insert ads on this content'
			);

			foreach ($checkboxes as $name => $message) {
				$value = get_post_meta( $post->ID, $name, true );
				echo '<input type="checkbox" name="'.$name.'" value="1" '.checked($value,1,false).' /> '.$message.' <br />';
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
		add_action( 'save_post', 'maa_save_data' );