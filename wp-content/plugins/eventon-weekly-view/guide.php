<?php
echo "
<h4>Introduction Shortcode Use</h4>
<p>Use this shortcode to add the calendar with week strip: <b>[add_eventon_wv]</b><br/><br/>
You can also use the variable <b>focus_week</b> to show a different focus weel just like the eventon calendar.<br/>
eg. <b>[add_eventon_wv focus_week='3']</b> - this will show events of the current month for the week #3.
</p>

<p>
Other variables that work with this shortcode are <b>event_type, event_type_2, and event_count</b>. Also note you will see easy shortcode buttons for weekly view in EventON shortcode popup in WYSIWYG text editor on pages.
</p>

<h4>How to customize the appearance of weeklyView Section</h4>
<p>Go to <b>myEventON> Settings > Appearance</b> and under WeeklyView Styles you can configure colors for variety of elements in for weeklyView.</p>



<h4>Php template tags</h4>
<p>
&lt;?php<br/> if(function_exists(add_eventon_wv)){<br/>
add_eventon_wv(&#36;args);</br>
}?&gt;
</p>

";
?>