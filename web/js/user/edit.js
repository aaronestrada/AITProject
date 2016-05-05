var editFunctions = {
    register: function() {
        $('#editForm').submit(editFunctions.onSubmitEditUser);
    },
    onSubmitEditUser: function() {
        var actionUrl = $(this).attr('action');
        $.ajax({
            url: actionUrl,
            method: "POST",
            data: $('#editForm').serialize(),
            dataType: "json"
        }).done(function(response) {
            if(response.status == 'ok')
            //response is OK, user has been saved, redirect to login page
                location.href = '/user/edit';
            else
                //error on saving, show alert in screen
                $('#message').html(response.alertHtml);
        });
        return false;
    }
}

$(document).ready(editFunctions.register);