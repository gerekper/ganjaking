/**
 *	Copyright (C) 2015-20 CERBER TECH INC., https://wpcerber.com
 */
jQuery(document).ready(function ($) {

    window.crb_scan_id = 0;

    var crb_req_min_delay = 1000; // ms, throttling - making requests to the server not often than

    var crb_scan_mode = '';
    var crb_user_stop = false;
    var crb_scan_in_progress = false;
    var crb_issues_counter = [0, 0, 0, 0];
    var crb_issues_total = 0;

    var crb_response;
    var scanner_data;
    var all_issues = {};

    var crb_scan_requests = 0;
    var crb_server_errors = 0;

    var crb_scan_display = $("#crb-scan-display");
    var crb_scan_controls = $('#crb-scan-controls');
    var crb_file_controls = $('#crb-file-controls');
    var crb_scan_filter = $('#crb-scan-filter');

    var crb_scan_details = $('#crb-scan-details');
    var crb_scan_progress = $('#crb-scan-progress');
    var crb_scan_bar = crb_scan_progress.find('#the-scan-bar');

    var crb_scan_message = $("#crb-scan-message");
    var crb_scan_browser = $("#crb-browse-files");

    var crb_txt_strings = [];
    var crb_the_file;
    var crb_row_id = 0; // For local parent -> child relationship

    if (crb_admin_page === 'cerber-integrity'
        && (crb_admin_tab === '' || crb_admin_tab === 'scan_main')) {
        cerber_scan_load_data();
    }

    crb_scan_controls.find(':button,a').click(function (event) {
        var operation = $(event.target).data('control');
        switch (operation) {
            case 'start_scan':
                cerber_scan_start($(event.target));
                break;
            case 'continue_scan':
                cerber_scan_continue();
                break;
            case 'stop_scan':
                crb_user_stop = true;
                crb_scan_in_progress = false;
                //cerber_scan_controls('stopped');
                //cerber_scan_controls('disabled');
                break;
            case 'delete_file':
            case 'ignore_add_file':
                cerber_scan_bulk_files(operation);
                break;
            case 'full-paths':
                cerber_toggle_file_name(event.target);
                break;
        }

        if (crb_scan_in_progress) {
            window.onbeforeunload = function () {
                return 'Scanning in progress';
            }
        }
        else {
            window.onbeforeunload = null;
        }

        event.preventDefault();
    });

    function cerber_scan_start(object) {

        console.log('Start Scan');
        all_issues = {};
        crb_scan_mode = object.data('mode');
        crb_scan_requests = 0;
        crb_user_stop = false;
        crb_issues_counter = [0, 0, 0, 0];
        crb_issues_total = 0;

        crb_scan_display.find('[data-init]').each(function () {
            $(this).html($(this).data('init'));
        });

        crb_scan_filter.find('.crb-scan-flon').removeClass('crb-scan-flon');

        /*$('#crb-scan-details').find('[data-init]').each(function () {
            $(this).html($(this).data('init'));
        });*/

        crb_scan_message.slideDown().html(crb_scan_msg_steps[0]);
        cerber_update_bar(true);
        cerber_scan_controls('scanning');
        crb_scan_browser.find('tr').not('.crb-scan-container').remove();
        cerber_scan_step('start_scan');
    }

    function cerber_scan_continue() {
        cerber_scan_controls('scanning');
        cerber_scan_step();
    }

    function cerber_scan_step(operation) {
        console.log('Request ' + crb_scan_requests);

        if (!operation) {
            operation = 'continue_scan';
        }

        crb_scan_in_progress = true;
        crb_scan_requests++;

        cerber_rate_control.setState(0);
        setTimeout(function (state) {
            cerber_rate_control.setState(state);
        }, crb_req_min_delay, 1);

        $.post(ajaxurl, {
                action: 'cerber_scan_control',
                cerber_scan_do: operation,
                cerber_scan_mode: crb_scan_mode,
                ajax_nonce: crb_ajax_nonce
            },
            function (server_response) {
                cerber_scan_parse(server_response);
                cerber_scan_display(false);

                if (!crb_user_stop && crb_response.cerber_scan_do !== 'stop') {
                    cerber_scan_next_step();
                }
                else {
                    cerber_scan_ended();
                }

            }
        ).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Server error: ' + jqXHR.status);
            crb_server_errors++;
            if (crb_server_errors < 3) {
                cerber_scan_next_step();
            }
            else {
                cerber_scan_ended();
                alert('Process has been aborted due to server error. Check the browser console for errors.');
            }
        });
    }

    // Continue to scan with rate control
    function cerber_scan_next_step() {
        if (cerber_rate_control.getState()) {
            cerber_scan_step();
        }
        else {
            setTimeout(cerber_scan_step, crb_req_min_delay);
        }
    }

    function cerber_scan_ended(){
        window.onbeforeunload = null;
        crb_scan_in_progress = false;
        cerber_scan_controls('stopped');
        crb_scan_message.slideUp('slow');
        cerber_update_bar();
        if (scanner_data.aborted) {
            var msg = 'Scanning is aborted due to server error. ';
            if (scanner_data.errors && scanner_data.errors.length) {
                msg = msg + scanner_data.errors[0];
            }
            alert(msg);
        }
        else if (!crb_user_stop) {
            cerber_popup_show('The scan is finished', '<p style="text-align: center;">The scan is finished. Please review the results.</p>');
        }
    }

    function cerber_scan_display(no_scroll) {

        if (!scanner_data.started) {
            return;
        }

        var smode = scanner_data.mode;
        if (scanner_data.cloud) {
            smode += ', Scheduled';
        }
        else {
            smode += ', Manual';
        }
        smode = '<span style="text-transform: capitalize;">' + smode + '</span>';

        $("#crb-started").html(scanner_data.started);
        $("#crb-finished").html(scanner_data.finished);
        $("#crb-duration").html(scanner_data.duration);
        $("#crb-performance").html(scanner_data.performance);
        $("#crb-smode").html(smode);

        $.each(scanner_data.numbers, function (type, value) {
            var e = $('#crb-numbers-' + type);
            if (e.length) {
                e.find('.crb-scan-number').html(value);
                e.find('span').addClass('crb-scan-flon');
            }
        });

        $("#crb-total-files").html(scanner_data.total.files);
        $("#crb-scanned-files").html(scanner_data.scanned.files);

        if (!scanner_data.aborted && crb_scan_in_progress) {
            crb_scan_message.html(crb_scan_msg_steps[scanner_data.step]);
        }

        cerber_update_bar();

        // Displaying issues

        var issues;
        if (!scanner_data.issues && scanner_data.step_issues) {
            issues = scanner_data.step_issues;
        }
        else {
            issues = scanner_data.issues;
        }

        $.each(issues, function (section_id, section_data) {
            var the_items = [];

            if (!this.issues.length) {
                //return;
            }

            // Avoid JS undefined error with an old data set
            var vul_list;
            if (typeof section_data.sec_details !== 'undefined') {
                vul_list = section_data.sec_details.vul_list;
            }

            var section_name = section_data.name;
            var setype = section_data.setype;

            var section_header_class = 'crb-scan-section';
            if (section_data.container) {
                section_header_class = section_header_class + ' section-' + section_data.container;
            }

            var section_items = [];
            var issue_type_id, f_name, isize, itime, full_name;
            var risk;
            var rbox;
            var name_classes;
            var version;

            var target_section = crb_scan_browser.find('#' + section_id);

            var parent_section_id  = (target_section.length ? target_section.data('row-id') : crb_row_id);

            var section_header = '<tr id="' + section_id + '" class="' + section_header_class + '" data-row-id="' + crb_row_id + '" data-section-name="' + section_name + '" data-setype="' + setype + '"><td></td><td colspan = 5><span>' + section_name + '</span></td></tr>';

            $.each(this.issues, function (index, single_issue) {
                issue_type_id = single_issue[0];
                f_name = single_issue[1];
                risk = single_issue[2];
                var extra_issue = (single_issue[3] ? single_issue[3] : 0 );

                isize = '';
                if (single_issue.data.size) {
                    isize = single_issue.data.size;
                }
                itime = '';
                if (single_issue.data.time) {
                    itime = single_issue.data.time;
                }
                version = '';
                if (single_issue.data.version) {
                    version = single_issue.data.version;
                }
                full_name = '';
                name_classes = '';
                if (single_issue.data.name) {
                    full_name = single_issue.data.name;
                    name_classes += ' cursor-pointer';
                }

                if (issue_type_id < 10 ) {
                    // Section -------------------------

                    if (issue_type_id === 4) {
                        return; // skip 4
                    }

                    var extra = '';

                    if (vul_list) {
                        extra += '<span class="crb-it-4 scan-ilabel">' + crb_scan_msg_issues[4] + '</span>';
                    }

                    if (issue_type_id === 5) {
                        extra += ' &mdash; ';
                        extra += '<span class="crb-it-' + issue_type_id + '">' + crb_scan_msg_issues[issue_type_id] + '</span>';
                        extra += ' &mdash; <a href="#" class="crb-issue-link" data-itype="' + issue_type_id + '" data-section-name="' + section_name + ' v. ' + version + '">' + crb_txt_strings['explain'][9] + '</a>';
                    }
                    else {
                        extra += '<span class="crb-it-' + issue_type_id + ' scan-ilabel">' + crb_scan_msg_issues[issue_type_id] + '</span>';
                    }

                    var under = '';
                    if (vul_list) {
                        $.each(vul_list, function (index, vuln) {
                            //under += '<i style="font-size: 125%; vertical-align: middle; margin-left: -2px;" class="crb-icon crb-icon-bxs-error-circle"></i> ' + vuln.n + '. Please update the plugins as soon as possible.<br/>';
                            under += vuln.vu_info + '<br/>';
                        });
                        //under += 'Please update the plugins as soon as possible.<br/>';
                        under = '<p class="crb-list-vlnb">' + under + '</p>';
                    }

                    section_header = '<tr id="' + section_id + '" class="' + section_header_class + '" data-row-id="' + crb_row_id + '" data-section-name="' + section_name + '" data-setype="' + setype + '"><td></td><td colspan = 5><span>' + section_name + '</span>' + extra + under + '</td></tr>';
                }
                else {
                    // Single file issue ----------------
                    rbox = '';
                    if (single_issue.data.fd_allowed) {
                        rbox = '<input type="checkbox">';
                    }
                    section_items.push('<tr class="crb-item-file" data-prid="' + parent_section_id + '" data-itype="' + issue_type_id + '" data-iextra="' + extra_issue + '" data-file_name="' + full_name + '"><td>' + rbox + '</td><td data-short="' + f_name + '" class="' + name_classes + '">' + f_name + '</td><td>' + cerber_get_issue_txt(index, single_issue) + '</td><td class="risk' + risk + '"><span>' + crb_scan_msg_risks[risk] + '</span></td><td>' + isize + '</td><td>' + itime + '</td></tr>');
                }

                crb_issues_counter[risk]++;
                if (issue_type_id > 1) {
                    crb_issues_total++;
                }
            });

            if (target_section.length) {
                target_section.after(section_items);
            }
            else {
                section_items.unshift(section_header);
                $.merge(the_items, section_items);
                crb_row_id++;
            }

            if (the_items) {
                var container = null;
                if (this.container) {
                    container = crb_scan_browser.find('#' + this.container);
                }
                if (container && container.length) {
                    container.after(the_items);
                }
                else {
                    crb_scan_browser.append(the_items);
                }
            }


        });

        $("#crb-critical").html(crb_issues_counter[3]);
        $("#crb-warning").html(crb_issues_total);

        if (!crb_scan_in_progress) {
            cerber_file_controls();
        }

        if (!no_scroll) {
            crb_scan_details.animate({scrollTop: crb_scan_details.prop("scrollHeight")}, 500);
        }
    }

    function cerber_scan_parse(server_response) {
        crb_response = $.parseJSON(server_response);
        if (!crb_response) {
            cerber_scan_ended();
            alert('Process has been aborted due to server error. Check the browser console for errors.');
            return false;
        }

        scanner_data = crb_response.cerber_scanner;

        if (scanner_data.issues) {
            all_issues = scanner_data.issues;
        }
        else if (scanner_data.step_issues) {
            $.each(scanner_data.step_issues, function (section_id, value) {
                all_issues[section_id] = value;
            });
        }

        window.crb_scan_id = scanner_data.scan_id;
        if (crb_response.strings) {
            crb_txt_strings = crb_response.strings;
        }

        if (scanner_data.errors && scanner_data.errors.length) {
            scanner_data.errors.forEach(function (item, index) {
                console.error('WP CERBER SCANNER: ' + item);
            });
        }
        if (crb_response.console_log && crb_response.console_log.length) {
            crb_response.console_log.forEach(function (item) {
                console.log('WP CERBER SCANNER: ' + item);
            });
        }
    }

    function cerber_scan_load_data() {
        $.post(ajaxurl, {
                action: 'cerber_scan_control',
                cerber_scan_do: 'get_last_scan',
                ajax_nonce: crb_ajax_nonce
            },
            function (server_response) {
                cerber_scan_parse(server_response);
                //scanner_data.step_issues = [];
                cerber_scan_display(true);
            }
        ).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('WP CERBER SCANNER ERROR: Unable to get scanner data from server. Server error code: ' + jqXHR.status);
        });
    }

    function cerber_get_issue_txt(index, issue) {
        var attr = '';
        var ret = crb_scan_msg_issues[issue[0]];

        if (issue.details.xdata && issue.details.xdata.length) {
            attr += ' data-idx="' + index + '" ';
        }
        if (attr || (issue[0] > 14 && issue[0] < 50)) {
            ret = '<a href="#" ' + attr + '>' + ret + '</a>';
        }

        if (typeof issue.data.prced !== "undefined") {
            ret += '<p>' + crb_scan_msg_issues[issue.data.prced] + '</p>';
        }
        else if (issue[3]) {
            ret += '<p>' + crb_scan_msg_issues[issue[3]] + '</p>';
        }

        return ret;
    }

    // Enable/disable scan controls
    function cerber_scan_controls(state) {
        var stop = $('#crb-stop-scan');
        cerber_file_controls();
        switch (state) {
            case 'scanning':
                crb_scan_controls.find(':button').hide();
                stop.show();
                break;
            case 'stopped':
                crb_scan_controls.find(':button').show();
                stop.hide();
                if (scanner_data.finished) {
                    $('#crb-continue-scan').hide();
                }
                break;
            case 'disabled':
                crb_scan_controls.find(':button').prop( "disabled", true );
                break;
        }
    }

    // Enable/disable file controls
    function cerber_file_controls() {
        var b = crb_file_controls.find(':button');
        if (crb_scan_browser.find('input[type=checkbox]').length) {
            b.show();
        }
        else {
            b.hide();
        }

        var a = crb_scan_controls.find('a');
        if (crb_scan_browser.find('.crb-item-file').length) {
            a.show();
        }
        else {
            a.hide();
        }
    }

    function cerber_update_bar(show) {
        if (!crb_scan_in_progress) {
            if (!show) {
                crb_scan_progress.hide();
            }
            else {
                crb_scan_progress.show();
            }
            crb_scan_bar.width(0);
            return;
        }

        crb_scan_progress.show();
        crb_scan_progress.width('100%');

        if (scanner_data.scanned.files > 0) {
            percentage = 30 + (scanner_data.scanned.files / scanner_data.total.files) * 70;
        }
        else {
            if (scanner_data.step < 3) {
                percentage = 10;
            }
            else {
                percentage = 10 + crb_scan_requests * 5;
            }
        }

        crb_scan_bar.animate({width: percentage + '%'}, 1000);

        //var bar_width = (percentage*crb_scan_bar.parent().width()/100)+'px';
        //crb_scan_bar.width('100px');
        //crb_scan_bar.find('div').animate({width: bar_width}, 1000);
        ///crb_scan_bar.animate({width: bar_width}, 1000);

        //crb_scan_bar.find('div').animate({width: percentage + '%'}, 1000);
        //crb_scan_bar.animate({width: percentage + '%'}, 1000);

    }

    // Rate limiting helper
    var cerber_rate_control = (function () {
        var state = 0;
        var obj = {};
        obj.setState = function (setnew) {
            state = setnew;
        };
        obj.getState = function() {
            return state;
        };
        return obj;
    }());

    function cerber_scan_bulk_files(operation) {
        var selected = crb_scan_browser.find('input[type=checkbox]:checked');
        if (!selected.length) {
            return;
        }
        if (!cerber_user_confirm(crb_scan_msg_misc[operation][0])) {
            return;
        }
        var files = [];
        $.each(selected, function () {
            files.push($(this).closest('tr').data('file_name'));
        });
        cerber_scan_ajax_operation(files, operation);
    }

    function cerber_scan_ajax_operation(files, operation) {
        if (!files.length) {
            return;
        }
        var formData = new FormData();
        formData.append('action', 'cerber_scan_bulk_files');
        formData.append('ajax_nonce', crb_ajax_nonce);
        formData.append('scan_id', window.crb_scan_id);
        formData.append('scan_file_operation', operation);
        if (files instanceof Array) {
            $.each(files, function (index, value) {
                formData.append('files[]', value);
            });
        }
        else {
            formData.append('files[]', files);
        }
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json'
        }).done(function (server_response) {
            var msg = '', title = '';
            if (server_response.errors && server_response.errors.length) {
                title = crb_scan_msg_misc['file_error'];
                msg = '<div style="color: #c91619;"><p><b>' + crb_scan_msg_misc['file_error'] + '</b></p><p>' + server_response.errors.join('</p><p>') + '</p></div>';
            }
            if (server_response.processed && server_response.processed.length) {
                msg = msg + '<div><p><b>' + crb_scan_msg_misc[operation][1] + '</b></p><p>' + server_response.processed.join('</p><p>') + '</p></div>';
            }
            if (!title) {
                title = crb_scan_msg_misc['all_ok'];
            }

            if (server_response.processed && server_response.processed.length) {
                $.each(server_response.processed, function (index, file_name) {
                    //crb_scan_browser.find('td[data-file-name="' + file_name + '"]').parent().remove();
                    crb_scan_browser.find('tr[data-file_name="' + file_name + '"]').remove();
                });
            }

            cerber_popup_show(title, msg);

        }).fail(function (jqXHR, textStatus, errorThrown) {
            cerber_popup_show('Something went wrong on the server', jqXHR.responseText);
        });
    }

    function cerber_toggle_file_name(control) {
        window.cerber_name_toggler = (!window.cerber_name_toggler) ? 1 : 0;
        var full_name, td;
        if (window.cerber_name_toggler) {
            $('.crb-item-file').each(function () {
                full_name = $(this).data('file_name');
                $(this).find('td:nth-child(2)').html(full_name);
            });
        }
        else {
            $('.crb-item-file').each(function () {
                td = $(this).find('td:nth-child(2)');
                td.html($(td).data('short'));
            });
        }
    }


    // Filtering

    crb_scan_filter.on('click', 'span', function (event) {
        if (!$(this).hasClass('crb-scan-flon')) {
            return;
        }

        var all_rows = crb_scan_browser.find('tr');
        all_rows.hide();

        // Single issues
        var show_issues = $(this).data('itype-list');
        if (typeof show_issues !== 'undefined') {
            $(show_issues).each(function (index, value) {
                var filtered_rows = all_rows.filter('.crb-item-file').filter('[data-itype=' + value + '],[data-iextra=' + value + ']');
                filtered_rows.show();
                filtered_rows.each(function () {
                    $(this).prevAll('.crb-scan-section').first().show();
                });
            });
        }

        // Whole sections
        var show_sections = $(this).data('setype-list');
        if (typeof show_sections !== 'undefined') {
            $(show_sections).each(function (index, value) {
                var filtered_sections = all_rows.filter('.crb-scan-section[data-setype=' + value + ']');
                filtered_sections.show();
                filtered_sections.each(function () {
                    // All rows in the section
                    $(this).nextAll('.crb-item-file[data-prid=' + $(this).data('row-id') + ']').show();
                });
            });
        }

    });

    // Popups for an issue

    crb_scan_browser.on('click', 'a', function (event) {
        var issue = $(this).data('itype');
        if (issue && issue === 5) {
            $('#ref-section-name').text($(this).data('section-name'));
            crb_enable_ref_form();
            crb_upload_form_ul.children().hide();
            tb_show(crb_txt_strings['explain'][8], '#TB_inline?width=420&height=400&inlineId=crb-ref-upload-dialog');
            $('#TB_closeWindowButton').blur();
        }
        else {
            cerber_issue_popup(this);
        }
        event.preventDefault();
    });

    function cerber_issue_popup(element) {

        var info = [];
        //var section = $(element).closest('tr').prevAll('.crb-scan-section:first');
        var section = cerber_get_section(element);
        var section_type = section.data('setype');
        var itype = cerber_get_itype(element);
        crb_the_file = cerber_get_ifile(element);

        if (itype === 15 || itype === 18) {
            var section_name = section.data('section-name');
            cerber_popup_show($(element).text(), cerber_get_issue_explain(itype, section_name), true);
            return;
        }

        if (section_type === 20 && itype <= 25) {
            info.push('<p>' + crb_txt_strings['explain'][0] + '</p>');
        }

        // Some data after file inspection?

        var d = cerber_xdata_info(section.prop('id'), $(element).data('idx'));
        if (d.length) {
            info.push(d);
        }

        if (section_type > 20) {
            info.push(cerber_get_issue_explain(itype));
        }

        cerber_popup_show($(element).text(), info, true);

    }

    function cerber_xdata_info(section_id, idx) {

        if (!section_id || typeof idx === 'undefined') {
            return '';
        }

        if (typeof all_issues[section_id].issues[idx].details === 'undefined') {
            return '';
        }

        var xdata = all_issues[section_id].issues[idx].details.xdata;
        if (!xdata.length) {
            return '';
        }

        var itype = all_issues[section_id].issues[idx][0];

        var tokens = [], regs = [], info = '', ls = [];

        $.each(xdata, function (index, e) {
            if (e[0] === 1) {
                tokens.push('<code>Line ' + e[2] + ': <b>' + e[1] + '</b></code><p>' + crb_txt_strings[e[0]][e[1]][1] + '</p>');
            }
            else {
                ls = [];
                $.each(e[2], function (index, s) {
                    ls.push('<code>Line ' + s[2] + ': <b>' + s[0] + '</b></code>');
                });
                regs.push(ls.join('<br />') + '<p>' + crb_txt_strings[e[0]][e[1]] + ' (' + e[1] + ')' + '</p>');
            }
        });

        if (tokens.length) {
            info += '<p><b> ' + crb_txt_strings['explain'][3] + '</b></p><div>' + tokens.join('</div><div>') + '</div>';
        }
        if (regs.length) {
            var title = (itype === 26) ? crb_txt_strings['explain'][5] : crb_txt_strings['explain'][4];

            info += '<p><b>' + title + '</b></p><div>' + regs.join('</div><div>') + '</div>';
        }

        return info;
    }

    // Explainer for end-user
    function cerber_get_issue_explain(itype, subject) {
        if (typeof subject === 'undefined' || !subject) {
            subject = 'WordPress';
        }
        subject = '<b>' + subject + '</b>';
        var ret = [];
        switch (itype) {
            case 15:
                ret.push(crb_txt_strings['explain'][6]);
                ret.push(crb_txt_strings['explain'][7].replace('%s', subject));
                break;
            default:
                ret.push(crb_txt_strings['explain'][1]);
                ret.push(crb_txt_strings['explain'][2].replace('%s', subject));
                break;
        }

        return '<p>'+ ret.join('</p><p>') + '</p>'
    }

    function cerber_get_itype(e) {
        return $(e).closest('tr').data('itype');
    }

    function cerber_get_section(e) {
        return $(e).closest('tr').prevAll('.crb-scan-section:first');
    }

    function cerber_get_ifile(e) {
        return $(e).closest('tr').data('file_name');
    }

    /*
    function cerber_load_strings() {
        $.get(ajaxurl, {
                action: 'cerber_get_strings',
                ajax_nonce: crb_ajax_nonce,
            },
            function (server_response) {
                crb_scan_strings = $.parseJSON(server_response);
                if (!crb_scan_strings.complete) {
                    alert('Unable to load strings due to a server error.');
                }
            }).fail(function () {
            alert('Unable to load strings due to a server error.');
        });
    }*/

    // Uploader

    var crb_upload_form = $('#crb-ref-upload-dialog').find('form');
    var crb_upload_form_ul = $(crb_upload_form).find('ul');

    crb_upload_form.submit(function (event) {

        var formData = new FormData($(this)[0]);
        formData.append('action', 'cerber_ref_upload');
        formData.append('ajax_nonce', crb_ajax_nonce);

        crb_upload_form.find('input').prop('disabled', true);
        crb_upload_form.find('input').hide();
        //crb_upload_form_ul.find('li').not(':nth-child(-n+2)').hide();
        crb_upload_form_ul.children().hide();
        crb_upload_form_ul.find('li:nth-child(1)').show();
        //ref_file_name = $(this).find('input[name="refile"]').val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            enctype: 'multipart/form-data',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json'
        }).done(crb_ref_step2);

        crb_upload_form.trigger('reset');
        event.preventDefault();
    });

    function crb_ref_step2(server_response) {
        if (!server_response.error) {
            crb_upload_form_ul.find('li:nth-child(2)').show();

            $.post(ajaxurl, {
                    action: 'cerber_ref_upload',
                    ajax_nonce: crb_ajax_nonce,
                },
                crb_ref_done,
                'json');
        }
        else {
            crb_ref_done(server_response);
        }
    }

    function crb_ref_done(server_response) {
        crb_ref_errors(server_response);
        if (!server_response.error) {
            tb_remove();
        }
        crb_enable_ref_form();
    }

    function crb_ref_errors(response) {
        if (response.error) {
            crb_upload_form_ul.append('<li>Error: ' + response.error + '</li>');
            crb_upload_form_ul.append('<li style="color: red;">Process aborted</li>');
        }
    }

    function crb_enable_ref_form() {
        crb_upload_form.find('input').prop('disabled', false);
        crb_upload_form.find('input').show();
        crb_upload_form.trigger('reset');
    }

    crb_upload_form.find('input').change(function () {
        crb_upload_form_ul.children().hide();
    });


    // File viewer

    crb_scan_browser.on('click', 'td', function (event) {
        if (typeof $(this).data('short') === "undefined" || $(this).data('short') === '') {
            return;
        }
        //var file_name = $(this).data('file-name');
        var file_name = $(this).closest('tr').data('file_name');

        var view_width = window.innerWidth * 0.8;
        var view_height = window.innerHeight * 0.8;

        tb_show("File: " + file_name, ajaxurl + '?action=cerber_view_file&ajax_nonce=' + crb_ajax_nonce + '&file=' + file_name + '&scan_id=' + window.crb_scan_id + '&sheight=' + view_height + '&width=' + view_width + '&height=' + view_height + '&TB_iframe=1');
        $('#TB_closeWindowButton').blur();

        event.preventDefault();
    });



    //

    function cerber_user_confirm(message) {
        return confirm(message);
    }

    // Simple popups based on WP thickbox

    function cerber_popup_show(title, message, b) {
        if (typeof message !== 'string'){
            message = message.filter(function (e) {
                return (e !== 'undefined' && e !== null && e !== '');
            });
            message = '<div>' + message.join('</div><div>') + '</div>';
        }

        wmax = (window.innerWidth < 600) ? window.innerWidth * 0.9 : window.innerWidth * 0.5;
        hmax = (window.innerHeight < 600) ? window.innerHeight * 0.9 : window.innerHeight * 0.5;

        w = 200 + message.length;
        h = 140 + Math.round(message.length / 2);
        w = (w < 400) ? 400 : w;
        h = (h < 170) ? 170 : h;
        w = (w > wmax) ? wmax : w;
        h = (h > hmax) ? hmax : h;

        var max = h - 70;

        var button = '<input type="button" value="OK" class="button button-primary">';
        if (b) {
            button += '<input type="button" id="add2ignore" value=" Add to ignore list " class="button button-secondary">';
        }

        var popup = cerber_init_popup('crb-popup-box');
        popup.html('<div class="crb-popup-inner" style="max-height: ' + max + 'px;">' + message + '</div>' +
            '<p class="crb-popup-controls">'
            + button
            + '</p>');
        $('#TB_window .crb-popup-inner').html('');
        //popup.find('input[type=button]').off('click');
        popup.find('input[type=button]').on('click', function (event) {
            //$(this).off('click');
            /*event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();*/
            cerber_popup_close(this);
        });

        tb_show(title, '#TB_inline?width=' + w + '&height=' + h + '&inlineId=crb-popup-box');
        $('#TB_closeWindowButton').blur();
    }

    /*
     function cerber_dialog_show(title, message, control_id) {
     var button = '<p style="text-align: center; position: absolute; bottom: 20px; left:0; right:0; margin: auto;"><input type="button" id="' + control_id + '" value=" Yes " class="button button-primary"> <input type="button" value=" Cancel " class="button button-primary"></p>';

     var popup = cerber_init_popup('crb-popup-dialog');
     popup.html('<div style="margin-top: 1em; max-height: 280px; overflow: auto;">' + message + '</div>' + button);
     popup.find('input[type=button]').on('click', function (event) {
     //e.preventDefault();
     cerber_popup_close();
     });

     tb_show(title, '#TB_inline?width=500&height=150&inlineId=crb-popup-dialog');
     $('#TB_closeWindowButton').blur();
     }*/

    function cerber_init_popup(id) {
        var body = $("body");
        var popup = body.find('#' + id);
        if (popup.length) {
            return popup;
        }
        body.append('<div id="' + id + '" style="display: none;"></div>');
        return body.find('#' + id);
    }

    function cerber_popup_close(element) {
        tb_remove();
        if (element.id === 'add2ignore') {
            cerber_scan_ajax_operation(crb_the_file, 'ignore_add_file');
        }
    }

});
