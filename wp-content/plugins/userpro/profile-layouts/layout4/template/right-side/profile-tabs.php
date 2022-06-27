<?php
defined('ABSPATH') || exit;

$upNavigation = apply_filters('up_navigation_tabs', array());

global $up_user;
?>

<div class="up-profile-nav">
    <ul>
        <?php
        $x = 0;
        foreach ($upNavigation as $key => $val): ?>
            <li class="<?php if ($x === 0) {
                echo 'active';
            } ?>">
                <a href="#up-tab-<?= $val['id']; ?>"><i class="<?= $val['icon'] ?>"></i>
                    <?= $val['title'] ?></a>
            </li>
            <?php
            $x++;
        endforeach; ?>
    </ul>
</div>
<?php do_action('up_before_professional_user_fields') ?>
<div class="up-tab-container">
<?php $x = 0;
foreach ($upNavigation as $key => $val) : ?>

    <?php if (isset($val['callback']) && isset($val['type'])) :

        if ($val['type'] === 'object') {
            list($className, $function) = $val['callback'];
            $object = new $className;
            $tabContent = call_user_func([$object, $function], $up_user->getUserId());
        }

        $class = ($x === 0) ? 'up-profile-information--visible' : '';
        ?>


        <div id="up-tab-<?= $val['id'] ?>" class="up-profile-information <?= $class ?>">
            <?php echo $tabContent; ?>
        </div>
    <?php endif; ?>
    <?php $x++; endforeach; ?>
</div>
<?php do_action('up_after_professional_user_fields') ?>


