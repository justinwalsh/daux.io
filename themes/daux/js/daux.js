
_ = {};

_.now = Date.now || function() {
    return new Date().getTime();
};

_.debounce = function(func, wait, immediate) {
    var timeout, args, context, timestamp, result;

    var later = function() {
        var last = _.now() - timestamp;

        if (last < wait && last >= 0) {
            timeout = setTimeout(later, wait - last);
        } else {
            timeout = null;
            if (!immediate) {
                result = func.apply(context, args);
                if (!timeout) context = args = null;
            }
        }
    };

    return function() {
        context = this;
        args = arguments;
        timestamp = _.now();
        var callNow = immediate && !timeout;
        if (!timeout) timeout = setTimeout(later, wait);
        if (callNow) {
            result = func.apply(context, args);
            context = args = null;
        }

        return result;
    };
};

var codeBlocks, codeBlockView, toggleCodeBlockBtn, codeBlockState;
function toggleCodeBlocks() {
    codeBlockState = (codeBlockState + 1) % 3;
    localStorage.setItem("codeBlockState", codeBlockState);
    setCodeBlockStyle(codeBlockState);
}

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

$(function () {
    // Tree navigation
    $('.aj-nav').click(function (e) {
        e.preventDefault();
        $(this).parent().siblings().find('ul').slideUp();
        $(this).next().slideToggle();
    });

    // Responsive navigation
    $('#menu-spinner-button').click(function () {
        $('#sub-nav-collapse').slideToggle();
    });

    //Github ribbon placement
    var ribbon = $('#github-ribbon');
    function onResize() {
        //Fix GitHub Ribbon overlapping Scrollbar
        var a = $('article');
        if (ribbon[0] && a[0] && a[0].scrollHeight > $('.right-column').height()) {
            ribbon[0].style.right = '16px';
        } else {
            ribbon[0].style.right = '';
        }
    }
    $(window).resize(_.debounce(onResize, 100));
    onResize();

    //Initialize CodeBlock Visibility Settings
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
