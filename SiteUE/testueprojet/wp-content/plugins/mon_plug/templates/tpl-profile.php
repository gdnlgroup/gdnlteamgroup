<?php
/*
Template Name:Connexion
*/
    $user=wp_get_current_user();
    $userid->ID;
    $error=false;
    if($user->ID == 0){
        header('location:login');
    } else if(!empty($_POST)){
	
	$d=$_POST;
	
	if($d['user_pass'] != $d['user_pass2']){
            $error = "Les 2 mot de passes ne correspondent pas";
        }else {
	    if(strlen ($d['user_pass']<6)){
		     $error ='Le mot de passe est trop court il faut au minimuin 6 caractères';
	    }else {
                $userid = wp_update_user( array(
                    'ID' =>  $user->ID,
                    'user_pass'  =>  $d['user_pass']
                ));
                if(is_wp_error($user)){
                    $error= $user->get_error_message();
                }/* else{
                   /* ///add_user_meta($user,' ') ;
                    $msg= "L'administrateur vous a accepté votre demande sur le site de l'UE projet.";
                    $headers= 'From:'. get_option('admin_email')."\r\n";
                   /// wp_mail($d['user_email'],'Inscription reussie',$msg,$headers);
                    $d=array();
                    wp_signon('$user');
                    die($d['user_pass']);
                    header('location:profil');
                   
                } */
		   update_user_meta($userid, 'motdepasse', $d['user_pass']);
	    }
	} 
    }
    $passendur= get_user_meta( $userid, 'motdepasse', true ); 
?>


<?php get_header();?>
    
	 <fieldset style="border:groove;width:50%;padding-left:3%">
	 
	<legend>Mes informations</legend>
	 <?php if(!empty($_POST)): ?>
	 
	    <?php if($error):?>
	<div class="error" style="color: red;">
	    <?php echo $error;?>
         </div> 
       <?php else :?>
        <div style="color:green">
	    <?php echo 'Succes' ;?>
	</div>
	<?php endif; ?>
        <?php endif; ?>
	   
	 
	
	<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
	<label> Votre login:</label>  
        <label><?php echo $user->user_login?></label></br>
        
        <label> Votre email:</label>
        <label><?php echo $user->user_email?></label></br>
	
        <label> Votre mot de passe:</label>
        <label><?php echo $passendur ?></label></br>
        
	Nouveau mot de pass</br>
        <input type="password" id="user_pass" name="user_pass"/> </br>
	Confirmer </br>
        <input type="password" id="user_pass2" name="user_pass2" /></br>
	<input type="submit" name="modifier" value="modifier">
	</form>
    </fieldset>
     
<?php get_footer();?>