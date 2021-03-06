<?php
/**
 * theme base function file
 * @package Louie
 * @since Theme version 1.0.0
 */

/**
 * 添加功能
 */
add_theme_support( 'post-thumbnails' );
add_action( 'after_setup_theme', 'theme_setup' );
add_filter( 'pre_option_link_manager_enabled', '__return_true' );
function theme_setup() {
    register_nav_menu( 'top', '顶部菜单' );
    register_nav_menu( 'main', '导航菜单' );
    register_nav_menu( 'mobile', '移动端菜单' );
    //add_theme_support( 'post-formats', array('status', 'video') );
}
add_filter( 'upload_mimes', 'add_upload_mimes' );
function add_upload_mimes( $mimes = array() ) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'widget_text', 'execute_php', 100 );
function execute_php( $html ){
     if( strpos( $html, "<"."?php" ) !== false ) {
          ob_start();
          eval( "?".">".$html );
          $html = ob_get_contents();
          ob_end_clean();
     }
     return $html;
}


/**
 * 移除功能
 */
add_action( 'init', 'disable_emojis' );
add_action( 'widgets_init', 'unregister_default_widgets', 11 );
function unregister_default_widgets() {
	unregister_widget( 'WP_Widget_Pages' );
	unregister_widget( 'WP_Widget_Calendar' );
	unregister_widget( 'WP_Widget_Archives' );
	unregister_widget( 'WP_Widget_Links' );
	unregister_widget( 'WP_Widget_Meta' );
	unregister_widget( 'WP_Widget_Search' );
	unregister_widget( 'WP_Widget_Categories' );
	unregister_widget( 'WP_Widget_Recent_Posts' );
	unregister_widget( 'WP_Widget_Recent_Comments' );
	unregister_widget( 'WP_Widget_RSS' );
    unregister_widget( 'WP_Widget_Text' );
	unregister_widget( 'WP_Widget_Tag_Cloud' );
	unregister_widget( 'WP_Nav_Menu_Widget' );
}
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	//add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
remove_action( 'wp_head', 'wp_generator');
remove_action( 'wp_head', 'wlwmanifest_link');
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'parent_post_rel_link' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );

show_admin_bar( false );
remove_filter( 'term_description', 'wp_kses_data' );
remove_filter( 'pre_term_description', 'wp_filter_kses' );

/**
 * 优化站点标题
 */
add_filter( 'wp_title', 'site_title', 10, 2 );
function site_title( $title, $sep ) {

    global $paged, $page, $wp_query,$post;

    if ( is_feed() || $post->post_type == 'reads')
        return $post->post_title ;

    $title .= get_bloginfo( 'name', 'display' );

    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) )
        $title = "$title $sep $site_description";

    if ( is_search() )
        $title = get_search_query()."的搜索結果";

    if ( $paged >= 2 || $page >= 2 )
        $title = "第" .max( $paged, $page ) ."页 ". $sep . " " . $title;

    return $title;
}

/**
 * 注册小工具
 */
add_action( 'widgets_init', 'widgets_init' );
function widgets_init() {
	register_sidebar(array(
        'name'          => '左边栏',
        'id'            => 'sidebar-1',
        'description'   => '全局显示',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

	register_sidebar(array(
        'name'          => '右边栏',
        'id'            => 'sidebar-2',
        'description'   => '局部显示',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => '内页边栏',
        'id'            => 'sidebar-3',
        'description'   => '局部显示',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}

/**
 * 优化头像源
 */
add_filter( 'get_avatar', 'get_ssl_avatar' );
function get_ssl_avatar( $avatar ) {
    if (preg_match_all(
        '/(src|srcset)=["\']https?.*?\/avatar\/([^?]*)\?s=([\d]+)&([^"\']*)?["\']/i',
        $avatar,
        $matches
    ) > 0) {
        $url = 'https://secure.gravatar.com';
        $size = $matches[3][0];
        $vargs = array_pad(array(), count($matches[0]), array());
        for ( $i = 1; $i < count($matches); $i++ ) {
            for ( $j = 0; $j < count($matches[$i]); $j++ ) {
                $tmp = strtolower($matches[$i][$j]);
                $vargs[$j][] = $tmp;
                if ( $tmp == 'src' ) {
                    $size = $matches[3][$j];
                }
            }
        }
        $buffers = array();
        foreach ( $vargs as $varg ) {
            $buffers[] = vsprintf(
                '%s="%s/avatar/%s?s=%s&%s"',
                array($varg[0], $url, $varg[1], $varg[2], $varg[3])
           );
        }
        return sprintf(
                '<img alt="avatar" %s class="avatar avatar-%s" height="%s" width="%s" />',
                implode(' ', $buffers), $size, $size, $size
            );
    }
    else {
        return false;
    }
}

/**
 * 文章摘要
 */
//add_filter( 'excerpt_more', 'changes_post_excerpt_more' );
//add_filter( 'excerpt_length', 'changes_post_excerpt_length', 999 );
function changes_post_excerpt_length( $length ) {
    $length = object( 'excerpt_length' );
    return $length;
}
function changes_post_excerpt_more( $more ) {
    $excerpt = ' ...';
    $thumb = object( 'excerpt_thumb' );
    
    if ( $thumb == 'feature' ) {
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'large' )[0];
    }
    elseif ( $thumb == 'illustration' ) {
        $image = get_post_image( get_the_ID() );
    }

    if ( preview() ) {
        $excerpt .= $image ? '<img src="'. $image .'" class="entry-image" itemprop="image">' : '';
    }
    else {
        $excerpt .= $image ? '<a href="'. esc_url( get_permalink() ) .'" class="entry-image"><img src="'. $image .'" class="entry-image" itemprop="image"></a>' : '';
    }
    
    return $excerpt;
}
function post_excerpt( $post = false, $excerpt_length = 233 ) {
    $excerpt_length = object( 'excerpt_length' );
    $thumb = object( 'excerpt_thumb' );
    if ( $thumb == 'feature' ) {
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'large' )[0];
    }
    elseif ( $thumb == 'illustration' ) {
        $image = get_post_image( get_the_ID() );
    }

    if ( preview() ) {
        $post_img .= $image ? '<img src="'. $image .'" class="entry-image" itemprop="image">' : '';
    }
    else {
        $post_img .= $image ? '<a href="'. esc_url( get_permalink() ) .'" class="entry-image"><img src="'. $image .'" class="entry-image" itemprop="image"></a>' : '';
    }

    if( ! $post ) $post = get_post();
    $post_excerpt = $post->post_excerpt;
    if( $post_excerpt == '' ) {
        $post_content = $post->post_content;
        $post_content = do_shortcode( $post_content );
        $post_content = wp_strip_all_tags( $post_content );
	if(post_password_required()){
	$post_excerpt = '竟然要密码才能访问，这里面会有什(jiao)么(yi)？ ≖‿≖✧';
	}
	else {
        $post_excerpt = mb_strimwidth( $post_content, 0, $excerpt_length, ' ...', 'utf-8' );
	}
    }

    $post_excerpt = wp_strip_all_tags( $post_excerpt );
    $post_excerpt = trim( preg_replace( "/[\n\r\t ]+/", ' ', $post_excerpt ), ' ' );

    echo $post_excerpt.$post_img;
}

/**
 * 日期格式
 */
add_filter( 'the_time', 'post_time' );
function post_time() {
	global $post;
	$date = $post->post_date;
	$time = get_post_time( 'G', true, $post );
	$time_diff = time() - $time;
	if ( $time_diff > 0 && $time_diff < 30 * 7 * 24 * 60 * 60 )
		$display = sprintf( __('%s前'), human_time_diff( $time ) );
	else
		$display = date( get_option( 'date_format' ) * 3600 , strtotime( $date ) );

	if(is_single()) 
		return get_post_time( 'Ag:i Y-m-d' );
	
	return $display;
}

/**
 * 文章浏览数
 */
if ( ! object( 'post_preview' ) ) {
    add_action( 'get_header', 'set_views' );
}
function set_views() {
    global $post;
    $post_id = intval( $post->ID );
    $count_key = 'views';
    $views = get_post_custom( $post_id );
    $views = intval( $views['views'][0] );
    if( is_single() || is_page() ) {
        if( ! update_post_meta( $post_id, 'views', ( $views + 1 ) ) )
            add_post_meta( $post_id, 'views', 1, true );
    }
}
function set_views_ajax( $postID ) {
    $count_key = 'views';
    $count = get_post_meta( $postID, $count_key, true );
    if( $count == '' ){
        $count = 0;
        delete_post_meta( $postID, $count_key );
        add_post_meta( $postID, $count_key, '0' );
    }
    else {
        $count++;
        update_post_meta( $postID, $count_key, $count );
    }
}
function get_views( $post_id ) {
    $count_key = 'views';
    $views = get_post_custom( $post_id );
    $views = intval( $views['views'][0] );
    $post_views = intval( post_custom('views') );
    if( $views == '' )
        return 0;
    else
        return $views;
}

/**
 * 最后一次登录的时间
 */
add_action( 'wp_login', 'set_last_login' );
function set_last_login( $login ) {
	//date(time() + 8*3600); 8小时时差
	update_user_meta( 1, 'last_login', time() );
}
function last_login() {
	$time = get_user_meta( 1, 'last_login' )[0];
	echo human_time_diff( $time );
}

/**
 * 处理加密文章
 */
add_filter( 'the_password_form', 'changes_password_protected' );
add_filter( 'protected_title_format', 'changes_protected_title_prefix' );
function changes_protected_title_prefix() {
    return '%s';
}
function changes_password_protected( $content ) {
    global $post;
    if ( ! empty( $post->post_password ) && stripslashes( $_COOKIE['wp-postpass_'.COOKIEHASH] ) != $post->post_password ) {
        $output = '<div class="post-password-form">';
	$output .= '看来要有神秘代码才能解除封印..<br><br>';
        $output .= '<input name="post_password" class="input" type="password" size="25" placeholder="输入密码" />';
        $output .= '<input type="submit" name="Submit" class="button" value="' . __( "Continue" ) . '" />';
        $output .= '</div>';
        return $output;
    }
    else {
        return $content;
    }
}

/**
 * 视频短代码
 */
add_shortcode( 'mp4', 'video_shortcode' );
function video_shortcode( $atts, $content = null ) {
    extract( shortcode_atts( array(
        'url' => $url,
    ), $atts ) );

    return '<p id="media"><video id="video" src="'. $url .'" controls="controls" width="100%"></video></p>';
}

/**
 * 下载短代码
 */
add_shortcode( 'dl', 'download_shortcode' );
function download_shortcode( $atts, $content = null ) {
    extract( shortcode_atts( array(
        'url' => $url,
        'text' => $text
    ), $atts ) );

    return '<p id="download"><span class="icon">&#xed0a;</span><a href="'. $url .'" rel="nofollow" target="_blank">'. $text .'</a></p>';
}

/**
 * 文章内链短代码
 */
add_shortcode( 'insert', 'post_insert_shortcode' );
function post_insert_shortcode( $atts, $content = null ) {
    extract( shortcode_atts( array(
        'ids' => $ids,
    ), $atts ) );

    if ( preview() ) {
        $before = '<div class="'. preview() .'">';
        $after = '</div>';
    }
    return $before.get_post_info( $ids ).$after;
}

/**
 * 增加编辑快捷按钮
 */
function themes_add_quicktags() { ?> 
    <script type="text/javascript"> 
        QTags.addButton( '视频播放器', '视频播放器', '\n[mp4 url="填写视频地址"]', '' );
        QTags.addButton( '文章内链', '文章内链', '\n[insert ids="填写文章的ID，多个用英文逗号隔开"]', '' );
        QTags.addButton( '下载', '下载', '\n[dl url="下载链接地址" text="提示文字"]', '' );
    </script>
<?php
}
add_action('admin_print_footer_scripts', 'themes_add_quicktags' );

/**
 * 优化标题描述
 */
add_action( 'wp_head', 'site_seo', 0 );
function site_seo() {
    global $s, $post , $wp_query;
    $keywords = '';
    $description = '';
    $blog_name = get_bloginfo( 'name' );
    if ( is_singular() ) {
        $ID = $post->ID;
        $title = $post->post_title;
        $author = $post->post_author;
        $user_info = get_userdata( $author );
        $post_author = $user_info->display_name;
        if ( ! get_post_meta( $ID, 'meta-description', true )) {
            $description = $title.' - 作者: '. $post_author .',出自'. $blog_name;
        }
        else {
            $description = get_post_meta( $ID, 'meta-description', true );
        }
    }
    elseif ( is_home () ) {
        $description = object( 'seo_description' );
        $keywords = object( 'seo_keywords' );
    }
    elseif ( is_tag() ) {
        $description = single_tag_title( '', false ) . " - ". trim( strip_tags( tag_description() ) );
    }
    elseif ( is_category() ) {
        $description = single_cat_title( '', false ) . " - ". trim( strip_tags( category_description() ) );
    }
    elseif ( is_archive() ) {
        $description = $blog_name . "'" . trim( wp_title( '', false ) ) . "'";
    }
    elseif ( is_search() ) {
        $description = $blog_name . ": '" . esc_html( $s, 1 ) . "' 的搜索結果";
    }
    else { 
        $description = $blog_name . "'" . trim( wp_title( '', false ) ) . "'";
    }

    $description = mb_substr( $description, 0, 220, 'utf-8' );
    $pingback_url = get_bloginfo( 'pingback_url' );
    $rss2_url = get_bloginfo( 'rss2_url' );
    $atom_url = get_bloginfo( 'atom_url' );
    $favicon = object( 'site_favicon' );
    echo "<meta name=\"description\" content=\"$description\">\n";
    echo "<meta name=\"keywords\" content=\"$keywords\">\n";
    echo "<link rel=\"profile\" href=\"https://gmpg.org/xfn/11\">\n";
    echo "<link rel=\"pingback\" href=\"$pingback_url\" />\n";
    echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"$blog_name\" href=\"$rss2_url\" />\n";
    echo "<link rel=\"alternate\" type=\"application/atom+xml\" title=\"$blog_name\" href=\"$atom_url\" />\n";
    echo "<link rel=\"shortcut icon\" type=\"images/x-icon\" href=\"$favicon\" />\n";
}

/**
 * 外部链接自动加nofollow
 */
add_filter( 'the_content', 'link_nofollow');
function link_nofollow( $content ) {
    $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
    if( preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER) ) {
        if( ! empty($matches) ) {
            $srcUrl = get_option( 'siteurl' );
            for ( $i=0; $i < count($matches); $i++ ){
                $tag = $matches[$i][0];
                $tag2 = $matches[$i][0];
                $url = $matches[$i][0];
                $noFollow = '';
                $pattern = '/target\s*=\s*"\s*_blank\s*"/';
                preg_match( $pattern, $tag2, $match, PREG_OFFSET_CAPTURE );
                if( count($match) < 1 ) $noFollow .= ' target="_blank" ';
                $pattern = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
                preg_match( $pattern, $tag2, $match, PREG_OFFSET_CAPTURE );
                if( count($match) < 1 ) $noFollow .= ' rel="nofollow" ';
                $pos = strpos( $url, $srcUrl );
                if ( $pos === false ) {
                    $tag = rtrim ( $tag, '>' );
                    $tag .= $noFollow.'>';
                    $content = str_replace( $tag2, $tag, $content );
                }
            }
        }
    }

    $content = str_replace( ']]>', ']]>', $content );
    return $content;
}

/**
 * 安全问题
 */
add_filter( 'comment_class', 'change_comment_class' );
add_filter( 'request', 'change_author_link_request' );
add_filter( 'author_link', 'change_author_link', 10, 2 );
// 评论类
function change_comment_class( $who ){
    $admin_info = get_userdata(1);
    $replace = array(
        'comment-author-'.$admin_info->user_nicename => 'the-author',
    );
    $who = str_replace( array_keys( $replace ), $replace, $who );
    return $who;
}
// 存档方面
function change_author_link( $link, $author_id) {
    global $wp_rewrite;
    $author_id = (int) $author_id;
    $link = $wp_rewrite->get_author_permastruct();
 
    if ( empty($link) ) {
        $file = home_url( '/' );
        $link = $file . '?author=' . $author_id;
    } else {
        $link = str_replace( '%author%', $author_id, $link );
        $link = home_url( user_trailingslashit( $link ) );
    }
 
    return $link;
}
function change_author_link_request( $query_vars ) {
    if ( array_key_exists( 'author_name', $query_vars ) ) {
        global $wpdb;
        $author_id = $query_vars['author_name'];
        if ( $author_id ) {
            $query_vars['author'] = $author_id;
            unset( $query_vars['author_name'] );    
        }
    }
    return $query_vars;
}
if( !is_user_logged_in() )
add_filter( 'preprocess_comment', 'usercheck' );
function usercheck( $incoming_comment ) {
    $admin_info = get_userdata(1);
    $isSpam = 0;
    if ( trim( $incoming_comment['comment_author'] ) == $admin_info->user_nicename )
    $isSpam = 1;
    if ( trim( $incoming_comment['comment_author'] ) == $admin_info->display_name )
    $isSpam = 1;
    if ( trim( $incoming_comment['comment_author_email'] ) == $admin_info->user_email )
    $isSpam = 1;
    if( !$isSpam ) return $incoming_comment;
    err( '你这是要搞事情啊！' );
}

/**
 * 评论未审核通知
 */
add_filter('wp_footer','get_not_audit_comments');
function get_not_audit_comments() {
    if( is_home() && current_user_can( 'level_10' ) ){
        $awaiting_mod = wp_count_comments();
        $awaiting_mod = $awaiting_mod->moderated;
        if( $awaiting_mod && !wp_is_mobile() ) {
            echo '<a id="awaiting-comments" href="'. admin_url( 'edit-comments.php' ) .'" target="_top"><i class="icon">&#xe623;</i>你有 '.$awaiting_mod.' 条回复正在等待审核。</a>';
        }
    }
}

/**
 * 后台元素
 */
add_action( 'admin_bar_menu', 'remove_logo', 999 );
add_action( 'admin_menu', 'disable_dashboard_widgets' );
add_filter( 'admin_title', 'custom_admin_title', 10, 2 );
add_filter( 'admin_footer_text', 'change_footer_admin', 9999 );
add_filter( 'update_footer', 'change_footer_version', 9999 );
function custom_admin_title( $admin_title, $title ){
    return $title .' &lsaquo; '. get_bloginfo( 'name' );
}
function remove_logo( $wp_toolbar ) {
    $wp_toolbar->remove_node( 'wp-logo' );
}
function change_footer_admin () { return ''; }  
function change_footer_version() { return ''; }  
function disable_dashboard_widgets() {
    remove_meta_box( 'dashboard_primary', 'dashboard', 'core' );   // 博客  
    remove_meta_box( 'dashboard_secondary', 'dashboard', 'core' ); // 其它新闻 
    remove_meta_box( 'dashboard_right_now', 'dashboard', 'core' ); // 概况   
}

add_action( 'login_footer', 'custom_html' );
add_filter( 'login_headertitle', 'custom_headertitle' );
add_filter( 'login_headerurl', 'custom_loginlogo_url' );
function custom_html() {
    $avatar = get_avatar_url( cs_get_option( 'sns_email' ) );
    echo "<style type=\"text/css\">\n";
    echo ".login h1 a { background-image:url(\"$avatar\"); border-radius: 999em; }\n";
    echo "</style>";
}
function custom_loginlogo_url( $url ) {
    return esc_url( home_url('/') );
}
function custom_headertitle ( $title ) {
    return get_bloginfo( 'name' );
}
