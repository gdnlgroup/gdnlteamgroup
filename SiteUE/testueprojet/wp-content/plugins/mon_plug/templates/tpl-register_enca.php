<?php
/*
Template Name:register_encadrant
*/
    $error= false;
    if(!empty($_POST)){
        
        $d=$_POST;
        $d['user_pass']="azerty";
            if( !is_email($d['user_email'])){
                $error ='Veuillez enter un email valide';
            }else {
		if( !empty($d['user_name'])  && !empty($d['user_firstname'])){
		     $error ='Les noms et prenom sont obligatoire';
		}else {
		    $user = wp_insert_user( array(
			'user_login' =>  $d['user_email'],
			'user_pass'  =>  $d['user_pass'],
			'user_email' =>  $d['user_email'],
			'user_registered' =>  date('Y-m-d H:i:s')
		    ));
		    if(is_wp_error($user)){
			$error= $user->get_error_message();
		    }else{
			///add_user_meta($user,' ') ;
			 add_user_meta($user,'motdepasse', $d['user_pass'],true);
                         add_user_meta($user,'first_name', $d['first_name'],true);
                         add_user_meta($user,'last_name', $d['last_name'],true);
			$msg= "L'administrateur a accepté votre demande d'inscription sur le site de l'UE projet.\r\n ".
				"Voici vos identifiant de connexion:\r\n".
				"Login:".$d['user_login']."\r\n".
				"Mot de Passe:".$d['user_pass']."\r\n";
			$headers= 'From:'. get_option('admin_email')."\r\n";
		        wp_mail($d['user_email'],'Inscription reussie',$msg,$headers);
		      
			$d=array();
			header('location:confirmation-inscription');
			die();
		    }
		}
            }
            
        }
        
    

?>


<?php get_header();?>
	 <fieldset style="border:groove;width:50%;padding-left:3%">
	    
	<legend>Nouveau Compte</legend>
	<?php if($error)?>
	    <div class="error">
	    <?php echo $error; ?>
	</div>
	
	
    <form action=" <?php echo $_SERVER['REQUEST_URI'];?>" method="POST">
    	
        
        <label for="user_email"> Votre email </label><br>
    	<input type="text" value="" id="user_email" name="user_email"/><br>
        
        <label for="user_login">Prenom </label><br>
    	<input type="text" value="" id="first_name" name="first_name"/><br>
        
        <label for="user_login">Nom </label><br>
    	<input type="text" value="" id="last_name" name="last_name"/><br>
      <!--  
        <label for="user_pass"> Votre mot de passe </label><br>
        <input type="password" value="" id="user_pass" name="user_pass"/><br>
        
        <label for="user_pass2"> Confirmer votre mot de passe </label><br>
        <input type="password" value="" id="user_pass2" name="user_pass2"/><br>
        -->
         
        <input type="submit" value="Creer le compte" />
    </form>
    </fieldset>
     
<?php get_footer();?>