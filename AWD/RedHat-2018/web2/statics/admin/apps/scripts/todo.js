/**
Todo Module
**/
var AppTodo = function () {

    // private functions & variables

    var _initComponents = function() {
        
        // init datepicker
        $('.todo-taskbody-due').datepicker({
            rtl: App.isRTL(),
            orientation: "left",
            autoclose: true
        });

        // init tags        
        $(".todo-taskbody-tags").select2({
            tags: ["Testing", "Important", "Info", "Pending", "Completed", "Requested", "Approved"]
        });
    }

    var _handleProjectListMenu = function() {
        if (App.getViewPort().width <= 992) {
            $('.todo-project-list-content').addClass("collapse");
        } else {
            $('.todo-project-list-content').removeClass("collapse").css("height", "auto");
        }
    }

    // public functions
    return {

        //main function
        init: function () {
            _initComponents();     
            _handleProjectListMenu();

            App.addResizeHandler(function(){
                _handleProjectListMenu();    
            });       
        }

    };

}();

jQuery(document).ready(function() {
    AppTodo.init();
});