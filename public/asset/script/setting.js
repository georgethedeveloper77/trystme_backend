$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".otherSideA").addClass("activeLi");

    $(document).on("change", "#is_dating", function (event) {
        event.preventDefault();

        if ($(this).prop("checked") == true) {
            var value = 1;
        } else {
            value = 0;
        }

        var updateEventStatusUrl =
            `${domainUrl}changeFromDatingAppToLivestreamApp` + "/" + value;

        $.getJSON(updateEventStatusUrl).done(function (data) {
            if (data.status) {
                iziToast.success({
                    title: "Update Successful..",
                    message: "Settings Updated Successfully !",
                    position: "topRight",
                });
            } else {
                iziToast.error({
                    title: "Failed!",
                    message: "Something went wrong!",
                    position: "topRight",
                });
            }
        });
    });
    $(document).on("change", "#is_social_media", function (event) {
        event.preventDefault();

        if ($(this).prop("checked") == true) {
            var value = 1;
        } else {
            value = 0;
        }

        var updateEventStatusUrl =
            `${domainUrl}changeFromSocialMedia` + "/" + value;

        $.getJSON(updateEventStatusUrl).done(function (data) {
            if (data.status) {
                iziToast.success({
                    title: "Update Successful..",
                    message: "Settings Updated Successfully !",
                    position: "topRight",
                });
            } else {
                iziToast.error({
                    title: "Failed!",
                    message: "Something went wrong!",
                    position: "topRight",
                });
            }
        });
    });

    $(".appdataForm").on("submit", function (event) {
        event.preventDefault();

        if (user_type == "1") {
            var formdata = new FormData($(this)[0]);

            $.ajax({
                url: `${domainUrl}updateAppdata`,
                type: "POST",
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    console.log(response);

                    if (response.status == true) {
                        // location.reload();
                        iziToast.success({
                            title: `${app.Success}!`,
                            message: `${app.settingUpdatedSuccessfully}`,
                            position: "topRight",
                        });
                    }
                },
                error: function (err) {
                    $(".loader").hide();

                    console.log(err);
                },
            });
        } else {
            $(".loader").hide();
            iziToast.error({
                title: `${app.Error}!`,
                message: `${app.tester}`,
                position: "topRight",
            });
        }
    });

    $(".otherForm").on("submit", function (event) {
        event.preventDefault();
        $(".loader").show();

        if (user_type == "1") {
            var formdata = new FormData($(this)[0]);

            $.ajax({
                url: `${domainUrl}updateOther`,
                type: "POST",
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    console.log(response);

                    if (response.status == true) {
                        location.reload();
                    }
                },
                error: function (err) {
                    $(".loader").hide();

                    console.log(JSON.stringify(err));
                },
            });
        } else {
            $(".loader").hide();
            iziToast.error({
                title: `${app.Error}!`,
                message: `${app.tester}`,
                position: "topRight",
            });
        }
    });
});
