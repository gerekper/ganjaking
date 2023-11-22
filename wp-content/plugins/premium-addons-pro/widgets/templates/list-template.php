<?php
/**
 * Template: Premium Social Feed List Template
 */
?>
{{? it.counter === 1 && it.screen_name && it.showHeader }}
	<div class="premium-twitter-user-cover">
		<div class="premium-twitter-header-banner">
			<img src="{{=it.cover_picture}}" alt="{{=it.author_name}}">
		</div>
		<div class="premium-twitter-header-wrapper">
				<div class="premium-twitter-feed-header-user">
					<div class="premium-twitter-img-container">
						<a href="{{=it.author_link}}" target="_blank"><img class="premium-twitter-header-img" src="{{=it.author_picture_hq}}" alt="{{=it.author_name}}"></a>
					</div>
					<div class="premium-twitter-user-details-wrapper">
						<div class="premium-twitter-header-link-wrapper">
							<a class="premium-twitter-header-link" href="{{=it.author_link}}" target="_blank">
								<span class="premium-twitter-feed-name" >{{=it.author_name}}</span>
								<span class="premium-twitter-screen-name">@{{=it.screen_name}}</span>
							</a>
						</div>
						<div class="premium-twitter-feed-header-statistics">
							<p class="premium-twitter-header-count"><a href='{{=it.author_link}}' target="_blank"><span>{{=it.tweets_count}}</span><span> Tweets</span></a></p>
							<p class="premium-twitter-header-count"><a href='{{=it.author_link}}/following' target="_blank"><span>{{=it.following_count}}</span><span> Following</span></a></p>
							<p class="premium-twitter-header-count"><a href='{{=it.author_link}}/followers' target="_blank"><span>{{=it.followers_count}}</span> <span>Followers</span></a></p>
						</div>
					</div>
				</div>
				<div class="premium-twitter-feed-follow">
					<a rel="nofollow" href="https://twitter.com/intent/follow?screen_name={{=it.screen_name}}" target="_blank">
						Follow
					</a>
				</div>
		</div>
	</div>
{{?}}
<div class="premium-social-feed-element {{? !it.moderation_passed}}hidden{{?}}" dt-create="{{=it.dt_create}}" social-feed-id = "{{=it.id}}">
	<a class="premium-feed-element-author-img" href="{{=it.author_link}}" target="_blank">
		<img class="media-object" src="{{=it.author_picture}}">
	</a>
	<div class="media-body">
		<div class="premium-feed-element-meta">
			<i class="fab fa-{{=it.social_network}} premium-social-icon"></i>
			<span class="premium-feed-element-author"><a href="{{=it.author_link}}" target="_blank">{{=it.author_name}}</a></span>
			<span class="muted premium-feed-element-date"><a href="{{=it.link}}" target="_blank">{{=it.time_ago}}</a></span>  
		</div>
		<div class="premium-feed-element-content-wrap">
			<p class="premium-feed-element-text">{{=it.text}} </p>
			<div class="premium-feed-read-more-wrap"><a href="{{=it.link}}" target="_blank" class="premium-feed-element-read-more">{{=it.readMore}}</a></div>
		</div>
		{{? it.screen_name }}
		<div class="premium-twitter-comments-box">
			<a href="https://twitter.com/intent/tweet?in_reply_to={{=it.id}}&related={{=it.screen_name}}" target="_blank" title="Comments" class="premium-twitter-comments-field">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11.5 11.5"><path d="M4.6 0H7c1.3 0 2.4.4 3.2 1.3.9.9 1.3 2 1.3 3.2 0 .7-.2 1.3-.5 2-.3.6-.8 1.2-1.3 1.7-.6.6-2.2 1.7-4.7 3.3-.1 0-.2.1-.2.1-.2 0-.3-.1-.4-.2 0-.1-.1-.1-.1-.2V8.9c-1.2 0-2.3-.5-3.1-1.3C.4 6.8 0 5.7 0 4.6c0-1.3.4-2.4 1.3-3.2.9-1 2-1.4 3.3-1.4m4.6 7.6c.5-.4.8-.9 1.1-1.4.3-.5.4-1.1.4-1.6 0-1-.4-1.9-1.1-2.6C8.9 1.2 8 .9 7 .9H4.6C3.6.9 2.7 1.3 2 2 1.2 2.7.9 3.5.9 4.6c0 1 .3 1.8 1 2.5.7.6 1.6 1 2.6 1h.2c.2-.1.3-.1.4 0 .1.1.1.2.1.3v1.9c2.2-1.4 3.5-2.4 4-2.7"/></svg>
			</a>
			<a href="https://twitter.com/intent/retweet?tweet_id={{=it.id}}&related={{=it.screen_name}}" target="_blank" title="Retweet" class="premium-twitter-comments-field">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 9.7"><path d="M4.2 8.8h3.4c.1 0 .2 0 .3.1.1.1.2.2.2.3 0 .1 0 .2-.1.3-.1.1-.2.2-.4.2H4.2c-.6 0-1.1-.2-1.5-.7-.4-.4-.6-.9-.6-1.5v-6L.8 2.8c-.1.1-.2.1-.4.1s-.2 0-.3-.1C0 2.7 0 2.6 0 2.5c0-.1 0-.2.1-.3l2-2c.2-.2.3-.2.4-.2.1 0 .2 0 .3.1l2.1 2c0 .2.1.3.1.4 0 .1 0 .2-.1.3-.1.1-.2.1-.4.1-.1 0-.2 0-.3-.1L2.9 1.5v6c0 .4.1.7.4.9.3.3.6.4.9.4m9.7-1.9c.1.1.1.2.1.3s0 .2-.1.3l-2 2c-.1.1-.2.1-.3.1-.1 0-.2 0-.3-.1l-2.1-2c-.1-.1-.2-.2-.2-.3s0-.2.1-.3c.1-.1.2-.1.3-.1s.2 0 .3.1L11 8.2v-6c0-.4-.1-.7-.4-.9-.2-.3-.5-.4-.8-.4H6.4c-.1 0-.2 0-.3-.1C6 .7 5.9.6 5.9.4c0-.1 0-.2.1-.3.1-.1.2-.1.4-.1h3.4c.6 0 1.1.2 1.5.6.4.4.6.9.6 1.5v6l1.3-1.3c.1-.1.2-.1.3-.1s.3.1.4.2"/></svg>
				<span>{{=it.retweet_count}}</span> 
			</a>
			<a href="https://twitter.com/intent/like?tweet_id={{=it.id}}&related={{=it.screen_name}}" target="_blank" title="Like" class="premium-twitter-comments-field">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 14"><path d="M5.1 13.1c-.8-.6-1.5-1.3-2.3-2.2-.7-.9-1.4-1.9-2-3.1C.3 6.5 0 5.4 0 4.3s.4-2.1 1.1-3C1.9.4 2.8 0 3.8 0 5 0 6.1.7 7 2c.9-1.4 2-2 3.2-2 1 0 1.9.4 2.6 1.3.8.9 1.1 1.9 1.1 3s-.3 2.3-.8 3.5c-.6 1.2-1.2 2.3-2 3.1-.8.9-1.5 1.6-2.3 2.2-.7.6-1.3.9-1.8.9s-1.1-.3-1.9-.9m-3.2-11c-.6.6-.9 1.3-.9 2.2 0 .8.2 1.6.5 2.5.3.9.8 1.7 1.3 2.4s1 1.3 1.6 1.9c.6.6 1.1 1 1.5 1.3.5.3.8.5 1 .5.2 0 .6-.2 1-.5.5-.3 1-.8 1.5-1.3.6-.6 1.1-1.2 1.6-1.9s.9-1.5 1.3-2.4c.5-.9.7-1.7.7-2.5s-.3-1.6-.8-2.2c-.5-.6-1.2-.9-1.9-.9-.3 0-.6.1-1 .2-.3.1-.6.3-.8.5-.2.2-.4.4-.5.6-.2.3-.3.4-.4.6-.1.1-.1.2-.1.2-.1.2-.3.3-.5.3s-.4-.1-.5-.3c-.1-.2-.1-.3-.3-.5-.1-.1-.2-.4-.5-.7-.2-.3-.5-.5-.8-.7-.4-.2-.8-.3-1.1-.3-.8 0-1.4.3-1.9 1"/></svg>
				<span>{{=it.favorite_count}}</span>
			</a>
		</div>
		{{?}}
	</div>
	{{=it.attachment}}
</div>

