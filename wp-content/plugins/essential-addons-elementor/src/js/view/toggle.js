ea.hooks.addAction("init", "ea", () => {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-toggle.default",
		($scope, $) => {
			let context = $scope[0];

			// make primary active on init
			context
				.querySelector(".eael-primary-toggle-label")
				.classList.add("active");

			context.querySelector(".eael-toggle-switch").onclick = (e) => {
				e.preventDefault();

				let current = context
					.querySelector(".eael-toggle-content-wrap")
					.classList.contains("primary")
					? "primary"
					: "secondary";

				if (current == "primary") {
					context
						.querySelector(".eael-toggle-content-wrap")
						.classList.remove("primary");
					context
						.querySelector(".eael-toggle-content-wrap")
						.classList.add("secondary");
					context
						.querySelector(".eael-toggle-switch-container")
						.classList.add("eael-toggle-switch-on");
					context
						.querySelector(".eael-primary-toggle-label")
						.classList.remove("active");
					context
						.querySelector(".eael-secondary-toggle-label")
						.classList.add("active");
				} else {
					context
						.querySelector(".eael-toggle-content-wrap")
						.classList.add("primary");
					context
						.querySelector(".eael-toggle-content-wrap")
						.classList.remove("secondary");
					context
						.querySelector(".eael-toggle-switch-container")
						.classList.remove("eael-toggle-switch-on");
					context
						.querySelector(".eael-primary-toggle-label")
						.classList.add("active");
					context
						.querySelector(".eael-secondary-toggle-label")
						.classList.remove("active");
				}

				ea.hooks.doAction("ea-toggle-triggered", context);
			};
		}
	);
});
