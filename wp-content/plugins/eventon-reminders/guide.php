<?php
ob_start();
?>

<h3>How to set up Reminders for RSVP</h3>
<p>Go to EventON RSVP settings > Reminders</a></p>

<p>You can set up reminders for types of attendees. Keep in mind the reminders function use wordpress Cron-jobs to send out emails, so the emailing might not get sent out at the exact time, but little later.</p>

<p>Once you set reminders within Reminder Settings, you will see option to enable them for individual events, in event edit page under RSVP box.</p>

<h3>How to set up Reminders for Tickets</h3>
<p>Go to EventON Tickets settings > Reminders</a></p>

<p>Similar way as above you can configure which reminders to take place in reminder settings for tickets.</p>

<p>Once you set reminders within tickets Reminder Settings, you will see option to enable them for individual events, in event edit page under Tickets box.</p>

<p><b>More Documentation:</b> http://docs.myeventon.com/documentation/addons/reminders/</p>

<p><b>Requirements:</b> EventON and RSVP Addon OR Tickets Addon</p>

<?php echo ob_get_clean();?>