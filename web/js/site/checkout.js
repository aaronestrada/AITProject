var checkoutFunctions = {
    register: function () {
        $('#checkoutprocessForm').submit(checkoutFunctions.onSubmitCheckoutProcess);
    },
    onSubmitCheckoutProcess: function () {
        var actionUrl = $(this).attr('action');
        $.ajax({
            url: actionUrl,
            method: "POST",
            data: $('#checkoutprocessForm').serialize(),
            dataType: "json",
            statusCode: {
                403: function() {
                    location.href = '/user/login';
                }
            }
        }).done(function (response) {
            if (response.status == 'ok')
            //response is OK, user has been saved, redirect to login page
                location.href = '/user/orders';
            else
            //error on saving, show alert in screen
                $('#message').html(response.alertHtml);
        });
        return false;
    }
}

$(document).ready(checkoutFunctions.register);
