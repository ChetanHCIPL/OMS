 $(document).ready(function() {  
  //$('#mobile').inputmask("(999) 999-999-9999");
  $(".select2-placeholder").select2({
    placeholder: "Select Country",
    allowClear: true
  });
  $(".select2-state").select2({
    placeholder: "Select State",
    allowClear: true
  });
  $(".select2-city").select2({
    placeholder: "Select City",
    allowClear: true
  });
  $(".select2-status").select2({
    placeholder: "Select Status",
    allowClear: true
  }); 
  $('#country_code').change(function()
  { 
      var cid = $(this).val();  
      $.ajax({
          "data":{"_token":csrf_token,"countrycode":cid},
          "url": statelist,
          "type": "post",
          "success":function(data)
          {
            if(data.success == 'success')
            {
              $('#state_code').html(data.html);
              $('#city_code').html('<option value="">Select City</option>');
            }
          }
        });
    });/*
   $('#state_code').change(function()
    { 
        var sid = $(this).val(); 
        $.ajax({
            "data":{"_token":csrf_token,"citycode":sid},
            "url": citylist,
            "type": "post",
            "success":function(data)
            {
              if(data.success == 'success')
              {
                $('#city_code').html(data.html); 
              }
            }
          });
      });*/
     $("#useraddForm").validate({
            rules: {
              first_name:{
                required: true    
              },
              last_name:{
                required: true    
              },
              email: {
                required: true,
                email: true
              },
              username: {
                required: true 
              }, 
              mobile_isd:{
                required: true 
              },
              address: {
                required: true, 
              },
              mobile: {
                required: true,
                number: true,
                minlength:10,
                maxlength:12,
              },
              area: {
                required: true 
              },
              country_code: {
                required: true 
              },
              state_code: {
                required: true 
              },
               city_code: {
                required: true,  
              },  
              zip: {
                 required: true, 
              }, 
              status: {
                 required: true, 
              }, 
              action: "required"
            }, 
            submitHandler: function(form) { 
                  return truel;
            }
    });
});