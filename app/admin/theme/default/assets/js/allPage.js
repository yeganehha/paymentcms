
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