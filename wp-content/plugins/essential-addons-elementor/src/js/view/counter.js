
var CounterHandler = function($scope, $) {
	var counter_elem = $scope.find(".eael-counter").eq(0),
		$target = counter_elem.data("target");

	$(counter_elem).waypoint(
		function() {
			$($target).each(function() {
				var v = $(this).data("to"),
					speed = $(this).data("speed"),
					od = new Odometer({
						el: this,
						value: 0,
						duration: speed
					});
				od.render();
				setInterval(function() {
					od.update(v);
				});
			});
		},
		{
			offset: "80%",
			triggerOnce: true
		}
	);
};

jQuery(window).on("elementor/frontend/init", function() {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-counter.default",
		CounterHandler
	);
});
