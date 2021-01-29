<?php
namespace WeDevs\PM_Pro\File\Controllers;

use WP_REST_Request;
use WeDevs\PM\File\Models\File;
use WeDevs\PM_Pro\File\Models\File as Pro_File;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM_Pro\File\Transformers\File_Transformer as Pro_File_Transformer;
use WeDevs\PM\File\Transformers\File_Transformer;
use WeDevs\PM\Common\Models\Boardable;
use WeDevs\PM\Common\Traits\Request_Filter;
use WeDevs\PM\Common\Models\Meta;
use WeDevs\PM\File\Controllers;
use WeDevs\PM\Common\Traits\File_Attachment;
use WeDevs\PM\Core\File_System\File_System;
use Illuminate\Pagination\Paginator;

class File_Controller {

    use Transformer_Manager, Request_Filter, File_Attachment;

    public function index( WP_REST_Request $request ) {

        $child_id   = $request->get_param('childId');
        $folder_id  = $request->get_param('folder_id');
        $project_id = $request->get_param( 'project_id' );
        $per_page   = $request->get_param( 'per_page' );
        $per_page   = $per_page ? $per_page : 50;
        $page       = $request->get_param( 'page' );
        $page       = $page ? $page : 1;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        if ( $child_id ) {
            $folder_id = $folder_id ? $folder_id : 0;
        }

        if ( $child_id ) {
            $files = Pro_File::with(['meta']);

            if ( !pm_user_can( 'view_private_file', $project_id ) ) {
                $files = $files->private();
            }

            $files = $files->where('id', $child_id)
                ->where('project_id', $project_id )
                ->where('parent', $folder_id)
                ->first();

            $resource = new Item( $files, new Pro_File_Transformer );

        } else if ( $folder_id ) {

            $files = Pro_File::with(['meta']);
            if ( !pm_user_can( 'view_private_file', $project_id ) ) {
                $files = $files->private();
            }
            $files = $files->where( 'project_id', $project_id )
                ->where('parent', $folder_id)
                ->paginate( $per_page );

            $file_collection = $files->getCollection();

            $resource = new Collection( $file_collection, new Pro_File_Transformer );
            $resource->setPaginator( new IlluminatePaginatorAdapter( $files ) );

        } else {
            $files = Pro_File::with(['meta']);
            if ( !pm_user_can( 'view_private_file', $project_id ) ) {
                $files = $files->private();
            }
            $files = $files->where( 'project_id', $project_id )
                ->where('parent', 0)
                ->paginate( $per_page );

            $file_collection = $files->getCollection();

            $resource = new Collection( $file_collection, new Pro_File_Transformer );
            $resource->setPaginator( new IlluminatePaginatorAdapter( $files ) );
        }

        if ( $folder_id ) {
            $resource->setMeta( [
                'parent' => $this->get_file( $folder_id )
            ] );
        }

        $response =  apply_filters( 'pm_pro_after_get_files', $this->get_response( $resource ), $files, $resource, $request->get_params() );

       $data = $response['data'];

       if ( !isset($data['id']) ) {
           $data_array_blank = [];
           foreach ($data as $key => $value) {
               if ($value['file_type'] == 'folder') {
                   $data_array_blank['folder'][] = $value;
               } elseif ($value['file_type'] == 'pro_file') {
                   $data_array_blank['pro_file'][] = $value;
               } else {
                   $data_array_blank['doc'][] = $value;
               }
           }
           $data_array_folder   = (!empty($data_array_blank['folder'])) ? $data_array_blank['folder'] : [];
           $data_array_pro_file = (!empty($data_array_blank['pro_file'])) ? $data_array_blank['pro_file'] : [];
           $data_array_doc      = (!empty($data_array_blank['doc'])) ? $data_array_blank['doc'] : [];

           array_multisort(array_column($data_array_folder, "title"), SORT_ASC | SORT_NATURAL | SORT_FLAG_CASE, $data_array_folder);
           array_multisort(array_column($data_array_pro_file, "name"), SORT_ASC | SORT_NATURAL | SORT_FLAG_CASE, $data_array_pro_file);
           array_multisort(array_column($data_array_doc, "title"), SORT_ASC | SORT_NATURAL | SORT_FLAG_CASE, $data_array_doc);

           $response['data'] = array_merge($data_array_folder, $data_array_pro_file, $data_array_doc);
       }

       return $response ;


    }

    public function get_file( $folder_id ) {
        $file     = Pro_File::find($folder_id);

        $transformer = new Pro_File_Transformer;
        $resource = new Item( $file, $transformer->setDefaultIncludes(['parents']) );
        // $resource = new Item( $file, new Pro_File_Transformer );

        return $this->get_response( $resource );
    }


    public function store( WP_REST_Request $request ) {
        $data                  = $request->get_params();
        $data['fileable_type'] = 'file';
        $media_data            = $request->get_file_params();
        $type                  = $request->get_param('type');
        $parent                = $request->get_param('parent');
        $attach_files          = array_key_exists( 'files', $media_data ) ? $media_data['files'] : null;

        if ( $type == 'pro_file' ) {
            $attachment_ids = File_System::multiple_upload( $attach_files );
            $file_records = [];

            foreach ( $attachment_ids as $attachment_id ) {

                $file_records[] = $file = Pro_File::create([
                    'fileable_id'   => 0,
                    'fileable_type' => 'file',
                    'attachment_id' => $attachment_id,
                    'project_id'    => $data['project_id'],
                    'parent'        => (int) $parent,
                    'type'          => 'pro_file'
                ]);

                $this->add_meta( $file->id, $data );
            }

            $resource = new Collection( $file_records, new Pro_File_Transformer );

        } else if ( $type == 'doc' ) {
            $file = Pro_File::create( $data );

            if ( $file ) {
                $this->add_meta( $file->id, $data );
            }

            if ( $attach_files ) {
            	$doc_file = clone $file;

                $doc_file->parent_id = $doc_file->id;
                $doc_file->type = 'doc_file';
                $this->attach_files( $doc_file, $attach_files );
            }

            $resource = new Item( $file, new Pro_File_Transformer );

        } else {
            $file = Pro_File::create( $data );

            if ( $file ) {
                $this->add_meta( $file->id, $data );
            }

            if ( $attach_files ) {
                $this->attach_files( $file, $attach_files );
            }

            $resource = new Item( $file, new Pro_File_Transformer );
        }

        return $this->get_response( $resource );
    }

    public function add_meta( $id, $data ) {

        if ( ! empty( $data['description'] ) ) {
            pm_add_meta( $id, $data['project_id'], $data['fileable_type'], 'description', $data['description'] );
        }

        if ( isset( $data['private'] ) ) {
            pm_add_meta( $id, $data['project_id'], $data['fileable_type'], 'private', $data['private'] );
        }

        if ( ! empty( $data['title'] ) ) {
            pm_add_meta( $id, $data['project_id'], $data['fileable_type'], 'title', $data['title'] );
        }

        if ( ! empty( $data['url'] ) ) {
            pm_add_meta( $id, $data['project_id'], $data['fileable_type'], 'url', $data['url'] );
        }

    }

    public function update_meta( $id, $data ) {

        if ( ! empty( $data['description'] ) ) {
            pm_update_meta( $id, $data['project_id'], 'file', 'description', $data['description'] );
        }

        if ( isset( $data['private'] ) ) {
            pm_update_meta( $id, $data['project_id'], 'file', 'private', $data['private'] );
        }

        if ( ! empty( $data['title'] ) ) {
            pm_update_meta( $id, $data['project_id'], 'file', 'title', $data['title'] );
        }

        if ( ! empty( $data['url'] ) ) {
            pm_update_meta( $id, $data['project_id'], 'file', 'url', $data['url'] );
        }

    }

    public function update( WP_REST_Request $request ) {
        //$data         = $this->extract_non_empty_values( $request );
        $id           = $request->get_param('file_id');
        $project_id   = $request->get_param('project_id');
        $data         = $request->get_params();
        $media_data   = $request->get_file_params();
        $type         = $request->get_param('type');
        $attach_files = array_key_exists( 'files', $media_data ) ? $media_data['files'] : null;
        $files_to_delete = $request->get_param( 'files_to_delete' );

        $file = Pro_File::where( 'id', $id )
            ->where( 'project_id', $project_id )
            ->first();

        if ( $file ) {
            // If we have a valid $file object, we may let others filter the $data array
            //$data = apply_filters( 'pm_pro_before_file_update', $request->get_file_params(), $file );
            $data = apply_filters( 'pm_pro_before_file_update', $data, $file );

            $file->update_model( $data );
            $this->update_meta( $file->id, $data );
        }

        if ( $attach_files ) {
        	if( $file->type == 'doc' ) {
        		//$file_att = new \stdClass();

        		//$file_att->id            = $file->id;
                //$file_att->project_id    = $file->project_id;
                //$file_att->parent_id     = $file->id;
                //$file_att->type          = 'doc_file';
                $file->setAttribute('parent_id', $file->id);
                $file->setAttribute('type', 'doc_file');
        	}

            $this->attach_files( $file, $attach_files );
        }

        if ( $files_to_delete ) {
            $this->detach_files( $file, $files_to_delete );
        }

        $resource = new Item( $file, new Pro_File_Transformer );

        return apply_filters( 'pm_pro_after_file_update', $this->get_response( $resource ), $file, $resource, $request->get_params() );
    }

    public function destroy( WP_REST_Request $request ) {
        // Grab user inputs
        $project_id = $request->get_param( 'project_id' );
        $file_id    = $request->get_param( 'file_id' );

        // Select the task list to be deleted
        $file_list = Pro_file::where( 'id', $file_id )
            ->where( 'project_id', $project_id )
            ->first();

        // Delete relations
        $this->detach_all_relations( $file_list );

        // Delete the task list
        $file_list->delete();

        wp_send_json_success();
    }

    function detach_all_relations( $file ) {

        $file_children = Pro_File::where( 'parent', $file->id );

        if ( $file_children ) {
            $file_children->delete();
        }

        pm_delete_meta( $file->id, $file->project_id, $file->fileable_type );
    }

    public function get_all_folder( WP_REST_Request $request ) {
        $type       = $request->get_param( 'type' );
        $project_id = $request->get_param( 'project_id' );
        $files      = Pro_File::with(['meta'])->where('project_id', $project_id )->where('type','folder')->get();
        $resource   = new Collection( $files, new Pro_File_Transformer );
        $response   =  apply_filters( 'pm_pro_after_get_files', $this->get_response( $resource ), $files, $resource, $request->get_params() );
        $data       = $response['data'];

        return $response;
    }

    public function sorting( WP_REST_Request $request ) {
        $project_id     = $request->get_param( 'project_id' );
        $source         = $request->get_param( 'source' );
        $destination    = $request->get_param( 'destination' );

        $file = Pro_File::find( $source );

        $file->update([
            'parent' => $destination
        ]);


        wp_send_json_success( [
            'file'        => $file,
            'source'      => $source,
            'destination' => $destination,
            'project_id'  => $project_id
        ] );
    }

    public function file_search( WP_REST_Request $request ) {
        $project_id = $request->get_param( 'project_id' );
        $title      = $request->get_param( 'title' );
        $files = Pro_File::with(['meta' => function( $q ) use( $title, $project_id ) {
                        $q->where('meta_key', 'title')
                        ->where('meta_value', 'like', '%'.$title.'%')
                        ->where( 'project_id', $project_id );
                }])->whereHas('meta', function( $q ) use( $title, $project_id ) {
                        $q->where('meta_key', 'title')
                        ->where('meta_value', 'like', '%'.$title.'%')
                        ->where( 'project_id', $project_id );
                })->where('type','folder')->paginate( $per_page );

        $file_collection = $files->getCollection();
        $resource        = new Collection( $file_collection, new Pro_File_Transformer );
        $resource->setPaginator( new IlluminatePaginatorAdapter( $files ) );
        $response =  apply_filters( 'pm_pro_after_get_files', $this->get_response( $resource ), $files, $resource, $request->get_params() );
        return $response;
    }
}
