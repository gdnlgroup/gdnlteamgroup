<?php
/*
Template Name:inscriptetudiant
*/
function wpuf_get_post_types1() {

    $post_types = get_post_types();

  

    //insert the custom post types
   // $cus_post_type = get_option( 'wpuf_post_types' );
    //echo ($cus_post_type);
    //if ( $cus_post_type ) {
      //  $cus_post_type = explode( ',', $cus_post_type );

        //foreach ($cus_post_type as $cus_type) {
          //  $post_types[$cus_type] = $cus_type;
        //}
    //}
      //print_r ($post_types);
    return $post_types;
}
?>
<select name="role" id="role">
                        <?php
                        if ( !$new_user_role ) {
                            $new_user_role = !empty( $current_role ) ? $current_role : get_option( 'default_role' );
                        }
                        wp_dropdown_roles( $new_user_role );
                        ?>
                    </select>
                    <?php
print_r(get_option( 'default_role' ));
    ?>