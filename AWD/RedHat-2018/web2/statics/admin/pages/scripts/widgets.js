var Widgets = function () {

	var handleMapplic = function () {
		$('#mapplic').mapplic({
			source: '../../assets/global/plugins/mapplic/world.json',
			height: 265,
			animate: false,
			sidebar: false,
			minimap: false,
            locations: true,
			deeplinking: true,
			fullscreen: false,
			hovertip: true,
			zoombuttons: false,
			clearbutton: false,
			developer: false,
			maxscale: 2,
			skin: 'mapplic-dark',
			zoom: true
		});
	}

	var handleSparklineChart = function () {
		$("#widget_sparkline_bar").sparkline([8, 7, 9, 8.5, 8, 8.2, 8, 8.5, 9, 8, 9], {
            type: 'bar',
            width: '100',
            barWidth: 5,
            height: '30',
            barColor: '#4db3a4',
            negBarColor: '#e02222'
        });
        $("#widget_sparkline_bar2").sparkline([8, 7, 9, 8.5, 8, 8.2, 8, 8.5, 9, 8, 9], {
            type: 'bar',
            width: '100',
            barWidth: 5,
            height: '30',
            barColor: '#f36a5a',
            negBarColor: '#e02222'
        });
        $("#widget_sparkline_bar3").sparkline([8, 7, 9, 8.5, 8, 8.2, 8, 8.5, 9, 8, 9], {
            type: 'bar',
            width: '100',
            barWidth: 5,
            height: '30',
            barColor: '#5b9bd1',
            negBarColor: '#e02222'
        });
        $("#widget_sparkline_bar4").sparkline([8, 7, 9, 8.5, 8, 8.2, 8, 8.5, 9, 8, 9], {
            type: 'bar',
            width: '100',
            barWidth: 5,
            height: '30',
            barColor: '#9a7caf',
            negBarColor: '#e02222'
        });
	}
    
    var handleCounter = function () {
        $('.counter').counterUp({
            delay: 10,
            time: 1000
        });
    }

	return {

        // main function
        init: function () {
            handleMapplic(); // handle mapplic map
            handleSparklineChart(); // handle sparkline chart
            handleCounter(); // handle counter
        }
        
    };
}();

jQuery(document).ready(function() {    
   Widgets.init();
});