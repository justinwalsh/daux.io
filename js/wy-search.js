// source: http://css-tricks.com/snippets/jquery/make-jquery-contains-case-insensitive/
// Allows non-case-sensitive searching.
// WARNING: changes default case-sensitive behaviour of :contains, so may not be desired
$.expr[":"].contains = $.expr.createPseudo(function(arg) {
    return function( elem ) {
        return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});

$(document).ready(function ()
{
    $("#wy-search").keyup(function (e)
    {
        // Search on every key up event
        searchDoc(e.currentTarget.value);
    });

    function searchDoc (searchStr)
    {
        // Unhighlight all
        $('.wy-hl').removeClass("wy-hl");

        // Close all list (exept parent list)
        var toClose = $('.article-tree ul.nav.nav-list').css("display", "none");
        $(toClose).parent("#sub-nav-collapse").children().css("display", "block");

        // If at least one match is found, highlight and reveal in list
        if (searchStr.length > 0)
        {
            var r2 = $('.article-tree .nav-list a:contains("' + searchStr + '")').addClass("wy-hl");
            $(r2).parentsUntil("ul.nav nav-list").css("display", "block");
        }
    }
});