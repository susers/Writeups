var ComponentsTypeahead = function () {

    var handleTwitterTypeahead = function() {

        // Example #1
        // instantiate the bloodhound suggestion engine
        var numbers = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.num); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          local: [
            { num: 'metronic' },
            { num: 'keenthemes' },
            { num: 'metronic theme' },
            { num: 'metronic template' },
            { num: 'keenthemes team' }
          ]
        });
         
        // initialize the bloodhound suggestion engine
        numbers.initialize();
         
        // instantiate the typeahead UI
        if (App.isRTL()) {
          $('#typeahead_example_1').attr("dir", "rtl");  
        }
        $('#typeahead_example_1').typeahead(null, {
          displayKey: 'num',
          hint: (App.isRTL() ? false : true),
          source: numbers.ttAdapter()
        });

        // Example #2
        var countries = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.name); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          limit: 10,
          prefetch: {
            url: '../demo/typeahead_countries.json',
            filter: function(list) {
              return $.map(list, function(country) { return { name: country }; });
            }
          }
        });
 
        countries.initialize();
         
        if (App.isRTL()) {
          $('#typeahead_example_2').attr("dir", "rtl");  
        } 
        $('#typeahead_example_2').typeahead(null, {
          name: 'typeahead_example_2',
          displayKey: 'name',
          hint: (App.isRTL() ? false : true),
          source: countries.ttAdapter()
        });

        // Example #3
        var custom = new Bloodhound({
          datumTokenizer: function(d) { return d.tokens; },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          remote: '../demo/typeahead_custom.php?query=%QUERY'
        });
         
        custom.initialize();
         
        if (App.isRTL()) {
          $('#typeahead_example_3').attr("dir", "rtl");  
        }  
        $('#typeahead_example_3').typeahead(null, {
          name: 'datypeahead_example_3',
          displayKey: 'value',
          source: custom.ttAdapter(),
          hint: (App.isRTL() ? false : true),
          templates: {
            suggestion: Handlebars.compile([
              '<div class="media">',
                    '<div class="pull-left">',
                        '<div class="media-object">',
                            '<img src="{{img}}" width="50" height="50"/>',
                        '</div>',
                    '</div>',
                    '<div class="media-body">',
                        '<h4 class="media-heading">{{value}}</h4>',
                        '<p>{{desc}}</p>',
                    '</div>',
              '</div>',
            ].join(''))
          }
        });

        // Example #4

        var nba = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.team); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          prefetch: '../demo/typeahead_nba.json'
        });
         
        var nhl = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.team); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          prefetch: '../demo/typeahead_nhl.json'
        });
         
        nba.initialize();
        nhl.initialize();
         
        if (App.isRTL()) {
          $('#typeahead_example_4').attr("dir", "rtl");  
        }
        $('#typeahead_example_4').typeahead({
          hint: (App.isRTL() ? false : true),
          highlight: true
        },
        {
          name: 'nba',
          displayKey: 'team',
          source: nba.ttAdapter(),
          templates: {
                header: '<h3>NBA Teams</h3>'
          }
        },
        {
          name: 'nhl',
          displayKey: 'team',
          source: nhl.ttAdapter(),
          templates: {
                header: '<h3>NHL Teams</h3>'
          }
        });

    }

    var handleTwitterTypeaheadModal = function() {

        // Example #1
        // instantiate the bloodhound suggestion engine
        var numbers = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.num); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          local: [
            { num: 'metronic' },
            { num: 'keenthemes' },
            { num: 'metronic theme' },
            { num: 'metronic template' },
            { num: 'keenthemes team' }
          ]
        });
         
        // initialize the bloodhound suggestion engine
        numbers.initialize();
         
        // instantiate the typeahead UI
        if (App.isRTL()) {
          $('#typeahead_example_modal_1').attr("dir", "rtl");  
        }
        $('#typeahead_example_modal_1').typeahead(null, {
          displayKey: 'num',
          hint: (App.isRTL() ? false : true),
          source: numbers.ttAdapter()
        });

        // Example #2
        var countries = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.name); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          limit: 10,
          prefetch: {
            url: '../demo/typeahead_countries.json',
            filter: function(list) {
              return $.map(list, function(country) { return { name: country }; });
            }
          }
        });
 
        countries.initialize();
         
        if (App.isRTL()) {
          $('#typeahead_example_modal_2').attr("dir", "rtl");  
        }
        $('#typeahead_example_modal_2').typeahead(null, {
          name: 'typeahead_example_modal_2',
          displayKey: 'name',
          hint: (App.isRTL() ? false : true),
          source: countries.ttAdapter()
        });

        // Example #3
        var custom = new Bloodhound({
          datumTokenizer: function(d) { return d.tokens; },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          remote: '../demo/typeahead_custom.php?query=%QUERY'
        });
         
        custom.initialize();
         
        if (App.isRTL()) {
          $('#typeahead_example_modal_3').attr("dir", "rtl");  
        }
        $('#typeahead_example_modal_3').typeahead(null, {
          name: 'datypeahead_example_modal_3',
          displayKey: 'value',
          hint: (App.isRTL() ? false : true),
          source: custom.ttAdapter(),
          templates: {
            suggestion: Handlebars.compile([
              '<div class="media">',
                    '<div class="pull-left">',
                        '<div class="media-object">',
                            '<img src="{{img}}" width="50" height="50"/>',
                        '</div>',
                    '</div>',
                    '<div class="media-body">',
                        '<h4 class="media-heading">{{value}}</h4>',
                        '<p>{{desc}}</p>',
                    '</div>',
              '</div>',
            ].join(''))
          }
        });

        // Example #4

        var nba = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.team); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          limit: 3,
          prefetch: '../demo/typeahead_nba.json'
        });
         
        var nhl = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.team); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          limit: 3,
          prefetch: '../demo/typeahead_nhl.json'
        });
         
        nba.initialize();
        nhl.initialize();
         
        $('#typeahead_example_modal_4').typeahead({
            hint: (App.isRTL() ? false : true),
            highlight: true
        },
        {
          name: 'nba',
          displayKey: 'team',
          source: nba.ttAdapter(),
          templates: {
                header: '<h3>NBA Teams</h3>'
          }
        },
        {
          name: 'nhl',
          displayKey: 'team',
          source: nhl.ttAdapter(),
          templates: {
                header: '<h3>NHL Teams</h3>'
          }
        });

    }

    return {
        //main function to initiate the module
        init: function () {
            handleTwitterTypeahead();
            handleTwitterTypeaheadModal();
        }
    };

}();

jQuery(document).ready(function() {    
   ComponentsTypeahead.init(); 
});