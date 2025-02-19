$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".usersSideA").addClass("activeLi");

    var id = $("#userId").val();
    console.log(id);

    $("#btnAddImage").on("click", function (event) {
        event.preventDefault();
        $("#addImageModal").modal("show");
    });

    // Image Add
    $("#addForm").submit(function (e) {
        e.preventDefault();
        if (user_type == 1) {
            var formdata = new FormData($("#addForm")[0]);

            console.log(formdata);
            $.ajax({
                url: `${domainUrl}addUserImage`,
                type: "POST",
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    if (response.status == true) {
                        console.log(response.status);
                        window.location.href = "";
                    } else if (response.status == false) {
                        console.log(err);
                    }
                },
                error: function (err) {
                    console.log(err);
                },
            });
        } else {
            iziToast.error({
                title: "Tester Login",
                message: "you are tester",
                position: "topRight",
                timeOut: 4000,
            });
        }
    });

    $(document).on("click", ".btnRemove", function (event) {
        event.preventDefault();
        var imgId = $(this).data("imgid");
        console.log(imgId);
        swal({
            title: app.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                if (user_type == "1") {
                    var url = `${domainUrl}deleteUserImage` + "/" + imgId;
                    $.getJSON(url).done(function (response) {
                        if (response.status == true) {
                            console.log(response.status);
                            location.reload();
                        } else if (response.status == false) {
                            console.log(response);
                            iziToast.error({
                                title: `${app.Error}!`,
                                message: response.message,
                                position: "topRight",
                            });
                        }
                    });
                } else {
                    iziToast.error({
                        title: `${app.Error}!`,
                        message: app.tester,
                        position: "topRight",
                    });
                }
            }
        });
    });

    // form data update
    $("#userUpdate").submit(function (e) {
        e.preventDefault();
        if (user_type == 1) {
            var formdata = new FormData($("#userUpdate")[0]);

            console.log(formdata);
            $.ajax({
                url: `${domainUrl}updateUser`,
                type: "POST",
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    if (response.status == true) {
                        console.log(response.status);
                        window.location.href = "";
                    } else if (response.status == false) {
                        console.log(err);
                    }
                },
                error: function (err) {
                    console.log(err);
                },
            });
        } else {
            iziToast.error({
                title: "Tester Login",
                message: "you are tester",
                position: "topRight",
                timeOut: 4000,
            });
        }
    });

    $(document).on("click", ".allow-live", function (e) {
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
                            url: `${domainUrl}allowLiveToUser`,
                            dataType: "json",
                            data: {
                                user_id: id,
                            },
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                } else if (response.status == true) {
                                    iziToast.show({
                                        title: app.Success,
                                        message: app.thisUserisAllowedToGoLive,
                                        color: app.greenToast,
                                        position: app.toastPosition,
                                        transitionIn: app.fadeInAction,
                                        transitionOut: app.fadeOutAction,
                                        timeout: app.timeout,
                                        animateInside: false,
                                        iconUrl: app.checkCircleIcon,
                                    });
                                    $("#UsersTable")
                                        .DataTable()
                                        .ajax.reload(null, false);
                                    $("#FakeUsersTable")
                                        .DataTable()
                                        .ajax.reload(null, false);
                                    $("#StreamersTable")
                                        .DataTable()
                                        .ajax.reload(null, false);
                                    $("#reloadContent").load(
                                        location.href + " #reloadContent>*",
                                        ""
                                    );
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

    $(document).on("click", ".restrict-live", function (e) {
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
                            url: `${domainUrl}restrictLiveToUser`,
                            dataType: "json",
                            data: {
                                user_id: id,
                            },
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                } else if (response.status == true) {
                                    iziToast.show({
                                        title: app.Success,
                                        message: app.restrictLiveAccessToUser,
                                        color: app.greenToast,
                                        position: app.toastPosition,
                                        transitionIn: app.fadeInAction,
                                        transitionOut: app.fadeOutAction,
                                        timeout: app.timeout,
                                        animateInside: false,
                                        iconUrl: app.checkCircleIcon,
                                    });
                                    $("#UsersTable")
                                        .DataTable()
                                        .ajax.reload(null, false);
                                    $("#FakeUsersTable")
                                        .DataTable()
                                        .ajax.reload(null, false);
                                    $("#StreamersTable")
                                        .DataTable()
                                        .ajax.reload(null, false);
                                    $("#reloadContent").load(
                                        location.href + " #reloadContent>*",
                                        ""
                                    );
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

    $(document).on("click", ".block", function (e) {
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
                            url: `${domainUrl}blockUser`,
                            dataType: "json",
                            data: {
                                user_id: id,
                            },
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                } else if (response.status == true) {
                                    iziToast.show({
                                        title: app.Success,
                                        message: app.thisUserHasBeenBlocked,
                                        color: app.greenToast,
                                        position: app.toastPosition,
                                        transitionIn: app.fadeInAction,
                                        transitionOut: app.fadeOutAction,
                                        timeout: app.timeout,
                                        animateInside: false,
                                        iconUrl: app.checkCircleIcon,
                                    });
                                    $("#UsersTable")
                                        .DataTable()
                                        .ajax.reload(null, false);
                                    $("#FakeUsersTable")
                                        .DataTable()
                                        .ajax.reload(null, false);
                                    $("#StreamersTable")
                                        .DataTable()
                                        .ajax.reload(null, false);
                                    $("#reloadContent").load(
                                        location.href + " #reloadContent>*",
                                        ""
                                    );
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

    $(document).on("click", ".unblock", function (e) {
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
                            url: `${domainUrl}unblockUser`,
                            dataType: "json",
                            data: {
                                user_id: id,
                            },
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                } else if (response.status == true) {
                                    iziToast.show({
                                        title: app.Success,
                                        message: app.thisUserHasBeenUnblocked,
                                        color: app.greenToast,
                                        position: app.toastPosition,
                                        transitionIn: app.fadeInAction,
                                        transitionOut: app.fadeOutAction,
                                        timeout: app.timeout,
                                        animateInside: false,
                                        iconUrl: app.checkCircleIcon,
                                    });
                                    $("#UsersTable")
                                        .DataTable()
                                        .ajax.reload(null, false);
                                    $("#FakeUsersTable")
                                        .DataTable()
                                        .ajax.reload(null, false);
                                    $("#StreamersTable")
                                        .DataTable()
                                        .ajax.reload(null, false);
                                    $("#reloadContent").load(
                                        location.href + " #reloadContent>*",
                                        ""
                                    );
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

    $("#userPostTable").dataTable({
        processing: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2, 3, 4],
                orderable: false,
            },
        ],
        ajax: {
            url: `${domainUrl}userPostList`,
            data: {
                userId: id,
            },
            error: (error) => {
                console.log(error);
            },
        },
    });

    $("#userPostTable").on("click", ".deletePost", function (e) {
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
                        $.ajax({
                            type: "POST",
                            url: `${domainUrl}deletePostFromUserPostTable`,
                            dataType: "json",
                            data: {
                                post_id: id,
                            },
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                } else if (response.status == true) {
                                    iziToast.show({
                                        title: app.Success,
                                        message: app.postDeleteSuccessfully,
                                        color: app.greenToast,
                                        position: app.toastPosition,
                                        transitionIn: app.fadeInAction,
                                        transitionOut: app.fadeOutAction,
                                        timeout: app.timeout,
                                        animateInside: false,
                                        iconUrl: app.checkCircleIcon,
                                    });
                                    $("#userPostTable")
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

    $(document).on("click", ".viewStory", function (e) {
        e.preventDefault();
        var story = $(this).data("image");

        $("#story_content").attr("src", story);
        $("#viewStoryModal").modal("show");
    });

    $(document).on("click", ".viewStoryVideo", function (e) {
        e.preventDefault();
        var story = $(this).data("image");

        $("#story_content_video").attr("src", story);
        $("#viewStoryVideoModal").modal("show");
    });

    $("#userStoryTable").dataTable({
        process: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1],
                orderable: false,
            },
        ],
        ajax: {
            url: `${domainUrl}userStoryList`,
            data: {
                user_id: id,
            },
            error: (error) => {
                console.log(error);
            },
        },
    });

    $("#userStoryTable").on("click", ".deleteStory", function (e) {
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
                        $.ajax({
                            type: "POST",
                            url: `${domainUrl}deleteStoryFromAdmin`,
                            dataType: "json",
                            data: {
                                story_id: id,
                            },
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                } else if (response.status == true) {
                                    iziToast.show({
                                        title: "Deleted",
                                        message: "Story Delete Succesfully",
                                        color: "green",
                                        position: "bottomCenter",
                                        transitionIn: "fadeInUp",
                                        transitionOut: "fadeOutDown",
                                        timeout: 3000,
                                        animateInside: false,
                                        iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                                    });
                                    $("#userStoryTable")
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
                title: "Oops",
                message: "You are tester",
                color: "red",
                position: toastPosition,
                transitionIn: "fadeInUp",
                transitionOut: "fadeOutDown",
                timeout: 3000,
                animateInside: false,
                iconUrl: `${domainUrl}asset/img/x.svg`,
            });
        }
    });

        $(document).on("click", ".deleteUser", function (e) {
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
                                url: `${domainUrl}deleteUserFromAdmin`,
                                dataType: "json",
                                data: {
                                    user_id: id,
                                },
                                success: function (response) {
                                    if (response.status == false) {
                                        console.log(response.message);
                                    } else if (response.status == true) {
                                        window.location.href = `${domainUrl}users`;
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
