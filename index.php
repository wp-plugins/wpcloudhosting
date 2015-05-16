﻿<?php
/**
 * Plugin Name: WPCloudHosting
 * Plugin URI: http://www.s-media.si/hosting-wordpress-blog-on-aws-s3/
 * Description: Capture all blog pages and upload them to your S3 bucket
 * Version: 0.0.1
 * Author: Matej Šircelj
 * Author URI: http://www.s-media.si
 * License: A short license name. Example: GPL2
 */
 
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
add_action( 'init', 'WPCloudHosting_init' );

function WPCloudHosting_init() {
	add_action( 'admin_menu', 'WPCloudHosting_add_admin_menu' );
	add_action( 'admin_init', 'WPCloudHosting_AWS_settings_init' );
	add_action( 'admin_init', 'WPCloudHosting_GCS_settings_init' );
}

function WPCloudHosting_add_admin_menu(  ) { 
	add_menu_page( 'WPCloudHosting', 'WPCloudHosting', 'manage_options', 'WPCloudHosting', 'WPCloudHosting_options_page' );
}

function WPCloudHosting_AWS_settings_init(  ) { 
	register_setting( 'WPCloudHosting_AWS_settings', 'WPCloudHosting_AWS_settings' );

	add_settings_section(
		'WPCloudHosting_AWS_section', 
		__( 'AWS S3 settings:', 'WPCloudHosting' ), 
		'WPCloudHosting_AWS_settings_section_callback', 
		'WPCloudHosting_AWS_settings'
	);

	add_settings_field( 
		'aws_run', 
		__( 'Upload to S3: ', 'WPCloudHosting' ), 
		'WPCloudHosting_aws_run_render', 
		'WPCloudHosting_AWS_settings', 
		'WPCloudHosting_AWS_section' 
	);
	
	add_settings_field( 
		'aws_id', 
		__( 'AWS access key ID', 'WPCloudHosting' ), 
		'WPCloudHosting_aws_id_render', 
		'WPCloudHosting_AWS_settings', 
		'WPCloudHosting_AWS_section' 
	);

	add_settings_field( 
		'aws_secret', 
		__( 'AWS secret access key', 'WPCloudHosting' ), 
		'WPCloudHosting_aws_secret_render', 
		'WPCloudHosting_AWS_settings', 
		'WPCloudHosting_AWS_section' 
	);

	add_settings_field( 
		'aws_region', 
		__( 'AWS region', 'WPCloudHosting' ), 
		'WPCloudHosting_aws_region_render', 
		'WPCloudHosting_AWS_settings', 
		'WPCloudHosting_AWS_section' 
	);

	add_settings_field( 
		'aws_bucket', 
		__( 'Bucket name', 'WPCloudHosting' ), 
		'WPCloudHosting_aws_bucket_render', 
		'WPCloudHosting_AWS_settings', 
		'WPCloudHosting_AWS_section' 
	);
	
	add_settings_field( 
		'aws_error', 
		__( 'Error page', 'WPCloudHosting' ), 
		'WPCloudHosting_aws_error_render', 
		'WPCloudHosting_AWS_settings', 
		'WPCloudHosting_AWS_section' 
	);
}

function WPCloudHosting_GCS_settings_init(  ) { 
	register_setting( 'WPCloudHosting_GCS_settings', 'WPCloudHosting_GCS_settings' );

	add_settings_section(
		'WPCloudHosting_GCS_section', 
		__( 'GCS settings:', 'WPCloudHosting' ), 
		'WPCloudHosting_GCS_settings_section_callback', 
		'WPCloudHosting_GCS_settings'
	);

	add_settings_field( 
		'gcs_run', 
		__( 'Upload to GCS: ', 'WPCloudHosting' ), 
		'WPCloudHosting_GCS_run_render', 
		'WPCloudHosting_GCS_settings', 
		'WPCloudHosting_GCS_section' 
	);
	
	add_settings_field( 
		'gcs_email', 
		__( 'GCS client email', 'WPCloudHosting' ), 
		'WPCloudHosting_GCS_email_render', 
		'WPCloudHosting_GCS_settings', 
		'WPCloudHosting_GCS_section' 
	);
	
	add_settings_field( 
		'gcs_secret', 
		__( 'GCS private key', 'WPCloudHosting' ), 
		'WPCloudHosting_GCS_secret_render', 
		'WPCloudHosting_GCS_settings', 
		'WPCloudHosting_GCS_section' 
	);

	add_settings_field( 
		'gcs_bucket', 
		__( 'Bucket name', 'WPCloudHosting' ), 
		'WPCloudHosting_GCS_bucket_render', 
		'WPCloudHosting_GCS_settings', 
		'WPCloudHosting_GCS_section' 
	);
	
	add_settings_field( 
		'gcs_error', 
		__( 'Error page', 'WPCloudHosting' ), 
		'WPCloudHosting_GCS_error_render', 
		'WPCloudHosting_GCS_settings', 
		'WPCloudHosting_GCS_section' 
	);
}


function check_aws_id($options){
	if(!isset($options["aws_id"]) || !$options["aws_id"]){
		echo "Please provide AWS ID";
		return false;
	}else{
		return true;
	}
}

function check_aws_key($options){
	if(!isset($options["aws_key"]) || !$options["aws_key"]){
		echo "Please provide AWS secret key";
		return false;
	}else{
		return true;
	}
}

function check_region($options){
	if(!isset($options["aws_region"]) || !$options["aws_region"]){
		echo "Please provide S3 bucket region";
		return false;
	}else{
		return true;
	}
}

function check_bucket($options){
	if(!isset($options["aws_bucket"]) || !$options["aws_bucket"]){
		echo "Please provide S3 bucket name";
		return false;
	}else{
		return true;
	}
}

function check_aws_connection($aws_id, $aws_secret, $region, $bucket){
	$bucket_check = Cache::check_credentials($aws_id, $aws_secret, $region, $bucket);
	if($bucket_check == false){
		echo "Could not connect to the bucket. Make sure that bucket name is correct and that you defined correct AWS credentials.";
		return false;
	}else if(isset($bucket_check['error'])){
		echo "Could not authenticate with AWS ID and AWS secret key";
		return false;
	}else{
		return true;
	}
}

function check_error_page($error_page){
	if(!$error_page){
		echo "Please define your error page";
		return false;
	}else{
		return true;
	}
}

/*
 * AWS
 */
 
function WPCloudHosting_aws_run_render(  ) { 
	$options = get_option( 'WPCloudHosting_AWS_settings' );
	?>
	<input type="checkbox" name="WPCloudHosting_AWS_settings[aws_run]" <?= (isset($options['aws_run']) ? "checked" : ""); ?>><br>
	<?php

}

function WPCloudHosting_aws_id_render(  ) { 
	$options = get_option( 'WPCloudHosting_AWS_settings' );
	?>
	<input type='text' name='WPCloudHosting_AWS_settings[aws_id]' value='<?php echo (isset($options['aws_id']) ? $options['aws_id'] : ""); ?>'>
	<?php

}


function WPCloudHosting_aws_secret_render(  ) { 
	$options = get_option( 'WPCloudHosting_AWS_settings' );
	?>
	<input type='password' name='WPCloudHosting_AWS_settings[aws_key]' value='<?php echo (isset($options['aws_key']) ? $options['aws_key'] : ""); ?>'>
	<?php

}

function WPCloudHosting_aws_region_render() {  
	$options = get_option('WPCloudHosting_AWS_settings');
	?>
	<select name='WPCloudHosting_AWS_settings[aws_region]'>
		<option value='ap-northeast-1' <?= (isset($options['aws_region']) && $options['aws_region'] == 'ap-northeast-1' ? "selected" : ""); ?>>Asia Pacific (Tokyo)</option>
		<option value='ap-southeast-1' <?= (isset($options['aws_region']) && $options['aws_region'] == 'ap-southeast-1' ? "selected" : ""); ?>>Asia Pacific (Singapore)</option>
		<option value='ap-southeast-2' <?= (isset($options['aws_region']) && $options['aws_region'] == 'ap-southeast-2' ? "selected" : ""); ?>>Asia Pacific (Sydney)</option>
		<option value='eu-central-1' <?= (isset($options['aws_region']) && $options['aws_region'] == 'eu-central-1' ? "selected" : ""); ?>>EU (Frankfurt)</option>
		<option value='eu-west-1' <?= (isset($options['aws_region']) && $options['aws_region'] == 'eu-west-1' ? "selected" : ""); ?>>EU (Ireland)</option>
		<option value='sa-east-1' <?= (isset($options['aws_region']) && $options['aws_region'] == 'sa-east-1' ? "selected" : ""); ?>>South America (Sao Paulo)</option>
		<option value='us-east-1' <?= (isset($options['aws_region']) && $options['aws_region'] == 'us-east-1' ? "selected" : ""); ?>>US East (N. Virginia)</option>
		<option value='us-west-1' <?= (isset($options['aws_region']) && $options['aws_region'] == 'us-west-1' ? "selected" : ""); ?>>US West (N. California)</option>
		<option value='us-west-2' <?= (isset($options['aws_region']) && $options['aws_region'] == 'us-west-2' ? "selected" : ""); ?>>US West (Oregon)</option>
	</select>
	<?php
}

function WPCloudHosting_aws_bucket_render(  ) { 
	$options = get_option( 'WPCloudHosting_AWS_settings' );
	?>
	<input type='text' name='WPCloudHosting_AWS_settings[aws_bucket]' value='<?php echo (isset($options['aws_bucket']) ? $options['aws_bucket'] : ""); ?>'>
	<?php

}

function WPCloudHosting_aws_error_render(  ) { 
	$options = get_option( 'WPCloudHosting_AWS_settings' );
	?>
	<input type='text' name='WPCloudHosting_AWS_settings[404_page]' value='<?php echo (isset($options['404_page']) ? $options['404_page'] : ""); ?>'>
	<?php

}

/*
 * GCS
 */
 
function WPCloudHosting_GCS_run_render(  ) { 
	$options = get_option( 'WPCloudHosting_GCS_settings' );
	?>
	<input type="checkbox" name="WPCloudHosting_GCS_settings[gcs_run]" <?= (isset($options['gcs_run']) ? "checked" : ""); ?>><br>
	<?php
}

function WPCloudHosting_GCS_email_render(  ) { 
	$options = get_option( 'WPCloudHosting_GCS_settings' );
	?>
	<input type='text' name='WPCloudHosting_GCS_settings[gcs_email]' value='<?php echo (isset($options['gcs_email']) ? $options['gcs_email'] : ""); ?>'>
	<?php
}

function WPCloudHosting_GCS_secret_render(  ) { 
	$options = get_option( 'WPCloudHosting_GCS_settings' );
	?>
	<textarea name='WPCloudHosting_GCS_settings[gcs_key]'><?php echo (isset($options['gcs_key']) ? $options['gcs_key'] : ""); ?></textarea>
	<?php
}

function WPCloudHosting_GCS_bucket_render(  ) { 
	$options = get_option( 'WPCloudHosting_GCS_settings' );
	?>
	<input type='text' name='WPCloudHosting_GCS_settings[gcs_bucket]' value='<?php echo (isset($options['gcs_bucket']) ? $options['gcs_bucket'] : ""); ?>'>
	<?php
}

function WPCloudHosting_GCS_error_render(  ) { 
	$options = get_option( 'WPCloudHosting_GCS_settings' );
	?>
	<input type='text' name='WPCloudHosting_GCS_settings[404_page]' value='<?php echo (isset($options['404_page']) ? $options['404_page'] : ""); ?>'>
	<?php
}

function WPCloudHosting_AWS_settings_section_callback(  ) { }

function WPCloudHosting_GCS_settings_section_callback(  ) { }


function WPCloudHosting_options_page(  ) { 

	?>
	<style>
		.WPCloudHosting_wrap{
			margin-top: 30px;
			display: flex;
		}
		
		.progress{
			flex: 2;
			text-align: center;
		}
		
		.settings_s3{
			flex: 1;
			background-color: white;
			border: 1px solid rgb(210, 210, 210);
			border-radius: 5px;
			margin: 0px 10px;
			padding: 0 0 0 15px;
		}
		
		.settings_gcs{
			flex: 1;
			background-color: white;
			border: 1px solid rgb(210, 210, 210);
			border-radius: 5px;
			margin: 0px 10px;
			padding: 0 0 0 15px;
		}
		
		.progress_bar { 
			height: 5px;
			position: relative;
			background: #555;
			-moz-border-radius: 25px;
			-webkit-border-radius: 25px;
			border-radius: 8px;
			padding: 4px;
			margin-bottom: 10px;
		}
		
		.progress_bar > span {
			display: block;
			height: 100%;
			border-radius: 20px;
			background-color: rgb(43,194,83);
			background-image: linear-gradient(
			center bottom,
			rgb(43,194,83) 37%,
			rgb(84,240,84) 69%
			);
			box-shadow: 
			inset 0 2px 9px  rgba(255,255,255,0.3),
			inset 0 -2px 6px rgba(0,0,0,0.4);
			position: relative;
			overflow: hidden;
		}
		
		.search_pages_loading_wrap{
			height: 240px;
		}
		
		#search_pages_loading{
			margin: 40px auto;
		}
		
		.scanning_text{
			text-align: center;
			display: none;
		}
		
		.found_pages_text{
			text-align: center;
			font-size: 34px;
			display: none;
			margin-top: 100px;
		}
		
		#aws_progress{
			height: 50px;
			display:none;
		}
		
		#gcs_progress{
			height: 50px;
			display:none;
		}
		
		#WPCloudHosting_get_files{
			background: #00a0d2;
			border-color: #0073aa;
			-webkit-box-shadow: inset 0 1px 0 rgba(120,200,230,.5),0 1px 0 rgba(0,0,0,.15);
			box-shadow: inset 0 1px 0 rgba(120,200,230,.5),0 1px 0 rgba(0,0,0,.15);
			color: #fff;
			text-decoration: none;
			
			display: inline-block;
			text-decoration: none;
			font-size: 24px;
			line-height: 26px;
			margin: 30px 0 0 0;
			padding: 10px;
			cursor: pointer;
			border-width: 1px;
			border-style: solid;
			-webkit-appearance: none;
			-webkit-border-radius: 3px;
			border-radius: 3px;
			white-space: nowrap;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
		}
		
	</style>
		<script type="text/javascript">
			var cSpeed=5;
			var cWidth=128;
			var cHeight=128;
			var cTotalFrames=18;
			var cFrameWidth=128;
			var cImageSrc='data:image/png;base64,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';
			
			var cImageTimeout=false;
			var cIndex=0;
			var cXpos=0;
			var cPreloaderTimeout=false;
			var SECONDS_BETWEEN_FRAMES=0;
			
			function startAnimation(){
				
				document.getElementById('search_pages_loading').style.backgroundImage='url('+cImageSrc+')';
				document.getElementById('search_pages_loading').style.width=cWidth+'px';
				document.getElementById('search_pages_loading').style.height=cHeight+'px';
				
				//FPS = Math.round(100/(maxSpeed+2-speed));
				FPS = Math.round(100/cSpeed);
				SECONDS_BETWEEN_FRAMES = 1 / FPS;
				
				cPreloaderTimeout=setTimeout('continueAnimation()', SECONDS_BETWEEN_FRAMES/1000);
				
			}
			
			function continueAnimation(){
				
				cXpos += cFrameWidth;
				//increase the index so we know which frame of our animation we are currently on
				cIndex += 1;
				 
				//if our cIndex is higher than our total number of frames, we're at the end and should restart
				if (cIndex >= cTotalFrames) {
					cXpos =0;
					cIndex=0;
				}
				
				if(document.getElementById('search_pages_loading'))
					document.getElementById('search_pages_loading').style.backgroundPosition=(-cXpos)+'px 0';
				
				cPreloaderTimeout=setTimeout('continueAnimation()', SECONDS_BETWEEN_FRAMES*1000);
			}
			
			function stopAnimation(){ //stops animation
				clearTimeout(cPreloaderTimeout);
				cPreloaderTimeout=false;
			}
			
			function imageLoader(s, fun) //Pre-loads the sprites image
			{
				clearTimeout(cImageTimeout);
				cImageTimeout=0;
				genImage = new Image();
				genImage.onload=function (){cImageTimeout=setTimeout(fun, 0)};
				genImage.onerror=new Function('alert(\'Could not load the image\')');
				genImage.src=s;
			}
		</script>

	<div class="WPCloudHosting_wrap">
		<div class="progress">
			<h2><b>WPCloudHosting</b> - take snapshot of your blog and upload it to AWS S3 or GCS</h2>
			
			<button type="button" id="WPCloudHosting_get_files">Scan & Upload</button>
			
			<div class="search_pages_loading_wrap">
				<div id="search_pages_loading"></div>
				<div class="scanning_text">Scanning for blog pages</div>
				<div class="found_pages_text"></div>
			</div>
			
			<div id="aws_progress">
				<div id="aws_out_of"></div>
				<div class="progress_bar">
				  <span id="aws_progress_bar" style="width: 0%;"></span>
				</div>
			</div>
			
			<div id="gcs_progress">
				<div id="gcs_out_of"></div>
				<div class="progress_bar">
				  <span id="gcs_progress_bar"  style="width: 0%;"></span>
				</div>
			</div>
		</div>
		<div class="settings_s3">
			<form action='options.php' method='post'>
				<?php
				settings_fields( 'WPCloudHosting_AWS_settings' );
				do_settings_sections( 'WPCloudHosting_AWS_settings' );
				submit_button('Save AWS changes');
				?>
			</form>
		</div>
		<div class="settings_gcs">
			<form action='options.php' method='post'>
				<?php
				settings_fields( 'WPCloudHosting_GCS_settings' );
				do_settings_sections( 'WPCloudHosting_GCS_settings' );
				submit_button('Save GCS changes');
				?>
			</form>
		</div>
	</div>
	<?php

}

add_action('admin_footer', 'setup_js');

function setup_js() {?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		var total_pages_aws = 0;
		var total_pages_gcs = 0;
		var s3 = <?= (isset(get_option( 'WPCloudHosting_AWS_settings' )["aws_run"]) ? "true" : "false"); ?>;
		var gcs = <?= (isset(get_option( 'WPCloudHosting_GCS_settings' )["gcs_run"]) ? "true" : "false"); ?>;
		
		$('#WPCloudHosting_get_files').live('click',function(){ 
			//The following code starts the animation
			$("#search_pages_loading").show();
			$(".found_pages_text").hide();
			new imageLoader(cImageSrc, 'stopAnimation()');
			new imageLoader(cImageSrc, 'startAnimation()');
			
			$.post(ajaxurl, {
				'action': 'get_files',
			}, function(response) {
				var pages_aws = JSON.parse(response);
				var pages_gcs = JSON.parse(response);
				
				var pages_urls_aws = Object.keys(pages_aws);
				var pages_urls_gcs = Object.keys(pages_gcs);
				
				new imageLoader(cImageSrc, 'stopAnimation()');
				total_pages_aws = pages_urls_aws.length;
				total_pages_gcs = pages_urls_gcs.length;
				
				$("#search_pages_loading").hide();
				$(".scanning_text").hide();
				$(".found_pages_text").html(total_pages_aws+" pages found.");
				$(".found_pages_text").css("display", "inline-block");
				
				
				if(s3){
					$("#aws_progress").show();
					upload_s3_page_chain(pages_urls_aws, pages_aws);
				}
				
				if(gcs){
					$("#gcs_progress").show();
					upload_gcs_page_chain(pages_urls_gcs, pages_gcs);
				}
				
			});
		});
		
		
		var upload_s3_page_chain = function(pages_urls_aws, pages_aws){
			var urls = [];
			for(var i = 0; i < 10; i++){
				var quequed_page = pages_urls_aws.shift();
				if(!quequed_page){
					break;
				}
				urls.push({
					url	: quequed_page,
					type: pages_aws[quequed_page]
				});
			}
			
			$("#aws_out_of").html("Uploaded files to S3 bucket: <b>"+(total_pages_aws-pages_urls_aws.length)+" / "+total_pages_aws)+"</b>";
			$("#aws_progress_bar").width(((total_pages_aws-pages_urls_aws.length)/total_pages_aws*100)+"%");
					
			if(pages_urls_aws.length !== 0){
				$.post(ajaxurl, {
					'action': 'upload_page_aws',
					'urls': urls,
				}, function(response) {
					upload_s3_page_chain(pages_urls_aws, pages_aws);
				});
			}
		}
		
		var upload_gcs_page_chain = function(pages_urls_gcs, pages_gcs){
			var urls = [];
			for(var i = 0; i < 10; i++){
				var quequed_page = pages_urls_gcs.shift();
				if(!quequed_page){
					break;
				}
				urls.push({
					url	: quequed_page,
					type: pages_gcs[quequed_page]
				});
			}
			
			$("#gcs_out_of").html("Uploaded files to GCS bucket: <b>"+(total_pages_gcs-pages_urls_gcs.length)+" / "+total_pages_gcs)+"</b>";
			$("#gcs_progress_bar").width(((total_pages_gcs-pages_urls_gcs.length)/total_pages_gcs*100)+"%");
					
			if(pages_urls_gcs.length !== 0){
				$.post(ajaxurl, {
					'action': 'upload_page_gcs',
					'urls': urls,
				}, function(response) {
					upload_gcs_page_chain(pages_urls_gcs, pages_gcs);
				});
			}
		}
	});
	</script>
<?php
}
require_once 'aws/aws-autoloader.php';
require_once 'gcs/google-api-php-client-master/src/Google/autoload.php';
require_once 'includes/Cache.php';

add_action( 'wp_ajax_get_files', 'get_files_callback' );
function get_files_callback() {
	$aws_options = get_option( 'WPCloudHosting_AWS_settings' );
	$gcs_options = get_option( 'WPCloudHosting_GCS_settings' );
	
	if(
		check_aws_id($aws_options) &
		check_aws_key($aws_options) &
		check_region($aws_options) &
		check_bucket($aws_options) &&
		check_aws_connection($aws_options["aws_id"], $aws_options["aws_key"], $aws_options["aws_region"], $aws_options["aws_bucket"]) &&
		check_error_page($aws_options["404_page"])
	){
		$cache = new Cache($aws_options, $gcs_options);
		echo json_encode($cache->get_all_page_links());
		wp_die();
	}else{
		echo "Wrong configuration!";
		wp_die();
	}
}


	
add_action('wp_ajax_upload_page_aws', 'upload_page_callback_aws');
add_action('wp_ajax_upload_page_gcs', 'upload_page_callback_gcs');

function upload_page_callback_aws() {
	$aws_options = get_option( 'WPCloudHosting_AWS_settings' );
	
	$urls = $_POST['urls'];
	
	$cache = new Cache($aws_options, []);
	echo $cache->upload_page_contents($urls);
	
	wp_die();
}

function upload_page_callback_gcs() {
	$gcs_options = get_option( 'WPCloudHosting_GCS_settings' );
	
	$urls = $_POST['urls'];
	
	$cache = new Cache([], $gcs_options);
	echo $cache->upload_page_contents($urls);
	
	wp_die();
}
?>