$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".livehistorySideA").addClass("activeLi");

    $("#table-22").dataTable({
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
            url: `${domainUrl}fetchLiveHistory`,
            data: function (data) {},
        },
    });


});
