<?php
/** @version 1.5 */
?>
<p><b><u>How to add map using Blocks</u></b></p>
<p>Use EventON Block within your page block editor. Within the EventON block, open shortcode generator and then navigate to EventMap. From there configure the map to your desired settings.</p>
<br/>

<p><b><u>How to add map using shortcode</u></b></p>
<p>Use this shortcode to add the calendar with event map: <b>[add_eventon_evmap]</b></p>

<br/>
<p><b><u>Basic Shortcode Guide</u></b></p>
<p><b>cal_id</b> - Unique calendar ID</p>
<p><b>map_height</b> - Height of the google map in pixels</p>
<p><b>show_allev</b> - Show all events on first load</p>
<p>Use eventON shortcode generator to generate event map shortcodes easily</p>

<br/>
<p><b><u>Add maps using PHP code</u></b></p>
<p>
&lt;?php<br/> if(function_exists(add_eventon_evmap)){<br/>
add_eventon_evmap(&#36;args);</br>
}?&gt;
</p>
