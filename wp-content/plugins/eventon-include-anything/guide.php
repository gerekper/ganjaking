
<?php
// @version 0.5
ob_start();?>

<h3>How to Use & Setup Include Anything Addon for EventON</h3></br>

<p>You can enable include anything on wordpress posts that are not event posts. And then have those be included in the event calendar.</p>

<p>Step 1: Head over to posts edit page you want to be included in the eventON calendar</p>
<p>Step 2: Activate "Include this post in Event Calendar" and provide necessary data for the post as mentioned in the form. And Save changes</p>
<p>Step 3: Go to a page where you have EventON Calendar enabled. </p>
<p>Step 4: Edit the shortcode or add include_any='yes' to the shortcode. </p>

<p>That is it, now on the front end calendar you should see the non-event post appear in the date range you selected.</p>


<p><b>Requirements:</b> EventON version 4.2 or higher</p>

<?php echo ob_get_clean(); ?>