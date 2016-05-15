var cartFunctions = {
    register: function () {
        $('.cartButton').click(cartFunctions.removeCartButton);
        $('.moreInfoButton').click(documentFunctions.gotoOverview);
        $('#checkoutButton').click(cartFunctions.proceedToCheckout);
    },
    removeCartButton: function () {
        var document_id = $(this).data('id');

        $.ajax({
            url: '/document/togglecart',
            method: "POST",
            data: {'document_id': document_id, 'action': 'remove_from_cart'},
            dataType: "json"
        }).done(function (response) {
            if (response.status == 'ok') {
                //response is OK, document has been removed from cart, clear div
                $('#document_' + document_id).remove();
            }

            //If no more documents in cart, show alert of no items
            if(response.document_count == 0) {
                $('#alertNoItems').removeClass('hidden');
                $('#itemsList').addClass('hidden');
                $('#checkoutBlock').addClass('hidden');
            }
            else
                $('#checkoutTotal').text(response.checkout_total)

            //show message
            $('#message').html(response.alertHtml);
        });
        return false;
    },
    proceedToCheckout: function() {
        location.href = '/site/checkout';
    }
};

$(document).ready(cartFunctions.register);