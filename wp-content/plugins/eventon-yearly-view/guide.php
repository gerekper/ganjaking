<?php
echo "
<h4>Introduction Shortcode Use</h4>
<p>Use this shortcode to add the calendar with year view: <b>[add_eventon_yv]</b><br/><br/>
You can also use the variable <b>fixed_year</b> to show a different year than current year.<br/>
eg. <b>[add_eventon_yv fixed_year='2020']</b> - this will show the year of 2020.
</p>
<p>
Other variables that work with this shortcode are can be found from shortcode generator. </p>


<h4>Php template tags</h4>
<p>
&lt;?php<br/> if(function_exists(add_eventon_yv)){<br/>
add_eventon_yv(&#36;args);</br>
}?&gt;
</p>

";
?>