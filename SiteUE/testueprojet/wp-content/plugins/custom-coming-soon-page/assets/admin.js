jQuery(document).ready(function($){
    
    // WYSIWYG
    $('.wysiwyg').cleditor({
        width: '79.6%',
        height: 200
        
    })
    
    // jQuery UI Date Picker
    $(function() {
        $(".date-picker").datepicker();
        $(".date-picker").change(function() {
            $( this ).datepicker( "option", "dateFormat", 'd MM, yy' );
        });
    });
    
    // Message
    $(".fade").animate({
        width:'auto'
    }, 2500).slideToggle(350);

    // Delete Confiramation
    $(".fw-delete").click(function(){
        var answer = confirm("Are you sure?")
        if (answer){
            return true;
        }
        else{
            return false;
        }
    })    

})