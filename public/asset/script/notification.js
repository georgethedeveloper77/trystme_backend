$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".notificationSideA").addClass("activeLi");

    $(".addModalBtn").on("click", function (event) {
        event.preventDefault();
        $("#addForm")[0].reset();
    });

    $("#notificationTable").dataTable({
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
            url: `${domainUrl}fetchAllNotification`,
            data: function (data) {},
        },
    });

    $("#addForm").on("submit", function (event) {
        event.preventDefault();
        if (user_type == 1) {
            var formdata = new FormData($("#addForm")[0]);
            console.log(formdata);
            $.ajax({
                url: `${domainUrl}addNotification`,
                type: "POST",
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    console.log(response);

                    if (response.status == false) {
                        console.log(response.message);
                    } else if (response.status == true) {
                        iziToast.show({
                            title: app.Success,
                            message: app.notificationSend,
                            color: app.greenToast,
                            position: app.toastPosition,
                            transitionIn: app.fadeInAction,
                            transitionOut: app.fadeOutAction,
                            timeout: app.timeout,
                            animateInside: false,
                            iconUrl: app.checkCircleIcon,
                        });
                        $("#notificationTable")
                            .DataTable()
                            .ajax.reload(null, false);
                        $("#addNotificationModal").modal("hide");
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

    $("#notificationTable").on("click", ".edit", function (e) {
        e.preventDefault();

        var id = $(this).attr("rel");
        var title = $(this).data("title");
        var message = $(this).data("message");

        $("#notificationID").val(id);
        $("#editNotificationTitle").val(title);
        $("#editNotificationMessage").val(message);

        $("#editNotificationModal").modal("show");
    });

    $(document).on("submit", "#editNotificationForm", function (e) {
        e.preventDefault();
        var id = $("#notificationID").val();
        if (user_type == 1) {
            let EditformData = new FormData($("#editNotificationForm")[0]);
            EditformData.append("notificationID", id);
            $.ajax({
                type: "POST",
                url: `${domainUrl}updateNotification`,
                data: EditformData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == false) {
                        console.log(response.message);
                    } else if (response.status == true) {
                        iziToast.show({
                            title: app.Success,
                            message: app.notificationUpdated,
                            color: app.greenToast,
                            position: app.toastPosition,
                            transitionIn: app.fadeInAction,
                            transitionOut: app.fadeOutAction,
                            timeout: app.timeout,
                            animateInside: false,
                            iconUrl: app.checkCircleIcon,
                        });
                        $("#notificationTable")
                            .DataTable()
                            .ajax.reload(null, false);
                        $("#editNotificationModal").modal("hide");
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

    $("#notificationTable").on("click", ".delete", function (e) {
        e.preventDefault();
        var id = $(this).attr("rel");
        console.log(id);
        if (user_type == 1) {
            swal({
                title: "Are you sure?",
                icon: "error",
                buttons: true,
                dangerMode: true,
                buttons: ["Cancel", "Yes"],
            }).then((deleteValue) => {
                if (deleteValue) {
                    if (deleteValue == true) {
                        $.ajax({
                            type: "POST",
                            url: `${domainUrl}deleteNotification`,
                            dataType: "json",
                            data: {
                                notification_id: id,
                            },
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                } else if (response.status == true) {
                                    iziToast.show({
                                        title: app.Success,
                                        message: app.notificationDeleted,
                                        color: app.greenToast,
                                        position: app.toastPosition,
                                        transitionIn: app.fadeInAction,
                                        transitionOut: app.fadeOutAction,
                                        timeout: app.timeout,
                                        animateInside: false,
                                        iconUrl: app.checkCircleIcon,
                                    });
                                    $("#notificationTable")
                                        .DataTable()
                                        .ajax.reload(null, false);
                                    console.log(response.message);
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

    $("#notificationTable").on("click", ".repeat", function (e) {
        e.preventDefault();
        var id = $(this).attr("rel");
        var title = $(this).data("title");
        var message = $(this).data("message");

        var button = $(this);
        button.addClass("spinning");
        button.addClass("disabled");

        if (user_type == 1) {
            let editformData = new FormData();
            editformData.append("title", title);
            editformData.append("message", message);
            for (var pair of editformData.entries()) {
                console.log(pair[0] + ", " + pair[1]);
            }
            $.ajax({
                type: "POST",
                url: `${domainUrl}repeatNotification`,
                data: editformData,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log(response);
                    if (response.status == true) {
                        button.removeClass("spinning");
                        button.removeClass("disabled");
                        iziToast.show({
                            title: app.Success,
                            message: app.notificationSend,
                            color: app.greenToast,
                            position: app.toastPosition,
                            transitionIn: app.fadeInAction,
                            transitionOut: app.fadeOutAction,
                            timeout: app.timeout,
                            animateInside: false,
                            iconUrl: app.checkCircleIcon,
                        });
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
});
