<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php /** This is a template to be used on the javascript side */ ?>

<h3 class="mepr-page-heading"><?php _e('Description', 'memberpress-developer-tools'); ?></h3>
<p>{{desc}}</p>

<div>&nbsp;</div>
<h3 class="mepr-page-heading"><?php _e('Definition', 'memberpress-developer-tools'); ?></h3>
<p><pre>{{method}} {{url}}</pre></p>
<p><strong><em><?php _e('(Requires Authentication)', 'memberpress-developer-tools'); ?></em></strong></p>

<div>&nbsp;</div>
<h3 class="mepr-page-heading"><?php _e('Example Request', 'memberpress-developer-tools'); ?></h3>
<pre id="mpdt_route_request" class="mpdt_code_display"><code class="bash">{{request}}</code></pre>

<div>&nbsp;</div>
<h3 class="mepr-page-heading"><?php _e('Search Arguments', 'memberpress-developer-tools'); ?></h3>
{{search_args}}

<div>&nbsp;</div>
<h3 class="mepr-page-heading"><?php _e('Update Arguments', 'memberpress-developer-tools'); ?></h3>
{{update_args}}

<div>&nbsp;</div>
<h3 class="mepr-page-heading"><?php _e('Example Response', 'memberpress-developer-tools'); ?></h3>
<pre id="mpdt_route_response" class="mpdt_code_display"><code class="json">{{response}}</code></pre>

