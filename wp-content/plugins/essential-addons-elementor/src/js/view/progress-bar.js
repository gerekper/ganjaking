ea.hooks.addAction("progressBar.initValue", "ea", ($wrap, $layout, $num) => {
	if ($layout == "line_rainbow") {
		jQuery(".eael-progressbar-line-fill", $wrap).css({
			width: $num + "%",
		});
	} else if ($layout == "half_circle_fill") {
		jQuery(".eael-progressbar-circle-half", $wrap).css({
			transform: "rotate(" + $num * 1.8 + "deg)",
		});
	} else if ($layout == "box") {
		jQuery(".eael-progressbar-box-fill", $wrap).css({
			height: $num + "%",
		});
	}
});
