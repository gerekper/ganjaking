<?php

declare(strict_types=1);

namespace ACP\ListScreen;

use AC;
use AC\ColumnRepository;
use AC\MetaType;
use AC\Type\Uri;
use AC\Type\Url;
use AC\Type\Url\EditorNetworkColumns;
use ACP\Column;
use ACP\Editing;

class MSSite extends AC\ListScreen
    implements Editing\ListScreen, AC\ListScreen\ManageValue
{

    public function __construct()
    {
        parent::__construct('wp-ms_sites', 'sites-network');

        $this->label = __('Network Sites');
        $this->singular_label = __('Network Site');
        $this->group = __('network');
        $this->meta_type = MetaType::SITE;
    }

    public function manage_value(): AC\Table\ManageValue
    {
        return new AC\Table\ManageValue\MsSite(new ColumnRepository($this));
    }

    public function get_table_url(): Uri
    {
        return new Url\ListTableNetwork('sites.php', $this->has_id() ? $this->get_id() : null);
    }

    public function get_editor_url(): Uri
    {
        return new EditorNetworkColumns($this->key, $this->has_id() ? $this->get_id() : null);
    }

    public function editing()
    {
        return new Editing\Strategy\Site();
    }

    protected function register_column_types(): void
    {
        $this->register_column_types_from_list([
            Column\Actions::class,
            Column\NetworkSite\BlogID::class,
            Column\NetworkSite\CommentCount::class,
            Column\NetworkSite\Domain::class,
            Column\NetworkSite\LastUpdated::class,
            Column\NetworkSite\Name::class,
            Column\NetworkSite\Options::class,
            Column\NetworkSite\Path::class,
            Column\NetworkSite\Plugins::class,
            Column\NetworkSite\PostCount::class,
            Column\NetworkSite\Registered::class,
            Column\NetworkSite\SiteID::class,
            Column\NetworkSite\Status::class,
            Column\NetworkSite\Theme::class,
            Column\NetworkSite\UploadSpace::class,
        ]);
    }

}