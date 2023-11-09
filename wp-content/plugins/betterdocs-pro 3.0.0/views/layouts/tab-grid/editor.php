<script>
    jQuery(document).ready(function($) {
        jQuery('.betterdocs-tabs-nav-wrapper a').first().addClass('active');
        jQuery('.betterdocs-tabgrid-content-wrapper').first().addClass('active');
        jQuery('.tab-content-1').addClass('active');
        jQuery('.betterdocs-tabs-nav-wrapper a').click(function (e) {
            e.preventDefault();
            jQuery(this).addClass('active').siblings('a').removeClass('active');
            let selectedTab = jQuery(this).data('toggle-target');
            jQuery('.betterdocs-tabgrid-content-wrapper'+selectedTab).addClass('active').siblings().removeClass('active');
        });
    });
</script>
