<?php
/*
Template Name:Connexion
*/
    $error= false;
    if(!empty($_POST)){
        
        $d=$_POST;
        if($d['user_pass'] != $d['user_pass2']){
            $error = "Les 2 mot de passes ne correspondent pas";
        }else {
            if( !is_email($d['user_email'])){
                $error ='Veuillez enter un email valide';
            }else {
                $user = wp_insert_user( array(
                    'user_login' =>  $d['user_login'],
                    'user_pass'  =>  $d['user_pass'],
                    'user_email' =>  $d['user_email'],
                    'user_registered' =>  date('Y-m-d H:i:s')
                ));
                if(is_wp_error($user)){
                    $error= $user->get_error_message();
                }else{
                    ///add_user_meta($user,' ') ;
                    $msg= "L'administrateur vous a accepté votre demande sur le site de l'UE projet.";
                    $headers= 'From:'. get_option('admin_email')."\r\n";
                   /// wp_mail($d['user_email'],'Inscription reussie',$msg,$headers);
                    $d=array();
                    wp_signon('$user');
                    die($d['user_pass']);
                    header('location:profil');
                    
                }
            }
            
        }
        
    }

?>


<?php get_header();?>
	 <fieldset style="border:groove;width:50%;padding-left:3%">
	    
	<legend>Votre profil</legend>
	<?php if($error)?>
	    <div class="error">
	    <?php echo $error; ?>
	</div>
	
	
    <form action=" <?php echo $_SERVER['REQUEST_URI'];?>" method="POST">
    	<label for="user_login"> Votre login </label><br>
    	<input type="text" value="" id="user_login" name="user_login"/><br>
        
        <label for="user_email"> Votre email </label><br>
    	<input type="text" value="" id="user_email" name="user_email"/><br>
        
        <label for="user_pass"> Votre mot de passe </label><br>
        <input type="password" value="" id="user_pass" name="user_pass"/><br>
        
        <label for="user_pass2"> Confirmer votre mot de passe </label><br>
        <input type="password" value="" id="user_pass2" name="user_pass2"/><br>
        
         
        <input type="submit" value="S'inscrire" />
    </form>
    </fieldset>
     
<?php get_footer();?>