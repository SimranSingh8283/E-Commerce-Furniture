jQuery(document).ready(function ($) {

    // Auto move to next OTP input
    $(document).on('input', '.cw-otp-inputs input', function () {
        if (this.value.length === 1) {
            $(this).next('input').focus();
        }
    });

    // Collect OTP before submit
    $(document).on('submit', '.cw-otp-form', function () {
        let otp = '';
        $('.cw-otp-inputs input').each(function () {
            otp += this.value;
        });
        $('input[name="cw_otp"]').val(otp);
    });

});
