<?php
// @version 0.1
ob_start();?>

<h3>How to use & setup Advent Calendar addon for EventON</h3>

<p>On Event Edit page for all the events you will now find a new meta box called "Advent Event" once you install and activate this addon.</p>

<p>Step 1: In event Edit page, enable Advent event for the event. Once enable you can select other various options inside the new event edit setting. Once you made necessary other changes inside Advent events box, save changes.</p>
<p>Step 2: Create a new page or go to edit an existing page, using EventON Shortcode generator create a shortcode. In the shortcode add <code>advent_events="yes"</code> and then save changes. </p>
<p>Step 3: Go to EventON settings > Advent. Here you can select which fields on the eventCard you want to hide before the event date. Make necessary changes in here and save.</p>

<p>That is it, now when you view the page you will see all advent events. And they will be hidden until the events date occur. You can also set specific end and start times for event or make the events all day. If the event have specific start end times, those times will be considered when showing event details.</p>

<p><b>NOTE:</b> Advent calendar is compatible with basic calendar, list versions of calendars, tiles view, dailyview, fullcal and weeklyview.</p>

<p><b>Requirements:</b> EventON version 2.6.2 or higher</p>

<?php echo ob_get_clean(); ?>