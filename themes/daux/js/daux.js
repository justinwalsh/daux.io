
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
    var hasFloat = $(document.body).hasClass("with-float")? 3 : 2;
    codeBlockState = (codeBlockState + 1) % hasFloat;
    localStorage.setItem("codeBlockState", codeBlockState);
    setCodeBlockStyle(codeBlockState);
}

function setCodeBlockStyle(x) {
    switch (x) {
        default:
        case 0:
            toggleCodeBlockBtn.html("Show Code Blocks");
            codeBlockView.removeClass('float-view');
            codeBlocks.addClass('hidden');
            break;
        case 1:
            toggleCodeBlockBtn.html("Hide Code Blocks");
            codeBlockView.removeClass('float-view');
            codeBlocks.removeClass('hidden');
            break;
        case 2:
            toggleCodeBlockBtn.html("Show Code Blocks Inline");
            codeBlockView.addClass('float-view');
            codeBlocks.removeClass('hidden');
            break;
    }
}

//Initialize CodeBlock Visibility Settings
$(function () {
    codeBlocks = $('.content-page article > pre');
    toggleCodeBlockBtn = $('#toggleCodeBlockBtn');

    // If there is no code block we hide the link
    if (!codeBlocks.size()) {
        toggleCodeBlockBtn.addClass('hidden');
        return;
    }

    codeBlockView = $('.right-column');
    if (!codeBlockView.size()) return;

    codeBlockState = localStorage.getItem("codeBlockState");
    if (!codeBlockState) {
        codeBlockState = 2;
        localStorage.setItem("codeBlockState", codeBlockState);
    } else codeBlockState = parseInt(codeBlockState);

    setCodeBlockStyle(codeBlockState);
});


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
        if (ribbon.length && a.length) {
            if (a[0] && a[0].scrollHeight > $('.right-column').height()) {
                ribbon[0].style.right = '16px';
            } else {
                ribbon[0].style.right = '';
            }
        }
    }
    $(window).resize(_.debounce(onResize, 100));
    onResize();
});

