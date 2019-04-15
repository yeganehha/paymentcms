
$(document)
    .ajaxStart(function () {
        $('.PaymentCMSLoading').show();
    })
    .ajaxStop(function () {
        $('.PaymentCMSLoading').hide()
    });
$(document).ready(function() {
    $('.PaymentCMSLoading').hide()
});
function tooltip(massage) {
    $.notify(
        {
            icon: "help",
            message: massage
        }, {
            type: "info",
            placement: {
                from: "top",
                align: "left"
            }
        }
    );
}