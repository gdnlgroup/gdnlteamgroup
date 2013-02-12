<?php
/*
Plugin Name: Social Media User Detection
Plugin URI: http://adaptpartners.com/wordpress/plugins/social-media-user-detection/
Version: 1.0.1
License: GPL2
Description: Detects the login status of social media users and records to your site's Google Analytics. (Currently supports Facebook, Twitter, Google, Google+)
Usage:  Add your Facebook Application ID and make sure the plugin is activated. That's about it.  For detailed instructions, see the plugin URI.
Author: Adapt Partners
Author URI: http://adaptpartners.com/
*/

require_once('adapt_custom_api_settings.php');

// Functions to add tracking code to WP
function adapt_smud_google_analytics() {
	$credits = '<!-- Social Media User Detection plugin by Adapt Partners - http://adaptpartners.com/ -->' . "\n";
	$end_credits = "\n" . '<!-- END Social Media User Detection plugin -->' . "\n";
	$async =	$credits . '<script type="text/javascript">
				function record_login_status(slot, network, status) {
				if (status) {
					_gaq.push(["_setCustomVar", slot, network + "_State", "LoggedIn", 1]);
				} else {
					_gaq.push(["_setCustomVar", slot, network + "_State", "NotLoggedIn", 1]);
				}
				}
				</script>' . $end_credits;
	$oldie =	$credits . '<script type="text/javascript">
				function record_login_status(slot, network, status) {
				if (status) {
					pageTracker._setCustomVar(slot, network + "_State", "LoggedIn", 1);
				} else {
					pageTracker._setCustomVar(slot, network + "_State", "NotLoggedIn", 1);
				}
				}
				</script>' . "\n";

	$options = get_option('adapt_smud_options');
	if($options['analytics_type'] == 'Asynchronous') {
		echo $async;
	} elseif($options['analytics_type'] == 'Traditional') {
		echo $oldie;
	} else {
		echo $async;
	}
}
add_action('wp_head','adapt_smud_google_analytics');

function adapt_smud_footer_scripts() {
	$options = get_option('adapt_smud_options');
	
	// Google Account, Gmail, etc.	
	$output .= '<img style="display:none;" onload="record_login_status(1, \'Google\', true)" onerror="record_login_status(1, \'Google\', false)" src="https://accounts.google.com/CheckCookie?continue=https://www.google.com/intl/en/images/logos/accounts_logo.png" />' . "\r\n";
	
	// Google+
	$output .= '<img style="display:none;" onload="record_login_status(2, \'GooglePlus\', true)" onerror="record_login_status(2, \'GooglePlus\', false)" src="https://plus.google.com/up/?continue=https://www.google.com/intl/en/images/logos/accounts_logo.png&type=st&gpsrc=ogpy0" />' . "\r\n";
	
	// Twitter
	$output .= '<img style="display:none;" src="https://twitter.com/login?redirect_after_login=%2Fimages%2Fspinner.gif" onload="record_login_status(3, \'Twitter\', true)" onerror="record_login_status(3, \'Twitter\', false)" />' . "\r\n";
	
	// Facebook
	$facebook_app_id = $options['facebook_app_id'];
	if($facebook_app_id) {
		$output .= <<<FACEBOOK
		<div id="fb-root"></div>
		<script>
			window.fbAsyncInit = function(){
				FB.init({ appId:'$facebook_app_id', status:true,  cookie:true, xfbml:true});
				FB.getLoginStatus(function(response){
					if (response.status != "unknown")
					{
						record_login_status(4, "Facebook", true);
					}else{
						record_login_status(4, "Facebook", false);
					}
				});
			};
			// Load the SDK Asynchronously
			(function(d){
				var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
				js = d.createElement('script'); js.id = id; js.async = true;
				js.src = "//connect.facebook.net/en_US/all.js";
				d.getElementsByTagName('head')[0].appendChild(js);
			}(document));
		</script>
FACEBOOK;
		$output .= "\r\n";
	} else {
		$output .= '<!-- No Facebook App ID is set in the plugin. -->' . "\r\n";
	}
	
	echo $output;
}
add_action('wp_footer','adapt_smud_footer_scripts');

// ADMIN PAGES
add_action('admin_menu','adapt_smud_menu');
function adapt_smud_menu() {
	add_options_page('Social Media User Detection','User Detection','manage_options','adapt_smud','adapt_smud_admin');
}

add_action('admin_init','adapt_smud_admin_init');
function adapt_smud_admin_init() {
	register_setting('adapt_smud_plugin_options','adapt_smud_options');
	add_settings_section('adapt_smud_analytics_settings','Google Analytics Setup','adapt_smud_analytics_section_text','adapt_smud_options');
	add_settings_section('adapt_smud_facebook_settings','Facebook API Setup','adapt_smud_facebook_section_text','adapt_smud_options');
	add_settings_field('adapt_smud_analytics','','adapt_smud_field_string','adapt_smud_options','adapt_smud_analytics_settings');
	add_settings_field('adapt_smud_facebook','','adapt_smud_facebook_field_string','adapt_smud_options','adapt_smud_facebook_settings');
}

function adapt_smud_field_string() {
	$options = get_option('adapt_smud_options');
	$items = array("Asynchronous","Traditional");
	echo "
		<tr valign=\"top\">
			<th scope=\"row\" valign=\"top\"><label for=\"adapt_smud_field\">Google Analytics Type:</label></th>
			<td>";
	foreach($items as $item) {
		$checked = ($options['analytics_type']==$item) ? ' checked="checked" ' : '';
		echo "<input ".$checked." value='$item' name='adapt_smud_options[analytics_type]' type='radio' /> <label>$item Snippet</label>&nbsp;&nbsp;";
	}
	echo "</td></tr>";
}

function adapt_smud_analytics_section_text() {
	echo '
		<p>Select whether you are using the newer Async Snippet version or the Traditional (ga.js) Snippet version of Google Analytics.</p>
		<p>Not sure?  Ask your SEO or Analytics ninja.</p>
	';
}

function adapt_smud_facebook_section_text() {
	echo '
		<p>After you have set up your Facebook App, enter the App ID below.</p>
	';
}

function adapt_smud_facebook_field_string() {
	$options = get_option('adapt_smud_options');
	echo "
		<tr valign=\"top\">
			<th scope=\"row\" valign=\"top\"><label for=\"adapt_smud_field\">Facebook App ID:</label></th>
			<td><input value='" . $options['facebook_app_id'] . "' name='adapt_smud_options[facebook_app_id]' type='Text' />
		</td></tr>";
}

function adapt_smud_admin() {
?>
	<div class="wrap">
		
	<div id="adapt_left" class="postbox-container" style="width: 70%; float: left; margin-right: 10px;">
	<h2>Social Media User Detection <small style="font-size: 0.65em;">from <em><a href="http://adaptpartners.com/">Adapt Partners</a></em></small></h2>
	<div class="metabox-holder">
	<div class="meta-box-sortables ui-sortable">
	    
	    <form action="options.php" method="post">
		    <?php
				settings_fields('adapt_smud_plugin_options');
				adapt_do_settings_sections('adapt_smud_options');
			?>

	    <div class="submit"><input class="button-primary" type="submit" name="submit" value="Save Settings" /></div>
	    </form>
	    
    </div>
    </div>
    </div>
    </div>
    
    <div id="adapt_right" class="postbox-container" style="width: 20%; float: left;">
    	<div class="metabox-holder">
    		<div class="meta-box-sortables ui-sortable">
    			<div id="adapt-partners" class="postbox">
				<!--<div title="Click to toggle" class="handlediv"><br /></div>-->
					<h3 style="cursor: inherit;"><span>Adapt Partners</span></h3>
					<div class="inside">
					<?php $logo = plugin_basename(__FILE__); ?>
					<img src="<?php echo plugin_dir_url($logo); ?>assets/adapt-partners-logo.png" width="150" height="150" alt="Adapt Partners" style="margin: 0 auto; display: block;" />
					<p><a href="http://adaptpartners.com/">Adapt Partners</a> is a search optimization and marketing agency serving medium to large businesses from the Research Triangle of North Carolina, USA.</p>
				</div>
			</div>
			<div id="adapt-plugins" class="postbox">
				<!--<div title="Click to toggle" class="handlediv"><br /></div>-->
				<h3 style="cursor: inherit;"><span>Our Plugins</span></h3>
				<div class="inside">
					<p>Thank you for using the Social Media User Detection plugin.</p>
					<p>We release <a href="http://adaptpartners.com/internet-marketing-tools/">our plugins</a> out of <span style="color: red;">love</span> and respect for you, the WordPress community.</p>
					<p>We hope you find this plugin really useful.  If you do, <a href="http://wordpress.org/extend/plugins/social-network-user-detection/" target="_blank">we'd appreciate a 5★ rating on WordPress.org</a>.</p>
				</div>
			</div>
		</div>
	</div>
    	
    </div>
<?php
}

/*function adapt_admin_scripts() {
	
		wp_enqueue_script('postbox');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('common');
	
}
add_action('wp_enqueue_scripts', 'adapt_admin_scripts');*/

?>
