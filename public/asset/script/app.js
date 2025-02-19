$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


var user_type = $('#user_type').val();




$(document).on("hidden.bs.modal", function () {
    // $("form")[0].reset();
    $("form")[0].reset();
    $(this).data("bs.modal", null);
    $("#viewPostModal").load(location.href + " #viewPostModal>*", "");
    $(".swiper-slide video").attr("src", "");
    $("form").trigger("reset");
    
    $(this).data("bs.modal", null);
    $(".saveButton").removeClass("spinning");
    $(".saveButton").removeClass("disabled");

    $(".saveButton1").removeClass("spinning");
    $(".saveButton1").removeClass("disabled");
});

$("form").on("submit", function () {
    $(".saveButton").addClass("spinning");
    $(".saveButton").addClass("disabled");

    $(".saveButton1").addClass("spinning");
    $(".saveButton1").addClass("disabled");
});

$("#settingsForm").on("submit", function () {
    $(".saveButton2").addClass("spinning");
    $(".saveButton2").addClass("disabled");

    setTimeout(function () {
        $(".saveButton2").removeClass("spinning");
        $(".saveButton2").removeClass("disabled");
    }, 1000);
});   


$(document).on("hidden.bs.modal", function () {

});


if ($(window).width() >= 1199) {
    $("table").removeClass("table-responsive");
}
if ($(window).width() <= 1199) {
    $("table").addClass("table-responsive");
}
if ($(window).width() <= 1800) {
    $("#UsersTable").addClass("table-responsive");
}
// add class on responsive
$(window).on("resize", function () {
    if ($(window).width() >= 1199) {
        $("table").removeClass("table-responsive");
    }
    if ($(window).width() <= 1199) {
        $("table").addClass("table-responsive");
    }
    if ($(window).width() <= 1800) {
        $("#UsersTable").addClass("table-responsive");
    }
    if ($(window).width() <= 1650) {
        $("#table-pending").addClass("table-responsive");
        $("#table-completed").addClass("table-responsive");
    }
});


$(document).on("click", ".viewDescPost", function (e) {
    e.preventDefault();
    var description = $(this).data("description");
    $("#postDesc1").text(description);
    $("#viewPostDescModal").modal("show");
});

$(document).on("click", ".viewPost", function (e) {
    e.preventDefault();
    function empty(element) {
        element.replaceChildren();
    }
    let parentClass = document.getElementById("post_contents");
    empty(parentClass);
    var description = $(this).data("description");
    var images = $(this).data("image");
    for (var i = 0; i < images.length; i++) {
        var parent = document.getElementById("post_contents");
        const fragment = document.createDocumentFragment();
        const img = fragment
            .appendChild(document.createElement("div"))
            .appendChild(document.createElement("img"));
        img.src = sourceUrl + images[i];
        parent.appendChild(fragment);
        // console.log(images[i]);
    }
    $("#post_contents").addClass("swiper-wrapper");
    $("#post_contents div").each(function () {
        $(this).addClass("swiper-slide");
    });
    var swiper = new Swiper(".mySwiper", {
        spaceBetween: 30,
        pagination: {
            el: ".swiper-pagination",
            type: "fraction",
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
    $("#postDesc").text(description);
    $("#viewPostModal").modal("show");
});

$(document).on("click", ".viewVideoPost", function (e) {
    e.preventDefault();
    function empty(element) {
        element.replaceChildren();
    }
    let parentClass = document.getElementById("post_contents");
    empty(parentClass);
    var description = $(this).data("description");
    var images = $(this).data("image");
    for (var i = 0; i < images.length; i++) {
        var parent = document.getElementById("post_contents");
        const fragment = document.createDocumentFragment();
        const video = fragment
            .appendChild(document.createElement("div"))
            .appendChild(document.createElement("video"));
        video.src = sourceUrl + images[i];

        parent.appendChild(fragment);
    }
    $("#post_contents").addClass("swiper-wrapper");
    $("#post_contents div").each(function () {
        $(this).addClass("swiper-slide");
    });
    $("video").each(function () {
        $(this).attr("controls", true);
    });

    var swiper = new Swiper(".mySwiper", {
        spaceBetween: 30,
        pagination: {
            el: ".swiper-pagination",
            type: "fraction",
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });

    $("#postDesc").text(description);
    $("#viewPostModal").modal("show");
});
