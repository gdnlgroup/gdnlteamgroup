 <?php
        $nomgroupe=get_user_meta($userid,mongroupe,true);
         $query = "SELECT post_id FROM `wp_postmeta` WHERE  meta_value like '$mongroupe'";
          $result = mysql_query($query);
           while ($row = mysql_fetch_assoc($result)){
            $definie[] = mysql_fetch_assoc($result);
            }
            
           
          
       print_r($definie);
      //  $definie=get_user_meta(get_current_user_id(), 'favoris', false);
      ?> jj
        <?php $postid=get_the_ID(); ?>
        <?php if ( !empty($definie) && in_array($postid,$definie))  : ?>
                 <h1>Choisi</h1>
        <?php else : ?>
            <form action="<?php echo bloginfo('url')?>/postuler" method="post">
                <input type="hidden" value="<?php the_ID(); ?>" name="postid"/>
                <input type="hidden" value="<?php echo $_SERVER['REQUEST_URI']; ?>" name="precedent"/>
                <input type="submit" value="postuler"/>
            </form>
        <?php endif; ?>
       <?php
    
       ?>