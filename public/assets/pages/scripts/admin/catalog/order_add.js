jQuery(document).ready(function () {
    $('.selectize-select').selectize({ create: false, removeItemButton: true, sortField: { field: 'text', direction: 'asc' }, dropdownParent: 'body'});    
    $('#billing_name').change(function(){
        $('#address').show();
    });
    $('#shipping_address').change(function(){
        $('#address2').show();
    });

    $('#same-address').change(function() {
        if(this.checked) {
            $('#address2').show();
        }else{
            $('#address2').hide();
        }
        
    });

    $('.add').click(function() {
        $('#list-product').show();
    });
    $('.remove').click(function() {
        $('#list-product').hide();
    });

    $('#filters').click(function(){
        var x = document.getElementById("ftr");
        if (x.style.display === "block") {
            $("#btn-label").html('<i class="ft-filter"></i> Show Filter');
            x.style.display = "none";
        } else {
            $("#btn-label").html('<i class="ft-filter"></i> Hide Filer');
            x.style.display = "block";
        }
        var x = document.getElementById("ftr2");
        if (x.style.display === "block") {
            x.style.display = "none";
        } else {
            x.style.display = "block";
        }
    });
});