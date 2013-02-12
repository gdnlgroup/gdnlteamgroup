<?php
/*
  Template Name:liste
 */?>
<?php
session_start();
$error=false;
$user=wp_get_current_user();
$userid=$user->ID;
$precedent=$_SERVER['REQUEST_URI'];
  $user= get_current_user_id();
if($user == 0) :
    $_SESSION['context']=$precedent;
    header('location:login');
   else :
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
	 
	get_header();
	 if($error)
	    echo'<div class="error">';
	    echo  utf8_encode ($error);
	echo'</div>';
	$favoris=groupe_favoris($_POST['groupe']);
	 
       $id= get_current_user_id();
        query_posts('author='.$id);
	?>
	<div id="primary">
	    <div id="content" role="main">
	    <h3>Mes sujets </h3>
	     
	    <div style="float: left; width: 70%;";>
	    <table>
	    <tr>
	    <th> N</th><th><h5>Titre</h5></th><th><h5>Etat</h5></th>
	    </tr>
         
	 <?php
	  $idsujet=1;
	 $messujet= array();
	 while (have_posts()) :
	    the_post();
	   ?>
	    
	    
	    <tr>
	    <td><?php echo $idsujet; ?></td>
	    <td>
	    <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">
	    <?php
	    if ( get_the_title() ){ $title = the_title('', '', false);
	    echo $title; }else{ _e('Sans titre');  }
	     ?>
	     </a>
	     </td>
		<td>
		    <?php
			$attribue= get_post_meta(get_the_ID(),affecte,true);
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
			    $messujets[get_the_ID()]= $idsujet;
			    		    
			}
			$idsujet++;	
		    ?>
		</td>
	    </tr>	 
	    <?php endwhile; // end of the loop. ?>
	    </table>
	    </div>
	    <div >
	    <fieldset style="border:groove;width:20%;padding-left:3%">
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
		    
		?>
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
	     
	     
	      <?php }
	      
	      endif;
	      
	      wp_reset_postdata();?>
	            
       </div><!-- #content -->
       <div>
<a href="javascript:history.back()">Retour</a>
  </div>
</div><!-- #primary -->

<?php
 get_footer();
 ?>
 
 