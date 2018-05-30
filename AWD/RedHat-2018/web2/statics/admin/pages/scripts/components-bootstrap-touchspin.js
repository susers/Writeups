var ComponentsBootstrapTouchSpin = function() {

    var handleDemo = function() {

        $("#touchspin_1").TouchSpin({
            min: 0,
            max: 100,
            step: 0.1,
            decimals: 2,
            boostat: 5,
            maxboostedstep: 10,
            postfix: '%'
        });

        $("#touchspin_2").TouchSpin({
            min: -1000000000,
            max: 1000000000,
            stepinterval: 50,
            maxboostedstep: 10000000,
            prefix: '$'
        });

        $("#touchspin_3").TouchSpin({
            verticalbuttons: true
        });

        $("#touchspin_4").TouchSpin({
            verticalbuttons: true,
            verticalupclass: 'glyphicon glyphicon-plus',
            verticaldownclass: 'glyphicon glyphicon-minus'
        });

        $("#touchspin_5").TouchSpin();

        $("#touchspin_6").TouchSpin({
            initval: 40
        });

        $("#touchspin_7").TouchSpin({
            initval: 40
        });

        $("#touchspin_8").TouchSpin({
            postfix: "a button",
            postfix_extraclass: "btn red"
        });

        $("#touchspin_9").TouchSpin({
            postfix: "a button",
            postfix_extraclass: "btn green"
        });

        $("#touchspin_10").TouchSpin({
            prefix: "pre",
            postfix: "post"
        });

        $("#touchspin_11").TouchSpin({
            buttondown_class: "btn blue",
            buttonup_class: "btn red"
        });

    }

    return {
        //main function to initiate the module
        init: function() {
            handleDemo();
        }
    };

}();

jQuery(document).ready(function() {
    ComponentsBootstrapTouchSpin.init();
});