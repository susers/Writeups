var ChartsFlowchart = function() {

    var handleDemo1 = function() {

    	var flow = '';

    	flow += 'st=>start: Start:>http://keenthemes.com[blank]' + "\n";
		flow += 'e=>end:>http://keenthemes.com' + "\n";
		flow += 'op1=>operation: My Operation' + "\n";
		flow += 'sub1=>subroutine: My Subroutine' + "\n";;
		flow += 'cond=>condition: Yes' + "\n";
		flow += 'or No?:>http://keenthemes.com' + "\n";
		flow += 'io=>inputoutput: catch something...' + "\n";
		flow += 'st->op1->cond' + "\n";
		flow += 'cond(yes)->io->e' + "\n"; 
		flow += 'cond(no)->sub1(right)->op1';

        var diagram = flowchart.parse(flow);

        diagram.drawSVG('diagram_1', {
            'x': 0,
            'y': 0,
            'line-width': 3,
            'line-length': 50,
            'text-margin': 10,
            'font-size': 14,
            'font-color': 'black',
            'line-color': 'black',
            'element-color': 'black',
            'fill': 'white',
            'yes-text': 'yes',
            'no-text': 'no',
            'arrow-end': 'block',
            'scale': 1,
            // style symbol types
            'symbols': {
                'start': {
                    'font-color': 'red',
                    'element-color': 'green',
                    'fill': 'yellow'
                },
                'end': {
                    'class': 'end-element'
                }
            },
            // even flowstate support ;-)
            'flowstate': {
                'past': {
                    'fill': '#CCCCCC',
                    'font-size': 12
                },
                'current': {
                    'fill': 'yellow',
                    'font-color': 'red',
                    'font-weight': 'bold'
                },
                'future': {
                    'fill': '#FFFF99'
                },
                'request': {
                    'fill': 'blue'
                },
                'invalid': {
                    'fill': '#444444'
                },
                'approved': {
                    'fill': '#58C4A3',
                    'font-size': 12,
                    'yes-text': 'APPROVED',
                    'no-text': 'n/a'
                },
                'rejected': {
                    'fill': '#C45879',
                    'font-size': 12,
                    'yes-text': 'n/a',
                    'no-text': 'REJECTED'
                }
            }
        });
    }

    var handleDemo2 = function() {

    	var flow = '';

    	flow += 'st=>start: Start:>http://keenthemes.com[blank]' + "\n";
		flow += 'st=>start: Start|past:>http://keenthemes.com[blank]' + "\n";
		flow += 'e=>end: End|future:>http://keenthemes.com' + "\n";
		flow += 'op1=>operation: My Operation|past' + "\n";
		flow += 'op2=>operation: Stuff|current' + "\n";
		flow += 'sub1=>subroutine: My Subroutine|invalid' + "\n";
		flow += 'cond=>condition: Yes' + "\n";
		flow += 'or No?|approved:>http://keenthemes.com' + "\n";
		flow += 'c2=>condition: Good idea|rejected' + "\n";
		flow += 'io=>inputoutput: catch something...|future' + "\n";
		flow += 'st->op1(right)->cond' + "\n";
		flow += 'cond(yes, right)->c2' + "\n";
		flow += 'cond(no)->sub1(left)->op1' + "\n";
		flow += 'c2(yes)->io->e' + "\n";
		flow += 'c2(no)->op2->e' + "\n";

        var diagram = flowchart.parse(flow);

        diagram.drawSVG('diagram_2', {
            'x': 0,
            'y': 0,
            'line-width': 3,
            'line-length': 50,
            'text-margin': 10,
            'font-size': 14,
            'font-color': 'black',
            'line-color': 'black',
            'element-color': 'black',
            'fill': 'white',
            'yes-text': 'yes',
            'no-text': 'no',
            'arrow-end': 'block',
            'scale': 1,
            // style symbol types
            'symbols': {
                'start': {
                    'font-color': 'red',
                    'element-color': 'green',
                    'fill': 'yellow'
                },
                'end': {
                    'class': 'end-element'
                }
            },
            // even flowstate support ;-)
            'flowstate': {
                'past': {
                    'fill': '#CCCCCC',
                    'font-size': 12
                },
                'current': {
                    'fill': 'yellow',
                    'font-color': 'red',
                    'font-weight': 'bold'
                },
                'future': {
                    'fill': '#FFFF99'
                },
                'request': {
                    'fill': 'blue'
                },
                'invalid': {
                    'fill': '#444444'
                },
                'approved': {
                    'fill': '#58C4A3',
                    'font-size': 12,
                    'yes-text': 'APPROVED',
                    'no-text': 'n/a'
                },
                'rejected': {
                    'fill': '#C45879',
                    'font-size': 12,
                    'yes-text': 'n/a',
                    'no-text': 'REJECTED'
                }
            }
        });
    }

    return {

        init: function() {
            handleDemo1();
            handleDemo2();
        }

    };

}();

jQuery(document).ready(function() {
    ChartsFlowchart.init();
});