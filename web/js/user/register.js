var registerFunctions = {
    register: function() {
        $('#registerForm').submit(registerFunctions.validateRegister);
    },
    validateRegister: function() {
        $.ajax({
            url: "/user/validateregister",
            method: "POST",
            data: $('#registerForm').serialize(),
            dataType: "json"
        }).done(function(response) {
            if(response.status != 'ok')
                $('#message').html(response.alertHtml);
        });
        return false;
    }
}

$(document).ready(registerFunctions.register);