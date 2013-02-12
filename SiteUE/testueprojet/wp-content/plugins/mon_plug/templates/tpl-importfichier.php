<?php
/*
  Template Name:attente
 */
?>

<?php
if( isset($_POST['upload']) ) // si formulaire soumis
{
    $content_dir = 'upload\\'; // dossier où sera déplacé le fichier

    $tmp_file = $_FILES['fichier']['tmp_name'];

    if( !is_uploaded_file($tmp_file) )
    {
        exit("Le fichier est introuvable");
    }

    // on vérifie maintenant l'extension
    //$type_file = $_FILES['fichier']['type'];
	
	$type_file = strtolower(  substr(  strrchr($_FILES['fichier']['name'], '.')  ,1)  );
	$extensions = array('doc', 'docx', 'pdf', 'odf');
    if(!in_array($type_file, $extensions))
    {
        exit("Le fichier n'est pas document");
    }

    // on copie le fichier dans le dossier de destination
    $name_file = $_FILES['fichier']['name'];
	if( preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $name_file) )
	{
    	exit("Nom de fichier non valide");
	}
	
    if( !move_uploaded_file($tmp_file, $content_dir . $name_file) )
    {
        exit("Impossible de copier le fichier dans $content_dir");
    }

    echo "Le fichier a bien été uploadé";
	sleep (5);
	header('Location: ./gestionfichier.php'); 
}
	else echo"<h1> Le document n'a pas ete soumis</h1>";
	header('Location: ./gestionfichier.php'); 

?>


<form method="post" enctype="multipart/form-data" action="upload.php">
<p>
<input type="hidden" name="MAX_FILE_SIZE" value="5000000">
<input type="file" name="fichier" size="30">
<input type="submit" name="upload" value="Uploader">
</p>
</form>
