(function ($) {
  // console.info("dci sdk.js loaded");
  $(document).on("click", ".dci-button-allow, .dci-button-skip, .dci-button-disallow", function () {

    let nonce = $(this).closest('.dci-notice-data').find("[name='nonce']").val(),
      dci_name = $(this).closest('.dci-notice-data').find("[name='dci_name']").val(),
      date_name = $(this).closest('.dci-notice-data').find("[name='dci_date_name']").val(),
      allow_name = $(this).closest('.dci-notice-data').find("[name='dci_allow_name']").val();

    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: {
        action: "dci_sdk_insights",
        button_val: this.value,
        nonce: nonce,
        dci_name: dci_name,
        date_name: date_name,
        allow_name: allow_name,
      },
      success: function (response) {
        console.log(response);
        if (response.status == "success") {
          location.reload();
        } else {
          alert(response.message);
        }
      },
    });

  });

  $(document).on("click", ".dci-global-notice .notice-dismiss", function () {

    let nonce = $(this).closest('.dci-notice-data').find("[name='nonce']").val(),
      dci_name = $(this).closest('.dci-notice-data').find("[name='dci_name']").val();

    $.ajax({
      url: ajaxurl,
      type: 'POST',
      data: {
        action: 'dci_sdk_dismiss_notice',
        nonce: nonce,
        dci_name: dci_name,
      },
    });

  });

    // Button Color
    window.CSS.registerProperty({
      name: '--primaryColor',
      syntax: '<color>',
        inherits: false,
        initialValue: '#AA00FF',
      });
      
      window.CSS.registerProperty({
      name: '--secondaryColor',
      syntax: '<color>',
        inherits: false,
        initialValue: '#FF2661',
      });


})(jQuery);

