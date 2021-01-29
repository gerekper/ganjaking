<?php

namespace WeDevs\PM_Pro\File\Transformers;

use WeDevs\PM_Pro\File\Models\File;
use League\Fractal\TransformerAbstract;
use WeDevs\PM\User\Transformers\User_Transformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use WeDevs\PM\Common\Traits\Resource_Editors;
use WeDevs\PM_Pro\Common\Transformers\Meta_Transformer;
use WeDevs\PM\Core\File_System\File_System;
use WeDevs\PM\Comment\Transformers\Comment_Transformer;
use WeDevs\PM\File\Transformers\File_Transformer as Main_File_Transformer;
use Illuminate\Pagination\Paginator;

class File_Transformer extends TransformerAbstract {

    use Resource_Editors;

    protected $defaultIncludes = [
        'creator', 'updater', 'files'
    ];

    protected $availableIncludes = [
        'comments', 'revisions', 'parents', 'children'
    ];

    public function transform( File $item ) {
        $file = File_System::get_file( $item->attachment_id );
        $file = is_array( $file ) ? $file : [];

        $model_data = [
            'id'            => (int) $item->id,
            'private'       => $this->is_private( $item->meta ),
            'title'         => $this->title( $item->meta ),
            'description'   => $this->description( $item->meta ),
            'url'           => $this->url( $item->meta ),
            'fileable_id'   => $item->fileable_id,
            'file_type'     => $item->type,
            'fileable_type' => $item->fileable_type,
            'parent'        => $item->parent,
            'project_id'    => $item->project_id,
            'created_at'    => format_date( $item->created_at ),
            'fileable'      => $this->get_fileabel($item),
            'attachment_id' => $item->attachment_id,
            'comment_count' => $item->comments()->count()
        ];

        $transform_data = array_merge( $model_data, $file );

        return apply_filters( 'pm_pro_file_transform', $transform_data, $item, $file, $model_data );
    }

    public function get_fileabel( $item ) {

        if ( $item->fileable_type == 'comment') {

            $result = $item->comment()->get()->first();
            if ($result) {
                return $result->getAttributes();
            }

        }
    }

    private function url( $meta ) {
        $url_meta = $meta->where('meta_key', 'url')->first();

        return $url_meta ? $url_meta->meta_value : '';
    }

    private function description( $meta ) {
        $description_meta = $meta->where('meta_key', 'description')->first();

        return $description_meta ? $description_meta->meta_value : '';
    }

    private function title( $meta ) {
        $title_meta = $meta->where('meta_key', 'title')->first();

        return $title_meta ? $title_meta->meta_value : '';
    }

    private function is_private( $meta ) {
        $private_meta = $meta->where('meta_key', 'private')->first();

        return $private_meta ? $private_meta->meta_value : 0;
    }

    public function includeMeta( File $item ) {
        $meta = $item->meta;
        return $this->collection( $meta, new Meta_Transformer );
    }

    public function includeFiles( File $item ) {
        $page = isset( $_GET['file_page'] ) ? $_GET['file_page'] : 1;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $files = $item->files()->paginate( 10, ['*']);

        $file_collection = $files->getCollection();
        $resource = $this->collection( $file_collection, new Main_File_Transformer );

        $resource->setPaginator( new IlluminatePaginatorAdapter( $files ) );

        return $resource;
    }

    public function includeComments( File $item ) {
        $page = isset( $_GET['comment_page'] ) ? $_GET['comment_page'] : 1;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $comments = $item->comments()
            ->orderBy( 'created_at', 'ASC' )
            ->paginate( pm_config('app.comment_per_page'), ['*'] );

        $comment_collection = $comments->getCollection();
        $resource = $this->collection( $comment_collection, new Comment_Transformer );

        $resource->setPaginator( new IlluminatePaginatorAdapter( $comments ) );

        return $resource;
    }

    public function includeRevisions( File $item ) {

        $page = isset( $_GET['revision_page'] ) ? $_GET['revision_page'] : 1;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $revisions = $item->revision()
            ->orderBy( 'created_at', 'DESC' )
            ->paginate( 1000, ['*'] );

        $comment_collection = $revisions->getCollection();
        $resource = $this->collection( $comment_collection, new File_Transformer );

        $resource->setPaginator( new IlluminatePaginatorAdapter( $revisions ) );

        return $resource;
    }

    public function includeParents( File $item ) {
        $parent = $item->parent()->get();
        $transform = new self;
        return $this->collection( $parent,  $transform->setDefaultIncludes(['parents']) );
    }

    public function includeChildren( File $item ) {
        $children = $item->children()->get();
        return $this->collection( $children, new self );
    }
}

