<?php

/**
 * Handle's user dashboard functionality
 *
 * Insert shortcode [wpuf_dashboard] in a page to
 * show the user dashboard
 *
 * @since Version 0.1
 * @author Tareq Hasan
 * @package WP User Frontend
 */
function wpuf_user_dashboard( $atts ) {

    extract( shortcode_atts( array('post_type' => 'post'), $atts ) );

    ob_start();
    
    if ( is_user_logged_in() ) {
        wpuf_user_dashboard_post_list( $post_type );
    } else {
       Redirect_('login');
       die();
       // printf( __( "This page is restricted. Please %s to view this page.", 'wpuf' ), wp_loginout( '', false ) );
    }
    
    $content =  ob_get_contents();
    ob_end_clean();

    return $content;
}

add_shortcode( 'wpuf_dashboard', 'wpuf_user_dashboard' );

function wpuf_user_dashboardprojet( $atts ) {

    extract( shortcode_atts( array('post_type' => 'projet'), $atts ) );

    ob_start();
    
    if ( is_user_logged_in() ) {
        wpuf_user_dashboard_post_list( $post_type );
    } else {
        printf( __( "This page is restricted. Please %s to view this page.", 'wpuf' ), wp_loginout( '', false ) );
    }
    
    $content =  ob_get_contents();
    ob_end_clean();

    return $content;
}

add_shortcode( 'wpuf_dashboardprojet', 'wpuf_user_dashboardprojet' );



function wpuf_user_dashboardocument( $atts ) {

    extract( shortcode_atts( array('post_type' => 'document'), $atts ) );

    ob_start();
    
    if ( is_user_logged_in() ) {
        wpuf_user_dashboard_post_list( $post_type );
    } else {
       Redirect_('login');
       die();
       // printf( __( "This page is restricted. Please %s to view this page.", 'wpuf' ), wp_loginout( '', false ) );
    }
    
    $content =  ob_get_contents();
    ob_end_clean();

    return $content;
}

add_shortcode( 'wpuf_dashboarddocument', 'wpuf_user_dashboardocument' );

function wpuf_user_dashboaraffecter( $atts ) {

    extract( shortcode_atts( array('post_type' => 'projet'), $atts ) );

    ob_start();
    
    if ( is_user_logged_in() ) {
        wpuf_user_dash_affecter_list( $post_type );
    } else {
       Redirect_('login');
       die();
        //printf( __( "This page is restricted. Please %s to view this page.", 'wpuf' ), wp_loginout( '', false ) );
    }
    
    $content =  ob_get_contents();
    ob_end_clean();

    return $content;
}

add_shortcode( 'wpuf_dashboardaffecter', 'wpuf_user_dashboaraffecter' );








function wpuf_user_dash_affecter_list( $post_type ) {
    global $wpdb, $userdata;

    $userdata = get_userdata( $userdata->ID );
    //affecter post
           if(!empty($_POST)){
	    $d=$_POST;
	       if(empty($_POST['sujet']) || !isset($_POST['sujet'])|| $_POST['sujet']==""){
		    $error="Veuillez choisir un projet";
		    
		} 
		elseif(empty($_POST['groupe']) || !isset($_POST['groupe'])){
		    $error="Veuillez choisir un groupe";
		}else {
			 $res=add_post_meta($d['sujet'],'affecte', $d['groupe'],true);
			 if($res){
			    $favoris=groupe_favoris($d['groupe']);
			    foreach ($favoris as $key => $value){
			    delete_post_meta($value, 'favoris',$d['groupe']);
			    }
			    $error= "Le sujet  <strong>".$d['sujet']. "</strong> est affecté au <strong> ".$d['groupe']. "</strong>  avec succès";
			 }else {
			    
			    $error= "Erreur: Ce sujet deja affecté";
			 }
		}
       
       }
  


    //get the posts count from db
    $sql = "SELECT count(ID) FROM $wpdb->posts
            WHERE post_author = $userdata->ID AND post_type = '$post_type'
            AND post_status = 'publish'";

    $total = $wpdb->get_var( $sql );

    //setup the pagination variables
    $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
    $limit = ( get_option( 'wpuf_list_post_range' ) ) ? get_option( 'wpuf_list_post_range' ) : 10;
    $offset = ( $pagenum - 1 ) * $limit;
    $num_of_pages = ( $total > 0 ) ? ceil( $total / $limit ) : 0;

    $page_links = paginate_links( array(
        'base' => add_query_arg( 'pagenum', '%#%' ),
        'format' => '',
        'prev_text' => __( '&laquo;', 'aag' ),
        'next_text' => __( '&raquo;', 'aag' ),
        'total' => $num_of_pages,
        'current' => $pagenum
            ) );

            
            
            
            
    //get the posts
    $sql = "SELECT ID, post_title, post_name, post_status, post_date FROM $wpdb->posts
            WHERE post_author = $userdata->ID AND post_type = '$post_type'
            AND post_status = 'publish' ORDER BY post_date DESC LIMIT $offset, $limit";

    $posts = $wpdb->get_results( $sql );
    ?>

    <?php echo '<div style="color:red;" >'.$error.'</div>';?>

    <div style="float:left; width:65%;">
    
        
    <h2 class="page-head"><strong>
        <span class="colour"><?php printf( __( 'Vos '.$post_type.'s', 'wpuf')); ?></span>
        </strong>
    </h2>
    <?php if ( $posts ) { ?>

        <?php if ( get_option( 'wpuf_list_post_count' ) == 'yes' ) { ?>
            <div class="post_count"><?php _e( 'Vous avez ', 'wpuf' ); ?> <?php echo "<span>$total</span> $post_type"; ?>(s)</div>
        <?php } ?>
        
        <?php do_action( 'wpuf_dashboard', $userdata->ID, $post_type );
            $idsujet=0;
        ?>
          
             
        <table class="wpuf-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                     <th><?php echo "N"; ?></th>
                    <th><?php _e( 'Titre', 'wpuf' ); ?></th>
                    <th><?php _e( 'Etat', 'wpuf' ); ?></th>
                    <?php if ( get_option( 'wpuf_sub_charge_posting' ) == 'yes' )
                        echo '<th>' . __( 'Payment', 'wpuf' ) . '</th>'; ?>
                    <th><?php _e( 'Options', 'wpuf' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php wp_reset_query() ?>
                <?php foreach ($posts as $p) { ?>
                    <tr>
                        <td>
                            <?php echo ++$idsujet; ?>
                        </td>
                        <td>
                            <?php if ( $p->post_status == 'pending' || $p->post_status == 'draft' || $p->post_status == 'future' ) { ?>

                                <?php echo wptexturize( $p->post_title ); ?>

                            <?php } else { ?>

                                <a href="<?php echo get_permalink( $p->ID ) ?>"><?php echo wptexturize( $p->post_title ); ?></a>

                            <?php } ?>
                        </td>
                        <td>
                           
		    <?php
			$attribue= get_post_meta($p->ID,affecte,true);
			$blogusers=lesmesbres($attribue);
			if(!empty($attribue) && isset($attribue)){
			    echo'<small>Attribu&eacute; &agrave; <strong>'. $attribue .'</strong></small></td><td>
			    <a href="mailto:';
			    foreach ($blogusers as $user) {
			        echo  $user->user_email .';';
			    }			
			    ?>
		            ">contacter le groupe </a>
			    <?php
			}else {
			    $messujets[$p->ID]= $idsujet;
			    		    
			}
				
		    ?>
                        </td>

                        <?php
                        if ( get_option( 'wpuf_sub_charge_posting' ) == 'yes' ) {
                            $order_id = get_post_meta( $p->ID, 'wpuf_order_id', true );
                            ?>
                            <td>
                                <?php if ( $p->post_status == 'pending' && $order_id ) { ?>
                                    <a href="<?php echo get_permalink( get_option( 'wpuf_sub_pay_page' ) ); ?>?action=wpuf_pay&type=post&post_id=<?php echo $p->ID; ?>">Pay Now</a>
                                <?php } ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="wpuf-pagination">
            <?php if ( $page_links )
                echo $page_links; ?>
        </div>

    <?php } else { ?>

        <h3><?php _e( utf8_encode('Actuellement vous n\'avez aucun '.$post_type.' publié'), 'wpuf' ); ?></h3>

    <?php } ?>
 </div>
    
    <div style="float:right; width:34%">
	    <fieldset style="border:groove;padding-left:3%">
		<legend>Attribuer un Projet</legend>
		<form action=" <?php echo $_SERVER['REQUEST_URI'];?>" method="POST" ?>
		<p>
		<label for="sujet" >Quel projet ?</label> 
		<select id="sujet" name="sujet">;
		 <option value=""></option>
		<?php
		    if(!empty($messujets)&& isset($messujets)){
			foreach ($messujets as $key => $value){?>
			   <option value="<?php echo $key; ?>"><?php echo $value ;  ?></option>
			  
		   <?php 
			}
		     }?>
		
		</select>
		</p>
		<p>
		    <label for="sujet" >A quel groupe ?</label> 
		    <select id="groupe" name="groupe">
			<option>-- Groupe--</option>
		    </select>
		</p>
		<input type="submit" value="attribuer" name="affecter"/>
		</form>
	    
            </fieldset> 
</div>

    
    <?php
}










/**
 * List's all the posts by the user
 *
 * @since version 0.1
 * @author Tareq Hasan
 *
 * @global object $wpdb
 * @global object $userdata
 */
function wpuf_user_dashboard_post_list( $post_type ) {
    global $wpdb, $userdata;

    $userdata = get_userdata( $userdata->ID );
    //delete post
    if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == "del" ) {
        $nonce = $_REQUEST['_wpnonce'];
        if ( !wp_verify_nonce( $nonce, 'wpuf_del' ) )
            die( "Security check" );

        //check, if the requested user is the post author
        $post = get_post( $_REQUEST['pid'] );

        if ( $post->post_author == $userdata->ID ) {
            wp_delete_post( $_REQUEST['pid'] );
            echo '<div class="success">' . __( $post_type.' Supprimé', 'wpuf' ) . '</div>';
        } else {
            echo '<div class="error">' . __( 'Vous n\'êtes propriétaire de ce projet. triche!! huh!', 'wpuf' ) . '</div>';
        }
    }

    //get the posts count from db
    $sql = "SELECT count(ID) FROM $wpdb->posts
            WHERE post_author = $userdata->ID AND post_type = '$post_type'
            AND (post_status = 'publish' OR post_status = 'pending' OR post_status = 'draft' OR post_status = 'future') ";

    $total = $wpdb->get_var( $sql );

    //setup the pagination variables
    $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
    $limit = ( get_option( 'wpuf_list_post_range' ) ) ? get_option( 'wpuf_list_post_range' ) : 10;
    $offset = ( $pagenum - 1 ) * $limit;
    $num_of_pages = ( $total > 0 ) ? ceil( $total / $limit ) : 0;

    $page_links = paginate_links( array(
        'base' => add_query_arg( 'pagenum', '%#%' ),
        'format' => '',
        'prev_text' => __( '&laquo;', 'aag' ),
        'next_text' => __( '&raquo;', 'aag' ),
        'total' => $num_of_pages,
        'current' => $pagenum
            ) );

    //get the posts
    $sql = "SELECT ID, post_title, post_name, post_status, post_date FROM $wpdb->posts
            WHERE post_author = $userdata->ID AND post_type = '$post_type'
            AND (post_status = 'publish' OR post_status = 'pending' OR post_status = 'draft' OR post_status = 'future') ORDER BY post_date DESC LIMIT $offset, $limit";

    $posts = $wpdb->get_results( $sql );
    ?>

    <h2 class="page-head">
        <span class="colour"><?php printf( __( 'Vos '.$post_type.'s', 'wpuf')); ?></span>
    </h2>
    <?php if ( $posts ) { ?>

        <?php if ( get_option( 'wpuf_list_post_count' ) == 'yes' ) { ?>
            <div class="post_count"><?php _e( 'Vous avez ', 'wpuf' ); ?> <?php echo "<span>$total</span> $post_type"; ?>(s)</div>
        <?php } ?>
        
        <?php do_action( 'wpuf_dashboard', $userdata->ID, $post_type ) ?>
          
             
        <table class="wpuf-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th><?php _e( 'Titre', 'wpuf' ); ?></th>
                    <th><?php _e( 'Etat', 'wpuf' ); ?></th>
                    <?php if ( get_option( 'wpuf_sub_charge_posting' ) == 'yes' )
                        echo '<th>' . __( 'Payment', 'wpuf' ) . '</th>'; ?>
                    <th><?php _e( 'Options', 'wpuf' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php wp_reset_query() ?>
                <?php foreach ($posts as $p) { ?>
                    <tr>
                        <td>
                            <?php if ( $p->post_status == 'pending' || $p->post_status == 'draft' || $p->post_status == 'future' ) { ?>

                                <?php echo wptexturize( $p->post_title ); ?>

                            <?php } else { ?>

                                <a href="<?php echo get_permalink( $p->ID ) ?>"><?php echo wptexturize( $p->post_title ); ?></a>

                            <?php } ?>
                        </td>
                        <td>
                            <?php wpuf_show_post_status( $p->post_status ) ?>
                        </td>

                        <?php
                        if ( get_option( 'wpuf_sub_charge_posting' ) == 'yes' ) {
                            $order_id = get_post_meta( $p->ID, 'wpuf_order_id', true );
                            ?>
                            <td>
                                <?php if ( $p->post_status == 'pending' && $order_id ) { ?>
                                    <a href="<?php echo get_permalink( get_option( 'wpuf_sub_pay_page' ) ); ?>?action=wpuf_pay&type=post&post_id=<?php echo $p->ID; ?>">Pay Now</a>
                                <?php } ?>
                            </td>
                        <?php } ?>

                        <td>
                            <?php if ( get_option( 'wpuf_can_edit_post' ) == 'yes' ) { ?>
                                <?php
                                $edit_page = (int) get_option( 'wpuf_edit_page_url' );
                                $url = get_permalink( $edit_page );
                                ?>
                                <a href="<?php echo wp_nonce_url( $url . '?pid=' . $p->ID, 'wpuf_edit' ); ?>"><?php _e( 'Editer', 'wpuf' ); ?></a>
                            <?php } else { ?>
                                &nbsp;
                            <?php } ?>

                            <?php if ( get_option( 'wpuf_can_del_post' ) == 'yes' ) { ?>
                                <a href="<?php echo wp_nonce_url( "?action=del&pid=" . $p->ID, 'wpuf_del' ) ?>" onclick="return confirm('Are you sure to delete this post?');"><span style="color: red;"><?php _e( 'Supprimer', 'wpuf' ); ?></span></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="wpuf-pagination">
            <?php if ( $page_links )
                echo $page_links; ?>
        </div>

    <?php } else { ?>

        <h3><?php _e( utf8_encode('Actuellement vous n\'avez aucun '.$post_type.' publié'), 'wpuf' ); ?></h3>

    <?php } ?>
<?php the_widget('WP_Widget_Text'); ?> 

    <!--php if ( get_option( 'wpuf_list_user_info' ) == 'yes' ) { ?>
        <div class="wpuf-author">
            <h3><php _e( 'Author Info', 'wpuf' ); ?></h3>
            <div class="wpuf-author-inside odd">
                <div class="wpuf-user-image">php echo get_avatar( $userdata->user_email, 80 ); ?></div>
                <div class="wpuf-author-body">
                    <p class="wpuf-user-name"><a href="php echo get_author_posts_url( $userdata->ID ); ?>">php printf( esc_attr__( '%s', 'wpuf' ), $userdata->display_name ); ?></a></p>
                    <p class="wpuf-author-info">php echo $userdata->description; ?></p>
                </div>
            </div>
        </div> .author 
    php -->

    <?php
}






