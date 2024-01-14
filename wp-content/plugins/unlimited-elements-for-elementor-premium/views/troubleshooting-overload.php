<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');
?>


<h1>Unlimited Elements - Overload Test</h1>
<br><br>
you should see "test success" text at the end of this page.

<br><br>

put big string, size: 
<?php

$size = 1200000;

$strData = "this is text";
while(strlen($strData) < $size){
	$strData .= " this is text ";
}

echo(strlen($strData)."<br><br>");

?>
<div style="height:300px;overflow:auto;border:1px solid black;padding:5px;">

<?php echo UniteProviderFunctionsUC::escCombinedHtml($strData);?>

</div>

<br><br>
<b>
the test success!!!
</b>
<?php

