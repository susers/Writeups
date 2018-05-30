var ComponentsBootstrapTagsinput = function() {

    var handleDemo1 = function() {
        var elt = $('#object_tagsinput');
        
        elt.tagsinput({
          itemValue: 'value',
          itemText: 'text',
        });

        $('#object_tagsinput_add').on('click', function(){
            elt.tagsinput('add', { 
                "value": $('#object_tagsinput_value').val(), 
                "text": $('#object_tagsinput_city').val(), 
                "continent": $('#object_tagsinput_continent').val()    
            });
        });

        elt.tagsinput('add', { "value": 1 , "text": "Amsterdam"   , "continent": "Europe"    });
        elt.tagsinput('add', { "value": 4 , "text": "Washington"  , "continent": "America"   });
        elt.tagsinput('add', { "value": 7 , "text": "Sydney"      , "continent": "Australia" });
        elt.tagsinput('add', { "value": 10, "text": "Beijing"     , "continent": "Asia"      });
        elt.tagsinput('add', { "value": 13, "text": "Cairo"       , "continent": "Africa"    });
    }

    var handleDemo2 = function() {

        var elt = $('#state_tagsinput');

        elt.tagsinput({
            tagClass: function(item) {
                switch (item.continent) {
                    case 'Europe':
                        return 'label label-primary';
                    case 'America':
                        return 'label label-danger label-important';
                    case 'Australia':
                        return 'label label-success';
                    case 'Africa':
                        return 'label label-default';
                    case 'Asia':
                        return 'label label-warning';
                }
            },
            itemValue: 'value',
            itemText: 'text'
        });

         $('#state_tagsinput_add').on('click', function(){
            elt.tagsinput('add', { 
                "value": $('#state_tagsinput_value').val(), 
                "text": $('#state_tagsinput_city').val(), 
                "continent": $('#state_tagsinput_continent').val()    
            });
        });
        
        elt.tagsinput('add', {
            "value": 1,
            "text": "Amsterdam",
            "continent": "Europe"
        });
        elt.tagsinput('add', {
            "value": 4,
            "text": "Washington",
            "continent": "America"
        });
        elt.tagsinput('add', {
            "value": 7,
            "text": "Sydney",
            "continent": "Australia"
        });
        elt.tagsinput('add', {
            "value": 10,
            "text": "Beijing",
            "continent": "Asia"
        });
        elt.tagsinput('add', {
            "value": 13,
            "text": "Cairo",
            "continent": "Africa"
        });
    }

    return {
        //main function to initiate the module
        init: function() {
            handleDemo1();
            handleDemo2();
        }
    };

}();

jQuery(document).ready(function() {
    ComponentsBootstrapTagsinput.init();
});