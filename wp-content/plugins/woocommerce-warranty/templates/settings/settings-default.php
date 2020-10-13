<div id="warranty_settings_default">

    <?php WC_Admin_Settings::output_fields( $settings['default'] ); ?>

</div>
<script>
    jQuery("document").ready(function($) {
        $("#warranty_default_type").change(function() {
            $(".show-if-addon_warranty").parents("tr").hide();
            $(".show-if-included_warranty").parents("tr").hide();

            switch ($(this).val()) {

                case "included_warranty":
                    $(".show-if-included_warranty").parents("tr").show();
                    break;

                case "addon_warranty":
                    $(".show-if-addon_warranty").parents("tr").show();
                    break;

            }
        }).change();

        $("#warranty_default_length").change(function() {
            if ( $(this).val() == "limited" ) {
                $("#warranty_default_length_value").parents("tr").show();
                $("#warranty_default_length_duration").parents("tr").show();
            } else {
                $("#warranty_default_length_value").parents("tr").hide();
                $("#warranty_default_length_duration").parents("tr").hide();
            }
        }).change();
    });
</script>