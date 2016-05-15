var documentFunctions = {
    downloadDocument: function () {
        var document_id = $(this).data('id');
        $('#downloadFile').attr('src', '/document/download/id/' + document_id);
    },
    gotoOverview: function () {
        var document_id = $(this).data('id');
        location.href = '/site/overview/id/' + document_id;
    }
}