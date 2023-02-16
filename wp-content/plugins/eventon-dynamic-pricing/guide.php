<?php
ob_start();
?>

<p><b>Important:</b> At the moment dynamic pricing is only supported with simple event ticket products.</p>
<h3>How to enable dynamic pricing for tickets</h3>
<p>Go to the event edit page for the event where you want to enable dynamic pricing. Under Tickets meta box, towards the end you will find <b>Enable dynamic ticket pricing options for this event</b> </p>

<p>Once enable you can activate several options of the dynamic pricing which are: <b>Activate Separate Logged-in Member Pricing</b>, <b>Activate Time Based Ticket Pricing Blocks</b> and <b>Activate Tickets Unavailable for Sale Time Blocks</b></p>

<h3>Activate Time Based Ticket Pricing Blocks</h3>
<p>Once enable this you will see new options that will allow you to add new pricing block where you can set the price and time range for which the time will be honored.</p>

<h3>Activate Tickets Unavailable for Sale Time Blocks</h3>
<p>Once this option is enabled you can add new unavailable time blocks. The lighbox form will allow that pops to add new unavailable blocks will allow you to set start and end date and time range for which the ticket sales are stopped.</p>


<p><b>More Documentation:</b> http://docs.myeventon.com/documentation/addons/dynamic-pricing/</p>

<p><b>Requirements:</b> EventON, Woocommerce and Tickets Addon</p>

<?php echo ob_get_clean();?>