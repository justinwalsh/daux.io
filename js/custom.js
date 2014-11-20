$(function () {
    $('.aj-nav').click(function (e) {
        e.preventDefault();
        $(this).parent().siblings().find('ul').slideUp();
        $(this).next().slideToggle();
    });

    $('table').addClass('table');
    $('#menu-spinner-button').click(function () {
        $('#sub-nav-collapse').slideToggle();
    });

    $(window).resize(function () {
        // Remove transition inline style on large screens
        if ($(window).width() >= 768)
            $('#sub-nav-collapse').removeAttr('style');
    });
});

//Fix GitHub Ribbon overlapping Scrollbar
var t = $('#github-ribbon');
var a = $('article');
if (t[0] && a[0] && a[0].scrollHeight > $('.right-column').height()) t[0].style.right = '16px';

function setCodeBlockStyle(x) {
    switch (x) {
        default:
        case 0:
            toggleCodeBlockBtn.innerHTML = "Show Code Blocks Inline";
            codeBlockView.addClass('float-view');
            codeBlocks.removeClass('hidden');
            break;
        case 1:
            toggleCodeBlockBtn.innerHTML = "Hide Code Blocks";
            codeBlockView.removeClass('float-view');
            codeBlocks.removeClass('hidden');
            break;
        case 2:
            toggleCodeBlockBtn.innerHTML = "Show Code Blocks";
            codeBlockView.removeClass('float-view');
            codeBlocks.addClass('hidden');          
            break;
    }
}

function toggleCodeBlocks() {
    codeBlockState = (codeBlockState + 1) % 3;
    localStorage.setItem("codeBlockState", codeBlockState);
    setCodeBlockStyle(codeBlockState);
}

//Initialize CodeBlock Visibility Settings
$(function () {
    toggleCodeBlockBtn = $('#toggleCodeBlockBtn')[0];
    codeBlockView = $('.right-column');
    codeBlocks = $('.content-page article > pre');
    codeBlockState = localStorage.getItem("codeBlockState");
    if (!codeBlockState) {
        codeBlockState = 0;
        localStorage.setItem("codeBlockState", codeBlockState);
    } else codeBlockState = parseInt(codeBlockState);
    if (!codeBlockView.size()) return;
    if (!codeBlocks.size()) {
        codeBlockState = 2;
        toggleCodeBlockBtn.classList.add('hidden');
    }
    setCodeBlockStyle(codeBlockState);
});