/**
Todo 2 Module
**/
var AppTodo2 = function () {

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
            placeholder: 'Status'
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

if (App.isAngularJsApp() === false) {
    jQuery(document).ready(function() {
        AppTodo2.init();
    });
}