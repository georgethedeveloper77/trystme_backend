$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".diamondpackSideA").addClass("activeLi");

    $(".addModalBtn").on("click", function (event) {
        event.preventDefault();
        $("#addForm")[0].reset();
    });

    $("#diamondTable").dataTable({
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
            url: `${domainUrl}fetchDiamondPackages`,
            data: function (data) {},
        },
    });

    $(document).on("submit", "#addForm", function (e) {
        e.preventDefault();
        if (user_type == 1) {
            let formData = new FormData($("#addForm")[0]);
            $.ajax({
                type: "POST",
                url: `${domainUrl}addDiamondPack`,
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == true) {
                        iziToast.show({
                            title: app.Success,
                            message: app.diamondPackAdded,
                            color: app.greenToast,
                            position: app.toastPosition,
                            transitionIn: app.fadeInAction,
                            transitionOut: app.fadeOutAction,
                            timeout: app.timeout,
                            animateInside: false,
                            iconUrl: app.checkCircleIcon,
                        });
                        $("#diamondTable").DataTable().ajax.reload(null, false);
                        $("#addDiamondPack").modal("hide");
                        $("#addForm")[0].reset();
                        console.log(response.status);
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

    $("#diamondTable").on("click", ".edit", function (e) {
        e.preventDefault();
        var id = $(this).attr("rel");
        var amount = $(this).data("amount");
        var android_product_id = $(this).data("android_product_id");
        var ios_product_id = $(this).data("ios_product_id");

        $("#diamondPackId").val(id);
        $("#edit_amount").val(amount);
        $("#edit_playstore").val(android_product_id);
        $("#edit_appstore").val(ios_product_id);
        $("#editDiamondPack").modal("show");
    });
        
    $(document).on("submit", "#editDiamondPackForm", function (e) {
        e.preventDefault();
        if (user_type == 1) {
            let formData = new FormData($("#editDiamondPackForm")[0]);
            $.ajax({
                type: "POST",
                url: `${domainUrl}updateDiamondPack`,
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == true) {
                    iziToast.show({
                        title: app.Success,
                        message: app.diamondPackUpdated,
                        color: app.greenToast,
                        position: app.toastPosition,
                        transitionIn: app.fadeInAction,
                        transitionOut: app.fadeOutAction,
                        timeout: app.timeout,
                        animateInside: false,
                        iconUrl: app.checkCircleIcon,
                    });
                    $("#diamondTable").DataTable().ajax.reload(null, false);
                    $("#editDiamondPack").modal("hide");
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

    $("#diamondTable").on("click", ".delete", function (e) {
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
                            url: `${domainUrl}deleteDiamondPack`,
                            dataType: "json",
                            data: {
                                diamond_pack_id: id,
                            },
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                } else if (response.status == true) {
                                    iziToast.show({
                                        title: app.Success,
                                        message: app.diamondPackdeleted,
                                        color: app.greenToast,
                                        position: app.toastPosition,
                                        transitionIn: app.fadeInAction,
                                        transitionOut: app.fadeOutAction,
                                        timeout: app.timeout,
                                        animateInside: false,
                                        iconUrl: app.checkCircleIcon,
                                    });
                                    $("#diamondTable")
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

});
