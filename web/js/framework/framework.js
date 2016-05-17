var frameworkFunctions = {
    register: function () {
        $('#menuIcon').click(frameworkFunctions.menuClick);
        $(document).ajaxStart(frameworkFunctions.showLoader);
        $(document).ajaxStop(frameworkFunctions.hideLoader);
    },
    showLoader: function() {
        $('.loading').height($(document).height() + 100);
        $(".loading").removeClass('hidden');
    },
    hideLoader: function() {
        setTimeout('$(".loading").addClass("hidden");', 500);
    },
    menuClick: function () {
        var menuId = $(this).data('menu-id');
        $('#' + menuId).toggleClass('display-menu');
        $('#' + menuId).toggleClass('hidden-menu');
        return false;
    }
}

$(document).ready(frameworkFunctions.register);
