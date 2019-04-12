$('#productInput').click(function () {
    if( $(this).attr('data-status') == 'close' ){
        $('.product').css('display', 'block');
        $(this).attr('data-status', 'open');
    } else {
        $('.product').css('display', 'none');
        $(this).attr('data-status', 'close');
    }
})