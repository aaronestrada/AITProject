var searchFunctions = {
    register: function() {
        $('.cartButton').click(searchFunctions.toggleCartButton);
        $('.moreInfoButton').click(documentFunctions.gotoOverview);
        $('.downloadButton').click(documentFunctions.downloadDocument);
    },
    toggleCartButton: function() {
        var document_id = $(this).data('id');
        var buttonRole = $(this).data('role');
        var thisButton = $(this);

        $.ajax({
            url: '/document/togglecart',
            method: "POST",
            data: {'document_id': document_id, 'action' : buttonRole},
            dataType: "json",
            statusCode: {
                403: function() {
                    location.href = '/user/login';
                }
            }
        }).done(function(response) {
            if(response.status == 'ok') {
                //response is OK, document has been saved in / removed from cart, modify button
                if(buttonRole == 'add_to_cart') {
                    thisButton.data('role', 'remove_from_cart');
                    thisButton.removeClass('btn-success');
                    thisButton.addClass('btn-danger');
                    thisButton.val('Remove from cart');
                }
                else {
                    thisButton.data('role', 'add_to_cart');
                    thisButton.removeClass('btn-danger');
                    thisButton.addClass('btn-success');
                    thisButton.val('Add to cart');
                }
            }

            //show message
            $('#message').html(response.alertHtml);

            //update shopping cart value
            $('#shoppingCount').text('(' + response.document_count + ')');

            //scroll to top to show message
            $(document).scrollTop(0);
        });
        return false;
    }
};

$(document).ready(searchFunctions.register);