var frameworkFunctions = {
    register: function() {
        $('#menuIcon').click(frameworkFunctions.menuClick);
    },
    menuClick: function() {
        var menuId = $(this).data('menu-id');
        $('#' + menuId).toggleClass('display-menu');
        $('#' + menuId).toggleClass('hidden-menu');
        return false;
    }
}

$(document).ready(frameworkFunctions.register);
