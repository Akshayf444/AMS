
$("a > button").on('click', function() { 
    location.href = $(this).closest("a").attr("href");
});