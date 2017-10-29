var server = require('./server');
var router = require('./router');

global.problemText = [
    [
        'function check(input) {',
        '    while (input.indexOf(\'alert\') >= 0) {',
        '        input = input.replace(/(alert)+/g, \'\');',
        '    }',
        '    input = \'console.log("\' + input + \'");\';',
        '    var script = document.createElement(\'script\');',
        '    script.innerText = input;',
        '    document.body.appendChild(script);',
        '}',
    ].join('\n'),
    [
        'function check(input) {',
        '    input = input.replace(/[^\\[\\]\\!\\+]+/g, \'\');',
        '    console.log(\'Filtered input: \' + input);',
        '    eval(eval(input) + \'(1)\');',
        '}',
    ].join('\n'),
];

global.flag = [
    'nuaactf{3a5y_xSS_23333_66666}',
    'nuaactf{NOT_the_jsF**k_at_a11}',
];

server.start(router.route);
