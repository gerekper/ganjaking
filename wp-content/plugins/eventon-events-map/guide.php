<?php
/** @version 0.2 */
echo "
<p>Use this shortcode to add the calendar with event map: <b>[add_eventon_evmap]</b></p>

<p><b><u>Shortcode and PHP Variable guide</u></b></p>

<p><b>cal_id</b> - Unique calendar ID</p>
<p><b>map_height</b> - Height of the google map in pixels</p>
<p><b>show_allev</b> - Show all events on first load</p>
<p>Use eventON shortcode generator to generate event map shortcodes easily</p>

<br/>
<p>
<b>PHP template tags</b><br/>
&lt;?php<br/> if(function_exists(add_eventon_evmap)){<br/>
add_eventon_evmap(&#36;args);</br>
}?&gt;
</p>

";
?>