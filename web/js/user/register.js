var registerFunctions = {
    register: function() {
        $('#registerForm').submit(registerFunctions.onSubmitRegisterUser);
    },
    onSubmitRegisterUser: function() {
        var actionUrl = $(this).attr('action');
        $.ajax({
            url: actionUrl,
            method: "POST",
            data: $('#registerForm').serialize(),
            dataType: "json"
        }).done(function(response) {
            if(response.status == 'ok')
                //response is OK, user has been saved, redirect to login page
                location.href = '/user/login';
            else {
                //error on saving, show alert in screen
                $('#message').html(response.alertHtml);
                $(document).scrollTop(0);
            }
        });
        return false;
    }
}

$(document).ready(registerFunctions.register);