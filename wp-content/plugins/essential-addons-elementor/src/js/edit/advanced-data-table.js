class advancedDataTableProEdit {
	constructor() {
		ea.hooks.addAction("advancedDataTable.afterInitPanel", "ea", this.afterPanelInit);
		ea.hooks.addAction("advancedDataTable.panelAction", "ea", this.panelAction);
	}

	afterPanelInit(panel, model, view) {
		setTimeout(() => {
			let select = panel.el.querySelector('[data-setting="ea_adv_data_table_source_remote_table"]');

			if (select != null && select.length == 0) {
				model.attributes.settings.attributes.ea_adv_data_table_source_remote_tables.forEach((opt, index) => {
					select[index] = new Option(opt, opt, false, opt == model.attributes.settings.attributes.ea_adv_data_table_source_remote_table);
				});
			}
		}, 50);

		panel.el.addEventListener("mousedown", function (e) {
			if (e.target.classList.contains("elementor-section-title") || e.target.parentNode.classList.contains("elementor-panel-navigation-tab")) {
				setTimeout(() => {
					let select = panel.el.querySelector('[data-setting="ea_adv_data_table_source_remote_table"]');

					if (select != null && select.length == 0) {
						model.attributes.settings.attributes.ea_adv_data_table_source_remote_tables.forEach((opt, index) => {
							select[index] = new Option(opt, opt, false, opt == model.attributes.settings.attributes.ea_adv_data_table_source_remote_table);
						});
					}
				}, 50);
			}
		});
	}

	panelAction(panel, model, view, event) {
		if (event.target.dataset.event == "ea:advTable:connect") {
			let button = event.target;
			button.innerHTML = "Connecting";

			jQuery.ajax({
				url: localize.ajaxurl,
				type: "post",
				data: {
					action: "connect_remote_db",
					security: localize.nonce,
					host: model.attributes.settings.attributes.ea_adv_data_table_source_remote_host,
					username: model.attributes.settings.attributes.ea_adv_data_table_source_remote_username,
					password: model.attributes.settings.attributes.ea_adv_data_table_source_remote_password,
					database: model.attributes.settings.attributes.ea_adv_data_table_source_remote_database,
				},
				success(response) {
					if (response.connected == true) {
						button.innerHTML = "Connected";

						ea.hooks.doAction(
							"advancedDataTable.updateFromView",
							view,
							{
								ea_adv_data_table_source_remote_connected: true,
								ea_adv_data_table_source_remote_tables: response.tables,
							},
							true
						);

						// reload panel
						panel.content.el.querySelector(".elementor-section-title").click();
						panel.content.el.querySelector(".elementor-section-title").click();

						let select = panel.el.querySelector('[data-setting="ea_adv_data_table_source_remote_table"]');

						select.length = 0;
						response.tables.forEach((opt, index) => {
							select[index] = new Option(opt, opt);
						});
					} else {
						button.innerHTML = "Failed";
					}
				},
				error() {
					button.innerHTML = "Failed";
				},
			});

			setTimeout(() => {
				button.innerHTML = "Connect";
			}, 2000);
		} else if (event.target.dataset.event == "ea:advTable:disconnect") {
			ea.hooks.doAction(
				"advancedDataTable.updateFromView",
				view,
				{
					ea_adv_data_table_source_remote_connected: false,
					ea_adv_data_table_source_remote_tables: [],
				},
				true
			);

			// reload panel
			panel.content.el.querySelector(".elementor-section-title").click();
			panel.content.el.querySelector(".elementor-section-title").click();
		}
	}
}

ea.hooks.addAction("editMode.init", "ea", () => {
	new advancedDataTableProEdit();
});
