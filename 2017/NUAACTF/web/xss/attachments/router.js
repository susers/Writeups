var fs = require('fs');
var check = require('./checker').check;

function route(pathname, req, res) {
    var files = ['/main.css', '/main.js'];
    var mimeTypes = {
        css: 'text/css',
        js: 'application/javascript',
    };
    if (files.indexOf(pathname) >= 0) {
        fs.readFile('.' + pathname, function (err, data) {
            if (err) {
                throw err;
            } else {
                var ext = pathname.split('.')[1];
                res.writeHead(200, { 'Content-Type': mimeTypes[ext] });
                res.write(data);
                res.end();
            }
        });
    } else if (/^\/[12]$/.test(pathname)) {
        fs.readFile('./template.html', function (err, data) {
            if (err) {
                throw err;
            } else {
                data = data.toString();
                data = data.replace(/%difficulty%/g, (pathname === '/1') ? 'Easy' : 'Hard');
                data = data.replace(/%text%/g, JSON.stringify(global.problemText[pathname[1] - 1]));
                res.writeHead(200, { 'Content-Type': 'text/html' });
                res.write(data);
                res.end();
            }
        });
    } else if (pathname === '/check') {
        res.writeHead(200, { 'Content-Type': 'text/html' });
        var post = '';
        req.addListener('data', function (chunk) {
            post += chunk;
        });
        req.addListener('end', function () {
            try {
                post = JSON.parse(post);
                if (post.id && post.ans) {
                    check(post.ans, post.id, res);
                } else {
                    res.write('Error');
                    res.end();
                }
            } catch (e) {
                res.write('Error');
                res.end();
            }
        });
    } else {
        res.end('No route for ' + pathname + '.');
    }
}

exports.route = route;
