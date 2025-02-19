

$(document).ready(function () {

    $("#loginform").on('submit',function(event) {
event.preventDefault();
$('.loader').show();


var formdata = new FormData($("#loginform")[0]);
console.log(formdata);


$.ajax({
    url: `${domainUrl}login`,
    type: 'POST',
    data: formdata,
    dataType: "json",
    contentType: false,
    cache: false,
    processData: false,
    success: function(response) {
        console.log(response);
        $('.loader').hide();
        if(response.status == true){

            
            iziToast.success({
                title: `${app.Success}!`,
                message: `${app.LoginSuccessfull}`,
                position: 'topRight'
              });

              window.location.href = `${domainUrl}index`; 
             
              

        }else{

            $('#loginform')[0].reset();

            iziToast.error({
               title: `${app.Error}!`,
                message: `${app.EntervailedPasswordandUsername}`,
                position: 'topRight'
              });

        }
       

    },
    error: function(err) {

        $('#loginform')[0].reset();

            iziToast.error({
                title: `${app.Error}!`,
                message: `${app.EntervailedPasswordandUsername}`,
                position: 'topRight'
              });
        $('.loader').hide();
        console.log(err);

    }

});


});
});