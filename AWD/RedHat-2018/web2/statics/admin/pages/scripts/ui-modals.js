var UIModals = function () {

    var handleModals = function () {
        $("#draggable").draggable({
            handle: ".modal-header"
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            handleModals();
        }

    };

}();

jQuery(document).ready(function() {    
   UIModals.init();
});