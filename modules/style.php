<?php
/**
 * Theme style function file
 * @package Louie
 * @since Theme version 1.0.0
 */

add_action( 'wp_head', 'rewrite_style' );
function rewrite_style() { 
	$color = object( 'site_color' );
	$bg = object( 'site_bg' );
	$width = object( 'site_width' );
	$height = object( 'site_banner_height' );
	$fonts = object( 'site_font' )['family'] == 'Arial' ? '-apple-system,SF UI Text,Arial,PingFang SC,Hiragino Sans GB,Microsoft YaHei,WenQuanYi Micro Hei,sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";' : object( 'site_font' )['family'];
?>
<!-- 定制全局颜色 -->
<style type="text/css">
<?php if ( $color ) : ?>
a,
.list .hot,
.comment-to,
.blogname .ca-icon,
.trends .state-count,
.master-info-small .nickname,
.gotop,
.share,
#share a:hover,
.content h2:before,
.player .list li:hover,
.player .control .item:hover,
.archives .list li a:hover,
#overlay .full-link:hover {
    color: <?php echo $color; ?>;
}

.nav-menu a:hover,
.main-menu .current-menu-item a {
    border-color: <?php echo $color; ?>;
    color: <?php echo $color; ?>;
}

#button, 
.button a,
#awaiting-comments {
    background-color: <?php echo $color; ?>;
}

::-webkit-scrollbar-thumb {
    background-color: <?php echo $color; ?>;
}

.comment-reply,
.children .comment-body:before,
.loader .circle:after,
#nprogress .bar {
	background: <?php echo $color; ?>;
}

.tips-temp:before {
	border-bottom-color: <?php echo $color; ?>;
}
.tips-temp:after {
	background: <?php echo $color; ?>;
}

#nprogress .peg {
	box-shadow-color: 0 0 10px <?php echo $color; ?>, 0 0 5px <?php echo $color; ?>;
}

#nprogress .spinner-icon {
	border-top-color: <?php echo $color; ?>;
    border-left-color: <?php echo $color; ?>;
}

#notification .title {
	border-color: <?php echo $color; ?>;
}

.links-bar .item a:hover {
    background: <?php echo $color; ?>;
    color: #fff;
}

<?php endif; ?>

<?php if ( $bg ) : ?>
body {
	font-family: <?php echo $fonts; ?>;
	background: <?php echo $bg; ?>;
}
<?php endif; ?>

<?php if ( $height ) : ?>
.banner,#bgvideo {
	height: <?php echo $height; ?>px;
}
<?php endif; ?>

<?php if ( $width ) : ?>
.width {
	max-width: <?php echo $width; ?>;
}
<?php endif; ?>
</style>
<?php
}