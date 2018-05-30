jQuery(document).ready(function() {
  // MORRIS CHARTS DEMOS

  // LINE CHART
  new Morris.Line({
  	// ID of the element in which to draw the chart.
  	element: 'morris_chart_1',
  	// Chart data records -- each entry in this array corresponds to a point on
  	// the chart.
  	data: [
  		{ y: '2006', a: 100, b: 90 },
  	    { y: '2007', a: 75,  b: 65 },
  	    { y: '2008', a: 50,  b: 40 },
  	    { y: '2009', a: 75,  b: 65 },
  	    { y: '2010', a: 50,  b: 40 },
  	    { y: '2011', a: 75,  b: 65 },
  	    { y: '2012', a: 100, b: 90 }
  	],
  	// The name of the data record attribute that contains x-values.
  	xkey: 'y',
  	// A list of names of data record attributes that contain y-values.
  	ykeys: ['a', 'b'],
  	// Labels for the ykeys -- will be displayed when you hover over the
  	// chart.
  	labels: ['Values A', 'Values B']
  });


  // AREA CHART
  new Morris.Area({
    element: 'morris_chart_2',
    data: [
      { y: '2006', a: 100, b: 90 },
      { y: '2007', a: 75,  b: 65 },
      { y: '2008', a: 50,  b: 40 },
      { y: '2009', a: 75,  b: 65 },
      { y: '2010', a: 50,  b: 40 },
      { y: '2011', a: 75,  b: 65 },
      { y: '2012', a: 100, b: 90 }
    ],
    xkey: 'y',
    ykeys: ['a', 'b'],
    labels: ['Series A', 'Series B']
  });


  // BAR CHART
  new Morris.Bar({
    element: 'morris_chart_3',
    data: [
      { y: '2006', a: 100, b: 90 },
      { y: '2007', a: 75,  b: 65 },
      { y: '2008', a: 50,  b: 40 },
      { y: '2009', a: 75,  b: 65 },
      { y: '2010', a: 50,  b: 40 },
      { y: '2011', a: 75,  b: 65 },
      { y: '2012', a: 100, b: 90 }
    ],
    xkey: 'y',
    ykeys: ['a', 'b'],
    labels: ['Series A', 'Series B']
  });


  // PIE CHART
  new Morris.Donut({
    element: 'morris_chart_4',
    data: [
      {label: "Download Sales", value: 12},
      {label: "In-Store Sales", value: 30},
      {label: "Mail-Order Sales", value: 20}
    ]
  });
});
