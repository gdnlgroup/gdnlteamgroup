<?php
/*
  Template Name:postulat
 */?>
 
 <?php
 //// le traitement du formulaire  et le formulaire son sur la meme page
 //session_start();
  $error= false;
  $precedent=$_SERVER['REQUEST_URI'];
    $user=wp_get_current_user();
     $userid=$user->ID;
     // test si l'utilisateur est connecté
    if($user->ID == 0){
    $_SESSION['context']=$precedent;
    header('location:login');
    wp_die(); 
    }else{
     // il est connecte
        if(!empty($_POST)){
            $d=$_POST;
	    
	    // le cas de la creation d'un groupe 
            if(!empty($_POST['creation'])){
                
                if(empty($d['meta_groupe'])){
                    $error="Donner un nom de groupe";
                }else{
		      // ajout de la  meta mongroupe avec la valeur nom du groupe saisi par l'utilisateur
		      // add_user_meta sur google pour lire la doc 
                     $res=add_user_meta($userid,'mongroupe', $d['meta_groupe'],true);
                     if($res){
                        $error= "Le groupe <strong>".$d['meta_groupe']. "</strong> est creé avec succès";
                     }else {
                        
                        $error= "Erreur lors de la creation du groupe";
                     }
                }
            } elseif(!empty($_POST['rejoindre'])){
                
                if( empty($d['meta_groupe'])){
                    $error="Vous essayez de rejoindre un groupe inexistant";
                }else{
		 // meme principe pour la creation de groupe
                     $res=add_user_meta($userid,'mongroupe', $d['meta_groupe'],true);
                     if($res){
                        $error= "Vous a avez rejoint le groupe <strong>".$d['meta_groupe']. "</strong> avec succès";
                     }else {
                        
                        $error= "Erreur lors de votre inscription au groupe";
                     }
                }
            } elseif(!empty($_POST['quitter'])){
                if( empty($d['meta_groupe'])){
                    $error="Erreur lors de la suppression";  
                }else{
                     $res=delete_user_meta($userid,'mongroupe', $d['meta_groupe']);
                     if($res){
                        $error= "Vous a avez quitter le groupe <strong>".$d['meta_groupe']. " </strong> avec succès";
                     }else {
                        $error= "Erreur lors de la suppression"; 
                     }
                }
            }
            
        }
                
    }
        
?>
        
        
        
 <?php get_header();?>
    <div>
    <?php if($error)?>
	    <div class="error">
	    <?php echo  utf8_encode ($error); ?>
	</div>
     <?php  $rejoins = get_user_meta( $userid,'mongroupe', true ); 
	if(!$rejoins){?>
    <div style="float:right; width:30%;">
	 <fieldset style="border:groove;width:50%;padding-left:3%">
	    
	<legend>Nouveau groupe </legend>
	
	 
	
    <form action=" <?php echo $_SERVER['REQUEST_URI'];?>" method="POST" ?>
    	<label for="user_login"> Nom du groupe </label><br>
    	<input type="text" value="" id="meta_groupe" name="meta_groupe"/><br>        
         
        <input type="submit"  value="Creer un groupe" name="creation" />
    </form>
    </fieldset>
    </div>
     <?php }
          $query = "SELECT DISTINCT  meta_value FROM `wp_usermeta` WHERE  meta_key like 'mongroupe'";
          $result = mysql_query($query);
          
       ?>
       <div style="width:70%;">
       <small>Pour postuler creer ou rejoindre un groupe</small>
      
             <table>
            <th>Nom du Groupe</th> 
                <?php
                    while ($row = mysql_fetch_assoc($result)) {?>
                    <tr><td>
                          <?php echo $row['meta_value']; ?>
                    </td>
                    <td>
                    <?php
                       
                        if($rejoins==$row['meta_value']){
                    ?>
                        rejoins
                         <form action=" <?php echo $_SERVER['REQUEST_URI'];?>" method="POST"> 
                            <input name="meta_groupe"   type="hidden" value="<?php echo $row['meta_value'];?>"/>
                            <td><input type="submit" value="quitter" name="quitter" size="10" /></td>
                        </form>
                    <?php
                    } elseif(!$rejoins) {
                        ?>
                     <form action=" <?php echo $_SERVER['REQUEST_URI'];?>" method="POST" >
                        <input name="meta_groupe"   type="hidden" value="<?php echo $row['meta_value'];?>"/>
                        <input name="rejoindre"   type="submit" value="Rejoindre"/>
                    </form>  
                    </td>
                    </tr>
                    <?php }
                    }
                ?>

          </table>
        </div>

     </div>
     
<?php get_footer();?>
 