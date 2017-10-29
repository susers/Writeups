var http = require('http');
var url = require('url');

var port = 3000;

function start(route) {
    function onRequest(request, response) {
        var pathname = url.parse(request.url).pathname;
        console.log('Request for ' + pathname + ' received.'); // eslint-disable-line no-console
        route(pathname, request, response);
    }
    http.createServer(onRequest).listen(port);
    console.log('Listening at port ' + port + '.'); // eslint-disable-line no-console
}

exports.start = start;
