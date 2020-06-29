(function ($, window, document, undefined) {

    var YLC = "ylc",

        // The name of using in .data()
        data_plugin = "plugin_" + YLC,

        // Default options
        defaults = {
            app_id       : '',
            render       : true,
            display_login: true,
            user_info    : {
                user_id     : null,
                user_name   : null,
                user_email  : null,
                gravatar    : null,
                user_type   : null,
                avatar_type : null,
                avatar_image: null,
                current_page: null,
                user_ip     : null
            },
            styles       : {
                bg_color      : '#009EDB',
                x_pos         : 'right',
                y_pos         : 'bottom',
                border_radius : '5px 5px 0 0',
                popup_width   : 370,
                btn_width     : 260,
                btn_height    : 0,
                btn_type      : 'classic',
                form_width    : 260,
                animation_type: 'bounceIn',
                autoplay      : true,
                autoplay_delay: 1000
            }

        },
        wait_interval = null,
        premium = {
            /**
             * End chat options
             */
            end_chat_frontend : function (end_chat) {

                if (end_chat) {
                    var now = new Date();
                    this.trigger_premium('save_user_data', this.data.user.conversation_id, true, now.getTime(), true);
                }

            },
            /**
             * Frontend actions for premium version
             */
            premium_frontend  : function () {

                var self = this,
                    working_send = false,
                    working_eval = false,
                    working_req = false;

                //$(document).off('click', '#YLC_send_btn');
                $(document).on('click', '#YLC_send_btn', function (e) {

                    if (working_send) return false; // Don't allow to send form twice!

                    if (ylc.gdpr) {
                        if (!$('#YLC_gdpr_acceptance').is(':checked')) {
                            self.display_ntf(self.strings.msg.field_empty, 'error', '#YLC_offline_ntf');
                            return false;
                        }
                    }

                    // Display "sending" message
                    self.display_ntf(self.strings.msg.sending + '...', 'sending ', '#YLC_offline_ntf');

                    // Get login form data
                    var form_data = $('#YLC_popup_form').serializeArray(),
                        form_length = form_data.length - 1;

                    $.each(form_data, function (i, f) {

                        // Update current form data
                        self.data.current_form[f.name] = f.value;

                        // Is empty?
                        if (!f.value) {
                            self.display_ntf(self.strings.msg.field_empty, 'error', '#YLC_offline_ntf');
                            return false;
                        }

                        // Is valid email?
                        if (f.name === 'email') {

                            // Invalid email!
                            if (!self.validate_email(f.value)) {
                                self.display_ntf(self.strings.msg.invalid_email, 'error', '#YLC_offline_ntf');
                                return false;
                            }

                        } else if (f.name === 'name') {

                            if (!self.validate_username(f.value)) {
                                self.display_ntf(self.strings.msg.invalid_username, 'error', '#YLC_offline_ntf');
                                return false;
                            }

                        }

                        if (i === form_length) {

                            working_send = true;

                            var send_data = $('#YLC_popup_form').serialize() + '&vendor_id=' + ylc.active_vendor.vendor_id;

                            self.post('ylc_ajax_offline_form', send_data, function (r) {

                                working_send = false;

                                if (r.error) {

                                    self.display_ntf(r.error, 'error', '#YLC_offline_ntf'); // Display error message

                                } else if (r.warn) {

                                    self.display_ntf(r.warn, 'success', '#YLC_offline_ntf'); // Display message

                                    setTimeout(function () {

                                        self.clean_ntf(); // Clean display message

                                        self.minimize(); // Minimize popup

                                    }, 4000);

                                } else {

                                    self.display_ntf(r.msg, 'success', '#YLC_offline_ntf'); // Display message

                                    setTimeout(function () {

                                        self.clean_ntf(); // Clean display message

                                        self.minimize(); // Minimize popup

                                    }, 2000);
                                }

                            });

                        }

                    });

                    return false;

                });

                $(document).on('mouseenter', '#YLC_send_btn', function () {
                    $(this).css('background-color', self.data.primary_hover);
                });
                $(document).on('mouseleave', '#YLC_send_btn', function () {
                    $(this).css('background-color', self.opts.styles.bg_color);
                });

                //$(document).off('click', '#YLC_good_btn, #YLC_bad_btn');
                $(document).on('click', '#YLC_good_btn, #YLC_bad_btn', function (e) {

                    if (working_eval) return false; // Don't allow to send form twice!

                    working_eval = true;

                    self.display_ntf(self.strings.msg.sending + '...', 'sending', '#YLC_end_chat_ntf');

                    var evaluation = ($(this).attr('id') === 'YLC_good_btn') ? 'good' : 'bad',
                        receive_copy = $('#YLC_request_chat').is(':checked') ? 1 : 0;

                    self.post('ylc_ajax_chat_evaluation', {
                        conversation_id: self.data.user.conversation_id,
                        evaluation     : evaluation,
                        receive_copy   : receive_copy,
                        user_email     : self.data.user.user_email,
                        chat_with      : self.data.user.chat_with
                    }, function () {

                        working_eval = false;

                    });

                    self.minimize();

                    return false;

                });

                //$(document).off('click', '#YLC_chat_request');
                $(document).on('click', '#YLC_chat_request', function (e) {

                    if (working_req) return false; // Don't allow to send form twice!

                    working_req = true;

                    self.display_ntf(self.strings.msg.sending + '...', 'sending', '#YLC_end_chat_ntf');

                    var evaluation = '',
                        receive_copy = 1;

                    self.post('ylc_ajax_chat_evaluation', {
                        conversation_id: self.data.user.conversation_id,
                        evaluation     : evaluation,
                        receive_copy   : receive_copy,
                        user_email     : self.data.user.user_email,
                        chat_with      : self.data.user.chat_with
                    }, function () {

                        working_req = false;

                    });

                    self.minimize();

                    return false;

                });

                if (this.opts.styles.btn_type == 'round') {

                    var radius = this.opts.styles.btn_width / 2;

                    $('#YLC_chat_btn.btn-round').css({
                        'color'                : this.data.primary_fg,
                        'background-color'     : this.opts.styles.bg_color,
                        'width'                : this.opts.styles.btn_width + 'px',
                        'height'               : this.opts.styles.btn_width + 'px',
                        '-webkit-border-radius': radius + 'px',
                        '-moz-border-radius'   : radius + 'px',
                        'border-radius'        : radius + 'px',
                        'right'                : (this.opts.styles.x_pos === 'right' ? '20px' : 'auto'),
                        'left'                 : (this.opts.styles.x_pos === 'right' ? 'auto' : '20px'),
                        'top'                  : (this.opts.styles.y_pos === 'top' ? '20px' : 'auto'),
                        'bottom'               : (this.opts.styles.y_pos === 'top' ? 'auto' : '20px')
                    });

                    $('#YLC_chat_btn .chat-ico.chat').css({
                        'width': this.opts.styles.btn_width + 'px'
                    });

                    $('#YLC_chat_btn div').css({
                        'line-height': this.opts.styles.btn_width + 'px'
                    });

                } else {

                    $('#YLC_chat_btn').css({
                        'color'                : this.data.primary_fg,
                        'background-color'     : this.opts.styles.bg_color,
                        'width'                : this.opts.styles.btn_width + 'px',
                        '-webkit-border-radius': this.opts.styles.border_radius,
                        '-moz-border-radius'   : this.opts.styles.border_radius,
                        'border-radius'        : this.opts.styles.border_radius,
                        'right'                : (this.opts.styles.x_pos === 'right' ? '20px' : 'auto'),
                        'left'                 : (this.opts.styles.x_pos === 'right' ? 'auto' : '20px'),
                        'top'                  : (this.opts.styles.y_pos === 'top' ? '0' : 'auto'),
                        'bottom'               : (this.opts.styles.y_pos === 'top' ? 'auto' : '0')
                    });

                }

                $('#YLC_chat').css({
                    '-webkit-border-radius': this.opts.styles.border_radius,
                    '-moz-border-radius'   : this.opts.styles.border_radius,
                    'border-radius'        : this.opts.styles.border_radius,
                    'right'                : (this.opts.styles.x_pos === 'right' ? '40px' : 'auto'),
                    'left'                 : (this.opts.styles.x_pos === 'right' ? 'auto' : '40px'),
                    'top'                  : (this.opts.styles.y_pos === 'top' ? '0' : 'auto'),
                    'bottom'               : (this.opts.styles.y_pos === 'top' ? 'auto' : '0')
                });

                $('#YLC_chat_header').css({
                    'color'           : this.data.primary_fg,
                    'background-color': this.opts.styles.bg_color
                });

                $('.chat-form-btn').css({
                    'color'           : this.data.primary_fg,
                    'background-color': this.opts.styles.bg_color
                });

                if (this.opts.styles.y_pos !== 'bottom') {

                    $('.chat-body').css({

                        '-webkit-border-radius': this.opts.styles.border_radius,
                        '-moz-border-radius'   : this.opts.styles.border_radius,
                        'border-radius'        : this.opts.styles.border_radius
                    });

                }

            },
            /**
             * Calculates Chat Duration
             */
            chat_duration     : function (start_time, now_time) {

                if (now_time == '' || start_time == '') {
                    return '00:00:00'
                }

                var seconds = ((now_time - start_time) * 0.001) >> 0,
                    minutes = seconds / 60 >> 0,
                    hours = minutes / 60 >> 0;

                hours = hours % 60;
                minutes = minutes % 60;
                seconds = seconds % 60;

                hours = (hours < 10) ? '0' + hours : hours;
                minutes = (minutes < 10) ? '0' + minutes : minutes;
                seconds = (seconds < 10) ? '0' + seconds : seconds;

                return hours + ':' + minutes + ':' + seconds;

            },
            /**
             * Save user data into DB
             */
            save_user_data    : function (cnv_id, delete_from_app, end_chat, send_email, callback) {

                var self = this,
                    r = null;

                this.data.ref_cnv.child(cnv_id).once('value', function (snap_cnv) {

                    var exists = (snap_cnv.val() !== null),
                        cnv = snap_cnv.val();

                    if (!exists) {

                        if (callback) {

                            callback({});

                        }

                        return;
                    }

                    var user_id = cnv.user_id,
                        duration = self.trigger_premium('chat_duration', cnv.accepted_at, end_chat);

                    self.data.ref_users.child(user_id).once('value', function (snap_user) {

                        var user_data = snap_user.val();

                        user_data.created_at = cnv.created_at;
                        user_data.evaluation = cnv.evaluation;
                        user_data.duration = duration;
                        user_data.receive_copy = cnv.receive_copy;
                        user_data.send_email = send_email;

                        self.data.ref_msgs.once('value', function (snap_msgs) {

                            var msgs = snap_msgs.val(),
                                total_msgs = msgs ? Object.keys(msgs).length : 0,
                                i = 0,
                                msgs_data = {};

                            if (msgs) {

                                $.each(msgs, function (msg_id, msg) {

                                    i = i + 1;

                                    if (msg.conversation_id === cnv_id) {

                                        msgs_data[msg_id] = msg;

                                        if (delete_from_app)
                                            self.data.ref_msgs.child(msg_id).remove();

                                    }

                                    if (total_msgs === i) {

                                        user_data.msgs = msgs_data;

                                        self.post('ylc_ajax_save_chat', user_data, function (r) {

                                            if (callback)
                                                callback(r);

                                        });

                                    }

                                });

                            } else if (callback) {
                                callback({});
                            }

                            if (delete_from_app) {
                                self.data.ref_users.child(user_id).remove();
                                self.data.ref_cnv.child(cnv_id).remove();
                            }

                        });

                    });

                });

            },
            /**
             * Gravatar
             */
            set_avatar_premium: function (user_type, user_data) {

                if (user_type != 'admin')
                    return 'https://www.gravatar.com/avatar/' + user_data.gravatar + '.jpg?s=60&d=' + ylc.default_user_avatar;

                switch (user_data.avatar_type) {

                    case 'gravatar':
                        return 'https://www.gravatar.com/avatar/' + user_data.gravatar + '.jpg?s=60&d=' + ylc.default_admin_avatar;
                        break;

                    case 'image':
                        return user_data.avatar_image;
                        break;

                    default:

                        if (ylc.company_avatar != '') {

                            return ylc.company_avatar;

                        } else {

                            return this.data.assets_url + '/images/default-avatar-' + user_type + '.png';

                        }

                }

            },
            /**
             * Logged automatically authenticated
             */
            logged_users_auth : function () {

                if (this.opts.user_info.user_name != '' && this.opts.user_info.user_email != '') {

                    $('#YLC_field_name').val(this.opts.user_info.user_name);
                    $('#YLC_msg_name').val(this.opts.user_info.user_name);
                    $('#YLC_field_email').val(this.opts.user_info.user_email);
                    $('#YLC_msg_email').val(this.opts.user_info.user_email);

                }

            },
            /**
             * Chat auto opening
             */
            autoplay          : function () {

                if (this.opts.styles.autoplay_delay > 0) {

                    setTimeout(function () {

                        $('#YLC_chat_btn').click();

                    }, this.opts.styles.autoplay_delay);

                }

            }
        }; // premium options

    function Plugin() {

        this.opts = $.extend(defaults, ylc.defaults);
        this.premium = $.extend({}, premium);

    }

    Plugin.prototype = {

        init              : function () {

            this.data = {
                auth           : null, 		// Firebase auth reference
                ref            : null, 		// Firebase chat reference
                is_mobile      : false,
                active_user_id : 0,
                mode           : "offline",    // Current mode
                logged         : false,        // Logged in?
                assets_url     : ylc.plugin_url,
                animation_delay: 1000,
                show_delay     : ylc.show_delay,
                guest_prefix   : "Guest-",
                primary_fg     : null, 		// Primary foreground
                primary_hover  : null, 		// Primary hover color
                popup_status   : "close",      // Popup status: open, close
                user           : {}, 	        // User data
                current_form   : {}, 	        // Current form data
                online_ops     : {} 	        // Online operators list
            };

            this.strings = ylc.strings;

            this.objs = {
                btn         : null,
                popup       : null,
                popup_header: null,
                cnv         : null
            };

            var self = this;

            this.trigger_premium('logged_users_auth');
            this.trigger_premium('premium_frontend');

            // Is mobile?
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                this.data.is_mobile = true;
            }

            // Get application token
            this.post('ylc_ajax_get_token', {}, function (r) {
                if (!r.error) {
                    self.data.auth_token = r.token;
                    self.render_chat();

                    self.trigger_premium('autoplay');

                }
            });
        },
        /**
         * Authentication
         */
        auth              : function (callback) {

            var self = this;

            if (!this.opts.app_id) {
                console.error('App ID not provided');
                return;
            }

            if (this.data.ref == null) {

                this.data.ref = new Firebase('https://' + this.opts.app_id + '.firebaseIO.com');
                this.data.ref_conn = new Firebase('https://' + this.opts.app_id + '.firebaseIO.com/.info/connected');
                this.data.ref_cnv = new Firebase('https://' + this.opts.app_id + '.firebaseIO.com/chat_sessions');
                this.data.ref_msgs = new Firebase('https://' + this.opts.app_id + '.firebaseIO.com/chat_messages');
                this.data.ref_users = new Firebase('https://' + this.opts.app_id + '.firebaseIO.com/chat_users');
            }

            if (this.opts.display_login) {
                this.login(false, callback);
            } else {
                this.login(true, callback);
            }

        },
        /**
         * Login
         */
        login             : function (new_user, callback) {

            var self = this;

            this.purge_firebase();

            this.manage_connections();

            this.data._new_user = new_user;
            this.data.auth = this.data.ref.authWithCustomToken(this.data.auth_token, function (error) {

                if (error) {

                    console.error(error.code, error.message);

                    self.display_ntf(self.strings.msg.conn_err, 'error', '#YLC_login_ntf');

                } else {

                    self.data.logged = true;

                    self.data.ref_users.once('value', function (snap) {

                        var users = snap.val(),
                            guests = 0,
                            wait = false,
                            i = 0,
                            already_logged = false,
                            re_enter = false;

                        if (users !== null) {

                            var total_user = Object.keys(users).length;

                            $.each(users, function (user_id, user) {

                                i++;

                                if (user) {


                                    if (user.user_type == 'operator' && user.status === 'online') {

                                        if (self.valid_operator(user.vendor_id)) {

                                            self.data.online_ops[user.user_id] = user;

                                        }

                                    } else {

                                        if (user.user_type != 'operator') {

                                            if (user.user_name !== undefined && user.user_id != self.opts.user_info.user_id) {
                                                guests++;
                                            }

                                            if (user.user_email !== undefined && self.data.current_form.user_email !== undefined) {

                                                if (user.user_email == self.data.current_form.user_email && !ylc.frontend_op_access) {

                                                    if (user.user_ip == self.opts.user_info.user_ip) {

                                                        if (user.status === 'online' && user.user_id != self.opts.user_info.user_id) {

                                                            already_logged = true;

                                                        } else {

                                                            re_enter = user.conversation_id;
                                                        }

                                                    } else {

                                                        already_logged = true;

                                                    }

                                                }

                                            }

                                        }

                                    }

                                }

                                if (already_logged) {

                                    self.display_ntf(self.strings.msg.already_logged, 'error', '#YLC_login_ntf');

                                } else {

                                    if (i === total_user) {

                                        if (!self.total_online_ops() && !re_enter) {

                                            self.show_offline();

                                        } else {

                                            if (self.opts.user_info.user_type == 'operator' || guests < ylc.max_guests || ylc.max_guests == 0) {

                                                if (wait_interval != null)
                                                    clearInterval(wait_interval);

                                                if (self.opts.display_login) {

                                                    self.show_login();

                                                } else {

                                                    self.show_cnv(true);

                                                }

                                            } else {

                                                wait = true;
                                                self.show_offline(true);

                                            }

                                        }

                                        self.check_user(self.opts.user_info.user_id, wait, re_enter);

                                    }

                                }

                            });

                        } else {

                            self.show_offline();
                            self.check_user(self.opts.user_info.user_id, false);

                        }

                        if (callback)
                            callback(wait);

                    });

                }

            });

        },
        /**
         * Logout from Firebase
         */
        logout            : function (end_chat) {

            var self = this;

            if (this.data.user.user_id) {

                self.data.ref_user.off();   // Don't listen current user
                self.data.ref_users.off();  // Don't listen users
                self.data.ref_msgs.off();   // Don't listen message anymore

            }

            // Display offline form
            $('.chat-body').hide();
            $('#YLC_end_chat').show();

            if (ylc.is_premium) {

                self.trigger_premium('end_chat_frontend', end_chat);

            } else {

                if (end_chat)
                    self.clear_user_data(self.data.user.conversation_id);

                setTimeout(function () {

                    self.be_offline();
                    self.minimize();

                }, 2000);

            }

            self.objs.cnv.empty();

            self.objs.popup_header.click(function () {

                self.minimize();
                self.objs.popup_header.off('click');

            });

            // Resize window to ensure chat box is responsive
            $(window).trigger('resize');

        },
        /**
         * Just be offline, don't logout completely
         */
        be_offline        : function () {

            // Set mode
            this.data.mode = 'offline';

            if (this.data.ref_user) {

                // Set status offline in Firebase
                this.data.ref_user.child('status').set('offline');

                // Set last online
                this.data.ref_user.child('last_online').set(Firebase.ServerValue.TIMESTAMP);

            }

            // Force user to be offline
            this.check_mode(true);

        },
        /**
         * Change mode if necessary!
         */
        check_mode        : function (force_offline) {

            var last_mode = this.data.mode;

            if (force_offline) {

                // Show offline
                this.show_connecting();

                // Update mode
                this.data.mode = 'offline';

                // No operators online!
            } else if (!this.total_online_ops()) {

                switch (last_mode) { // Last mode

                    // Visitor is trying to login
                    case 'login':

                        this.show_offline(); // Show offline

                        break;

                    // Visitor is in conversation
                    case 'online':

                        if (!ylc.is_premium) {

                            if (this.opts.display_login) {

                                $('#YLC_cnv_reply').addClass('chat-disabled').attr('disabled', 'disabled');

                                this.display_ntf(this.strings.msg.no_op + '!', 'error', '#YLC_popup_ntf');

                            } else {

                                this.show_offline();
                            }

                        }

                        break;
                }

                this.data.mode = 'offline';

                // Some operator(s) online now!
            } else {

                // If last mode was online re-activate reply box and clean notifications
                if (last_mode === 'offline') {

                    // Disable reply box
                    $('#YLC_cnv_reply').removeClass('chat-disabled').removeAttr('disabled');

                    this.clean_ntf(); // Clean notification

                }

                // Update mode
                this.data.mode = (this.opts.display_login && last_mode != 'online') ? 'login' : 'online';

            }


        },
        /**
         * Show offline popup
         */
        show_offline      : function (busy) {

            var self = this;

            this.data.mode = 'offline'; // Update mode

            // Allow displaying?
            if (!this.allow_chatbox())
                return;

            // Update popup wrapper
            self.objs.popup.parent().removeClass().addClass('chat-offline');

            // Render popup body
            $('.chat-body').hide();
            $('#YLC_offline .chat-lead').hide();
            $('#YLC_offline').show();

            if (busy) {

                if (!ylc.show_busy_form) {

                    $('#YLC_popup_form').hide()

                }

                $('#YLC_offline .chat-lead.op-busy').show();

                if (wait_interval != null)
                    clearInterval(wait_interval);

                wait_interval = setInterval(function () {

                    self.data.ref_users.once('value', function (snap) {

                        var users = snap.val(),
                            guests = 0;

                        if (users !== null) {

                            $.each(users, function (user_id, user) {

                                if (user) {


                                    if (user.user_name !== undefined && user.user_type != 'operator' && user.user_id != self.opts.user_info.user_id) {

                                        guests++;
                                    }

                                }

                            });
                        }

                        if (guests < ylc.max_guests) {

                            if (self.opts.display_login)
                                self.login(false);
                            else
                                self.login(true);

                        }

                    });


                }, 30000);

            } else {

                $('#YLC_offline .chat-lead.op-offline').show();

            }

            // Resize window to ensure chat box is responsive
            $(window).trigger('resize');

        },
        /**
         * Show connecting popup
         */
        show_connecting   : function () {

            // Turn back to "connecting" popup
            $('.chat-body').hide();
            $('#YLC_connecting').show();

        },
        /**
         * Show login form in chat box
         */
        show_login        : function () {

            var self = this;

            if (!this.allow_chatbox()) {
                return;
            }

            if (this.opts.display_login && this.total_online_ops() && this.objs.popup) {
                // Update mode
                this.data.mode = 'login';

                // Update popup wrapper
                this.objs.popup.parent().removeClass().addClass('chat-login');

                // Render popup body
                $('.chat-body').hide();
                $('#YLC_login').show();

                // Resize window to ensure chat box is responsive
                $(window).trigger('resize');

                // Login button functions
                $('#YLC_login_btn').hover(
                    function () {
                        $(this).css('background-color', self.data.primary_hover);
                    },
                    function () {
                        $(this).css('background-color', self.opts.styles.bg_color);
                    }
                ).click(function () {

                    self.send_login_form();

                });

                $('#YLC_login_form').keydown(function (e) {

                    if (e.keyCode == 13 && !e.shiftKey) {
                        e.preventDefault();
                        self.send_login_form();
                    }

                });

                // Login can't be shown up right now,
                // So show current mode
            } else {

                if (self.data.mode === 'online') {
                    this.show_cnv();
                } else {
                    this.show_offline();
                }

            }

        },
        /**
         * Send login form
         */
        send_login_form   : function () {

            var self = this;

            if (ylc.chat_gdpr) {
                if (!$('#YLC_chat_gdpr_acceptance').is(':checked')) {
                    self.display_ntf(self.strings.msg.field_empty, 'error', '#YLC_login_ntf');
                    return false;
                }
            }

            // Display "Connecting" message
            this.display_ntf(this.strings.msg.connecting + '...', 'sending', '#YLC_login_ntf');

            // Get login form data
            var form_data = $('#YLC_login_form').serializeArray(),
                form_length = form_data.length - 1;

            // Validate login form
            $.each(form_data, function (i, f) {

                // Update current form data
                self.data.current_form[f.name] = f.value;

                // Is empty?
                if (!f.value) {
                    self.display_ntf(self.strings.msg.field_empty, 'error', '#YLC_login_ntf');
                    return false;
                }

                // Is valid email?
                if (f.name === 'user_email') {

                    // Invalid email!
                    if (!self.validate_email(f.value)) {

                        self.display_ntf(self.strings.msg.invalid_email, 'error', '#YLC_login_ntf');
                        return false;

                    } else {

                        // Create gravatar from email and add current form data
                        self.data.current_form.gravatar = self.md5(f.value);

                    }

                } else {

                    if (!self.validate_username(f.value)) {
                        self.display_ntf(self.strings.msg.invalid_username, 'error', '#YLC_login_ntf');
                        return false;
                    }

                }

                // Log user in now (form is valid)
                if (i === form_length) {

                    setTimeout(function () {

                        self.login(true);

                    }, 10000);
                }

            });

            return;

        },
        /**
         * Check user if exists in Firebase
         */
        check_user        : function (user_id, wait, re_enter) {

            var self = this;

            // User reference
            this.data.ref_user = this.data.ref_users.child(user_id);

            if (wait) {

                this.data.ref_user.child('status').set('wait');

            } else {

                // Get user
                this.data.ref_user.once('value', function (snap) {

                    var user_data = snap.val();

                    // User data must always be object
                    if (!user_data)
                        user_data = {};

                    // Get user now
                    self.get_user(user_id, user_data, re_enter);

                });

                this.data.ref_user.child('chat_with').on('value', function (snap) {

                    var value = snap.val();

                    if (value != null) {

                        self.data.user.chat_with = value;

                    }

                });
            }

            // Check current user connectivity
            this.data.ref_users.on('child_removed', function (snap) {

                var user = snap.val();

                if (!user) {
                    return;
                }

                if (user_id === user.user_id) {
                    self.logout();
                }

            });
        },
        /**
         * Get user from Firebase. If not exists, create new one
         */
        get_user          : function (user_id, user_data, re_enter, callback) {

            var self = this;

            // Get current user data
            if (user_data.user_id) {

                // Get user data
                this.data.user = user_data;

                // Update current mode in Firebase
                this.data.ref_user.child('status').set('online');

                // Update other user data
                this.data.ref_user.child('user_ip').set(self.opts.user_info.user_ip);
                this.data.ref_user.child('current_page').set(self.opts.user_info.current_page);
                this.data.ref_user.child('vendor_id').set(ylc.active_vendor.vendor_id);
                this.data.ref_user.child('vendor_name').set(ylc.active_vendor.vendor_name);
                this.data.ref_user.child('chat_with').set('free');

                // Show conversation
                if (this.total_online_ops()) {
                    this.show_cnv();
                } else {
                    this.show_offline();
                }

                // Check user connection
                this.manage_connections();

                // Now listen users activity
                self.listen_users();

                if (callback)
                    callback();

                // Create new user
            } else if (this.data._new_user === true) {

                // Create new conversation
                var cnv = this.data.ref_cnv.push({
                        user_id     : user_id,
                        created_at  : Firebase.ServerValue.TIMESTAMP,
                        accepted_at : '',
                        evaluation  : '',
                        user_type   : 'visitor',
                        receive_copy: false
                    }),
                    // Prepare user data
                    data = {
                        user_id        : user_id,
                        conversation_id: cnv.key(),
                        last_online    : '',
                        is_mobile      : this.data.is_mobile,
                        chat_with      : 'free',
                        status         : 'online', // Connection status
                        vendor_id      : ylc.active_vendor.vendor_id,
                        vendor_name    : ylc.active_vendor.vendor_name
                    };

                /*if ( ylc.is_premium && ylc.is_front_end && this.opts.styles.autoplay ) {

                 this.trigger_premium( 'autoplay_msg', cnv.key() );

                 }*/

                // Merge with default user data
                for (var i in this.opts.user_info) {
                    data[i] = this.opts.user_info[i];
                }

                // Merge with login form data
                for (var d in this.data.current_form) {
                    data[d] = this.data.current_form[d];
                }

                // Name field is empty? Find a name for user
                if (!data.user_name) {

                    // Use email localdomain part
                    if (data.user_email) {
                        data.user_name = data.user_email.substring(0, data.user_email.indexOf('@'));

                        // Give user a random name
                    } else {
                        data.user_name = this.data.guest_prefix + this.random_id(1000, 5000);
                    }
                }

                // Update user data
                this.data.user = data;

                // Create user in Firebase
                this.data.ref_user.set(data, function (error) {

                    if (!error) {

                        // Show conversation
                        self.show_cnv();

                        // Check this new user connection again
                        self.manage_connections();

                        // Now listen users activity
                        self.listen_users();

                        if (re_enter) {

                            if (ylc.is_premium) {

                                var now = new Date();
                                self.trigger_premium('save_user_data', re_enter, true, now.getTime(), false);

                            } else {

                                self.clear_user_data(re_enter);

                            }

                        }

                    }

                    if (callback)
                        callback();

                });


            } else {

                // Now listen users activity
                self.listen_users();

            }

        },
        /**
         * Show conversation in chat box
         */
        show_cnv          : function (no_anim) {

            var self = this;

            // Update mode
            this.data.mode = 'online';

            // Allow displaying?
            if (!this.allow_chatbox())
                return;

            // Update popup wrapper
            this.objs.popup.parent().removeClass().addClass('chat-online');

            // Render popup body
            $('.chat-body').hide();
            $('#YLC_chat_body').show();

            this.objs.cnv = $('#YLC_cnv');

            // Autosize and focus reply box
            if (!no_anim) {

                $('#YLC_cnv_reply').focus().autosize({append: ''}).trigger('autosize.resize');

            } else {

                setTimeout(function () {

                    $('#YLC_cnv_reply').focus().autosize({append: ''}).trigger('autosize.resize');

                }, this.data.animation_delay);

            }

            // Resize window to ensure chat box is responsive
            $(window).trigger('resize');

            // Listen messages
            this.listen_msgs();

            // Logout (End chat)
            $('#YLC_tool_end_chat').click(function () {

                self.push_msg('-- ' + self.strings.msg.close_msg_user + ' --');
                self.data.ref_cnv.child(self.data.user.conversation_id).child('status').set('closed');
                self.logout(true);

                return;

            });

            this.manage_reply_box(); // Manage reply box

        },
        /**
         * Get users
         */
        listen_users      : function () {

            var self = this;

            this.data.last_changed_id = null;

            // Listen users once in the beginning of page load
            this.data.ref_users.once('value', function (snap) {

                var users = snap.val(),
                    i = 0;

                if (users !== null) {

                    var total_user = Object.keys(users).length;

                    // Reset total ops
                    self.data.online_ops = {};

                    $.each(users, function (user_id, user) {

                        // Increase index
                        i = i + 1;

                        if (user) {

                            if (self.valid_operator(user.vendor_id)) {

                                if (user.user_type === 'operator') {

                                    // Check operator connection
                                    if (user.status === 'online') {

                                        self.data.online_ops[user.user_id] = user;

                                    } else {

                                        delete self.data.online_ops[user.user_id];
                                    }

                                }
                            }
                        }

                        if (i === total_user) { // Last index in the while

                            // Change mode if necessary!
                            self.check_mode();

                            // Listen new users
                            self.listen_new_users();

                        }

                    });

                }

            });
        },
        /**
         * Listen new users
         */
        listen_new_users  : function (callback) {

            var self = this;

            // Add users
            this.data.ref_users.on('value', function (snap) {

                var users = snap.val();

                $.each(users, function (user_id, user) {

                    self.update_user(user);

                });

            });

        },
        /**
         * Update user info in Firebase
         */
        update_user       : function (user, prev_id) {

            if (user) {

                // User is not ready for adding wait for all information added into Firebase
                if (!user.user_id) {
                    return;
                }
            }

            if (user) {

                if (user.conversation_id) {

                    if (user.user_type === 'operator') { // Don't repeat same changes triggered more than once

                        // Increase total operator number
                        if (user.status === 'online') {
                            this.data.online_ops[user.user_id] = user;

                            // Decrease total number of operator
                        } else {
                            delete this.data.online_ops[user.user_id];
                        }

                    }

                    // Change mode if necessary!
                    this.check_mode();

                    // Remove user. It is trash! Because it doesn't have cnv_id
                } else {

                    // Save user data, and then delete from Firebase
                    this.clean_user_data(user.user_id);

                }
            }

            // Update last changed id
            this.data.last_changed_id = prev_id;

        },
        /**
         * Clean user data from Firebase
         */
        clean_user_data   : function (user_id) {

            var self = this,
                ref_user = this.data.ref_users.child(user_id);

            // Remove user from users list
            ref_user.once('value', function (snap) {

                var user = snap.val();

                // Remove user reference
                ref_user.remove();

                // Clean user conversation
                if (user.conversation_id) {
                    self.ref_cnv.child(user.conversation_id);
                }

                // Remove user messages
                self.data.ref_msgs.once('value', function (msg_snap) {

                    var msgs = msg_snap.val();

                    if (msgs) {
                        $.each(msgs, function (msg_id, msg) {

                            if (msg.user_id === user_id) {
                                self.data.ref_msgs.child(msg_id).remove();
                            }

                        });
                    }

                });

            });

        },
        /**
         * Set avatar for user or operator
         */
        set_avatar        : function (user_type, user_data) {

            user_type = (user_type == 'operator') ? 'admin' : 'user';

            if (ylc.is_premium) {

                return this.trigger_premium('set_avatar_premium', user_type, user_data)

            } else {

                return this.data.assets_url + '/images/default-avatar-' + user_type + '.png';

            }

        },
        /**
         * Time template
         */
        time              : function (t, n) {

            return this.strings.time[t] && this.strings.time[t].replace(/%d/i, Math.abs(Math.round(n)));

        },
        /**
         * Time ago function
         */
        timeago           : function (time) {

            if (!time)
                return '';

            var now = new Date(),
                seconds = ((now.getTime() - time) * 0.001) >> 0,
                minutes = seconds / 60,
                hours = minutes / 60,
                days = hours / 24,
                years = days / 365;

            return (
                seconds < 45 && this.time('seconds', seconds) ||
                seconds < 90 && this.time('minute', 1) ||
                minutes < 45 && this.time('minutes', minutes) ||
                minutes < 90 && this.time('hour', 1) ||
                hours < 24 && this.time('hours', hours) ||
                hours < 42 && this.time('day', 1) ||
                days < 30 && this.time('days', days) ||
                days < 45 && this.time('month', 1) ||
                days < 365 && this.time('months', days / 30) ||
                years < 1.5 && this.time('year', 1) ||
                this.time('years', years)
            ) + ' ' + this.strings.time.suffix;

        },
        /**
         * Listen message
         */
        listen_msgs       : function () {

            var self = this;

            // Clear previous listen
            this.data.ref_msgs.off();

            // Get current messages
            this.data.ref_msgs.once('value', function (snap) {

                var msgs = snap.val(),
                    total_msgs = msgs ? Object.keys(msgs).length : 0,
                    i = 1;

                // Load old messages after page refresh
                if (msgs) {

                    $.each(msgs, function (msg_id, msg) {

                        // Update current conversation (front-end only)
                        if (self.data.user.conversation_id == msg.conversation_id) {

                            msg.id = msg_id; // Include msg id

                            self.add_msg(msg); // Add message

                        }

                        // First load
                        msg.first_load = true;

                        // Last msg id
                        if (total_msgs == i) {

                            self.listen_new_msgs(msg_id); // Listen new messages

                        }

                        // Increase index
                        i = i + 1;
                    });

                } else {

                    self.listen_new_msgs();

                }

            });

        },
        /**
         * Listen new messages
         */
        listen_new_msgs   : function (msg_id) {

            var self = this,
                ref_msgs = !msg_id ? self.data.ref_msgs : self.data.ref_msgs.startAt(null, msg_id),
                first = true;

            // Don't ignore first message when you check all messages
            if (!msg_id)
                first = false;

            ref_msgs.on('child_added', function (new_snap) {

                var new_msg = new_snap.val();

                // Include message id
                new_msg.id = new_snap.key();

                // Update current conversation (front-end only)
                if (self.data.user.conversation_id == new_msg.conversation_id) {

                    // Ignore first message
                    if (!first)
                        self.add_msg(new_msg);

                }

                // Show popup when new message arrived!
                if (!first)
                    self.show_popup();

                // Not first message anymore
                first = false;

            });

        },
        /**
         * Add message into conversation
         */
        add_msg           : function (msg) {

            var now = new Date(),
                d = new Date(msg.msg_time), // Chat message date
                t = d.getHours() + ':' + (d.getMinutes() < 10 ? '0' : '') + d.getMinutes(), // Chat message time
                msg_content = this.sanitize_msg(msg.msg),
                msg_time = (d.toDateString() == now.toDateString()) ? t : d.getUTCDate() + ' ' + this.strings.months_short[d.getUTCMonth()] + ', ' + t; // Set message time either time or short date like '21 May'

            // Hide welcome message
            this.objs.cnv.find('.chat-welc').hide();

            if (this.objs.cnv) {

                var css_class = (msg.user_id == this.data.user.user_id) ? ' chat-you' : '',
                    msg_date = d.getUTCDate() + ' ' + this.strings.months[d.getUTCMonth()] + ' ' + d.getUTCFullYear() + ' ' + t,
                    avatar = this.set_avatar(msg.user_type, {
                        gravatar    : msg.gravatar,
                        avatar_type : msg.avatar_type,
                        avatar_image: msg.avatar_image
                    });

                this.objs.cnv.append('<div id="YLC_msg_' + msg.id + '" class="chat-cnv-line' + css_class + '">' +
                    '<div title="' + msg_date + '" class="chat-cnv-time">' + msg_time + '</div>' +
                    '<div class="chat-avatar"><img src="' + avatar + '" /></div>' +
                    '<div class="chat-cnv-msg">' +
                    '<div class="chat-cnv-author">' + msg.user_name + '</div>' + msg_content + '</div>' +
                    '</div>' +
                    '<div class="chat-clear"></div>').scrollTop(this.objs.cnv.prop('scrollHeight'));
            }

        },
        /**
         * Sanitize message
         */
        sanitize_msg      : function (str) {

            var msg, pattern_url, pattern_pseudo_url, pattern_email, pattern_html, pattern_line;

            //removes html tags to avoid malicious code
            var tagsToReplace = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;'
            };

            msg = str.replace(/[&<>]/g, function (i) {
                return tagsToReplace[i] || i;
            });

            //pattern_html = /(<([^>]+)>)/gim;
            //msg = str.replace(pattern_html, '');

            //renders multiline
            pattern_line = /\n/gim;
            msg = msg.replace(pattern_line, '<br />');

            //URLs starting with http://, https://, or ftp://
            pattern_url = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
            msg = msg.replace(pattern_url, '<a href="$1" target="_blank">$1</a>');

            //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
            pattern_pseudo_url = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
            msg = msg.replace(pattern_pseudo_url, '$1<a href="http://$2" target="_blank">$2</a>');

            //Change email addresses to mailto:: links.
            pattern_email = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
            msg = msg.replace(pattern_email, '<a href="mailto:$1">$1</a>');

            return msg;

        },
        /**
         * Manage reply box
         */
        manage_reply_box  : function (last_cnv_id) {

            var self = this,
                writing = false,
                obj_reply = $('#YLC_cnv_reply'),
                fn_delay = (function () {
                    /**
                     * Delay for a specified time
                     */
                    var timer = 0;

                    return function (callback, ms) {
                        clearTimeout(timer);
                        timer = setTimeout(callback, ms);
                    };

                })();

            // First clean typing list in any case!
            this.data.ref_cnv.child(this.data.user.conversation_id + '/typing').remove();

            // Manage reply box
            obj_reply.keydown(function (e) {

                // When clicks ENTER key (but not shift + ENTER )
                if (e.keyCode === 13 && !e.shiftKey) {

                    e.preventDefault();

                    var msg = $(this).val();

                    if (msg) {

                        // Clean reply box
                        $(this).val('').trigger('autosize.resize');

                        // Send message to Firebase
                        self.push_msg(msg);

                        // User isn't typing anymore
                        self.data.ref_cnv.child(self.data.user.conversation_id + '/typing/' + self.data.user.user_id).remove();

                    }

                    // Usual writing..
                } else {

                    // Check if current user (operator & visitor) is typing...
                    if (!writing) {

                        // Don't listen some keys
                        switch (e.keyCode) {
                            case 17     : // ctrl
                            case 18     : // alt
                            case 16     : // shift
                            case 9      : // tab
                            case 8      : // backspace
                            case 224    : // cmd (firefox)
                            case 17     : // cmd (opera)
                            case 91     : // cmd (safari/chrome) Left Apple
                            case 93     : // cmd (safari/chrome) Right Apple
                                return;
                        }

                        // Add user typing list in current conversation
                        self.data.ref_cnv.child(self.data.user.conversation_id + '/typing/' + self.data.user.user_id).set(self.data.user.user_name);

                        // User is writing now
                        writing = true;

                    }

                    // Remove user from typing list after the user has stopped typing
                    // for a specified amount of time
                    fn_delay(function () {

                        // User isn't typing anymore
                        self.data.ref_cnv.child(self.data.user.conversation_id + '/typing/' + self.data.user.user_id).remove();

                        // User isn't writing anymore
                        writing = false;

                    }, 1300);

                }

            });

            // Stop listen last conversation
            if (last_cnv_id) {
                this.data.ref_cnv.child(last_cnv_id + '/typing').off();
            }

            // Check if a user is typing in current conversation...
            this.data.ref_cnv.child(this.data.user.conversation_id + '/typing').on('value', function (snap) {

                var i = 0,
                    users = snap.val(),
                    total_users = (users) ? Object.keys(users).length : 0;

                if (!users) {
                    self.clean_ntf();

                    return;
                }

                $.each(users, function (user_id, user_name) {

                    if (user_id != null && user_id != self.data.user.user_id) {

                        // Show notification
                        self.display_ntf(self.strings.msg.writing.replace(/%s/i, user_name), 'typing', '#YLC_popup_ntf');

                        return; // Don't check other writers
                    }

                    if (total_users === i) { // Last index
                        self.clean_ntf();
                    }

                    i = i + 1; // Increase index

                });
            });


            // Focus on reply box when user click around it
            this.objs.popup.find('.chat-cnv-reply').click(function () {
                obj_reply.focus();
            });


        },
        /**
         * Read current conversation messages and update cnv area (reload messages)
         * It is good to use when user open empty conversation box on user interface
         * and show up old messages
         */
        reload_cnv        : function (cnv_id) {

            var self = this;

            // Get current conversation messages
            this.data.ref_msgs.once('value', function (snap) {

                var now = new Date(),
                    all_msgs = snap.val(),
                    total_msgs = all_msgs ? Object.keys(all_msgs).length : 0,
                    total_user_msgs = 0,
                    i = 1;

                if (all_msgs) {

                    $.each(all_msgs, function (msg_id, msg) {

                        if (msg.conversation_id == cnv_id) {

                            // This message from chat history
                            msg.old_msg = true;

                            // Increase total number of user messages
                            total_user_msgs = total_user_msgs + 1;

                        }

                    });

                }

            });

        },
        /**
         * Create new message
         */
        push_msg          : function (msg) {

            // Push message to Firebase
            this.data.ref_msgs.push({
                user_id        : this.data.user.user_id,
                user_type      : this.data.user.user_type,
                conversation_id: this.data.user.conversation_id,
                user_name      : this.data.user.user_name || this.data.user.user_email,
                gravatar       : this.data.user.gravatar,
                avatar_type    : this.data.user.avatar_type,
                avatar_image   : this.data.user.avatar_image,
                msg            : msg,
                msg_time       : Firebase.ServerValue.TIMESTAMP,
                vendor_id      : ylc.active_vendor.vendor_id,
                read           : false
            });

        },
        /**
         * Get a user data
         */
        get_user_data     : function (user_id, callback) {

            this.data.ref_users.child(user_id).once('value', function (snap) {

                var user = snap.val();

                // Just run callback
                callback(user);

            });
        },
        /**
         * Render button before showing up
         */
        render_chat       : function () {

            var self = this;

            this.data.primary_fg = this.use_white(this.opts.styles.bg_color) ? '#ffffff' : '#444444';
            this.data.primary_hover = this.shade_color(this.opts.styles.bg_color, 7);

            this.objs.btn = $('#YLC_chat_btn');

            // Chat button hover
            this.objs.btn.hover(
                function () {
                    $(this).css('background-color', self.data.primary_hover);

                    if (self.opts.styles.btn_type == 'round' && ylc.button_animation) {

                        var autoWidth = $(this).css('width', 'auto').width();

                        $(this).width(self.opts.styles.btn_width).animate({
                            width: autoWidth + 10
                        }, 250);

                    }

                },
                function () {
                    $(this).css('background-color', self.opts.styles.bg_color);

                    if (self.opts.styles.btn_type == 'round' && ylc.button_animation) {

                        $(this).animate({
                            width: self.opts.styles.btn_width
                        }, 250);

                    }

                }
            );

            // Manage button
            this.objs.btn.click(function () {

                // Hide button
                $(this).hide();

                // Show popup
                self.show_popup();
                self.auth();

            });

            setTimeout(function () {
                self.show_btn();
            }, this.data.show_delay);

            this.show_connecting();

            this.objs.popup = $('#YLC_chat');
            this.objs.popup_header = $('#YLC_chat_header');

            // Send button hover
            $(document).on('hover', '#YLC_send_btn',
                function () {
                    $(this).css('background-color', self.data.primary_hover);
                },
                function () {
                    $(this).css('background-color', self.opts.styles.bg_color);
                }
            );

            /*this.objs.popup_header.click(function () {

             self.be_offline();
             self.minimize();
             //self.objs.popup_header.off('click');

             });*/

            // Set height of chat popup
            $(window).resize(function () {

                var w = window,
                    d = document,
                    e = d.documentElement,
                    g = d.getElementsByTagName('body')[0],
                    x = w.innerWidth || e.clientWidth || g.clientWidth,
                    y = w.innerHeight || e.clientHeight || g.clientHeight,
                    pop_y = self.objs.popup.height(), // Popup header height
                    pop_h_y = self.objs.popup_header.height(), // Popup header height
                    pop_b = parseInt(self.objs.popup.css('bottom'), 10); // Popup bottom


                // Set max height
                var chat_height = pop_y < y ? 'auto': y,
                    max_y =pop_y < y ? 'auto' : y - pop_h_y - pop_b;

                self.objs.popup.css('height', chat_height);
                $('#YLC_chat_body, #YLC_offline').css('height', max_y);

                var win_w = $(window).width();

                if (self.opts.styles.btn_type == 'round') {

                    self.objs.btn.css({
                        'width': self.opts.styles.btn_width + 'px',
                        'left' : (self.opts.styles.x_pos === 'right') ? 'auto' : '20px',
                        'right': (self.opts.styles.x_pos === 'left') ? 'auto' : '20px'
                    });

                } else {

                    if (win_w > 480) {

                        self.objs.btn.css({
                            'width': self.opts.styles.btn_width + 'px',
                            'left' : (self.opts.styles.x_pos === 'right') ? 'auto' : '40px',
                            'right': (self.opts.styles.x_pos === 'left') ? 'auto' : '40px'
                        });

                    } else {

                        self.objs.btn.css({
                            'width': '',
                            'left' : (self.opts.styles.x_pos === 'right') ? 'auto' : 0,
                            'right': (self.opts.styles.x_pos === 'left') ? 'auto' : 0
                        });

                    }

                }

                if (win_w > 480) {

                    self.objs.popup.css({
                        'left' : (self.opts.styles.x_pos === 'right') ? 'auto' : '40px',
                        'right': (self.opts.styles.x_pos === 'left') ? 'auto' : '40px'
                    });

                    $('.chat-body.chat-online').css('width', self.opts.styles.popup_width + 'px');

                    $('.chat-body.chat-form').css('width', self.opts.styles.form_width + 'px');

                } else {

                    self.objs.popup.css({
                        'left' : (self.opts.styles.x_pos === 'right') ? 'auto' : 0,
                        'right': (self.opts.styles.x_pos === 'left') ? 'auto' : 0
                    });

                    $('.chat-body').css('width', '');

                }

            }).trigger('resize');
        },
        /**
         * Show popup
         */
        show_popup        : function () {

            // Don't re-open popup
            if (this.data.popup_status == 'open') return;

            var self = this;

            // Display popup
            this.objs.popup.show();

            // Show popup with animation
            this.animate(this.objs.popup, this.opts.styles.animation_type);

            setTimeout(function () {
                self.objs.popup_header.click(function () {

                    self.be_offline();
                    self.minimize();
                    self.objs.popup_header.off('click');

                });
            }, 3000);

            // Focus on first field in the form
            setTimeout(function () {

                switch (self.data.mode) {

                    // Online mode
                    case 'online':

                        // Focus reply box
                        $('#YLC_cnv_reply').focus();

                        // Scroll down conversation if necessary
                        self.objs.cnv.scrollTop(self.objs.cnv.prop('scrollHeight'));

                        break;

                    // Offline or login mode
                    case 'offline':
                    case 'login':

                        // Focus first input in the form
                        $('#YLC_login_form .chat-line:first-child input').focus();

                        break;
                }

                // Update popup status
                self.data.popup_status = 'open';

            }, this.data.animation_delay);

        },
        /**
         * Show button
         */
        show_btn          : function (title) {

            var self = this;

            // Allow displaying?
            if (!this.allow_chatbox())
                return;

            // Just show btn
            this.objs.btn.show();

            // Update title
            this.objs.btn.find('.chat-title').html(title);

            // Show and animate
            this.animate(this.objs.btn, this.opts.styles.animation_type);

        },
        /**
         * Minimize popup
         */
        minimize          : function () {

            // Update popup status
            this.data.popup_status = 'close';

            // Hide popup
            if (this.objs.popup) {

                this.objs.popup.hide();
                this.show_connecting();
            }


            this.objs.btn.show();

            // Display button
            this.animate(this.objs.btn, this.opts.styles.animation_type);

        },
        /**
         * Manage connections
         */
        manage_connections: function () {

            var self = this;

            if (!this.data.ref_user) {
                return;
            }

            // Manage connections
            this.data.ref_conn.on('value', function (snap) {

                // User is connected (or re-connected)!
                // and things happen here that should happen only if online (or on reconnect)
                if (snap.val() === true) {

                    // Add this device to user's connections list
                    var conn = self.data.ref_user.child('connections').push(true);

                    // When user disconnect, remove this device
                    conn.onDisconnect().remove();

                    // Set online
                    self.data.ref_user.child('status').set('online');

                    // Update user connection status when disconnect
                    self.data.ref_user.child('status').onDisconnect().set('offline');

                    // Update last time user was seen online when disconnect
                    self.data.ref_user.child('last_online').onDisconnect().set(Firebase.ServerValue.TIMESTAMP);

                    // Remove user typing list on disconnect
                    self.data.ref_cnv.child(self.data.user.conversation_id + '/typing/' + self.data.user.user_id).onDisconnect().remove();

                } else {

                    self.show_offline();

                }

            });

        },
        /**
         * Custom POST wrapper
         */
        post              : function (action, data, callback) {

            $.post(ylc.ajax_url + '?action=' + action, data, callback, 'json')
                .fail(function (jqXHR) {

                    // Log error
                    console.log(action, ': ', jqXHR);

                    return false;

                });

        },
        /**
         * Trigger Premium
         */
        trigger_premium   : function (event, p1, p2, p3, p4, p5, p6) {

            if (!ylc.is_premium) {
                return;
            }

            return this.premium[event].call(this, p1, p2, p3, p4, p5, p6);

        },
        /**
         * Display notification
         */
        display_ntf       : function (ntf, type, id) {

            var icon;

            switch (type) {

                case 'success':
                case 'error':
                case 'typing':
                    icon = '<span class="ylc-icons ylc-icons-' + type + '"></span> ';
                    break;
                default:
                    icon = '';

            }

            $(id).removeClass().addClass('chat-ntf chat-' + type).html(icon + ntf).fadeIn(300);

        },
        /**
         * Clean notification
         */
        clean_ntf         : function () {

            $('.chat-ntf').html('').hide();

        },
        /**
         * Clear user data
         */
        clear_user_data   : function (cnv_id, callback) {

            var self = this;

            this.data.ref_cnv.child(cnv_id).once('value', function (snap_cnv) {

                var cnv = snap_cnv.val();

                if (!cnv)
                    return;

                var user_id = cnv.user_id;

                self.data.ref_msgs.once('value', function (snap_msgs) {

                    var msgs = snap_msgs.val(),
                        total_msgs = msgs ? Object.keys(msgs).length : 0,
                        i = 0;

                    if (msgs) {

                        $.each(msgs, function (msg_id, msg) {

                            i = i + 1;

                            if (msg.conversation_id === cnv_id) {

                                self.data.ref_msgs.child(msg_id).remove();

                            }

                            if (total_msgs === i) {

                                if (callback)
                                    callback();

                            }

                        });

                    } else if (callback) {

                        callback();

                    }

                    self.data.ref_users.child(user_id).remove();
                    self.data.ref_cnv.child(cnv_id).remove();

                });

            });

        },
        /**
         * Total number of online operators
         */
        total_online_ops  : function () {

            if (this.data.online_ops) {
                return Object.keys(this.data.online_ops).length;
            } else {
                return 0;
            }

        },
        /**
         * Chatbox allowed to show up?
         */
        allow_chatbox     : function () {

            return this.opts.render ? true : false;

        },
        /**
         * Animate
         */
        animate           : function (obj, anim) {

            $(window).trigger('resize');

            var direction = (this.opts.styles.y_pos === 'top') ? 'Down' : 'Up';


            obj.addClass('chat-anim chat-' + anim + direction);

            // Remove CSS animation
            setTimeout(function () {
                obj.removeClass('chat-anim chat-' + anim + direction);
            }, this.data.animation_delay);
        },
        /**
         * Shade color original code: Pimp Trizkit (http://stackoverflow.com/a/13542669/272478)
         */
        shade_color       : function (color, percent) {
            var num = parseInt(color.slice(1), 16),
                amt = Math.round(2.55 * percent),
                R = (num >> 16) + amt,
                B = (num >> 8 & 0x00FF) + amt,
                G = (num & 0x0000FF) + amt;

            return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 + (B < 255 ? B < 1 ? 0 : B : 255) * 0x100 + (G < 255 ? G < 1 ? 0 : G : 255)).toString(16).slice(1);
        },
        /**
         * Check if foreground color should be white? original code: Alnitak (http://stackoverflow.com/a/12043228/272478)
         */
        use_white         : function (c) {
            var c = c.substring(1);      // strip #
            var rgb = parseInt(c, 16);   // convert rrggbb to decimal
            var r = (rgb >> 16) & 0xff;  // extract red
            var g = (rgb >> 8) & 0xff;  // extract green
            var b = (rgb >> 0) & 0xff;  // extract blue

            var luma = 0.2126 * r + 0.7152 * g + 0.0722 * b; // per ITU-R BT.709

            if (luma < 180)
                return true; // use white

            return false; // use black
        },
        /**
         * Validate email
         */
        validate_email    : function (email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        },
        /**
         * Validate username
         */
        validate_username : function (username) {

            /**
             * Regular expression codes
             *
             * \u0030-\u0039    => Basic Latin ( 0-9 )
             * \u0041-\u005A    => Basic Latin ( A-Z )
             * \u0061-\u007A    => Basic Latin ( a-z )
             * \u00C0-\u00D6    => Latin-1 Supplement - Part 1 (only chars with grave, acute, diaeresis, circumflex, etc.)
             * \u00D8-\u00F6    => Latin-1 Supplement - Part 2 (only chars with grave, acute, diaeresis, circumflex, etc.)
             * \u00F8-\u00FF    => Latin-1 Supplement - Part 3 (only chars with grave, acute, diaeresis, circumflex, etc.)
             * \u0100-\u017F    => Latin Extended-A set
             * \u0180-\u024F    => Latin Extended-B set
             * \u0370-\u03FF    => Greek and Coptic set
             * \u0400-\u04FF    => Cyrillic set
             * \u0530-\u058F    => Armenian set
             * \u0590-\u05FF    => Hebrew set
             * \u0600-\u06FF    => Arabic set
             * \u1100-\u11FF    => Hangul Jamo set
             * \u3130-\u318F    => Hangul Compatibility Jamo
             * \uAC00-\uD7AF    => Hangul Syllables
             * \u2E80-\u2EFF    => CJK Radicals Supplement
             * \u3000-\u303F    => CJK Symbols and Punctuation
             * \u31C0-\u31EF    => CJK Strokes
             * \u3200-\u32FF    => Enclosed CJK Letters and Months
             * \u3300-\u33FF    => CJK Compatibility
             * \u3400-\u4DBF    => CJK Unified Ideographs Extension A
             * \u4E00-\u9FFF    => CJK Unified Ideographs
             * \uF900-\uFAFF    => CJK Compatibility Ideographs
             * \uFE30-\uFE4F    => CJK Compatibility Forms
             * \u3040-\u309F    => Hiragana
             * \u30A0-\u30FF    => Katakana
             * \u31F0-\u31FF    => Katakana Phonetic Extensions
             * \u0020           => Basic Latin ( space )
             * \u002D           => Basic Latin ( - )
             * \u002E           => Basic Latin ( . )
             * \u0040           => Basic Latin ( @ )
             * \u005F           => Basic Latin ( _ )
             */

            var re = /^[\u0030-\u0039\u0041-\u005A\u0061-\u007A\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u00FF\u0100-\u017F\u0180-\u024F\u0370-\u03FF\u0400-\u04FF\u0530-\u058F\u0590-\u05FF\u0600-\u06FF\u1100-\u11FF\u3130-\u318F\uAC00-\uD7AF\u2E80-\u2EFF\u3000-\u303F\u31C0-\u31EF\u3200-\u32FF\u3300-\u33FF\u3400-\u4DBF\u4E00-\u9FFF\uF900-\uFAFF\uFE30-\uFE4F\u3040-\u309F\u30A0-\u30FF\u31F0-\u31FF\u0020\u002D\u002E\u0040\u005F]+$/gim
            return re.test(username);

        },
        /**
         * MD5 hash (http://www.webtoolkit.info/javascript-md5.html)
         */
        md5               : function (e) {
            function h(a, b) {
                var c, d, e, f, g;
                e = a & 2147483648;
                f = b & 2147483648;
                c = a & 1073741824;
                d = b & 1073741824;
                g = (a & 1073741823) + (b & 1073741823);
                return c & d ? g ^ 2147483648 ^ e ^ f : c | d ? g & 1073741824 ? g ^ 3221225472 ^ e ^ f : g ^ 1073741824 ^ e ^ f : g ^ e ^ f
            }

            function k(a, b, c, d, e, f, g) {
                a = h(a, h(h(b & c | ~b & d, e), g));
                return h(a << f | a >>> 32 - f, b)
            }

            function l(a, b, c, d, e, f, g) {
                a = h(a, h(h(b & d | c & ~d, e), g));
                return h(a << f | a >>> 32 - f, b)
            }

            function m(a, b, d, c, e, f, g) {
                a = h(a, h(h(b ^ d ^ c, e), g));
                return h(a << f | a >>> 32 - f, b)
            }

            function n(a, b, d, c, e, f, g) {
                a = h(a, h(h(d ^ (b | ~c),
                    e), g));
                return h(a << f | a >>> 32 - f, b)
            }

            function p(a) {
                var b = "", d = "", c;
                for (c = 0; 3 >= c; c++) d = a >>> 8 * c & 255, d = "0" + d.toString(16), b += d.substr(d.length - 2, 2);
                return b
            }

            var f = [], q, r, s, t, a, b, c, d;
            e = function (a) {
                a = a.replace(/\r\n/g, "\n");
                for (var b = "", d = 0; d < a.length; d++) {
                    var c = a.charCodeAt(d);
                    128 > c ? b += String.fromCharCode(c) : (127 < c && 2048 > c ? b += String.fromCharCode(c >> 6 | 192) : (b += String.fromCharCode(c >> 12 | 224), b += String.fromCharCode(c >> 6 & 63 | 128)), b += String.fromCharCode(c & 63 | 128))
                }
                return b
            }(e);
            f = function (b) {
                var a, c = b.length;
                a =
                    c + 8;
                for (var d = 16 * ((a - a % 64) / 64 + 1), e = Array(d - 1), f = 0, g = 0; g < c;) a = (g - g % 4) / 4, f = g % 4 * 8, e[a] |= b.charCodeAt(g) << f, g++;
                a = (g - g % 4) / 4;
                e[a] |= 128 << g % 4 * 8;
                e[d - 2] = c << 3;
                e[d - 1] = c >>> 29;
                return e
            }(e);
            a = 1732584193;
            b = 4023233417;
            c = 2562383102;
            d = 271733878;
            for (e = 0; e < f.length; e += 16) q = a, r = b, s = c, t = d, a = k(a, b, c, d, f[e + 0], 7, 3614090360), d = k(d, a, b, c, f[e + 1], 12, 3905402710), c = k(c, d, a, b, f[e + 2], 17, 606105819), b = k(b, c, d, a, f[e + 3], 22, 3250441966), a = k(a, b, c, d, f[e + 4], 7, 4118548399), d = k(d, a, b, c, f[e + 5], 12, 1200080426), c = k(c, d, a, b, f[e + 6], 17, 2821735955),
                b = k(b, c, d, a, f[e + 7], 22, 4249261313), a = k(a, b, c, d, f[e + 8], 7, 1770035416), d = k(d, a, b, c, f[e + 9], 12, 2336552879), c = k(c, d, a, b, f[e + 10], 17, 4294925233), b = k(b, c, d, a, f[e + 11], 22, 2304563134), a = k(a, b, c, d, f[e + 12], 7, 1804603682), d = k(d, a, b, c, f[e + 13], 12, 4254626195), c = k(c, d, a, b, f[e + 14], 17, 2792965006), b = k(b, c, d, a, f[e + 15], 22, 1236535329), a = l(a, b, c, d, f[e + 1], 5, 4129170786), d = l(d, a, b, c, f[e + 6], 9, 3225465664), c = l(c, d, a, b, f[e + 11], 14, 643717713), b = l(b, c, d, a, f[e + 0], 20, 3921069994), a = l(a, b, c, d, f[e + 5], 5, 3593408605), d = l(d, a, b, c, f[e + 10], 9, 38016083),
                c = l(c, d, a, b, f[e + 15], 14, 3634488961), b = l(b, c, d, a, f[e + 4], 20, 3889429448), a = l(a, b, c, d, f[e + 9], 5, 568446438), d = l(d, a, b, c, f[e + 14], 9, 3275163606), c = l(c, d, a, b, f[e + 3], 14, 4107603335), b = l(b, c, d, a, f[e + 8], 20, 1163531501), a = l(a, b, c, d, f[e + 13], 5, 2850285829), d = l(d, a, b, c, f[e + 2], 9, 4243563512), c = l(c, d, a, b, f[e + 7], 14, 1735328473), b = l(b, c, d, a, f[e + 12], 20, 2368359562), a = m(a, b, c, d, f[e + 5], 4, 4294588738), d = m(d, a, b, c, f[e + 8], 11, 2272392833), c = m(c, d, a, b, f[e + 11], 16, 1839030562), b = m(b, c, d, a, f[e + 14], 23, 4259657740), a = m(a, b, c, d, f[e + 1], 4, 2763975236),
                d = m(d, a, b, c, f[e + 4], 11, 1272893353), c = m(c, d, a, b, f[e + 7], 16, 4139469664), b = m(b, c, d, a, f[e + 10], 23, 3200236656), a = m(a, b, c, d, f[e + 13], 4, 681279174), d = m(d, a, b, c, f[e + 0], 11, 3936430074), c = m(c, d, a, b, f[e + 3], 16, 3572445317), b = m(b, c, d, a, f[e + 6], 23, 76029189), a = m(a, b, c, d, f[e + 9], 4, 3654602809), d = m(d, a, b, c, f[e + 12], 11, 3873151461), c = m(c, d, a, b, f[e + 15], 16, 530742520), b = m(b, c, d, a, f[e + 2], 23, 3299628645), a = n(a, b, c, d, f[e + 0], 6, 4096336452), d = n(d, a, b, c, f[e + 7], 10, 1126891415), c = n(c, d, a, b, f[e + 14], 15, 2878612391), b = n(b, c, d, a, f[e + 5], 21, 4237533241),
                a = n(a, b, c, d, f[e + 12], 6, 1700485571), d = n(d, a, b, c, f[e + 3], 10, 2399980690), c = n(c, d, a, b, f[e + 10], 15, 4293915773), b = n(b, c, d, a, f[e + 1], 21, 2240044497), a = n(a, b, c, d, f[e + 8], 6, 1873313359), d = n(d, a, b, c, f[e + 15], 10, 4264355552), c = n(c, d, a, b, f[e + 6], 15, 2734768916), b = n(b, c, d, a, f[e + 13], 21, 1309151649), a = n(a, b, c, d, f[e + 4], 6, 4149444226), d = n(d, a, b, c, f[e + 11], 10, 3174756917), c = n(c, d, a, b, f[e + 2], 15, 718787259), b = n(b, c, d, a, f[e + 9], 21, 3951481745), a = h(a, q), b = h(b, r), c = h(c, s), d = h(d, t);
            return (p(a) + p(b) + p(c) + p(d)).toLowerCase()
        },
        /**
         * Random ID
         */
        random_id         : function (min, max) {

            return Math.floor(Math.random() * (max - min + 1)) + min;

        },
        /**
         * Purge Firebase from inactive users and conversations
         */
        purge_firebase    : function (force_purge) {

            var self = this;

            this.data.ref_users.once('value', function (snap) {

                var users = snap.val(),
                    i = 0,
                    del_list = [],
                    cnv_list = [],
                    op_cnv_list = [],
                    interval = (force_purge) ? 0 : 3600; //3600 = 1 hour

                if (users !== null) {

                    var total_user = Object.keys(users).length,
                        now = new Date();

                    $.each(users, function (user_id, user) {

                        i++;

                        if (user) {

                            if (user.status === 'offline') {

                                var seconds = ((now.getTime() - user.last_online) * 0.001) >> 0;

                                if (seconds >= interval) {

                                    if (user.user_type != 'operator') {


                                        if (user.conversation_id != null) {

                                            cnv_list.push(user.conversation_id)

                                        } else {

                                            del_list.push(user_id)

                                        }


                                    } else {

                                        del_list.push(user_id);
                                        op_cnv_list.push(user.conversation_id);

                                    }

                                }

                            } else if (user.status === 'wait') {

                                if (user.last_online === undefined) {
                                    del_list.push(user_id);

                                } else {

                                    var seconds = ((now.getTime() - user.last_online) * 0.001) >> 0;

                                    if (seconds >= (interval * 2)) {

                                        del_list.push(user_id);

                                    }


                                }


                            }

                        }

                        if (i === total_user) {

                            $.each(del_list, function (index, user_id) {

                                self.data.ref_users.child(user_id).remove()

                            });

                            $.each(op_cnv_list, function (index, cnv_id) {

                                self.data.ref_cnv.child(cnv_id).remove();
                            });

                            $.each(cnv_list, function (index, cnv_id) {

                                if (ylc.is_premium) {

                                    self.trigger_premium('save_user_data', cnv_id, true, now.getTime());

                                } else {

                                    self.clear_user_data(cnv_id);

                                }

                            });

                        }

                    });

                }

            });

        },
        /**
         * Valid Operator
         */
        valid_operator    : function (vendor_id) {

            if (!ylc.yith_wpv_active) {
                return true
            }

            if (ylc.yith_wpv_active && ylc.active_vendor.vendor_id === vendor_id) {
                return true;
            }

            if (ylc.yith_wpv_active && '0' === vendor_id && !ylc.vendor_only_chat) {
                return true;
            }

            return false;

        }
    };

    /*
     * Plugin wrapper, preventing against multiple instantiations and allowing any public function to be called via the jQuery plugin
     */
    $.fn[YLC] = function () {

        var instance;

        // only allow the plugin to be instantiated once
        if (!(this.data(data_plugin) instanceof Plugin)) {

            // if no instance, create one
            this.data(data_plugin, new Plugin(this));
        }

        instance = this.data(data_plugin);

        instance.el = this;

        instance.init();

    };

    $(document).ready(function () {

        $.post(document.location.href, function (data) {
            if (data !== '') {
                var c = $("<div></div>").html(data),
                    chat = c.find('#YLC');
                $('#YLC').html(chat.html()).ylc();

                $('a[href="#yith-live-chat"]').click(function (e) {

                    e.preventDefault();
                    $('#YLC_chat_btn').click();

                });
            }
        });


    });

}(jQuery, window, document));