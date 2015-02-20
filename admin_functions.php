<style type="text/css">

	*{
		-webkit-transition: all 0.2s ease-in-out;
		-moz-transition: all 0.2s ease-in-out;
		-o-transition: all 0.2s ease-in-out;
		transition: all 0.2s ease-in-out;
	}


	.form-table tr, .form-table td{
		border:none;
	}

	/* BUTTONS */
		.wp-core-ui .button-red.hover, .wp-core-ui .button-red:hover, .wp-core-ui .button-red.focus, .wp-core-ui .button-red:focus {
			background-color: #B72727;
			background-image: -webkit-gradient(linear,left top,left bottom,from(#D22E2E),to(#9B2121));
			background-image: -webkit-linear-gradient(top,#D22E2E,#9B2121);
			background-image: -moz-linear-gradient(top,#D22E2E,#9B2121);
			background-image: -ms-linear-gradient(top,#D22E2E,#9B2121);
			background-image: -o-linear-gradient(top,#D22E2E,#9B2121);
			background-image: linear-gradient(to bottom,#D22E2E,#9B2121);
			border-color: #7F1B1B;
			-webkit-box-shadow: inset 0 1px 0 rgba(230, 120, 120, 0.6);
			box-shadow: inset 0 1px 0 rgba(230, 120, 120, 0.6);
			color: #fff;
			text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3);
		}

		.wp-core-ui .button-red {
			background-color: #9B2121;
			background-image: -webkit-gradient(linear,left top,left bottom,from(#C52A2A),to(#9B2121));
			background-image: -webkit-linear-gradient(top,#C52A2A,#9B2121);
			background-image: -moz-linear-gradient(top,#C52A2A,#9B2121);
			background-image: -ms-linear-gradient(top,#C52A2A,#9B2121);
			background-image: -o-linear-gradient(top,#C52A2A,#9B2121);
			background-image: linear-gradient(to bottom,#C52A2A,#9B2121);
			border-color: #9B2121;
			border-bottom-color: #8D1E1E;
			-webkit-box-shadow: inset 0 1px 0 rgba(230, 120, 120, 0.5);
			box-shadow: inset 0 1px 0 rgba(230, 120, 120, 0.5);
			color: #fff;
			text-decoration: none;
			text-shadow: 0 1px 0 rgba(0,0,0,0.1);
		}
		.wp-core-ui .button.button-large, .wp-core-ui .button-group.button-large .button {
			font-size:17px;
			text-align: center;
			color: #777;
			padding-top: 1px;
		}

		.button-faded {
			opacity: .7;
		}
		/*
		.wp-core-ui .button.button-small, .wp-core-ui .button-group.button-small .button {
			height: 18px;
			line-height: 18px;
			padding: 0 5px;
			font-size: 12px;
		}
		*/
	.setting.disabled * {
		opacity: .5;
	}

	.ad_clot_label {
		font-size: 13px;
		font-weight: bold;
		color: #333;
	}
	/* CONSTANTS */
		.wrap {
			font-size: 14px;
		}
		table, tr, td, thead {
			vertical-align: top !important;
			text-align:left;
		}

		.mma_table select, .mma_table input[type=text], .mma_table textarea {
			width: 100%;
		}

		.hidden {
			display: none !important;
			background-color: transparent !important;
		}
		.hiddendivider,
		.deleted,
		.archived,
		.admin_hidden,
		.test_controls,
		/*
		.help_content {
			display: none !important;
			background-color: transparent !important;
		}
		*/
		.faded {
			opacity: .5 !important;
		}

		.form-table td p {
			margin-top: 10px !important;
		}

		.notice p{
			margin: 0;
			padding: 4px;
		}

		.notice {
			background-color:#fff;
			border-left:4px solid #7ad03a;
			-webkit-box-shadow:0 1px 1px 0 rgba(0,0,0,.1);
			box-shadow:0 1px 1px 0 rgba(0,0,0,.1);
			float: left;
			width: inherit;
			margin-top: 7px;
		}

		.notice.yellow {
			border-left:4px solid #D0D03A;
		}

		.notice.red {
			border-left:4px solid #dd3d36;
		}

		.full_wide {
			width:100%;
			text-align: center;
		}
		

	/**************/

	.maa_ad_type {
		float: right;
		margin-left: 5px !important;
	}
	.maalogo {
		width: 64px;
		vertical-align: middle;
	}

	.nopadding td {
		padding: 0;
	}
	table.nopadding {
		width:97%;
	}
	.lowpadding td {
		padding: 0 10px;
		margin: 0;
	}
	.centered {
		text-align: center;
	}
	.admin_hidden {
		background: yellow !important;
		padding: 10px !important;
	}

	.fullwidth_select {
		width:200px;
	}

	#information {
		display: none;
	}

	#helpbox h4 {
		margin-top: 0;
		font-size: 16px;
	}

	#helpbox b {
		font-weight: bold;
	}

	#helpdiv {
		max-width: 400px;
		margin-top: 50px;
	}

	#help_table {
		width:100%;
	}
	.prosetting {display: none;}
	.setting h4 {
		color: #222;
		font-size: 1.15em;
		text-align: center;
	}
</style>

<?php
$options = $MAA_vars['OPTIONS'];
wp_enqueue_script( 'jquery' );

if (isset($_GET['resetallsettings'])) {
	$MultiAuthorAdSense->reset_all_settings();
	echo "<script>window.location = 'admin.php?page=multi-author-advertising';</script>";
	exit;
}

$ad_mode = get_option( $options.'admin_ad_mode', 'advanced_adsense' );

if ($ad_mode == 'advanced_adsense') {
	$advanced_adsense = '';
	$basic_adsense = 'hidden';
	$standard_ad = 'hidden';

	$adv_button = 'primary';
	$bas_button = 'secondary';
	$std_button = 'secondary';
} elseif ($ad_mode == 'basic_adsense') {
	$advanced_adsense = 'hidden';
	$basic_adsense = '';
	$standard_ad = 'hidden';

	$adv_button = 'secondary';
	$bas_button = 'primary';
	$std_button = 'secondary';
} elseif ($ad_mode == 'standard_ad') {
	$advanced_adsense = 'hidden';
	$basic_adsense = 'hidden';
	$standard_ad = '';
	
	$adv_button = 'secondary';
	$bas_button = 'secondary';
	$std_button = 'primary';
}

$help = TRUE;
$button_text = 'Turn help off';
if ( get_option($options.'help_toggle',1) !== "1" ) {
	$help = FALSE;
	$button_text = 'Turn help on';
}

$demo = false;
if (get_option($options.'pro_demo') == 1) {
	$demo = true;
	?><style type="text/css">.prosetting {display: table-row}</style><?php
}

$pro = false;
if (function_exists('MAAPRO_Profile_Show_Settings')) {
	$pro = true;
	?><style type="text/css">.prosetting {display: table-row}</style><?php
}


