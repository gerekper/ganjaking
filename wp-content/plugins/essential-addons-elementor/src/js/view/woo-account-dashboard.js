var WooAccountDashboardHandler = function ($scope, $) {
    let $wooAccountDashboardWrapper = $(".eael-account-dashboard-wrapper", $scope);
	
	if( elementorFrontend.isEditMode() ){
		$( '.eael-account-dashboard-navbar li, .woocommerce-orders-table__cell-order-actions .view', $scope ).on('click', function(){
			const woo_nav_class = 'woocommerce-MyAccount-navigation-link';
			let classes = $(this).attr('class').split(' ');
			let target = '';
			
			if( classes.length ) {
				classes.forEach(function(className){
					if( className.includes( woo_nav_class + '--' ) ) {
						target = className.replace( woo_nav_class + '--', '' );
					}
				});
			}
			let $this_attr_class = $(this).attr('class');
			if( $this_attr_class.includes('woocommerce-button') && $this_attr_class.includes('view') ) {
				target = 'view-order';
			}
	
			$(`.eael-account-dashboard-body .${woo_nav_class}`, $scope).removeClass('is-active');
			$(`.eael-account-dashboard-body .${woo_nav_class}--${target}`, $scope).addClass('is-active');
	
			$('.eael-account-dashboard-body .tab-content', $scope).removeClass('active');
			$(`.eael-account-dashboard-body .tab-${target}`, $scope).addClass('active');
			
			let pageHeading = target[0].toUpperCase() + target.substring(1);
			$(`.eael-account-dashboard-header h3`, $scope).html(pageHeading);
		});
	}
};

ea.hooks.addAction("init", "ea", () => {
	if (ea.elementStatusCheck('eaelAccountDashboard')) {
		return false;
	}

	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-woo-account-dashboard.default",
		WooAccountDashboardHandler
	);
});