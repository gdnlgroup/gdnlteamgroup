<?php
/*
Template Name:Connexion
*/
session_start(); 
   
	$error=false;
	if(!empty($_POST)){
	    $user= wp_signon($_POST);
	    if(is_wp_error($user)){
		$error=$user->get_error_message();
	    }else{
		if(isset($_SESSION['context'])){
		    
		    header('location:'.$_SESSION["context"]);
		     unset($_SESSION["context"]);
		    die();
		   
		}else {
		  //  $root=str_replace('index.php','', $_SERVER['SCRIPT_NAME']);
		    //$url= str_replace($root,'',$_SERVER['REQUEST_URI']);
		    //$url=explode('/',$url);
		   header('location:'.$root);
		   die();
		   }
		
	    }
	}else{
	    $user=wp_get_current_user();
	    if($user->ID != 0){
		header('location:'.$root);
	    }
	 }
?>


<?php get_header();?>
	 <fieldset style="border:groove;width:50%;padding-left:3%">
	    
	<legend>Se Connecter<?php ?></legend>
	<?php if($error)?>
	    <div class="error">
	    <?php echo $error; ?>
	    
	</div>
	  
	
    <form action=" <?php echo $_SERVER['REQUEST_URI'];?>" method="POST">
    	<label for="user_login"> Votre login </label><br>
    	<input type="text" value="" id="user_login" name="user_login"/><br>
        
        <label for="user_password"> Votre mot de passe </label><br>
        <input type="password" value="" id="user_password" name="user_password"/><br>
        <input type="submit" value="Se connecter" />
    </form>
    </fieldset>
     
<?php get_footer();?>