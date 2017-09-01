					
				</div><!-- #loop ####-->
			</section><!-- #contents ###-->
		</div><!-- #container.site-refresh ##-->
		<div id="footer" class="site-footer">
			<div class="inner textcenter">
				<div class="copyright"><?php object('site_copyright', true); ?></div>
				<div class="power">
					<span>Website work <?php echo floor( (time()-strtotime(object('site_start_date')))/3600 ); ?> hours</span>
					<?php if ( is_user_logged_in() ) : ?>
					<span> / </span>
					<span><a href="<?php bloginfo('url') ?>/admin" target="_top">Backstage</a></span>
					<?php endif; ?>
					<?php if ( object('tongji_link') ) : ?>
					<span> / </span>
					<span><a href="<?php object('tongji_link', true); ?>" rel="nofollow" target="_blank">Statistics</a></span>
					<?php endif; ?>
					<span> / </span>
					<span><a href="<?php bloginfo('url'); ?>/sitemap.xml">XML</a></span>
				</div>
			</div>
		</div><!-- #footer.site-footer ##-->
	</div><!-- #wrapper.theme #-->
	<div class="gotop bg">
		<span class="icon">&#xe6a9;</span>
	</div><!-- .gotop #-->
	<?php wp_footer(); ?>
</body>
</html>