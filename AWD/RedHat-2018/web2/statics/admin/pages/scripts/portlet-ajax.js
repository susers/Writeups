var PortletAjax = function () {

    var handlePortletAjax = function () {
        //custom portlet reload handler
        $('#my_portlet .portlet-title a.reload').click(function(e){
            e.preventDefault();  // prevent default event
            e.stopPropagation(); // stop event handling here(cancel the default reload handler)
            // do here some custom work:
            App.alert({
                'type': 'danger', 
                'icon': 'warning',
                'message': 'Custom reload handler!',
                'container': $('#my_portlet .portlet-body') 
            });
        })
    }

    return {
        //main function to initiate the module
        init: function () {
            handlePortletAjax();
        }

    };

}();

jQuery(document).ready(function() {    
   PortletAjax.init();
});