<?php

//Remove Options

if( !defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) exit;

	delete_option("wpuf_select_set");
	delete_option("wpuf_mail_alarm");
	
	delete_option("wpuf_mail_notify");
	delete_option("wpuf_mail_alarm_spam");
	delete_option("wpuf_mail_alarm_hacker");
	
	delete_option("wpuf_mail_alarm_fc");
	delete_option("wpuf_mail_alarm_proxy");
	delete_option("wpuf_mail_alarm_bruteforce");
	
	delete_option("wpuf_recaptcha_sitekey");
	delete_option("wpuf_recaptcha_secretkey");
	delete_option("wpuf_uptimerobot_api");
	
	delete_option("wpuf_security_ban");
	delete_option("wpuf_gzip_comp");
	delete_option("wpuf_page_minifier");
	
	delete_option("wpuf_lazy_load");
	delete_option("wpuf_disable_emojis");
	delete_option("wpuf_remove_jquery_migrate");
	delete_option("wpuf_headtofooter_opt");
	delete_option("wpuf_asydef_attr");
	
	delete_option("wpuf_woo_remove_scripts");
	delete_option("wpuf_remove_bp_scripts");
	delete_option("wpuf_bbp_style_remover");
	
	delete_option("wpuf_header_sec");
	delete_option("wpuf_pingback_disable");
	delete_option("wpuf_proxy_protection");
	
	delete_option("wpuf_comment_sec_wc");
	delete_option("wpuf_content_security");
	delete_option("wpuf_disable_rcp_lgus");
	
	delete_option("wpuf_xr_security");
	delete_option("wpuf_wpscan_protection");
	delete_option("wpuf_sql_protection");
	
	delete_option("wpuf_badbot_protection");
	delete_option("wpuf_fakebot_protection");
	delete_option("wpuf_spam_attacks");
	
	delete_option("wpuf_spam_attacks_general");
	delete_option("wpuf_spam_attacks_bf");
	delete_option("wpuf_author_redirect");
	
	delete_option("wpuf_remove_shortlinks");
	delete_option("wpuf_remove_query_strings");
	delete_option("wpuf_remove_feeds");
	delete_option("wpuf_browser_caching");
	delete_option("wpuf_browser_cache_time");
	
	delete_option("wpuf_access_security");
	delete_option("wpuf_mail_alarm_admin");
	delete_option("wpuf_tor_protection");
	
	delete_option("wpuf_disable_fileedit");
	delete_option("wpuf_spam_attacks_psp");
	delete_option("wpuf_recaptcha_protection_lrl");
	
	delete_option("wpuf_recaptcha_protection_lrl_login");
	delete_option("wpuf_recaptcha_protection_lrl_registration");
	delete_option("wpuf_recaptcha_protection_lrl_lpf");
	
	delete_option('get_ip_list');
	delete_option('get_ua_list');
	delete_option('get_white_list');
	delete_option('get_blocked_country_list');
	
	//***