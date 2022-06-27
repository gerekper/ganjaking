<div class="up-timeline-wrapper">
<div id="timeline">
	<div id="up-time-line">
		<div class="up-timeline-icon-bottom">
			<a href="#" class="icon-totop" style="display: block;"></a>
		</div>
	</div>
		<?php
		 		$obj = UPTimelineApi::instance();
				$arr = get_user_meta( $args['user_id'],'up-timeline-actions',true);
				if( !empty( $arr ) ){
        $arr = array_reverse( $arr );
				foreach( $arr as $a ){
					echo $obj->get_timeline_content( $a, $args['user_id'] );
				}
			}
		 ?>
					<!--</div>
					<div id="profile-description">
						<div class="up-timeline-preview"><div class="up-timeline-icon-pp">
							<a href="/" class="up-icon-profile"></a></div><div class="up-timeline-pointer-pp">
								<span class="tl-pointer"></span>
								</div>
								<div class="up-timeline-content"><p class="userpro-timeline-description">
									Hello everyone, <br>
my name is Timeline. Tech enthusiast, gadget freak, amazed by anything mobile. This blog is dedicated to pictures mainly taken by mobile devices. Although sometimes when the needs arise, a more dedicated device will be used.
								</p></div>
								<div class="up-timeline-bar">
								<div class="postdte">20 Jan 2017</div>
								</div></div></div>

		<div id="profile-description">
			<div class="up-timeline-preview">
				<div class="up-timeline-icon-pp">
					<a href="/" class="up-icon-profile"></a>
				</div>
				<div class="up-timeline-pointer-pp">
					<span class="tl-pointer"></span>
				</div><div class="up-timeline-content"><p class="userpro-timeline-description">
									<span>
										<?php //_e('Updated Post','userpro'); ?>
									</span>
									<img src="http://68.media.tumblr.com/tumblr_lzd3drQ7v71rpzj0eo1_500.jpg" alt="">
								</p></div><div class="up-timeline-bar"><div class="postdte">20 Jan 2017</div></div></div></div>
-->
</div>
</div>
