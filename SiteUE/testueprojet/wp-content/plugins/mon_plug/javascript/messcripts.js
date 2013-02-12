function getBaseURL() {
    var url = location.href;
    var baseURL = url.substring(0, url.indexOf('/', 14)); 
 
    if (baseURL.indexOf('http://localhost') != -1) {
        var pathname = location.pathname;
        var index1 = url.indexOf(pathname);
        var index2 = url.indexOf("/", index1 + 1);
        var baseLocalUrl = url.substr(0, index2);
 
        return baseLocalUrl;
    }
    else {
        return baseURL;
    }
 
}

    
    
    
    
   jQuery(document).ready(function() {
   
    var $sujet = jQuery('#sujet');
    var $groupe= jQuery('#groupe');
    
    // chargement des regions
  /*  $.ajax({
        url: 'affectation.php',
        data: 'go', // on envoie $_GET['go']
        dataType: 'json', // on veut un retour JSON
        success: function(json) {
            $.each(json, function(index, value) { // pour chaque noeud JSON
                // on ajoute l option dans la liste
                $sujet.append('<option value="'+ index +'">'+ value +'</option>');
            });
        }
    });*/
 
    
    $sujet.on('change', function() {
        var val = jQuery(this).val(); // on récupère la valeur de la région
        //alert(val);
        if(val != '') {
            $groupe.empty(); // on vide la liste des départements
            
            jQuery.ajax({
                type: 'POST',
                url: getBaseURL()+'/affecter',
                data: 'id_sujet='+ val, // on envoie $_GET['id_region']
                dataType: 'json',
                success: function(json) {
                    jQuery.each(json, function(index, value) {
                        $groupe.append('<option value="'+ value +'">'+ value +'</option>');
                    });
                }
            });
        }
    });
});

