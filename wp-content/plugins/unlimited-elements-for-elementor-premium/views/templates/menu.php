<div class="ue-root ue-menu">
	<?php if(GlobalsUnlimitedElements::$enableDashboard === true): ?>
		<a
			class="ue-menu-item <?php echo $view === GlobalsUnlimitedElements::VIEW_DASHBOARD ? "ue-active" : ""; ?>"
			href="<?php echo HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_DASHBOARD); ?>"
		>
			<?php echo esc_html__("Home", "unlimited-elements-for-elementor"); ?>
			<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
				<path d="M15.167 22.168h7V11.642L14 5.29l-8.167 6.352v10.526h7v-7h2.334v7Zm9.333 1.166c0 .645-.522 1.167-1.167 1.167H4.667A1.167 1.167 0 0 1 3.5 23.334V11.071c0-.36.166-.7.45-.92l9.334-7.26a1.166 1.166 0 0 1 1.432 0l9.333 7.26c.285.22.451.56.451.92v12.263Z" />
			</svg>
		</a>
	<?php endif ?>
	<a
		class="ue-menu-item <?php echo $view === GlobalsUnlimitedElements::VIEW_ADDONS_ELEMENTOR ? "ue-active" : ""; ?>"
		href="<?php echo HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_ADDONS_ELEMENTOR); ?>"
	>
		<?php echo esc_html__("Widgets", "unlimited-elements-for-elementor"); ?>
		<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
			<path d="M12.833 5.833h-7v7h7v-7Zm2.334 0v7h7v-7h-7Zm7 9.334h-7v7h7v-7Zm-9.334 7v-7h-7v7h7ZM3.5 3.5h21v21h-21v-21Z" />
		</svg>
	</a>
	<?php if(HelperProviderUC::isBackgroundsEnabled() === true): ?>
		<a
			class="ue-menu-item <?php echo $view === GlobalsUnlimitedElements::VIEW_BACKGROUNDS ? "ue-active" : ""; ?>"
			href="<?php echo HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_BACKGROUNDS); ?>"
		>
			<?php echo esc_html__("Backgrounds", "unlimited-elements-for-elementor"); ?>
			<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
				<path d="M3.49 24.5c-.638 0-1.157-.52-1.157-1.159V4.659c0-.64.532-1.159 1.158-1.159H24.51c.639 0 1.157.52 1.157 1.159v18.682c0 .64-.531 1.159-1.157 1.159H3.49Zm19.843-7V5.833H4.668v16.334L16.334 10.5l7 7Zm0 3.3-7-7-8.366 8.367h15.367V20.8Zm-14-7.967a2.333 2.333 0 1 1 0-4.666 2.333 2.333 0 0 1 0 4.666Z" />
			</svg>
		</a>
	<?php endif; ?>
	<a
		class="ue-menu-item <?php echo $view === GlobalsUnlimitedElements::VIEW_TEMPLATES_ELEMENTOR ? "ue-active" : ""; ?>"
		href="<?php echo HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_TEMPLATES_ELEMENTOR); ?>"
	>
		<?php echo esc_html__("Templates", "unlimited-elements-for-elementor"); ?>
		<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
			<path d="m6.724 9.333-.345-.852a1.167 1.167 0 0 1 .645-1.519l10.817-4.37a1.167 1.167 0 0 1 1.519.645l6.556 16.225a1.166 1.166 0 0 1-.645 1.519l-10.817 4.37a1.167 1.167 0 0 1-1.519-.644l-.103-.255v.048H8.165A1.167 1.167 0 0 1 7 23.333v-.314l-3.908-1.58a1.167 1.167 0 0 1-.645-1.518L6.724 9.333Zm2.608 12.834h2.577l-2.577-6.378v6.378Zm-2.333-7.285-1.952 4.832 1.952.83v-5.662ZM8.98 8.689 14.66 22.75l8.654-3.496-5.681-14.063L8.98 8.69Zm3.474 2.37a1.167 1.167 0 1 1-.874-2.163 1.167 1.167 0 0 1 .874 2.164Z" />
		</svg>
	</a>
	<?php if(HelperProviderUC::isFormEntriesEnabled() === true): ?>
		<a
			class="ue-menu-item <?php echo $view === GlobalsUnlimitedElements::VIEW_FORM_ENTRIES ? "ue-active" : ""; ?>"
			href="<?php echo HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_FORM_ENTRIES); ?>"
		>
			<?php echo esc_html__("Form Entries", "unlimited-elements-for-elementor"); ?>
			<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
				<path d="m23.43 17.733 1.403.842a.584.584 0 0 1 0 1L14.6 25.715c-.37.221-.83.221-1.2 0l-10.233-6.14a.583.583 0 0 1 0-1l1.403-.842L14 23.391l9.43-5.658Zm0-5.483 1.403.841a.583.583 0 0 1 0 1L14 20.592l-10.833-6.5a.583.583 0 0 1 0-1l1.403-.841L14 17.908l9.43-5.658ZM14.6 1.527l10.233 6.14a.583.583 0 0 1 0 1L14 15.167l-10.833-6.5a.583.583 0 0 1 0-1L13.4 1.526c.37-.222.83-.222 1.2 0Zm-.6 2.36-7.131 4.28L14 12.444l7.131-4.279L14 3.888Z" />
			</svg>
		</a>
	<?php endif; ?>
	<a
		class="ue-menu-item <?php echo $view === GlobalsUnlimitedElements::VIEW_SETTINGS_ELEMENTOR ? "ue-active" : ""; ?>"
		href="<?php echo HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_SETTINGS_ELEMENTOR); ?>"
	>
		<?php echo esc_html__("Settings", "unlimited-elements-for-elementor"); ?>
		<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
			<path d="m10.135 4.666 3.041-3.041a1.167 1.167 0 0 1 1.65 0l3.041 3.04h4.3c.645 0 1.168.523 1.168 1.167v4.301l3.04 3.041a1.167 1.167 0 0 1 0 1.65l-3.04 3.041v4.3c0 .645-.523 1.167-1.167 1.167h-4.3l-3.042 3.041a1.167 1.167 0 0 1-1.65 0l-3.04-3.04H5.834a1.167 1.167 0 0 1-1.167-1.167v-4.301l-3.041-3.041a1.167 1.167 0 0 1 0-1.65l3.04-3.041v-4.3c0-.645.523-1.167 1.168-1.167h4.3ZM7.001 6.999v4.1l-2.9 2.9 2.9 2.9v4.1h4.1l2.9 2.9 2.9-2.9H21v-4.1l2.9-2.9-2.9-2.9V7h-4.1l-2.9-2.9-2.9 2.9h-4.1Zm7 11.667a4.667 4.667 0 1 1 0-9.334 4.667 4.667 0 0 1 0 9.334Zm0-2.334a2.333 2.333 0 1 0 0-4.666 2.333 2.333 0 0 0 0 4.666Z" />
		</svg>
	</a>
	<a
		class="ue-menu-item"
		href="<?php echo GlobalsUnlimitedElements::$urlAccount; ?>"
	>
		<?php echo esc_html__("Account", "unlimited-elements-for-elementor"); ?>
		<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
			<path d="M4.667 25.667a9.333 9.333 0 0 1 18.666 0H21a7 7 0 0 0-14 0H4.667ZM14 15.167c-3.868 0-7-3.133-7-7 0-3.868 3.132-7 7-7 3.867 0 7 3.132 7 7 0 3.867-3.133 7-7 7Zm0-2.334a4.665 4.665 0 0 0 4.666-4.666A4.665 4.665 0 0 0 14 3.5a4.665 4.665 0 0 0-4.667 4.667A4.665 4.665 0 0 0 14 12.833Z" />
		</svg>
	</a>
	<a
		class="ue-menu-item"
		href="<?php echo GlobalsUC::URL_SUPPORT; ?>"
		target="_blank"
	>
		<?php echo esc_html__("Support", "unlimited-elements-for-elementor"); ?>
		<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
			<path d="M23.261 9.333H24.5a2.333 2.333 0 0 1 2.333 2.334v4.666a2.333 2.333 0 0 1-2.333 2.334H23.26c-.574 4.604-4.502 8.166-9.261 8.166V24.5a7 7 0 0 0 7-7v-7a7 7 0 1 0-14 0v8.167H3.5a2.333 2.333 0 0 1-2.333-2.334v-4.666A2.333 2.333 0 0 1 3.5 9.333h1.239c.574-4.604 4.501-8.166 9.26-8.166 4.76 0 8.688 3.562 9.262 8.166ZM3.5 11.667v4.666h1.167v-4.666H3.5Zm19.833 0v4.666H24.5v-4.666h-1.167Zm-14.28 6.749 1.236-1.98A6.968 6.968 0 0 0 14 17.5c1.364 0 2.636-.39 3.711-1.063l1.237 1.979A9.29 9.29 0 0 1 14 19.833a9.29 9.29 0 0 1-4.947-1.417Z" />
		</svg>
	</a>
</div>
