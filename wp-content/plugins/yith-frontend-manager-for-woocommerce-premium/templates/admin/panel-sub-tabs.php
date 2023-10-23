<?php $is_first = true; ?>
<ul class="subsubsub">
    <?php foreach( $args as $tab ) : ?>
        <li class="<?php echo $is_first ? 'yith-wcfm-sub-tab-item current' : 'yith-wcfm-sub-tab-item'; ?>">
            <a href="#">
                <?php echo $tab ?>
            </a>
        </li>
    <?php
        $is_first = false;
    endforeach;?>
</ul>