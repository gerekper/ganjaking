<?php

namespace WeDevs\PM_Pro\Core\Notifications\Emails;

/**
* Email Notification When a new project created
*/
use WeDevs\PM\Core\Notifications\Email;
use WeDevs\PM\Project\Models\Project;
use WeDevs\PM\Task_List\Models\Task_List;
use WeDevs\PM\Task\Models\Task;
use WeDevs\PM\Discussion_Board\Models\Discussion_Board;
use WeDevs\PM\Common\Models\Meta;
use WeDevs\PM\Comment\Models\Comment;
use WeDevs\PM\File\Models\File;

class Comment_Mention_Notification extends Email {

    function __construct() {
        add_action('pm_after_new_comment_notification', array($this, 'trigger'), 10, 2 );
        add_action('pm_after_update_comment_notification', array($this, 'trigger'), 10, 2 );
    }

    public function trigger( $commentData, $request ) {

        if ( empty( $request['mentioned_users'] ) ){
            return ;
        }

        $project         = Project::find( $request['project_id'] );
        $users           = array();
        $mentioned_users = explode( ',',  $request['mentioned_users'] );

        foreach ( $mentioned_users as $u ) {
            if( $this->is_enable_user_notification( $u ) ) {
                $user_info = get_userdata( $u );
                $users[] = $user_info->user_email;
            }
        }

        if ( !$users ) {
            return ;
        }

        if ( $request['commentable_type'] == 'discussion_board' ) {
            $type = __( 'Message', 'pm' );
            $comment_link = $this->pm_link() . '#/projects/'.$project->id.'/discussions/'.$request['commentable_id'];
            $title = Discussion_Board::find( $request['commentable_id'] )->title;

        } else if ( $request['commentable_type'] == 'task_list' ) {
            $type = __( 'Task List', 'pm' );
            $comment_link = $this->pm_link() . '#/projects/'.$project->id.'/task-lists/'.$request['commentable_id'];
            $title = Task_List::find( $request['commentable_id'] )->title;

        } else if ( $request['commentable_type'] == 'task' ) {
            $type        = __( 'Task', 'pm' );
            $comment_link = $this->pm_link() . '#/projects/'.$project->id.'/task-lists/tasks/'.$request['commentable_id'];
            $title = Task::find( $request['commentable_id'] )->title;

        } else if ( $request['commentable_type'] == 'file' ) {
            $type        = __( 'File', 'pm' );
            $file = File::find($request['commentable_id']);
            $comment_link = $this->pm_link() . '#/projects/'. $project->id .'/files/'. $file->parent .'/'. $file->type .'/'. $request['commentable_id'];
            $filemeta = Meta::where( 'project_id', $request['project_id'] )
                            ->where( 'entity_type', 'file' )
                            ->where( 'entity_id',  $request['commentable_id'])
                            ->where( 'meta_key', 'title' )
                            ->first();
            $title = $filemeta->meta_value;
        }

        $template_name = apply_filters( 'pm_comment_mention_email_template_path', $this->get_template_path( '/html/mention.php' ) );
        $subject       = sprintf( __( '[%s][%s] Comment on: %s', 'pm' ), $this->get_blogname(), $project->title , $title );

        $message = $this->get_content_html( $template_name, [
            'id'                => $commentData['data']['id'],
            // 'user'              => $user_name,
            'content'           => $request['content'],
            'creator'           => $commentData['data']['creator']['data']['display_name'],
            'commnetable_title' => $title,
            'commnetable_type'  => $type,
            'comment_link'      => $comment_link
        ] );

        $this->send( $users, $subject, $message );
    }

}
