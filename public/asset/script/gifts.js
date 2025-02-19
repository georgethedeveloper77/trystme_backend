$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".giftSideA").addClass("activeLi");

    $(".addModalBtn").on("click", function (event) {
        event.preventDefault();
        $("#addForm")[0].reset();
    });

    $("#giftTable").dataTable({
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
            url: `${domainUrl}fetchAllGifts`,
            data: function (data) {},
        },
    });

    $(document).on("submit", "#addForm", function (e) {
        e.preventDefault();
        if (user_type == 1) {
            let formData = new FormData($("#addForm")[0]);
            $.ajax({
                type: "POST",
                url: `${domainUrl}addGift`,
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == true) {
                        iziToast.show({
                            title: app.Success,
                            message: app.giftAdded,
                            color: app.greenToast,
                            position: app.toastPosition,
                            transitionIn: app.fadeInAction,
                            transitionOut: app.fadeOutAction,
                            timeout: app.timeout,
                            animateInside: false,
                            iconUrl: app.checkCircleIcon,
                        });
                        $("#giftTable").DataTable().ajax.reload(null, false);
                        $("#addGift").modal("hide");
                        $("#addForm")[0].reset();
                    }
                },
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

    $("#giftTable").on("click", ".edit", function (e) {
        e.preventDefault();
        var id = $(this).attr("rel");
        var img = $(this).data("img");
        var price = $(this).data("price");

        $("#editGiftId").val(id);
        $("#gift-img-view").attr("src", img);
        $("#edit_coin_price").val(price);
        $("#editGift").modal("show");
    });

    $(document).on("submit", "#editGiftForm", function (e) {
        e.preventDefault();
        if (user_type == 1) {
            let formData = new FormData($("#editGiftForm")[0]);
            $.ajax({
                type: "POST",
                url: `${domainUrl}updateGift`,
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == true) {
                        iziToast.show({
                            title: app.Success,
                            message: app.giftUpdated,
                            color: app.greenToast,
                            position: app.toastPosition,
                            transitionIn: app.fadeInAction,
                            transitionOut: app.fadeOutAction,
                            timeout: app.timeout,
                            animateInside: false,
                            iconUrl: app.checkCircleIcon,
                        });
                        $("#giftTable").DataTable().ajax.reload(null, false);
                        $("#editGift").modal("hide");
                    }
                },
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

    $("#giftTable").on("click", ".delete", function (e) {
        e.preventDefault();
        if (user_type == 1) {
            var id = $(this).attr("rel");
            swal({
                title: "Are you sure?",
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
                            url: `${domainUrl}deleteGift`,
                            dataType: "json",
                            data: {
                                gift_id: id,
                            },
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                } else if (response.status == true) {
                                    iziToast.show({
                                        title: app.Success,
                                        message: app.giftdeleted,
                                        color: app.greenToast,
                                        position: app.toastPosition,
                                        transitionIn: app.fadeInAction,
                                        transitionOut: app.fadeOutAction,
                                        timeout: app.timeout,
                                        animateInside: false,
                                        iconUrl: app.checkCircleIcon,
                                    });
                                    $("#giftTable")
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

    $("#table-22").on("click", ".edit", function (event) {
        event.preventDefault();
        $("#edit_cat")[0].reset();

        $("#editId").val($(this).attr("rel"));
        $("#gift-img-view").attr("src", $(this).data("img"));
        $("#edit_coin_price").val($(this).data("price"));

        $("#edit_cat_modal").modal("show");
    });
});
