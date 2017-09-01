<aside id="aside" class="left">
	<div class="inner">
		<div class="sns master-info">
			<div class="base">
				<h2 class="blogname"><?php bloginfo('name'); ?><span class="icon ca-icon">&#xe645;</span></h2>
				<div class="nickname">@<?php echo get_the_author_meta('display_name', 1); ?></div>
				<p class="description"><?php echo about(); ?></p>
			</div>
			<ul class="items">
				<?php if ( object('sns_location') ) : ?>
				<li class="item location"><i class="dot"></i><?php object('sns_location', true); ?></li>
				<?php endif; ?>

				<?php $site_url = get_bloginfo('url'); ?>
				<li class="item site"><i class="dot"></i><a href="<?php echo $site_url; ?>"><?php echo substr($site_url,strpos($site_url,'/')+2); ?></a></li>

				<?php if ( object('sns_github') ) : ?>
				<li class="item github"><i class="dot"></i><a class="tips-right" aria-label="我的开源项目" href="<?php object('sns_github', true); ?>" rel="nofollow" target="_blank">fork me on github</a></li>
				<?php endif; ?>
				<li class="item active"><i class="dot"></i>在<?php last_login(); ?>前来过</li>
			</ul>
			<div class="items message">
				<?php if ( object('sns_email') ) : ?>
				<div class="item">
					<a href="mailto:<?php object('sns_email', true); ?>" rel="nofollow" target="_blank" id="button">
						<span class="icon">&#xe60c;</span>
						<span class="email">邮件</span>
					</a>
				</div>
				<?php endif; ?>

				<?php if ( object('sns_instant') ) : ?>
				<div class="item">
					<a href="https://wpa.qq.com/msgrd?v=3&uin=<?php object('sns_instant', true); ?>&site=qq&menu=yes" rel="nofollow" target="_blank" id="button" class="tips-top tips-medium" aria-label="你的话题将被筛选。">
						<span class="icon">&#xe712;</span>
						<span class="instant">即时</span>
					</a>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="alteration">
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
		</div>
	</div>
</aside>