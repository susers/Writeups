function check(input, id, res) {
    var phantom = require('phantom');
    var phInstance = null;
    var exitted = false;
    phantom.create()
        .then(instance => {
            phInstance = instance;
            return instance.createPage();
        })
        .then(page => {
            var index = parseInt(id) - 1;
            // TODO: use page to check input for problem id
            var script = (`
function () {
    var input = ${JSON.stringify(input)};
    var outputStr = '';
    function output(obj) {
        outputStr = JSON.stringify(obj);
    }
    window.onerror = function (a) {
        output({ error: a.toString() });
    }
    window.alert = function (a) {
        if (a === 1) output({ success: 1 });
        else if (a == 1) output({ error: "You should alert *NUMBER* 1." });
        else {
            output({ error: "Server check failed, you need to alert 1." });
        }
    };
// problem text start
${global.problemText[index]}
// problem text end
    try {
        check(input);
    } catch (e) {
        output({ error: e.toString().split("\\n")[0] });
    } finally {
        return outputStr;
    }
}`);
            var evaluation = page.evaluateJavaScript(script);
            evaluation.then(function (html) {
                html = JSON.parse(html);
                if (html.success) {
                    res.write('Check passed, flag: ' + global.flag[index]);
                    res.end();
                } else {
                    res.write(html.error);
                    res.end();
                }
                if (!exitted) {
                    phInstance.exit();
                    exitted = true;
                }
            });
        })
        .catch(error => {
            console.log(error); // eslint-disable-line no-console
            if (!exitted) {
                phInstance.exit();
                exitted = true;
                res.write('PhantomJS error');
                res.end();
            }
        });
    setTimeout(function () {
        if (!exitted) {
            phInstance.exit();
            exitted = true;
            res.write('TLE');
            res.end();
        }
    }, 5000);
}

exports.check = check;
