<?php
/*
Template Name:inscriptetudiant
*/
?>

<?php

$error = false;
if(isset($_POST['butonOK'])){
      require("connexion.php");
        
      if ($_FILES['monfichier']['error'] > 0) {
	 $error= "Erreur lors du transfert"; 
       }else if ($_FILES['monfichier']['size'] > 1000) {
	 $error = "Le fichier est trop gros";
      }
      else{
	
	    $adr=$_FILES['monfichier']['name'];
	    $extensions_valides = array('csv');
	    $capacite= 'a:1:{s:8:"etudiant";s:1:"1";}';
	    
            $type_file = strtolower(  substr(  strrchr($_FILES['monfichier']['name'], '.')  ,1)  );
	    $extensions = array('csv');
	    //echo $type_file;
	    //echo $extensions[0];
	   if(!in_array($type_file, $extensions)){
		   $error="Extension incorecte";
	    } else{
		      if (is_uploaded_file($_FILES['monfichier']['tmp_name'])) {
			if (!$fp = fopen($_FILES['monfichier']['tmp_name'],"r")) {
			      $error = "Echec de l'ouverture du fichier";
			       
			} else {
 
			      $role='etudiant';
			      while (($data = fgetcsv($fp, 0, ";")) !== FALSE) {
			      //$pass=generateur();
			      $e_mail= $data[0];
			     // $specialite=$data[3];
			  /*    $user = wp_insert_user( array(
				    'user_login' =>  $e_mail,
				    'user_pass'  =>  $pass,
				    'user_email' =>  $e_mail,
				    'user_registered' =>  date('Y-m-d H:i:s'),
				    'role' => $role
			       ));*/
			  
			       $res = monplug_register_new_user( $e_mail, $e_mail, $role);
			       if ( is_wp_error($res) )   
				 $error.= $res->get_error_message();
				  else
				  $successe.= utf8_encode('Inscription reussie. Un courriel sera envoyé à '.$res. '</br>');
				 
			     
			      /*  if(is_wp_error($user)){
				    $error.= $e_mail." ".$user->get_error_message()."</br>";
			        }else{
				   //
				    $msg= "L'administrateur vous a accepté votre demande sur le site de l'UE projet \r\n";
				    $msg.="login:". $e_mail. " \r\n";
				    $msg.="mot de passe: ".$pass."\r\n";
				    
				    $headers= 'From: abocoum@bocoum.fr'."\r\n";
				    mail($d['user_email'],'Inscription reussie',$msg);
				    add_user_meta($user,'motdepasse',$pass,true); 
				    $d=array();
				    add_user_meta($user,'user_specialite',$specialite); 
				    add_user_meta($user,'wp_capabilities',$capacite);*/
			      
				    
				
			      }
			 fclose($fp);
			}
		      } else{
			     $error= "Le fichier n'a pas été uploadé (trop gros ?)";
		      }
	    }
      }
}
      ?>
      <?php get_header();?>
      <h1 style="font-size: xx-large">Bienvenu sur la page d'inscription des &eacute;tudiants.</h1>  
      <?php if($error) echo  '<div style="color: red">'.$error.'</div>';
       elseif($_POST['butonOK']) echo '<div style="color: green">'. $successe .'</div>';?> 
     <div>
      <form action=" <?php echo $_SERVER['REQUEST_URI'];?>" method="POST" enctype="multipart/form-data">
	      Veuillez charger le fichier CSV : <br>
	      
	     <p> <input type="file" name="monfichier" /></p> <br/>
	      <p center="center"> <input  type="Submit" name="butonOK" /></p>
      </form>
      </div
      <br>
      <br><br>
      <a href="/gestion-du-site">retour</a>
      <?php get_footer();?>