<?php

namespace WeDevs\PM_Pro\File\Models;

use WeDevs\PM\Common\Traits\Model_Events;
use WeDevs\PM\File\Models\File as Main_File;
use WeDevs\PM\Common\Models\Meta;
use WeDevs\PM\Comment\Models\Comment;
use WeDevs\PM\Common\Models\Board;

class File extends Main_File {
    use Model_Events;

    public function scopePrivate( $query ) {
        return $query->where( function( $q ) {
            $q->doesntHave( 'meta', 'and', function ( $q2 ) {
                $q2->where( 'meta_key', '=', 'private' )
                    ->where( 'meta_value', '!=', 0 );
            });
            $q->orWhere( pm_tb_prefix().'pm_files.created_by', '=', get_current_user_id() );
        }); 
    }

    public function children() {
    	return $this->hasMany( $this, 'parent' );
    }
    public function parent() {
        return $this->belongsTo( $this, 'parent');
    }

    public function revision() {
        return $this->hasMany( $this, 'parent' )->where( 'type', 'revision' );
    }

    public function comments() {
        return $this->hasMany( 'WeDevs\PM\Comment\Models\Comment', 'commentable_id' )->where( 'commentable_type', 'file' );
    }

    public function files() {
        return $this->hasMany( $this, 'parent' )
            ->where( 'fileable_type', 'file' )
            ->whereNotNull( 'attachment_id' );
    }
    public function commentable() {
        return $this->belongsToMany( 'WeDevs\PM\Common\Models\Board', pm_tb_prefix() . 'pm_comments', 'id', 'commentable_id', 'fileable_id');
    }
}
