<?php
/*
  Template Name:liste
 */?>
<?php
session_start();

$precedent=$_SERVER['REQUEST_URI'];
  $user= get_current_user_id();
if($user == 0) :
    $_SESSION['context']=$precedent;
    header('location:login');
   else :
	get_header();
       $choisis=avoir_favoris();
       if($choisis){
       $my_query = new WP_Query(array( 'post__in' => $choisis));
	?>
	<div id="primary">
	    <div id="content" role="main">
	    <h3>Nos sujets Favoris</h3>
	    
	    <table style="width: 60%">
	    <th><h5>Titre</h5></th>
         <?php while ($my_query->have_posts()) :
	    $my_query->the_post(); ?>
	    
	    <tr>
	    <td>
	    <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">
	    <?php
	    if ( get_the_title() ){ $title = the_title('', '', false);
	    echo $title; }else{ _e('Sans titre');  }
	     ?>
	     </a>
	     </td>
	     <td align="center">
		<?php contacter ();?>
	     </td>
	    </tr>	 
	    <?php endwhile; // end of the loop. ?>
	    </table>
	     <?php }   
	 else { ?> <div>Vous n'avez pas de favoris actuellement</div> <?php } ?>
	    
	 <?php endif;?>
	
	 
	 
       </div><!-- #content -->
       <a href="javascript:history.back()">Retour</a>
</div><!-- #primary -->


  
<?php
 get_footer();
 ?>
 
 