$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".redeemrequestsSideA").addClass("activeLi");

    $("#table-pending").dataTable({
        processing: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2],
                orderable: false,
            },
        ],
        ajax: {
            url: `${domainUrl}fetchPendingRedeems`,
            data: function (data) {},
        },
    });

    $("#table-completed").dataTable({
        processing: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2],
                orderable: false,
            },
        ],
        ajax: {
            url: `${domainUrl}fetchCompletedRedeems`,
            data: function (data) {},
        },
    });


    $("#table-pending").on("click", ".delete", function (e) {
        e.preventDefault();
        if (user_type == 1) {
            var id = $(this).attr("rel");
            swal({
                title: app.sure,
                icon: "error",
                buttons: true,
                dangerMode: true,
                buttons: ["Cancel", "Yes"],
            }).then((deleteValue) => {
                if (deleteValue) {
                    if (deleteValue == true) {
                        console.log(true);
                        $.ajax({
                            type: "POST",
                            url: `${domainUrl}deleteRedeemRequest`,
                            dataType: "json",
                            data: {
                                redeem_id: id,
                            },
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                } else if (response.status == true) {
                                    iziToast.show({
                                        title: app.Success,
                                        message: app.deleteRedeemRequest,
                                        color: app.greenToast,
                                        position: app.toastPosition,
                                        transitionIn: app.fadeInAction,
                                        transitionOut: app.fadeOutAction,
                                        timeout: app.timeout,
                                        animateInside: false,
                                        iconUrl: app.checkCircleIcon,
                                    });
                                    console.log(response.message);
                                    $("#table-pending").DataTable().ajax.reload(null, false);
                                }
                            },
                        });
                    }
                }
            });
        } else {
            iziToast.show({
                title: `${app.Error}!`,
                message: app.tester,
                color: app.redToast,
                position: app.toastPosition,
                transitionIn: app.transitionInAction,
                transitionOut: app.transitionOutAction,
                timeout: app.timeout,
                animateInside: false,
                iconUrl: app.cancleIcon,
            });
        }
    });

    
    $("#table-completed").on("click", ".delete", function (e) {
        e.preventDefault();
        if (user_type == 1) {
            var id = $(this).attr("rel");
            swal({
                title: app.sure,
                icon: "error",
                buttons: true,
                dangerMode: true,
                buttons: ["Cancel", "Yes"],
            }).then((deleteValue) => {
                if (deleteValue) {
                    if (deleteValue == true) {
                        console.log(true);
                        $.ajax({
                            type: "POST",
                            url: `${domainUrl}deleteRedeemRequest`,
                            dataType: "json",
                            data: {
                                redeem_id: id,
                            },
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                } else if (response.status == true) {
                                    iziToast.show({
                                        title: app.Success,
                                        message: app.deleteRedeemRequest,
                                        color: app.greenToast,
                                        position: app.toastPosition,
                                        transitionIn: app.fadeInAction,
                                        transitionOut: app.fadeOutAction,
                                        timeout: app.timeout,
                                        animateInside: false,
                                        iconUrl: app.checkCircleIcon,
                                    });
                                    console.log(response.message);
                                    $("#table-completed")
                                        .DataTable()
                                        .ajax.reload(null, false);
                                }
                            },
                        });
                    }
                }
            });
        } else {
            iziToast.show({
                title: `${app.Error}!`,
                message: app.tester,
                color: app.redToast,
                position: app.toastPosition,
                transitionIn: app.transitionInAction,
                transitionOut: app.transitionOutAction,
                timeout: app.timeout,
                animateInside: false,
                iconUrl: app.cancleIcon,
            });
        }
    });
   

    $("#table-completed").on("click",".view-request",function(event) {
        event.preventDefault();

        var  id = $(this).attr('rel');

        // $('#editId').val($(this).attr('rel'));

        var url =  `${domainUrl}getRedeemById`+"/"+id;

        $.getJSON(url).done(function(data) {


           if(data.user.image == null){
            var image = 'http://placehold.jp/150x150.png';
           }else{
               var image = `${sourceUrl}`+data.user.image;
           }

            $('#user-img').attr('src', image);
            $('#user-fullname').text(data.user.fullname);
            $('#request-id').text(data.request_id);
            $('#coin_amount').val(data.coin_amount);
            $('#amount_paid').val(data.amount_paid);
            $('#payment_gateway').val(data.payment_gateway);
            $('#account_details').val(data.account_details);

            $('#amount_paid').attr("readonly", true);
            $('#div-submit').addClass('d-none');


        });
        $('#viewRequest').modal('show');
    });

    $("#table-pending").on("click",".complete-redeem",function(event) {
        event.preventDefault();

        var  id = $(this).attr('rel');

        $('#editId').val($(this).attr('rel'));

        var url =  `${domainUrl}getRedeemById`+"/"+id;

        $.getJSON(url).done(function(data) {


           if(data.user.image == null){
            var image = 'http://placehold.jp/150x150.png';
           }else{
               var image = `${sourceUrl}`+data.user.image;
           }

            $('#user-img').attr('src', image);
            $('#user-fullname').text(data.user.fullname);
            $('#request-id').text(data.request_id);
            $('#coin_amount').val(data.coin_amount);
            $('#payment_gateway').val(data.payment_gateway);
            $('#account_details').val(data.account_details);

            $('#amount_paid').attr("readonly", false);
            $('#div-submit').removeClass('d-none');

        });
        $('#viewRequest').modal('show');
    });

    $("#completeForm").on("submit", function (event) {
        event.preventDefault();
        $(".loader").show();

        if (user_type == "1") {
            var formdata = new FormData($("#completeForm")[0]);

            $.ajax({
                url: `${domainUrl}completeRedeem`,
                type: "POST",
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    console.log(response);
                    $("#table-pending").DataTable().ajax.reload(null, false);
                    $("#table-completed").DataTable().ajax.reload(null, false);

                    $(".loader").hide();
                    $("#viewRequest").modal("hide");


                    if (response.status == false) {
                        iziToast.error({
                            title: app.Error,
                            message: response.message,
                            position: "topRight",
                        });
                    }
                },
                error: function (err) {
                    console.log(err);
                },
            });
        } else {
            iziToast.error({
                title: app.Error,
                message: app.tester,
                position: "topRight",
            });
        }
    });




});
