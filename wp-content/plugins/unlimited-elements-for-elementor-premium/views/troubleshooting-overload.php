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

