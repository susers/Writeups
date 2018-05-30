var ComponentsCodeEditors = function () {
    
    var handleDemo1 = function () {
        var myTextArea = document.getElementById('code_editor_demo_1');
        var myCodeMirror = CodeMirror.fromTextArea(myTextArea, {
            lineNumbers: true,
            matchBrackets: true,
            styleActiveLine: true,
            theme:"ambiance",
            mode: 'javascript'
        });
    }

    var handleDemo2 = function () {
        var myTextArea = document.getElementById('code_editor_demo_2');
        var myCodeMirror = CodeMirror.fromTextArea(myTextArea, {
            lineNumbers: true,
            matchBrackets: true,
            styleActiveLine: true,
            theme:"material",
            mode: 'css'
        });
    }

    var handleDemo3 = function () {
        var myTextArea = document.getElementById('code_editor_demo_3');
        var myCodeMirror = CodeMirror.fromTextArea(myTextArea, {
            lineNumbers: true,
            matchBrackets: true,
            styleActiveLine: true,
            theme:"neat",
            mode: 'javascript',
            readOnly: true
        });
    }

    var handleDemo4 = function () {
        var myTextArea = document.getElementById('code_editor_demo_4');
        var myCodeMirror = CodeMirror.fromTextArea(myTextArea, {
            lineNumbers: true,
            matchBrackets: true,
            styleActiveLine: true,
            theme:"neo",
            mode: 'css',
            readOnly: true
        });
    }


    return {
        //main function to initiate the module
        init: function () {
            handleDemo1();
            handleDemo2();
            handleDemo3();
            handleDemo4();
        }
    };

}();

jQuery(document).ready(function() {    
   ComponentsCodeEditors.init(); 
});