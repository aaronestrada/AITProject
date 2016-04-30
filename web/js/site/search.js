var searchFunctions = {
    register: function() {
        $('.cartButton').click(searchFunctions.toggleCartButton);
    },
    toggleCartButton: function() {
        var document_id = $(this).data('id');
        var buttonRole = $(this).data('role');
        var thisButton = $(this);

        $.ajax({
            url: '/document/togglecart',
            method: "POST",
            data: {'document_id': document_id, 'action' : buttonRole},
            dataType: "json"
        }).done(function(response) {
            if(response.status == 'ok') {
                //response is OK, document has been saved in / removed from cart, modify button
                if(buttonRole == 'add_to_cart') {
                    thisButton.data('role', 'remove_from_cart');
                    thisButton.val('Remove from cart');
                }
                else {
                    thisButton.data('role', 'add_to_cart');
                    thisButton.val('Add to cart');
                }
            }

            //show message
            $('#message').html(response.alertHtml);
        });
        return false;
    }
};

$(document).ready(searchFunctions.register);