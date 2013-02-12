<?php
/**
 * @package Private Messages For WordPress
 *
 * @author: Rilwis
 * @url: http://www.deluxeblogtips.com
 * @email: rilwis@gmail.com
 
 Template Name: premier proposition
 
 */
 ?>

<?php
 

get_header();
?>
<div class="hfeed content">
	<h1 style="text-align: center; font-size:xx-large;"><?php the_title(); ?></h2>
 	<script type="text/javascript">
		// Switch between send page, inbox and outbox
		function pmSwitch(page) {
			document.getElementById('pm-send').style.display = 'none';
			document.getElementById('pm-inbox').style.display = 'none';
			document.getElementById('pm-outbox').style.display = 'none';
			document.getElementById(page).style.display = '';
			return false;
		}
	</script>
	<!-- Include scripts and style for autosuggest feature -->
	<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/private-messages-for-wordpress/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/private-messages-for-wordpress/js/jquery.autoSuggest.packed.js"></script>
	<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/private-messages-for-wordpress/js/script.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/private-messages-for-wordpress/css/style.css" />
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div class="post" id="post-<?php the_ID(); ?>">
		<?php
		$show = array(true, false, false);
		if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'rwpm_inbox') {
			$show = array(false, true, false);
		} elseif (isset($_REQUEST['page']) && $_REQUEST['page'] == 'rwpm_outbox') {
			$show = array(false, false, true);
		}
		?>
		<div id="pm-send" <?php if (!$show[0]) echo 'style="display:none"'; ?>><?php ecrire_ad();?></div>
		<div id="pm-inbox" <?php if (!$show[1]) echo 'style="display:none"'; ?>><?php rwpm_inbox();?></div>
		<div id="pm-outbox" <?php if (!$show[2]) echo 'style="display:none"'; ?>><?php rwpm_outbox();?></div>
	</div>
	<?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
