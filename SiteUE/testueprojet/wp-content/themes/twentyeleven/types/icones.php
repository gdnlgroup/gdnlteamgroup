<?php
add_action( 'admin_head', 'cpt_icons' );
function cpt_icons() {
?>
    <style type="text/css" media="screen">
        #menu-posts-projet .wp-menu-image {
            background: url(<?php bloginfo('template_url') ?>/types/images/document-attribute-p.png) no-repeat 6px -17px !important;
        }
		#menu-posts-projet:hover .wp-menu-image, #menu-posts-projet.wp-has-current-submenu .wp-menu-image {
            background-position:6px 7px!important;
        }
		
        #menu-posts-publication .wp-menu-image {
            background: url(<?php bloginfo('template_url') ?>/types/images/application-blog.png) no-repeat 6px -17px !important;
        }
		#menu-posts-publication:hover .wp-menu-image, #menu-posts-publication.wp-has-current-submenu .wp-menu-image {
            background-position:6px 7px!important;
        }
		
        #menu-posts-document .wp-menu-image {
            background: url(<?php bloginfo('template_url') ?>/types/images/drive-upload.png) no-repeat 6px -17px !important;
        }
		#menu-posts-document:hover .wp-menu-image, #menu-posts-document.wp-has-current-submenu .wp-menu-image {
            background-position:6px 7px!important;
        }
    </style>
<?php }
?>