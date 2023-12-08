<?php

declare(strict_types=1);

namespace ACP\Service;

use AC\ListScreen;
use AC\Registerable;

class TableCellWrapping implements Registerable
{

    public function register(): void
    {
        add_action('ac/admin_head', [$this, 'render_style']);
    }

    public function render_style(ListScreen $list_screen): void
    {
        $wrapping = $list_screen->get_preference('wrapping');

        if ('clip' !== $wrapping) {
            return;
        }

        ?>
		<style>table.wp-list-table td {
				white-space: nowrap;
				text-overflow: ellipsis;
				overflow: hidden;
			}
		</style>
        <?php
    }

}