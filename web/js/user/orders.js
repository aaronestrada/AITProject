var orderFunctions = {
    register: function() {
        $('.moreInfoButton').click(documentFunctions.gotoOverview);
        $('.downloadButton').click(documentFunctions.downloadDocument);
    }
}

$(document).ready(orderFunctions.register);