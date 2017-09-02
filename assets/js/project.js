/*
 * app.js
 * by louie
 * 2017-05-10 1.0.0
 */
var html = $('html'), body = $('body'), wrapper = $('#wrapper');


// 修改滚动条
function hide_scroll() {
    html.css('overflow-y', 'hidden');
    body.addClass('fix');
    fixbar = $('#fixedbar');
    if ( fixbar.hasClass('fix') ) {
        fixbar.addClass('fixed');
    }
}

function show_scroll() {
    html.css('overflow-y', 'auto');
    body.removeClass('fix');
    fixbar = $('#fixedbar');
    if ( fixbar.hasClass('fix') ) {
        fixbar.removeClass('fixed');
    }
}

/*================================
 Overlay add & remove
================================*/
function overlay_add(name) {
    tag = '<section id="overlay" class="'+ name +'"><div id="modal"></div></section>';
    if ( !$( '#overlay.'+ name )[0] ) {
        $('body').append(tag);
    }
}

function overlay_remove(name) {
    $('.'+name).click(function(e) {
        if (e.target.id == 'overlay') {
            $(this).slideUp(200, function() {
                $(this).remove();
            });

            show_scroll();
        }
    });
}

function overlay_disappear(name) {
    $('#overlay.' + name).slideUp(200, function() {
        $('#overlay.' + name).remove();
    });

    show_scroll();
}

/*================================
 Get post content
================================*/
function set_obj(tag, action, execute) {
    id = tag.data('id'),
    title = tag.data('title'),
    url = tag.data('url'),
    target = tag.attr('id');
    execute = execute ? execute : false;
    get_post_data(id, title, url, action, target, execute);
}

function get_action(type) {
    if ( type == 'post' ) action = 'ajax_content_post';
    if ( type == 'page' ) action = 'ajax_page_post';
    return action;
}

function get_post_data(id, title, url, action, target, execute) {
    link = '<a href="'+ url +'" class="full-link">查看完整内容</a>';
    className = 'jspost';
    $.ajax({
        type: 'POST',
        data: {
            action: get_action(action),
            id: id,
        },
        dataType:'html',
        timeout : 6000,
        beforeSend:function() {
            overlay_add(className);
            modal = $('#overlay #modal');
            loading_start(modal);
            //NProgress.start();
        },
        error:function(request, status, err) {
            if ( status == 'timeout' ) {
                alert("服务器没有响应");
                location.replace(url); // 延迟6秒后自动重载
            }
            overlay_disappear(className);
            //NProgress.done();
        },
        success:function(data) {
            hide_scroll();
            modal.html(data);
            NProgress.done();
            modal.find("#pagination").remove();
            modal.append(link);

            if ($('#main.plain')[0]) {
                $('#overlay #comments').remove();
            }

            if ( execute ) execute();
            
            window.addEventListener('popstate', function(e) {
                overlay_remove(className);
            },false);

            $('.full-link, .post-inser a, .tags a').click( function() {
               overlay_disappear(className);
            });

            overlay_remove(className);
        }
    }); // end ajax

    return;
}

/*================================
 Window tips
================================*/
function tips_add(title) {
    on = $('.no-tips');
    if ( !on[0] ) return false;

    obj = $('#notification');
    if ( obj[0] ) return false;

    title = '<span class="icon">&#xe6eb;</span>'+title;

    tag = '<div id="notification" class="js-msg">';
    tag += '<div class="title">'+ title +'</div>';
    tag += '<div class="info"></div>';
    tag += '<!-- #notification #--></div>';

    $('body').append(tag);
}

function tips_remove() {
    win = $('#notification')[0];
    if (win) win.remove();
}

function tips_update(content) {
    tips_add('新消息&nbsp;&nbsp;&nbsp;');
    win = $('#notification');
    info = $('#notification .info');
    if ( info.hasClass('has') ) {
        clearTimeout(close);
        info.removeClass('has');
    }

    msg = '<span class="msg">'+ content +'</span>';
    info.html(msg);
    info.addClass('has');
    win.show();

    close = setTimeout(function() {
        win.slideUp(300, function() {
            tips_remove();
        });
    }, 6000);
}


/*================================
 Loading
================================*/
function loading_template() {
    tag = '<div class="loader">';
    tag += '<div class="circle"></div>';
    tag += '<div class="circle"></div>';
    tag += '<div class="circle"></div>';
    tag += '<div class="circle"></div>';
    tag += '<div class="circle"></div>';
    tag += '</div>';

    return tag;
}

function loading_start(target) {
    target.append( loading_template() );
}

function loading_done(target) {
    target.children('.loader').remove();
}
