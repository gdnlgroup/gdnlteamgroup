<?php
/*
  Template Name:postulat
 */?>
<?php
session_start();
 if(!empty($_POST)){
    $postid= $_POST["postid"];
    $precedent= $_POST['precedent'];
    $userid= get_current_user_id();
     if($userid == 0){
      $_SESSION['context']=$precedent;
        header('location:login');
      die();
     }
    $nomgroupe=get_user_meta($userid,'mongroupe',true);
    if($nomgroupe){
      add_post_meta($postid,'favoris',$nomgroupe);
    //echo $precedent;
    header('location:'.$precedent);
    } else{
       header('location:creer_groupe');
       die();
          
    }
    
 }
 ?>
 