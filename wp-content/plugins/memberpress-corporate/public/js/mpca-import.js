// IIFE
(function($, window, document) {

  // $ is now locally scoped and available

  $(function() {

    // DOM is now ready

    'use strict';

    var $_GET = function(param) {
      var vars = {};
      window.location.href.replace( location.hash, '' ).replace(
        /[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
        function( m, key, value ) { // callback
          vars[key] = value !== undefined ? value : '';
        }
      );

      if ( param ) {
        return vars[param] ? decodeURIComponent(vars[param]) : null;
      }

      return vars;
    };

    var populate_corporate_accounts = function(data,cb) {
      // Empty the select list before re-populating it with ajax
      $('#ca_select').empty().append(' ');

      // Do ajax to query for matching corporate accounts
      $.post(ajaxurl, data, function (res) {

        // TODO why is there a blank option
        $.each(res.corporate_accounts, function() {
          $('#ca_select')
            .append( $('<option></option>').text(this.text).val(this.value) );
        });

        cb();
      });
    };

    var username = $_GET('parent');
    var ca = $_GET('ca');

    if(username && ca) {
      var data = {
        action: 'mpca_find_corporate_accounts',
        username: username
      };

      populate_corporate_accounts(data, function() {
        $('#ca_select').val(ca);
      });
    }

    $('#parent_autocomplete').on('change', function(e, ui) {
      var input = this;
      var selected = this.value;

      var data = {
        action: 'mpca_find_corporate_accounts',
        username: selected
      };

      populate_corporate_accounts(data,function(){});
    });

  });

}(window.jQuery, window, document));
