<?php
 
if(isset($_POST['go']) || isset($_POST['id_sujet'])) {
    
    $json = array();
    if(isset($_POST['id_sujet'])) {
      //  $id = htmlentities(intval($_GET['id_region']));
        // requ�te qui r�cup�re les d�partements selon la r�gion
        $requete = get_post_meta($_POST['id_sujet'],favoris,false);

    } 
    // r�sultats
    foreach ($requete as $key => $value){
        $json[$key][] = utf8_encode($value);
        
    }
  //  while($donnees = $resultat->fetch(PDO::FETCH_ASSOC)) {
        // je remplis un tableau et mettant l'id en index (que ce soit pour les r�gions ou les d�partements)
    //    $json[$donnees['id']][] = utf8_encode($donnees['nom']);
   // }
 
    // envoi du r�sultat au success
    echo json_encode($json);
}
?>