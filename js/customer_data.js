$(document).ready(function () {

    $('input[name=promo_type]').click(function () {

        let promotion_type = $(this).val();
        if (promotion_type == 'new_promo') {
            $('#new_promo_list').show(); $('#existing_promo_list').hide();
        } else if (promotion_type == 'existing_promo') {
            $('#new_promo_list').hide(); $('#existing_promo_list').show();
        }
    })

    $('#cus_mobile').change(function () {
        checkMobileNo($(this).val(), $(this).attr('id'));
    });

});