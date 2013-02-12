<?php 
require_once('theme_functions.php'); 
if(nccsf_top('color_scheme') == ''){
    $color_scheme = 'blue';
}else{
    $color_scheme = nccsf_top('color_scheme');
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo nccsf_top('page_title') ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo nccsf_product_info('extend_url'); ?>/themes/reset.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="<?php echo nccsf_product_info('extend_url'); ?>/themes/<?php echo $color_scheme; ?>.css" media="screen" />

        <script type="text/javascript" src="<?php echo nccsf_product_info('extend_url'); ?>/themes/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo nccsf_product_info('extend_url'); ?>/themes/twitter.min.js"></script>

        <script type="text/javascript" src="<?php echo nccsf_product_info('extend_url'); ?>/themes/theme.js"></script>

        <?php 
            if(nccsf_top('color_scheme') == 'custom'){
                $bg_color = nccsf_top('custom_color_scheme');
                $link_color = nccsf_top('custom_link_color');
                echo '
<style type="text/css">
body{background-color: #'.$bg_color.';} 
a{color:#'.$link_color.' !important;}
#email_subscription{background:#'.$bg_color.' !important;} 
</style>
';
            }
        ?>
        
    </head>
    <body>


        <div id="logo">
            <?php echo nccsf_logo(); ?>
        </div>

        <div id="content">
            <?php echo nccsf_content(); ?>

            <?php echo nccsf_countdown(); ?>

            <?php echo nccsf_email_subscription(); ?>

            <?php echo nccsf_twitter_feed(); ?>

            <span id="shadow">&nbsp;</span>
        </div>

        <div id="contact_social_media" class="clearfix">
            <?php echo nccsf_social_media_icons(); ?>
            <?php echo nccsf_contact_form(); ?>
        </div>

    </body>
</html>