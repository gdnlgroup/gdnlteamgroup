<?php
/*
Template Name:Connexion
*/
    $user=wp_get_current_user();
    
    if($user->ID == 0){
        header('location:login');
    }
?>


<?php get_header();?>
	 <fieldset style="border:groove;width:50%;padding-left:3%">
	    
	<legend>Mes informations</legend>
	<label> Votre login:</label></br>
        <label>place login</label>
        
        <label> Votre email:</label></br>
        <label>place login</label>
        
        <label> Votre mot de passe:</label></br>
        <label>place login</label>
        
        <label> Votre etc...:</label> </br>
        <label>place login</label>
        
    </fieldset>
     
<?php get_footer();?>