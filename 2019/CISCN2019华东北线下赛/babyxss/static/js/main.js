$(".article-click").click(function(){
    window.open($(this).attr("src"));
});

$(".a-click").click(function(){
    window.location.href =  $(this).attr("src");
});