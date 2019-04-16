$(function () {
    $("#submit").on("click", function () {
        var fileName = $('#file_name').val();
        var fileContent = $('#file_content').val();
        $.ajax({
            url: "/upload.php",
            method: "POST",
            data: {filename: fileName, filecontent: fileContent},
            success:function (data, status) {
                result = JSON.parse(data);
                var notice = $('<div class="alert m-5" role="alert">' + result["info"] + '</div>');
                if (result["state"]) {
                    notice.addClass("alert-success");
                } else
                    notice.addClass("alert-danger");
                if ($('div.alert')) {
                    $('div.alert').remove();
                }
                $('form.m-5').before(notice);
            }
        });
    });
    }
);