<div class="sidebar-info timer">
    <strong>
        <span class="ylc-icons ylc-icons-user-time"></span> <?php esc_html_e( 'Elapsed time', 'yith-live-chat' ) ?>
    </strong>
    <span id="YLC_timer">
    </span>
</div>
<div class="sidebar-info macro">
    <label>
        <select class="macro-select" style="width:100%;">
            <option value=""></option>
			<?php echo apply_filters( 'ylc_macro_options', '' ) ?>
        </select>
    </label>
</div>