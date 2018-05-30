var UIAlertsApi = function () {

    var handleDemo = function() {        
        $('#alert_show').click(function(){
            App.alert({
                container: $('#alert_container').val(), // alerts parent container(by default placed after the page breadcrumbs)
                place: $('#alert_place').val(), // append or prepent in container 
                type: $('#alert_type').val(),  // alert's type
                message: $('#alert_message').val(),  // alert's message
                close: $('#alert_close').is(":checked"), // make alert closable
                reset: $('#alert_reset').is(":checked"), // close all previouse alerts first
                focus: $('#alert_focus').is(":checked"), // auto scroll to the alert after shown
                closeInSeconds: $('#alert_close_in_seconds').val(), // auto close after defined seconds
                icon: $('#alert_icon').val() // put icon before the message
            });
        });
    }

    var handleCode = function() {
        var myTextArea = document.getElementById('code_editor_demo');
        var myCodeMirror = CodeMirror.fromTextArea(myTextArea, {
            lineNumbers: true,
            matchBrackets: true,
            styleActiveLine: true,
            mode: 'javascript',
            smartIndent: true,
            indentWithTabs: true,
            readOnly: true,
            inputStyle: 'textarea',
            theme: 'neo'
        });
    }

    return {

        //main function to initiate the module
        init: function () {
            handleDemo();
            handleCode();
        }
    };

}();

jQuery(document).ready(function() {    
   UIAlertsApi.init();
});