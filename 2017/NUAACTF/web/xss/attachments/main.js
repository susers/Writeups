window.problemText = decodeURIComponent('%3Cscript%3E') + '\n' + window.problemText + '\n' + decodeURIComponent('%3C%2Fscript%3E');
var tab = document.getElementById('tab');
var textarea = document.getElementById('textarea');
var problem = document.getElementById('problem');
var output = document.getElementById('output');
var iframe = document.getElementById('iframe');
var script = '%3Cscript%3E' + encodeURIComponent('window.onerror=function(a){parent.postMessage({error:a.toString()},"*")};window.console=window.console||{};window.console.log=function(a){parent.postMessage({console:a},"*")};window.alert=function(a){if(a===1)parent.postMessage({success:1},"*");else if(a==1)parent.postMessage({warning:"You should alert *NUMBER* 1."},"*");else{parent.postMessage({warning:"You need to alert 1."},"*")}};window.onmessage=function(a){try{check(a.data)}catch(e){parent.postMessage({error:e.toString().split("\\n")[0]},"*")}}') + '%3C%2Fscript%3E';
function localCheck() {
    problem.innerText = window.problemText;
    iframe.src = 'data:text/html,' + encodeURIComponent(window.problemText.replace(/\n\s*/g, '')) + script;
    iframe.onload = function () {
        this.contentWindow.postMessage(textarea.value, '*');
    };
}
localCheck();
window.onmessage = function (e) {
    var d = e.data;
    console.log(d);
    if (d.success !== undefined) {
        tab.className = 'rs-tab rs-tab-success';
        tab.innerText = 'Local check passed, running server check...';
        // server check
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    tab.innerText = 'Server response: \'' + xhr.responseText + '\'.';
                }
            }
        };
        xhr.open('POST', '/check', true);
        xhr.send(JSON.stringify({
            id: location.pathname.match(/^\/(\d+)$/)[1],
            ans: textarea.value,
        }));
    } else if (d.warning !== undefined) {
        tab.className = 'rs-tab rs-tab-warning';
        tab.innerText = d.warning;
    } else if (d.error !== undefined) {
        tab.className = 'rs-tab rs-tab-danger';
        tab.innerText = d.error;
        output.innerText = '';
    } else if (d.console !== undefined) {
        tab.className = 'rs-tab rs-tab-warning';
        tab.innerText = 'Not the answer yet...';
        output.innerText = d.console;
    }
};
