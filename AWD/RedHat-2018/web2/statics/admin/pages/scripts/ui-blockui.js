var UIBlockUI = function() {

    var handleSample1 = function() {

        $('#blockui_sample_1_1').click(function() {
            App.blockUI({
                target: '#blockui_sample_1_portlet_body'
            });

            window.setTimeout(function() {
                App.unblockUI('#blockui_sample_1_portlet_body');
            }, 2000);
        });

        $('#blockui_sample_1_2').click(function() {
            App.blockUI({
                target: '#blockui_sample_1_portlet_body',
                boxed: true
            });

            window.setTimeout(function() {
                App.unblockUI('#blockui_sample_1_portlet_body');
            }, 2000);
        });

        $('#blockui_sample_1_3').click(function() {
            App.blockUI({
                target: '#blockui_sample_1_portlet_body',
                animate: true
            });

            window.setTimeout(function() {
                App.unblockUI('#blockui_sample_1_portlet_body');
            }, 2000);
        });
    }

    var handleSample2 = function() {

        $('#blockui_sample_2_1').click(function() {
            App.blockUI();

            window.setTimeout(function() {
                App.unblockUI();
            }, 2000);
        });

        $('#blockui_sample_2_2').click(function() {
            App.blockUI({
                boxed: true
            });

            window.setTimeout(function() {
                App.unblockUI();
            }, 2000);
        });

        $('#blockui_sample_2_3').click(function() {
            App.startPageLoading({message: 'Please wait...'});

            window.setTimeout(function() {
                App.stopPageLoading();
            }, 2000);
        });

        $('#blockui_sample_2_4').click(function() {
            App.startPageLoading({animate: true});

            window.setTimeout(function() {
                App.stopPageLoading();
            }, 2000);
        });
    }

    var handleSample3 = function() {

        $('#blockui_sample_3_1_0').click(function() {
            App.blockUI({
                target: '#basic',
                overlayColor: 'none',
                cenrerY: true,
                animate: true
            });

            window.setTimeout(function() {
                App.unblockUI('#basic');
            }, 2000);
        });

        $('#blockui_sample_3_1').click(function() {
            App.blockUI({
                target: '#blockui_sample_3_1_element',
                overlayColor: 'none',
                animate: true
            });
        });

        $('#blockui_sample_3_1_1').click(function() {
            App.unblockUI('#blockui_sample_3_1_element');
        });

        $('#blockui_sample_3_2').click(function() {
            App.blockUI({
                target: '#blockui_sample_3_2_element',
                boxed: true
            });
        });

        $('#blockui_sample_3_2_1').click(function() {
            App.unblockUI('#blockui_sample_3_2_element');
        });
    }

    var handleSample4 = function() {

        $('#blockui_sample_4_1').click(function() {
            App.blockUI({
                target: '#blockui_sample_4_portlet_body',
                boxed: true,
                message: 'Processing...'
            });

            window.setTimeout(function() {
                App.unblockUI('#blockui_sample_4_portlet_body');
            }, 2000);
        });

        $('#blockui_sample_4_2').click(function() {
            App.blockUI({
                target: '#blockui_sample_4_portlet_body',
                iconOnly: true
            });

            window.setTimeout(function() {
                App.unblockUI('#blockui_sample_4_portlet_body');
            }, 2000);
        });

        $('#blockui_sample_4_3').click(function() {
            App.blockUI({
                target: '#blockui_sample_4_portlet_body',
                boxed: true,
                textOnly: true
            });

            window.setTimeout(function() {
                App.unblockUI('#blockui_sample_4_portlet_body');
            }, 2000);
        });
    }


    return {
        //main function to initiate the module
        init: function() {

            handleSample1();
            handleSample2();
            handleSample3();
            handleSample4();

        }

    };

}();

jQuery(document).ready(function() {    
   UIBlockUI.init();
});