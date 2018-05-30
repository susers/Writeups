jQuery(document).ready(function() {
  // HIGHMAPS DEMOS

  // MAP BUBBLE
  $.getJSON('http://www.highcharts.com/samples/data/jsonp.php?filename=world-population.json&callback=?', function (data) {

        var mapData = Highcharts.geojson(Highcharts.maps['custom/world']);

        // Correct UK to GB in data
        $.each(data, function () {
            if (this.code === 'UK') {
                this.code = 'GB';
            }
        });

        $('#highmaps_1').highcharts('Map', {
            chart : {
                style: {
		            fontFamily: 'Open Sans'
		        }
            },

            title: {
                text: 'World population 2013 by country'
            },

            subtitle : {
                text : 'Demo of Highcharts map with bubbles'
            },

            legend: {
                enabled: false
            },

            mapNavigation: {
                enabled: true,
                buttonOptions: {
                    verticalAlign: 'bottom'
                }
            },

            series : [{
                name: 'Countries',
                mapData: mapData,
                color: '#E0E0E0',
                enableMouseTracking: false
            }, {
                type: 'mapbubble',
                mapData: mapData,
                name: 'Population 2013',
                joinBy: ['iso-a2', 'code'],
                data: data,
                minSize: 4,
                maxSize: '12%',
                tooltip: {
                    pointFormat: '{point.code}: {point.z} thousands'
                }
            }]
        });

    });

	// HEAT MAP
	$('#highmaps_2').highcharts({

        data: {
            csv: document.getElementById('csv').innerHTML

        },

        chart: {
            type: 'heatmap',
            inverted: true,
            style: {
	            fontFamily: 'Open Sans'
	        }
        },


        title: {
            text: 'Highcharts heat map',
            align: 'left'
        },

        subtitle: {
            text: 'Temperature variation by day and hour through May 2015',
            align: 'left'
        },

        xAxis: {
            tickPixelInterval: 50,
            min: Date.UTC(2015, 4, 1),
            max: Date.UTC(2015, 4, 30)
        },

        yAxis: {
            title: {
                text: null
            },
            labels: {
                format: '{value}:00'
            },
            minPadding: 0,
            maxPadding: 0,
            startOnTick: false,
            endOnTick: false,
            tickPositions: [0, 6, 12, 18, 24],
            tickWidth: 1,
            min: 0,
            max: 23
        },

        colorAxis: {
            stops: [
                [0, '#3060cf'],
                [0.5, '#fffbbc'],
                [0.9, '#c4463a']
            ],
            min: -5
        },

        series: [{
            borderWidth: 0,
            colsize: 24 * 36e5, // one day
            tooltip: {
                headerFormat: 'Temperature<br/>',
                pointFormat: '{point.x:%e %b, %Y} {point.y}:00: <b>{point.value} ℃</b>'
            }
        }]

    });

	// TIMEZONE MAP
	// Instanciate the map
    $('#highmaps_3').highcharts('Map', {
        chart: {
            spacingBottom: 20,
            style: {
	            fontFamily: 'Open Sans'
	        }
        },
        title : {
            text : 'Europe time zones'
        },

        legend: {
            enabled: true
        },

        plotOptions: {
            map: {
                allAreas: false,
                joinBy: ['iso-a2', 'code'],
                dataLabels: {
                    enabled: true,
                    color: 'white',
                    formatter: function () {
                        if (this.point.properties && this.point.properties.labelrank.toString() < 5) {
                            return this.point.properties['iso-a2'];
                        }
                    },
                    format: null,
                    style: {
                        fontWeight: 'bold'
                    }
                },
                mapData: Highcharts.maps['custom/europe'],
                tooltip: {
                    headerFormat: '',
                    pointFormat: '{point.name}: <b>{series.name}</b>'
                }

            }
        },

        series : [{
            name: 'UTC',
            data: $.map(['IE', 'IS', 'GB', 'PT'], function (code) {
                return { code: code };
            })
        }, {
            name: 'UTC + 1',
            data: $.map(['NO', 'SE', 'DK', 'DE', 'NL', 'BE', 'LU', 'ES', 'FR', 'PL', 'CZ', 'AT', 'CH', 'LI', 'SK', 'HU',
                    'SI', 'IT', 'SM', 'HR', 'BA', 'YF', 'ME', 'AL', 'MK'], function (code) {
                return { code: code };
            })
        }, {
            name: 'UTC + 2',
            data: $.map(['FI', 'EE', 'LV', 'LT', 'BY', 'UA', 'MD', 'RO', 'BG', 'GR', 'TR', 'CY'], function (code) {
                return { code: code };
            })
        }, {
            name: 'UTC + 3',
            data: $.map(['RU'], function (code) {
                return { code: code };
            })
        }]
    });

});
