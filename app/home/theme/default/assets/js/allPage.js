
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
    if ($(window).width() <= 768) {
        $('.position-fixed-grid').addClass('position-tempfixed-grid').removeClass('position-fixed-grid');
    } else
        $('.position-tempfixed-grid').addClass('position-fixed-grid').removeClass('position-tempfixed-grid');

}

$(window).resize(function(){

    if ($(window).width() <= 768) {
        $('.position-fixed-grid').addClass('position-tempfixed-grid').removeClass('position-fixed-grid');
    } else
        $('.position-tempfixed-grid').addClass('position-fixed-grid').removeClass('position-tempfixed-grid');

});
window.onscroll = function() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        $('.position-fixed-grid').css('margin-top', document.documentElement.scrollTop+'px');
    } else {
        $('.position-fixed-grid').css('margin-top', '0px');
    }
};