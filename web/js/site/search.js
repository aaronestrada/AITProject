var searchFunctions = {
    register: function() {
        $('.addToCartButton').click(searchFunctions.addToCartButtonClick);
    },
    addToCartButtonClick: function() {
        var document_id = $(this).data('id');

        $.ajax({
            url: '/document/addtocart',
            method: "POST",
            data: {'document_id': document_id},
            dataType: "json"
        }).done(function(response) {
            if(response.status == 'ok') {
                //response is OK, document has been saved in cart, modify button
            }

            //show message
            $('#message').html(response.alertHtml);
        });
        return false;
    }
};

$(document).ready(searchFunctions.register);