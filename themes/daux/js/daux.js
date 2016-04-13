
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

var codeBlocks, codeBlockView, toggleCodeBlockBtn, toggleCodeSection, codeBlockState;
function toggleCodeBlocks() {
    setCodeBlockStyle(codeBlocks.hasClass('hidden') ? 1 : 0);
}

function setCodeBlockStyle(codeBlockState) {
    localStorage.setItem("codeBlockState", codeBlockState);

    switch (codeBlockState) {
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
    toggleCodeSection = $('#toggleCodeBlock');
    toggleCodeBlockBtn = $('#toggleCodeBlockBtn');

    // If there is no code block we hide the link
    if (!codeBlocks.size()) {
        toggleCodeSection.addClass('hidden');
        return;
    }

    $('#code-hide').click(function() { setCodeBlockStyle(0); });
    $('#code-below').click(function() { setCodeBlockStyle(1); });
    $('#code-float').click(function() { setCodeBlockStyle(2); });

    codeBlockView = $('.right-column');
    if (!codeBlockView.size()) return;

    var floating = $(document.body).hasClass("with-float");

    codeBlockState = localStorage.getItem("codeBlockState");

    if (!codeBlockState) {
        codeBlockState = floating? 2 : 1;
    } else {
        codeBlockState = parseInt(codeBlockState);
    }

    if (!floating && codeBlockState == 2) {
        codeBlockState = 1;
    }

    setCodeBlockStyle(codeBlockState);
});


$(function () {
    // Tree navigation
    $('.aj-nav').click(function (e) {
        e.preventDefault();
        $(this).parent().siblings().find('ul').slideUp();
        $(this).next().slideToggle();
    });

    // New Tree navigation
    $('ul.nav.nav-list > li.has-children > a > .arrow').click(function() {
        $(this).parent().parent().toggleClass('open');
        return false;
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

