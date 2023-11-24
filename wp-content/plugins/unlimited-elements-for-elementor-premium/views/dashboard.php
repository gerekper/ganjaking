<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

HelperUC::addStyle("jquery.fancybox", "fancybox", "assets_libraries/fancybox3");
HelperUC::addScript("jquery.fancybox", "fancybox", "assets_libraries/fancybox3");
HelperUC::addStyle("unitecreator_dashboard", "unitecreator_dashboard");

$isProVersion = GlobalsUC::$isProVersion;


$showFreeVersion = UniteFunctionsUC::getGetVar("showfreeversion", "", UniteFunctionsUC::SANITIZE_TEXT_FIELD);
$showFreeVersion = UniteFunctionsUC::strToBool($showFreeVersion);

if($showFreeVersion === true)
	$isProVersion = false;


$imagesUrl = GlobalsUC::$urlPluginImages . "dashboard/";

$videoItems = array(
	array(
		"url" => "https://youtu.be/SnNI9_KXY9Y?si=xz3to9IYlYeBJ8qd",
		"title" => __("Give Your Elementor Website Superpowers with Unlimited Elements", "unlimited-elements-for-elementor"),
		"image" => $imagesUrl . "tutorials/video-1.jpg",
	),
	array(
		"url" => "https://youtu.be/pvZ5Lvom470?si=cX2vhhgfzsjnBJd1",
		"title" => __("Unlimited Elements Widget Creator for Elementor Page Builder", "unlimited-elements-for-elementor"),
		"image" => $imagesUrl . "tutorials/video-2.jpg",
	),
	array(
		"url" => "https://youtu.be/ZdYCoD8_qxo?si=UalRKpw6udz9K3W0",
		"title" => __("Remote Control Widgets to Create Advanced Interactive Layouts", "unlimited-elements-for-elementor"),
		"image" => $imagesUrl . "tutorials/video-3.jpg",
	),
);

$blogItems = array(
	array(
		"url" => "https://unlimited-elements.com/10-ways-to-maximize-sales-during-black-friday-and-cyber-monday/",
		"title" => __("10 ways to maximize sales during Black Friday", "unlimited-elements-for-elementor"),
		"image" => $imagesUrl . "blog/post-1.png",
	),
	array(
		"url" => "https://unlimited-elements.com/how-to-get-your-elementor-website-ready-for-christmas/",
		"title" => __("How To Get Your Elementor Website Ready For Christmas", "unlimited-elements-for-elementor"),
		"image" => $imagesUrl . "blog/post-2.png",
	),
	array(
		"url" => "https://unlimited-elements.com/best-wordpress-black-friday-plugin-deals-2023/",
		"title" => __("Best WordPress Black Friday Deals 2023", "unlimited-elements-for-elementor"),
		"image" => $imagesUrl . "blog/post-3.jpg",
	),
);

$urlVideoTutorials = "https://www.youtube.com/channel/UCNYLnevs1ewIxKQqPiat0xQ";

$version = UNLIMITED_ELEMENTS_VERSION;

?>

<div class="ue-root ue-dash-content">

	<!-- Main content start -->
	<div class="ue-content-main ue-left">
		
		<div class="ue-social-content-wrapper">
			<!-- Youtube -->
			<div class="ue-content-card ue-yt">
				<div class="ue-content-icon ue-flex-center">
					<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
						<path d="M22.874 8.16c-.089-.348-.34-.61-.628-.69-.512-.143-2.995-.47-8.245-.47-5.25 0-7.734.327-8.247.47-.285.08-.537.341-.627.69-.127.495-.46 2.568-.46 5.84 0 3.27.333 5.344.46 5.84.089.346.34.608.627.689.513.143 2.997.47 8.247.47s7.733-.327 8.246-.47c.286-.08.537-.341.627-.69.127-.495.46-2.573.46-5.84 0-3.266-.333-5.344-.46-5.839Zm2.26-.58c.533 2.08.533 6.42.533 6.42s0 4.34-.533 6.418c-.297 1.15-1.163 2.053-2.261 2.359-1.994.556-8.872.556-8.872.556s-6.875 0-8.872-.556c-1.103-.31-1.97-1.213-2.262-2.359C2.334 18.34 2.334 14 2.334 14s0-4.34.533-6.418c.297-1.15 1.163-2.054 2.262-2.36C7.126 4.667 14 4.667 14 4.667s6.879 0 8.872.556c1.102.31 1.969 1.213 2.261 2.359ZM11.667 18.084V9.916l7 4.083-7 4.084Z" />
					</svg>
				</div>
				<div class="ue-content-title">
					<?php echo esc_html__("Subscribe to", "unlimited-elements-for-elementor"); ?>
					<?php echo esc_html__("Our YouTube Channel", "unlimited-elements-for-elementor"); ?>
				</div>
				<div class="ue-content-desc">
					<?php echo esc_html__("Subscribe to our YouTube channel and never miss out on exciting content, useful tutorials, and important updates! Hit the \"Subscribe\" button now to join our growing community of viewers.", "unlimited-elements-for-elementor"); ?>
				</div>
				<a class="ue-content-btn ue-flex-center" href="<?php echo GlobalsUC::URL_YOUTUBE; ?>" target="_blank">
					<?php echo esc_html__("Subscribe", "unlimited-elements-for-elementor"); ?>
				</a>
			</div>
			<!-- Facebook -->
			<div class="ue-content-card ue-fb">
				<div class="ue-content-icon ue-flex-center">
					<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
						<path d="M16.333 15.75h2.917l1.166-4.667h-4.083V8.75c0-1.201 0-2.333 2.334-2.333h1.75v-3.92c-.38-.05-1.817-.164-3.334-.164-3.167 0-5.416 1.933-5.416 5.483v3.267h-3.5v4.667h3.5v9.917h4.666V15.75Z" />
					</svg>
				</div>
				<div class="ue-content-title">
					<?php echo esc_html__("Join", "unlimited-elements-for-elementor"); ?>
					<?php echo esc_html__("Our Facebook Group", "unlimited-elements-for-elementor"); ?>
				</div>
				<div class="ue-content-desc">
					<?php echo esc_html__("Stay updated on the latest news, connect with like-minded individuals, and access exclusive content! Click the \"Join Group\" button below and be a part of our supportive community.", "unlimited-elements-for-elementor"); ?>
				</div>
				<a class="ue-content-btn ue-flex-center" href="<?php echo GlobalsUC::URL_FACEBOOK; ?>" target="_blank">
					<?php echo esc_html__("Join Group", "unlimited-elements-for-elementor"); ?>
				</a>
			</div>
		</div>

		<div class="ue-content-card ue-tutorials">
			<div class="ue-tutorials-header">
				<div class="ue-content-icon ue-flex-center">
					<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
						<path d="M2.334 4.659c0-.64.531-1.159 1.157-1.159h21.02c.638 0 1.156.52 1.156 1.159v18.682c0 .64-.53 1.159-1.157 1.159H3.491c-.639 0-1.157-.52-1.157-1.159V4.659Zm7 1.174v16.334h9.333V5.833H9.334Zm-4.667 0v2.334h2.334V5.833H4.667Zm16.334 0v2.334h2.333V5.833h-2.333ZM4.667 10.5v2.333h2.334V10.5H4.667Zm16.334 0v2.333h2.333V10.5h-2.333ZM4.667 15.167V17.5h2.334v-2.333H4.667Zm16.334 0V17.5h2.333v-2.333h-2.333ZM4.667 19.833v2.334h2.334v-2.334H4.667Zm16.334 0v2.334h2.333v-2.334h-2.333Z" />
					</svg>
				</div>
				<div class="ue-inner-content-wrapper-h">
					<div class="ue-content-title">
						<?php echo esc_html__("Video Tutorials", "unlimited-elements-for-elementor"); ?>
					</div>
					<div class="ue-content-desc">
						<?php echo esc_html__("3 important videos to get you started with Unlimited Elements.", "unlimited-elements-for-elementor"); ?>
					</div>
				</div>
				<a class="ue-content-btn ue-flex-center ue-tmore-btn-1" target="_blank" href="<?php echo $urlVideoTutorials?>">
					<?php echo esc_html__("View More", "unlimited-elements-for-elementor"); ?>
				</a>
			</div>
			<div class="ue-video-wrapper">
				<?php foreach($videoItems as $item): ?>
					<a class="ue-video-item" href="<?php echo $item["url"]; ?>" target="_blank" data-fancybox="gallery">
						<div class="ue-video ue-flex-center">
							<img class="ue-video-bg" src="<?php echo $item["image"]; ?>?ver=<?php echo $version?>" alt="<?php echo esc_attr($item["title"]); ?>" />
							<div class="ue-video-play-btn"></div>
						</div>
						<h3 class="ue-video-title"><?php echo esc_html($item["title"]); ?></h3>
					</a>
				<?php endforeach; ?>
			</div>
			<a class="ue-content-btn ue-flex-center ue-tmore-btn-2" href="#">
				<?php echo esc_html__("View More", "unlimited-elements-for-elementor"); ?>
			</a>
		</div>

		<div class="ue-content-card ue-full-card ue-flex-center">
			<div class="ue-inner-section-left">
				<div class="ue-content-icon ue-flex-center">
					<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
						<path d="M5.839 10.503c.644 0 1.166.523 1.166 1.167 1.907 0 3.662.654 5.052 1.75h2.532c1.554 0 2.95.675 3.911 1.748l3.672.002a5.834 5.834 0 0 1 5.269 3.327c-2.76 3.64-7.227 6.006-12.27 6.006-3.255 0-6.008-.704-8.237-1.934a1.165 1.165 0 0 1-1.095.768h-3.5a1.167 1.167 0 0 1-1.167-1.167v-10.5c0-.644.522-1.167 1.167-1.167h3.5Zm1.167 3.5v5.858l.052.04c2.093 1.47 4.822 2.269 8.114 2.269 3.505 0 6.765-1.348 9.14-3.651l.156-.156-.14-.117a3.493 3.493 0 0 0-1.917-.735l-.24-.008h-2.463c.086.375.13.766.13 1.167v1.167h-10.5v-2.334h7.922l-.04-.092a2.918 2.918 0 0 0-2.44-1.652l-.191-.006H11.17a5.815 5.815 0 0 0-4.165-1.75Zm-2.334-1.166H3.505v8.166h1.167v-8.166Zm11.254-8.662.412.412.413-.412a2.917 2.917 0 0 1 4.125 4.124l-4.537 4.538L11.8 8.299a2.917 2.917 0 1 1 4.125-4.124Zm-2.475 1.65a.583.583 0 0 0-.068.743l.067.08 2.887 2.888 2.889-2.887a.583.583 0 0 0 .067-.744l-.067-.08a.584.584 0 0 0-.744-.068l-.081.068-2.064 2.063-2.062-2.065-.08-.066a.584.584 0 0 0-.744.067Z" />
					</svg>
				</div>
				<div class="ue-content-title">
					<?php echo esc_html__("Do You Love Unlimited Elements?", "unlimited-elements-for-elementor"); ?>
				</div>
				<div class="ue-content-desc">
					<?php echo esc_html__("Let us know. Your ratings and reviews contribute to making our WordPress plugin even better. Help us out with a 5 star review on the WP plugin repository.", "unlimited-elements-for-elementor"); ?>
				</div>
				<a class="ue-content-btn ue-flex-center" href="<?php echo GlobalsUC::URL_RATE; ?>" target="_blank">
					<?php echo esc_html__("Rate Us", "unlimited-elements-for-elementor"); ?>
				</a>
			</div>
			<div class="ue-inner-section-right ue-flex-center">
				<img class="ue-illustration" src="<?php echo $imagesUrl . "rate.svg"; ?>" alt="" />
			</div>
		</div>

		<div class="ue-content-card ue-full-card ue-flex-center">
			<div class="ue-inner-section-left">
				<div class="ue-content-icon ue-flex-center">
					<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
						<path d="M24.5 21H7a1.167 1.167 0 1 0 0 2.334h17.5v2.333H7a3.5 3.5 0 0 1-3.5-3.5v-17.5a2.333 2.333 0 0 1 2.333-2.333H24.5v18.667ZM5.833 18.727c.189-.039.384-.059.584-.059h15.75v-14H5.833v14.059ZM18.667 10.5H9.333V8.167h9.334v2.334Z" />
					</svg>
				</div>
				<div class="ue-content-title">
					<?php echo esc_html__("Knowledge Base", "unlimited-elements-for-elementor"); ?>
				</div>
				<div class="ue-content-desc">
					<?php echo esc_html__("Looking for answers or helpful resources about our WordPress plugin? Check out our comprehensive Knowledge Base, where you'll find step-by-step guides, troubleshooting tips, and useful information to make the most of our plugin's features.", "unlimited-elements-for-elementor"); ?>
				</div>
				<a class="ue-content-btn ue-flex-center" href="<?php echo GlobalsUC::URL_DOCS; ?>" target="_blank">
					<?php echo esc_html__("Documentation", "unlimited-elements-for-elementor"); ?>
				</a>
			</div>
			<div class="ue-inner-section-right ue-flex-center">
				<img class="ue-illustration" src="<?php echo $imagesUrl . "documentation.svg"; ?>" alt="" />
			</div>
		</div>

		<?php if($isProVersion === false): ?>
			<div class="ue-content-card ue-full-card ue-flex-center">
				<div class="ue-inner-section-left">
					<div class="ue-content-icon ue-flex-center">
						<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
							<path d="m23.43 17.733 1.403.842a.584.584 0 0 1 0 1L14.6 25.715c-.37.221-.83.221-1.2 0l-10.233-6.14a.583.583 0 0 1 0-1l1.403-.842L14 23.391l9.43-5.658Zm0-5.483 1.403.841a.583.583 0 0 1 0 1L14 20.592l-10.833-6.5a.583.583 0 0 1 0-1l1.403-.841L14 17.908l9.43-5.658ZM14.6 1.527l10.233 6.14a.583.583 0 0 1 0 1L14 15.167l-10.833-6.5a.583.583 0 0 1 0-1L13.4 1.526c.37-.222.83-.222 1.2 0Zm-.6 2.36-7.131 4.28L14 12.444l7.131-4.279L14 3.888Z" />
						</svg>
					</div>
					<div class="ue-content-title">
						<?php echo esc_html__("Upgrade to Unlimited Elements Premium", "unlimited-elements-for-elementor"); ?>
					</div>
					<ul class="ue-features-list">
						<li class="ue-feature"><?php echo esc_html__("24/7 Premium Support", "unlimited-elements-for-elementor"); ?></li>
						<li class="ue-feature"><?php echo esc_html__("Copy & Paste Fully Designed Sections", "unlimited-elements-for-elementor"); ?></li>
						<li class="ue-feature"><?php echo esc_html__("30 Day Money Back Guarantee", "unlimited-elements-for-elementor"); ?></li>
						<li class="ue-feature"><?php echo esc_html__("Unique Widget Creator for Your Customer Requests", "unlimited-elements-for-elementor"); ?></li>
						<li class="ue-feature"><?php echo esc_html__("Easy Chat for Any Question", "unlimited-elements-for-elementor"); ?></li>
						<li class="ue-feature"><?php echo esc_html__("Animated Backgrounds & Premium Templates", "unlimited-elements-for-elementor"); ?></li>
					</ul>
					<a class="ue-content-btn ue-flex-center ue-pro-cta-btn" href="<?php echo GlobalsUC::URL_BUY; ?>" target="_blank">
						<?php echo esc_html__("Get Unlimited Elements Premium", "unlimited-elements-for-elementor"); ?>
					</a>
				</div>
				<div class="ue-inner-section-right ue-flex-center">
					<img class="ue-illustration" src="<?php echo $imagesUrl . "upgrade.svg"; ?>" alt="" />
				</div>
			</div>
		<?php endif; ?>

	</div>
	<!-- Main content end -->

	<!-- Sidebar start -->
	<div class="ue-sidebar">
		<div class="ue-cta-post-wrapper">

			<?php if($isProVersion === false): ?>
				<div class="ue-content-card ue-get-pro-cta">
					<div class="ue-cta-bg-overlay"></div>
					<div class="ue-content-icon ue-flex-center">
						<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
							<path d="M2.33887 22.1666H25.6722V24.4999H2.33887V22.1666ZM2.33887 5.83325L8.1722 9.91658L14.0056 2.33325L19.8389 9.91658L25.6722 5.83325V19.8333H2.33887V5.83325ZM4.6722 10.3148V17.4999H23.3389V10.3148L19.3495 13.1073L14.0056 6.1602L8.66158 13.1073L4.6722 10.3148Z" />
						</svg>
					</div>
					<div class="ue-content-title ue-cta-title">Get Unlimited Elements Pro</div>
					<div class="ue-cta-desc">Unlock access to all our premium widgets and features.</div>
					<a href="https://unlimited-elements.com/pricing/"
						target="_blank"
						class="ue-content-btn ue-flex-center ue-pro-cta-btn">Get Unlimited Elements Pro</a>
					<ul class="ue-cta-features-list">
						<li class="ue-cta-feature"><?php echo esc_html__("300+ Premium Widgets", "unlimited-elements-for-elementor"); ?></li>
						<li class="ue-cta-feature"><?php echo esc_html__("25+ Backgrounds", "unlimited-elements-for-elementor"); ?></li>
						<li class="ue-cta-feature"><?php echo esc_html__("Live Copy Paste", "unlimited-elements-for-elementor"); ?></li>
						<li class="ue-cta-feature"><?php echo esc_html__("Mega Menu", "unlimited-elements-for-elementor"); ?></li>
						<li class="ue-cta-feature"><?php echo esc_html__("Mega Slider", "unlimited-elements-for-elementor"); ?></li>
						<li class="ue-cta-feature"><?php echo esc_html__("Dynamic Loop Builder", "unlimited-elements-for-elementor"); ?></li>
						<li class="ue-cta-feature"><?php echo esc_html__("Multi-Source Widgets", "unlimited-elements-for-elementor"); ?></li>
						<li class="ue-cta-feature"><?php echo esc_html__("Calculators", "unlimited-elements-for-elementor"); ?></li>
					</ul>
					<a class="ue-cta-link" href="<?php echo GlobalsUC::URL_FEATURES; ?>" target="_blank">
						<?php echo esc_html__("View All", "unlimited-elements-for-elementor"); ?>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
							<path d="M10.782 7.333 7.206 3.757l.943-.943L13.335 8l-5.186 5.185-.943-.943 3.576-3.575H2.668V7.333h8.114Z" />
						</svg>
					</a>
				</div>
			<?php endif; ?>

			<div class="ue-content-card ue-blog-section">
				<div class="ue-content-icon ue-flex-center">
					<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
						<path d="M23.333 25.667H4.667A1.167 1.167 0 0 1 3.5 24.5v-21c0-.644.522-1.167 1.167-1.167h18.666c.645 0 1.167.523 1.167 1.167v21c0 .644-.522 1.167-1.167 1.167Zm-1.166-2.334V4.667H5.833v18.666h16.334ZM8.167 7h4.666v4.667H8.167V7Zm0 7h11.666v2.333H8.167V14Zm0 4.667h11.666V21H8.167v-2.333Zm7-10.5h4.666V10.5h-4.666V8.167Z" />
					</svg>
				</div>
				<div class="ue-content-title">
					<?php echo esc_html__("Our Blog", "unlimited-elements-for-elementor"); ?>
				</div>
				<div class="ue-post-wrapper">
					<?php foreach($blogItems as $item): ?>
						<a class="ue-post" href="<?php echo $item["url"]; ?>" target="_blank">
							<img class="ue-post-img" src="<?php echo $item["image"]; ?>?ver=<?php echo $version?>" alt="<?php echo esc_attr($item["title"]); ?>" />
							<h3 class="ue-post-title"><?php echo esc_html($item["title"]); ?></h3>
						</a>
					<?php endforeach; ?>
				</div>
				<a class="ue-post-view-more ue-content-btn ue-flex-center" href="<?php echo GlobalsUC::URL_BLOG; ?>" target="_blank">
					<?php echo esc_html__("View More", "unlimited-elements-for-elementor"); ?>
				</a>
			</div>

			<div class="ue-content-card ue-doubly-section">
				<div class="ue-content-icon ue-flex-center">
					<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
						<path d="M8.166 7V3.5c0-.644.523-1.166 1.167-1.166h14c.645 0 1.167.522 1.167 1.167v16.333c0 .644-.523 1.167-1.167 1.167h-3.5V24.5a1.17 1.17 0 0 1-1.174 1.167H4.674A1.168 1.168 0 0 1 3.5 24.5l.003-16.332a1.17 1.17 0 0 1 1.175-1.167h3.488Zm-2.33 2.334-.002 14H17.5v-14H5.836ZM10.5 7.001h9.333v11.666h2.333v-14H10.5v2.334Z" />
					</svg>
				</div>
				<div class="ue-content-title">
					<?php echo esc_html__("Live Copy Paste", "unlimited-elements-for-elementor"); ?>
				</div>
				<div class="ue-content-desc">
					<?php echo esc_html__("Copy and paste fully designed sections from the Unlimited Elements website directly to your website.", "unlimited-elements-for-elementor"); ?>
				</div>
				<a class="ue-content-btn ue-flex-center" href="<?php echo GlobalsUC::URL_DOUBLY; ?>" target="_blank">
					<?php echo esc_html__("See How it Works", "unlimited-elements-for-elementor"); ?>
				</a>
			</div>

		</div>
	</div>
	<!-- Sidebar end -->

</div>
