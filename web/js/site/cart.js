var cartFunctions = {
    register: function() {
        $('.cartButton').click(cartFunctions.removeCartButton);
    },
    removeCartButton: function() {
        var document_id = $(this).data('id');

        $.ajax({
            url: '/document/togglecart',
            method: "POST",
            data: {'document_id': document_id, 'action': 'remove_from_cart'},
            dataType: "json"
        }).done(function(response) {
            if(response.status == 'ok') {
                //response is OK, document has been removed from cart, clear div
                $('#document_' + document_id).remove();
            }

            //show message
            $('#message').html(response.alertHtml);
        });
        return false;
    }
};

$(document).ready(cartFunctions.register);