<div style="display: none">
    <div id="mo-create-fb-custom-audience-modal">
        <div class="mo-modal">
            <div class="mo-header">
                <h2><?php _e('Create new custom audience', 'mailoptin'); ?></h2>
            </div>
            <div class="mo-content">
                <p>
                    <label for="fbca-name"><?php _e('Name', 'mailoptin'); ?></label>
                    <input type="text" id="fbca-name">
                </p>
                <p>
                    <label for="fbca-description"><?php _e('Description', 'mailoptin'); ?></label>
                    <textarea id="fbca-description" rows="3"><?php _e('Powered by MailOptin', 'mailoptin'); ?></textarea><br>
                </p>
                <p>
                    <input type="submit" class="button button-primary" id="fbca-create-submit" value="<?php _e('Create Custom Audience', 'mailoptin'); ?>">
                    <img class="mo-spinner" id="mo-fbca-submit-spinner" style="margin:10px;display:none" src="<?php echo admin_url('images/spinner.gif'); ?>"/>
                </p>
                <div id="mo-fbca-submit-error" class="mailoptin-error" style="display:none;text-align:center;font-weight:normal;"><?php _e('An error occurred. Please try again.', 'mailoptin'); ?></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    (function ($) {
        $('#mo-create-fb-custom-audience').click(function (e) {
            e.preventDefault();
            $.fancybox.open({
                src: '#mo-create-fb-custom-audience-modal',
                type: 'inline'
            });
        });

        $(document.body).on('click', '#fbca-create-submit', function (e) {
            e.preventDefault();
            var _this = this;

            var name_obj = $('#fbca-name');
            var description_obj = $('#fbca-description');

            var name = name_obj.val();
            var description = description_obj.val();

            var isEmpty = function (str) {
                return (str.length === 0 || !str.trim());
            };

            if (isEmpty(name)) {
                name_obj.addClass('mailoptin-input-error');
            } else {
                name_obj.removeClass('mailoptin-input-error');
            }

            if (isEmpty(description)) {
                description_obj.addClass('mailoptin-input-error');
            } else {
                description_obj.removeClass('mailoptin-input-error');
            }

            if (isEmpty(name) || isEmpty(description)) return;

            $(_this).prop("disabled", true);
            $('#mo-fbca-submit-error').hide();
            $('#mo-fbca-submit-spinner').show();

            $.post(ajaxurl, {
                action: 'mailoptin_create_fbca',
                fbca_name: name,
                fbca_description: description,
                nonce: mailoptin_globals.nonce
            }, function (response) {
                if ('success' in response && response.success === true && typeof response.data.redirect !== 'undefined') {
                    window.location.assign(response.data.redirect);
                } else {
                    $(_this).prop("disabled", false);
                    $('#mo-fbca-submit-error').show().html(response.data);
                    $('#mo-fbca-submit-spinner').hide();
                }
            });

        });
    })(jQuery);
</script>