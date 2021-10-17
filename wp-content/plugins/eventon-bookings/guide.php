<?php
// @version 0.1
ob_start();?>

<h3>How to use & setup Bookings addon for EventON</h3>

<p>Once installed and activated, on all event edit pages under event tickets settings box you will find a new Yes/no button called <b>Enable booking blocks for this ticket</b> once enable you will find the necessary booking blocks options for this addon. </p>

<h4>How to add booking blocks to an event</h4>
<p>Step 1: Make sure the event you want to add booking blocks have tickets enable and that the event times are in the future.</p>
<p>Step 2: Click <b>Add new booking block</b> under booking blocks for this ticket in event tickets settings box. This will open a new lightbox where you can add block start and end dates and times, price for this block and capacity. Fill in necessary information and click <b>Save</b></p>
<p>Step 3: Once you add enough booking blocks, click publish or update the event</p>

<p>That is it! now you can see the booking blocks available on the frontend of the eventCard. If you are not seeing the booking blocks or the tickets section go to <b>EventON Settings > EventCard Data</b> and make sure the tickets field is enabled and save changes.</p>

<p><b>NOTE:</b> Bookings addon is compatible with Tickets addon and QR Code addon.</p>

<p><b>Requirements:</b> EventON, Tickets addon and Woocommerce</p>

<?php echo ob_get_clean(); ?>