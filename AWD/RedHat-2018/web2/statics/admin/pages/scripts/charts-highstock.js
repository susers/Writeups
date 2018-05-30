jQuery(document).ready(function() {
  // HIGHSTOCK DEMOS

  // COMPARE MULTIPLE SERIES
  	var seriesOptions = [],
        seriesCounter = 0,
        names = ['MSFT', 'AAPL', 'GOOG'],
        // create the chart when all data is loaded
        createChart = function () {

            $('#highstock_1').highcharts('StockChart', {
                chart : {
                    style: {
                        fontFamily: 'Open Sans'
                    }
                },

                rangeSelector: {
                    selected: 4
                },

                yAxis: {
                    labels: {
                        formatter: function () {
                            return (this.value > 0 ? ' + ' : '') + this.value + '%';
                        }
                    },
                    plotLines: [{
                        value: 0,
                        width: 2,
                        color: 'silver'
                    }]
                },

                plotOptions: {
                    series: {
                        compare: 'percent'
                    }
                },

                tooltip: {
                    pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.change}%)<br/>',
                    valueDecimals: 2
                },

                series: seriesOptions
            });
        };

    $.each(names, function (i, name) {

        $.getJSON('http://www.highcharts.com/samples/data/jsonp.php?filename=' + name.toLowerCase() + '-c.json&callback=?',    function (data) {

            seriesOptions[i] = {
                name: name,
                data: data
            };

            // As we're loading the data asynchronously, we don't know what order it will arrive. So
            // we keep a counter and create the chart when all the data is loaded.
            seriesCounter += 1;

            if (seriesCounter === names.length) {
                createChart();
            }
        });
    });

    // CANDLESTICK CHART
    $.getJSON('http://www.highcharts.com/samples/data/jsonp.php?filename=aapl-ohlcv.json&callback=?', function (data) {

        // split the data set into ohlc and volume
        var ohlc = [],
            volume = [],
            dataLength = data.length,
            // set the allowed units for data grouping
            groupingUnits = [[
                'week',                         // unit name
                [1]                             // allowed multiples
            ], [
                'month',
                [1, 2, 3, 4, 6]
            ]],

            i = 0;

        for (i; i < dataLength; i += 1) {
            ohlc.push([
                data[i][0], // the date
                data[i][1], // open
                data[i][2], // high
                data[i][3], // low
                data[i][4] // close
            ]);

            volume.push([
                data[i][0], // the date
                data[i][5] // the volume
            ]);
        }


        // create the chart
        $('#highstock_2').highcharts('StockChart', {
            chart : {
                style: {
                    fontFamily: 'Open Sans'
                }
            },

            rangeSelector: {
                selected: 1
            },

            title: {
                text: 'AAPL Historical'
            },

            yAxis: [{
                labels: {
                    align: 'right',
                    x: -3
                },
                title: {
                    text: 'OHLC'
                },
                height: '60%',
                lineWidth: 2
            }, {
                labels: {
                    align: 'right',
                    x: -3
                },
                title: {
                    text: 'Volume'
                },
                top: '65%',
                height: '35%',
                offset: 0,
                lineWidth: 2
            }],

            series: [{
                type: 'candlestick',
                name: 'AAPL',
                data: ohlc,
                dataGrouping: {
                    units: groupingUnits
                }
            }, {
                type: 'column',
                name: 'Volume',
                data: volume,
                yAxis: 1,
                dataGrouping: {
                    units: groupingUnits
                }
            }]
        });
    });

	// OHLC CHART
	 $.getJSON('http://www.highcharts.com/samples/data/jsonp.php?filename=aapl-ohlc.json&callback=?', function (data) {

        // create the chart
        $('#highstock_3').highcharts('StockChart', {
            chart : {
                style: {
                    fontFamily: 'Open Sans'
                }
            },

            rangeSelector : {
                selected : 2
            },

            title : {
                text : 'AAPL Stock Price'
            },

            series : [{
                type : 'ohlc',
                name : 'AAPL Stock Price',
                data : data,
                dataGrouping : {
                    units : [[
                        'week', // unit name
                        [1] // allowed multiples
                    ], [
                        'month',
                        [1, 2, 3, 4, 6]
                    ]]
                }
            }]
        });
    });

	// LINE CHART WITH FLAGS
	$.getJSON('http://www.highcharts.com/samples/data/jsonp.php?filename=usdeur.json&callback=?', function (data) {

        var year = new Date(data[data.length - 1][0]).getFullYear(); // Get year of last data point

        // Create the chart
        $('#highstock_4').highcharts('StockChart', {
            chart : {
                style: {
                    fontFamily: 'Open Sans'
                }
            },

            rangeSelector: {
                selected: 1
            },

            title: {
                text: 'USD to EUR exchange rate'
            },

            yAxis: {
                title: {
                    text: 'Exchange rate'
                }
            },

            series: [{
                name: 'USD to EUR',
                data: data,
                id: 'dataseries',
                tooltip: {
                    valueDecimals: 4
                }
            }, {
                type: 'flags',
                data: [{
                    x: Date.UTC(year, 1, 22),
                    title: 'A',
                    text: 'Shape: "squarepin"'
                }, {
                    x: Date.UTC(year, 3, 28),
                    title: 'A',
                    text: 'Shape: "squarepin"'
                }],
                onSeries: 'dataseries',
                shape: 'squarepin',
                width: 16
            }, {
                type: 'flags',
                data: [{
                    x: Date.UTC(year, 2, 1),
                    title: 'B',
                    text: 'Shape: "circlepin"'
                }, {
                    x: Date.UTC(year, 3, 1),
                    title: 'B',
                    text: 'Shape: "circlepin"'
                }],
                shape: 'circlepin',
                width: 16
            }, {
                type: 'flags',
                data: [{
                    x: Date.UTC(year, 2, 10),
                    title: 'C',
                    text: 'Shape: "flag"'
                }, {
                    x: Date.UTC(year, 3, 11),
                    title: 'C',
                    text: 'Shape: "flag"'
                }],
                color: Highcharts.getOptions().colors[0], // same as onSeries
                fillColor: Highcharts.getOptions().colors[0],
                onSeries: 'dataseries',
                width: 16,
                style: { // text style
                    color: 'white'
                },
                states: {
                    hover: {
                        fillColor: '#395C84' // darker
                    }
                }
            }]
        });
    });

});