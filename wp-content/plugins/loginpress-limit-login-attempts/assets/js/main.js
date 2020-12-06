(function ($) {

  'use strict';

  $(function () {

		var bulkIps = [];
		var limitTable = $('#loginpress_limit_login_log').DataTable({
		// 	'columnDefs': [{
		// 		'targets': 0,
		// 		'searchable': false,
		// 		'orderable': false,
		// 		'className': 'dt-body-center',
		// 		'render': function (data, type, full, meta){
		// 				return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';
		// 		}
		//  }],
		 'order': [[1, 'asc']]
		});
		var blackTable = $('#loginpress_limit_login_blacklist').DataTable();
		var whiteTable = $('#loginpress_limit_login_whitelist').DataTable();
		
		// Handle click on "Select all" control
		$('#example-select-all').on('click', function(){
      // Get all rows with search applied
      var rows = limitTable.rows({ 'search': 'applied' }).nodes();
      // Check/uncheck checkboxes for all rows in the table
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
   	});

   	// Handle click on checkbox to set state of "Select all" control
   	$('#loginpress_limit_login_log tbody').on('change', 'input[type="checkbox"]', function(){
      // If checkbox is not checked
      if(!this.checked){
         var el = $('#example-select-all').get(0);
         // If "Select all" control is checked and has 'indeterminate' property
         if(el && el.checked && ('indeterminate' in el)){
            // Set visual state of "Select all" control
            // as 'indeterminate'
            el.indeterminate = true;
         }
      }
	 	});
	 
		// Handle form submission event
		$('#loginpress_limit_bulk_blacklist_submit').on('click', function(e){
				// Iterate over all checkboxes in the table
				limitTable.$('input[type="checkbox"]').each(function(){
					// if(!$.contains(document, this)){
							if(this.checked) {
								// console.log($(this).parent().parent().data('ip'));
								bulkIps.push($(this).parent().parent().data('ip'));
								// Create a hidden element
								// $(form).append(
								// 		$('<input>')
								// 			// .attr('type', 'hidden')
								// 			.attr('name', this.name)
								// 			.val(this.value)
								// );
							}
					// }
				});

				const bulkAction = $('#loginpress_limit_bulk_blacklist').val();
				const _nonce = $('#loginpress__llla_bulk_nonce').val();

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'loginpress_attempts_bulk',
						bulk_action: bulkAction,
						bulk_ips: bulkIps,
						security: _nonce,
					},
					beforeSend: function () {
	
						// tr.find( '.loginpress_autologin_code p' ).html('');
						// tr.find('.autologin-sniper').show();
						// tr.find('.loginpress-attempts-unlock').attr("disabled", "disabled");
						// tr.find('.loginpress-attempts-whitelist').attr("disabled", "disabled");
						// tr.find('.loginpress-attempts-blacklist').attr("disabled", "disabled");
					},
					success: function (response) {
						console.log(response);
						
	
						// $('#loginpress_limit_login_blacklist .dataTables_empty').remove();
						// var blacklist_ip = $('#loginpress_attempts_id_' + id).find('.sorting_1').html();
						// $('#loginpress_attempts_id_' + id).find('td').eq(2).find('.attempts-sniper').remove();
						// var blacklist_user = $('#loginpress_attempts_id_' + id).find('td').eq(2).html();
						// // var blacklist_tr = '<tr id="loginpress_blacklist_id_' + id + '" data-login-blacklist-user="' + id + '" role="row" class="even"><td class="sorting_1">' + blacklist_user + '</td><td>' + blacklist_ip + '</td><td><input class="loginpress-blacklist-clear button button-primary" type="button" value="Clear"></td></tr>';
						// var blacklist_tr = '<tr id="loginpress_blacklist_id_' + id + '" data-login-blacklist-user="' + id + '" role="row" class="even"><td>' + blacklist_ip + '</td><td><input class="loginpress-blacklist-clear button button-primary" type="button" value="Clear"><input type="hidden" class="loginpress__user-bl_nonce" name="loginpress__user-bl_nonce" value="' + _nonce + '"></td></td></tr>';
						// // Remove data from limit attemps table.
						// var row = limitTable.row(el.parents('tr'));
						// row.remove();
	
						// // Add data to black_table.
						// var getNode = $.parseHTML(blacklist_tr);
	
						// blackTable.row.add(getNode[0])
						// 	.draw();
						// var ip = el.closest('tr').attr("data-ip");
						// limitTable.rows('[data-ip="' + ip + '"]').remove().draw(false);
						// if ($('.loginpress_limit_login_log_message').length == 0) {
						// 	$('<div class="loginpress_limit_login_log_message"><span>This IP(<em>' + blacklist_ip + '</em>) is Blacklisted</span></div>').appendTo($('#loginpress_limit_login_log_wrapper'));
						// 	$('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').fadeIn();
						// 	setTimeout(function () {
						// 		$('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').fadeOut();
						// 	}, 500);
						// } else {
						// 	$('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').children('span').html('This IP(<em>' + blacklist_ip + '</em>) is Blacklisted');
						// 	$('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').fadeIn();
						// 	setTimeout(function () {
						// 		$('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').fadeOut();
						// 	}, 500);
						// }
					}
				}); // !Ajax.

				bulkIps = [];				
		});

    // Handle LoginPress - Limit Login Attemps tabs.
    $('.loginpress-limit-login-tab').on('click', function (event) {

      event.preventDefault();

      var target = $(this).attr('href');
      $(target).show().siblings('table').hide();
      $(this).addClass('loginpress-limit-login-active').siblings().removeClass('loginpress-limit-login-active');

      if (target == '#loginpress_limit_login_settings') { // Settings Tab.
        $('#loginpress_limit_login_log_wrapper').hide();
        $('#loginpress_limit_login_whitelist_wrapper').hide();
        $('#loginpress_limit_login_blacklist_wrapper').hide();
        $('#loginpress_limit_login_attempts .form-table').show();
        $('#loginpress_limit_login_attempts .submit').show();
      }

      if (target == '#loginpress_limit_login_log') { // Attempts Log Tab.
        $('#loginpress_limit_login_log_wrapper').show();
        $('#loginpress_limit_login_whitelist_wrapper').hide();
        $('#loginpress_limit_login_blacklist_wrapper').hide();
        $('#loginpress_limit_login_attempts .form-table').hide();
        $('#loginpress_limit_login_attempts .submit').hide();
      }

      if (target == '#loginpress_limit_login_whitelist') { // Whitelist Tab.
        $('#loginpress_limit_login_log_wrapper').hide();
        $('#loginpress_limit_login_whitelist_wrapper').show();
        $('#loginpress_limit_login_blacklist_wrapper').hide();
        $('#loginpress_limit_login_attempts .form-table').hide();
        $('#loginpress_limit_login_attempts .submit').hide();
      }

      if (target == '#loginpress_limit_login_blacklist') { // Blacklist Tab.
        $('#loginpress_limit_login_log_wrapper').hide();
        $('#loginpress_limit_login_whitelist_wrapper').hide();
        $('#loginpress_limit_login_blacklist_wrapper').show();
        $('#loginpress_limit_login_attempts .form-table').hide();
        $('#loginpress_limit_login_attempts .submit').hide();
      }
    });

    // Apply ajax on click attempts tab whitelist button.
    $(document).on("click", "input.loginpress-attempts-whitelist", function (event) {

      event.preventDefault();

      var el = $(this);
      var tr = el.closest('tr');
      var id = tr.attr("data-login-attempt-user");
      var ip = el.closest('tr').attr("data-ip");
      var _nonce = tr.find('.loginpress__user-llla_nonce').val();

      $.ajax({

        url: ajaxurl,
        type: 'POST',
        data: 'id=' + id + '&ip=' + ip + '&action=loginpress_attempts_whitelist' + '&security=' + _nonce,
        beforeSend: function () {

          // tr.find( '.loginpress_autologin_code p' ).html('');
          tr.find('.autologin-sniper').show();
          tr.find('.loginpress-attempts-unlock').attr("disabled", "disabled");
          tr.find('.loginpress-attempts-whitelist').attr("disabled", "disabled");
          tr.find('.loginpress-attempts-blacklist').attr("disabled", "disabled");
        },
        success: function (response) {

          $('#loginpress_limit_login_whitelist .dataTables_empty').remove();
          var white_list_ip = $('#loginpress_attempts_id_' + id).find('td:first-child').html();
          $('#loginpress_attempts_id_' + id).find('td').eq(2).find('.attempts-sniper').remove();
          var white_list_user = $('#loginpress_attempts_id_' + id).find('td').eq(2).html();
          // var whitelist_tr = '<tr id="loginpress_whitelist_id_' + id + '" data-login-whitelist-user="' + id + '" role="row" class="even"><td class="sorting_1">' + white_list_user + '</td><td>' + white_list_ip + '</td><td><input class="loginpress-whitelist-clear button button-primary" type="button" value="Clear"></td></tr>';
          var whitelist_tr = '<tr id="loginpress_whitelist_id_' + id + '" data-login-whitelist-user="' + id + '" role="row" class="even"><td>' + white_list_ip + '</td><td><input class="loginpress-whitelist-clear button button-primary" type="button" value="Clear"><input type="hidden" class="loginpress__user-wl_nonce" name="loginpress__user-wl_nonce" value="' + _nonce + '"></td></tr>';
          // Remove data from limit attemps table.
          var row = limitTable.row(el.parents('tr'));
          row.remove();
          // Add data to white_table.
          var getNode = $.parseHTML(whitelist_tr);
          whiteTable.row.add(getNode[0])
            .draw();
          var ip = el.closest('tr').attr("data-ip");
          limitTable.rows('[data-ip="' + ip + '"]').remove().draw(false);
          if ($('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').length == 0) {
            $('<div class="loginpress_limit_login_log_message"><span>This IP(<em>' + white_list_ip + '</em>) is Whitelisted </span></div>').appendTo($('#loginpress_limit_login_log_wrapper'));
            $('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').fadeIn();
            setTimeout(function () {
              $('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').fadeOut();
            }, 500);
          } else {
            $('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').children('span').html('This IP(<em>' + white_list_ip + '</em>) is Whitelisted');
            $('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').fadeIn();
            setTimeout(function () {
              $('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').fadeOut();
            }, 500);
          }
        }
      }); // !Ajax.

    }); // !click .loginpress-attempts-whitelist.

    // Apply ajax on click attempts tab blacklist button.
    $(document).on("click", "input.loginpress-attempts-blacklist", function (event) {

      event.preventDefault();

      var el = $(this);
      var tr = el.closest('tr');
      var id = tr.attr("data-login-attempt-user");
      var ip = el.closest('tr').attr("data-ip");
      var _nonce = tr.find('.loginpress__user-llla_nonce').val();

      $.ajax({

        url: ajaxurl,
        type: 'POST',
        data: 'id=' + id + '&ip=' + ip + '&action=loginpress_attempts_blacklist' + '&security=' + _nonce,
        beforeSend: function () {

          // tr.find( '.loginpress_autologin_code p' ).html('');
          tr.find('.autologin-sniper').show();
          tr.find('.loginpress-attempts-unlock').attr("disabled", "disabled");
          tr.find('.loginpress-attempts-whitelist').attr("disabled", "disabled");
          tr.find('.loginpress-attempts-blacklist').attr("disabled", "disabled");
        },
        success: function (response) {

          $('#loginpress_limit_login_blacklist .dataTables_empty').remove();
          var blacklist_ip = $('#loginpress_attempts_id_' + id).find('td:first-child').html();
          $('#loginpress_attempts_id_' + id).find('td').eq(2).find('.attempts-sniper').remove();
          var blacklist_user = $('#loginpress_attempts_id_' + id).find('td').eq(2).html();
          // var blacklist_tr = '<tr id="loginpress_blacklist_id_' + id + '" data-login-blacklist-user="' + id + '" role="row" class="even"><td class="sorting_1">' + blacklist_user + '</td><td>' + blacklist_ip + '</td><td><input class="loginpress-blacklist-clear button button-primary" type="button" value="Clear"></td></tr>';
          var blacklist_tr = '<tr id="loginpress_blacklist_id_' + id + '" data-login-blacklist-user="' + id + '" role="row" class="even"><td>' + blacklist_ip + '</td><td><input class="loginpress-blacklist-clear button button-primary" type="button" value="Clear"><input type="hidden" class="loginpress__user-bl_nonce" name="loginpress__user-bl_nonce" value="' + _nonce + '"></td></td></tr>';
          // Remove data from limit attemps table.
          var row = limitTable.row(el.parents('tr'));
          row.remove();

          // Add data to black_table.
          var getNode = $.parseHTML(blacklist_tr);

          blackTable.row.add(getNode[0])
            .draw();
          var ip = el.closest('tr').attr("data-ip");
          limitTable.rows('[data-ip="' + ip + '"]').remove().draw(false);
          if ($('.loginpress_limit_login_log_message').length == 0) {
            $('<div class="loginpress_limit_login_log_message"><span>This IP(<em>' + blacklist_ip + '</em>) is Blacklisted</span></div>').appendTo($('#loginpress_limit_login_log_wrapper'));
            $('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').fadeIn();
            setTimeout(function () {
              $('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').fadeOut();
            }, 500);
          } else {
            $('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').children('span').html('This IP(<em>' + blacklist_ip + '</em>) is Blacklisted');
            $('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').fadeIn();
            setTimeout(function () {
              $('#loginpress_limit_login_log_wrapper .loginpress_limit_login_log_message').fadeOut();
            }, 500);
          }
        }
      }); // !Ajax.

    }); // !click .loginpress-attempts-blacklist.

    // Apply ajax on click attempts tab unlock button.
    $(document).on("click", ".loginpress-attempts-unlock", function (event) {

      event.preventDefault();

      var el = $(this);
      var tr = el.closest('tr');
      var id = tr.attr("data-login-attempt-user");
      var ip = el.closest('tr').attr("data-ip");
      var _nonce = tr.find('.loginpress__user-llla_nonce').val();

      $.ajax({

        url: ajaxurl,
        type: 'POST',
        data: 'id=' + id + '&ip=' + ip + '&action=loginpress_attempts_unlock' + '&security=' + _nonce,
        beforeSend: function () {
          // tr.find( '.loginpress_autologin_code p' ).html('');
          tr.find('.autologin-sniper').show();
          tr.find('.loginpress-attempts-unlock').attr("disabled", "disabled");
          tr.find('.loginpress-attempts-whitelist').attr("disabled", "disabled");
          tr.find('.loginpress-attempts-blacklist').attr("disabled", "disabled");
        },
        success: function (response) {
          var ip = el.closest('tr').attr("data-ip");
          limitTable.rows('[data-ip="' + ip + '"]').remove().draw(false);
        }
      }); // !Ajax.

    }); // !click .loginpress-attempts-unlock.


    // Apply ajax on click whitelist tab clear button.
    $(document).on("click", ".loginpress-whitelist-clear", function (event) {

      event.preventDefault();

      var el = $(this);
      var tr = el.closest('tr');
      var id = tr.attr("data-login-whitelist-user");
      var ip = el.parent().prev().html();
      var _nonce = tr.find('.loginpress__user-wl_nonce').val();

      $.ajax({

        url: ajaxurl,
        type: 'POST',
        data: 'id=' + id + '&ip=' + ip + '&action=loginpress_whitelist_clear' + '&security=' + _nonce,
        beforeSend: function () {
          // tr.find( '.loginpress_autologin_code p' ).html('');
          tr.find('.autologin-sniper').show();
          tr.find('.loginpress-whitelist-clear').attr("disabled", "disabled");
        },
        success: function (response) {
          var row = whiteTable.row(el.parents('tr'))
            .remove()
            .draw();
        }
      }); // !Ajax.

    }); // !click .loginpress-whitelist-clear.

    // Apply ajax on click blacklist tab clear button.
    $(document).on("click", ".loginpress-blacklist-clear", function (event) {

      event.preventDefault();

      var el = $(this);
      var tr = el.closest('tr');
      var id = tr.attr("data-login-blacklist-user");
      var ip = el.parent().prev().html();
      var _nonce = tr.find('.loginpress__user-bl_nonce').val();

      $.ajax({

        url: ajaxurl,
        type: 'POST',
        data: 'id=' + id + '&ip=' + ip + '&action=loginpress_blacklist_clear' + '&security=' + _nonce,
        beforeSend: function () {
          // tr.find( '.loginpress_autologin_code p' ).html('');
          tr.find('.autologin-sniper').show();
          tr.find('.loginpress-blacklist-clear').attr("disabled", "disabled");
        },
        success: function (response) {
          var row = blackTable.row(el.parents('tr'))
            .remove()
            .draw();
          // blackTable.rows('.seleted').remove().draw(false);
        }
      }); // !Ajax.

    }); // !click .loginpress-whitelist-clear.

    // Block "+", "-" in input fields.
    $('#loginpress_limit_login_attempts .form-table input[type="number"]').on('keypress', function (evt) {
      if (evt.which != 8 && evt.which != 0 && evt.which < 48 || evt.which > 57) {
        evt.preventDefault();
      }
    });
    $(document).on("submit", "#loginpress_limit_login_attempts form", function (event) {
      $('.ip_add_remove input[type="text"]').val('');
    })

    $(document).on('click', '.add_white_list , .add_black_list', function (el) {
      var ip = $('.ip_add_remove input[type="text"]').val();
      var _security = $(this).data('nonce');
      var action = $(this).data('action');

      $('.ip_add_remove td .message').remove();
      if (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ip)) {
        console.log('ip is  correct');
        $('.ip_add_remove td .message').remove();
      } else {
        $('.ip_add_remove td').append('<p class="message error"> Your IP format is not correct </p>')
        return false;
      }

      var request_data = {
        'security': _security,
        'ip_action': action,
        'ip': ip,
        'action': 'loginpress_white_black_list_ip',
      }
      $.ajax({

        url: ajaxurl,
        type: 'POST',
        data: request_data,
        beforeSend: function () {
          $('.ip_add_remove button').attr('disabled', true)
        },
        success: function (res) {
          if (res.success) {
            $('.ip_add_remove td').append('<p class="message success">' + res.data.message + '</p>')
            refreshIpList('white_list');
            refreshIpList('black_list');
          } else {
            $('.ip_add_remove td').append('<p class="message error">' + res.data.message + '</p>')
          }
        }
      }); // !Ajax.

    });

    /**
     * Get and update list of ip.
     * 
     * @since 1.3.0
     * @param {string} list name on list to update
     */
    function refreshIpList(list) {

      var request_data = {
        'action': 'loginpress_' + list + '_records',
      }
      $.ajax({

        url: ajaxurl,
        type: 'POST',
        data: request_data,
        success: function (res) {

          if (res.success) {
            if (list == 'white_list') {
              let tableWhiteList = '#loginpress_limit_login_whitelist';
              $(tableWhiteList).find('tbody').html(res.data.tbody);
              $(tableWhiteList).DataTable();
            }

            if (list == 'black_list') {
              let tableBlackList = '#loginpress_limit_login_blacklist';
              $(tableBlackList).find('tbody').html(res.data.tbody);
              $(tableBlackList).DataTable();

            }
            $('.ip_add_remove button').attr('disabled', false)

          }
          //var table = jQuery('#loginpress_limit_login_whitelist').dataTable()
          //table.fnClearTable()
        }
      }); // !Ajax.
    }
  });
})(jQuery);
