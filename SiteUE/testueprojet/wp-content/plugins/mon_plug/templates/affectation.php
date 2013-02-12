<?php
 
if(isset($_POST['go']) || isset($_POST['id_sujet'])) {
    
    $json = array();
    if(isset($_POST['id_sujet'])) {
      //  $id = htmlentities(intval($_GET['id_region']));
        // requête qui récupère les départements selon la région
        $requete = get_post_meta($_POST['id_sujet'],favoris,false);

    } 
    // résultats
    foreach ($requete as $key => $value){
        $json[$key][] = utf8_encode($value);
        
    }
  //  while($donnees = $resultat->fetch(PDO::FETCH_ASSOC)) {
        // je remplis un tableau et mettant l'id en index (que ce soit pour les régions ou les départements)
    //    $json[$donnees['id']][] = utf8_encode($donnees['nom']);
   // }
 
    // envoi du résultat au success
    echo json_encode($json);
}
?>