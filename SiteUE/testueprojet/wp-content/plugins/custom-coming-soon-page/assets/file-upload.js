$(document).ready(function() {

    $('.upload_image_button').click(function() {
        
        
        
        formfield = $(this).parent().find('.upload_image').attr('name');
        
        imginputbox = $(this).parent().find('.upload_image').attr('id');
        
        
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        $("#TB_window").attr('style', 'display:block; width:670px; position:fixed; top:100px; left:50%; margin-left:-335px');
        return false;
    });

    window.send_to_editor = function(html) {
        imgurl = $('img',html).attr('src');
        $('#'+imginputbox).val(imgurl);
        tb_remove();
    }

});
