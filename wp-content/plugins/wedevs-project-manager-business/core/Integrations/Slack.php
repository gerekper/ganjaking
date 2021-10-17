<?php
namespace WeDevs\PM_Pro\Core\Integrations;

use WeDevs\PM\Task\Models\Task;

/**
 * Slack Integrations
 *
 * @package weForms\Integrations
 */
class Slack {

    private static $_instance;
    private $settings;

    public static function getInstance() {
        if ( !self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Initialize the plugin
     */
    function __construct() {

        $this->icon  = pm_config('frontend.assets_url') . '/images/pm-log.gif';

        add_action( 'pm_create_task_aftre_transformer', array( $this, 'new_task' ), 10, 2 );
        add_action( 'pm_updated', array( $this, 'update_task' ) );
        add_action( 'pm_updated', array( $this, 'update_sub_task' ) );

        add_action( 'pm_after_new_comment', array( $this, 'new_task_comment' ), 10, 2 );
        add_action( 'pm_after_update_comment', array( $this, 'update_task_comment' ), 10, 2 );
        add_action( 'pm_update_task_status', array( $this, 'update_task_status' ), 10, 3 );

        add_action( 'pm_create_subtask_after_transformer', array( $this, 'new_subtask' ), 10, 2 );

    }

    function update_sub_task_status( $task ) {
        $task   = pm_get_task( $task->id );
        $task   = $task['data'];
        $status = $task['status'];

        if ( $status == 'complete' ) {
            if ( !$this->has_permission( $task['project_id'], 'subTasks', 'complete' ) ) {
                return;
            }
        } else {
            if ( !$this->has_permission( $task['project_id'], 'subTasks', 'incomplete' ) ) {
                return;
            }
        }

        $is_admin = $_POST['is_admin'];
        $parent   = pm_get_task( $task['parent_id'] );
        
        if ( empty( $parent['data'] ) ) {
            return;
        }
        
        $parent         = $parent['data'];
        $parent_title   = $parent['title'];
        $parent_url     = pm_get_task_url( $parent['project_id'], $parent['task_list']['data']['id'], $parent['id'], $is_admin );
        $task_title     = $task['title'];
        $task_url       = $parent_url;
        $creator_avatar = $task['updater']['data']['avatar_url'];
        $display_name   = $task['updater']['data']['display_name'];
        $creator_url    = pm_get_user_url( $task['updater']['data']['id'], $_POST['is_admin'] );
        //$new_date       = date( 'F j, Y', strtotime( $new ) );
        //$old_date       = date( 'F j, Y', strtotime( $old ) );

        if ( $status == 'complete' ) {
            $body = sprintf( "%s has been completed.", "~*<$task_url|$task_title>*~" );
        }

        if ( $status == 'incomplete' ) {
            $body = sprintf( "%s has been re-opened.", "*<$task_url|$task_title>*" );
        }

        $attachments = [
            [
                "author_name" => ucfirst( $display_name ),
                "author_link" => $creator_url,
                "author_icon" => $creator_avatar,
                "mrkdwn_in"   => ["fields"],
                "fields" => [
                    [
                        "title" => "",
                        "value" => $body,
                        "short" => false
                    ],
                ],
                "footer_icon" => $this->icon,
                "ts" => current_time( 'timestamp', true )
            ]
        ];

        $this->send_notification( $attachments, '' );
    }

    function update_sub_task( $model ) {
        $class_name = class_basename( $model );

        if ( $class_name != 'Sub_Tasks' ) {
            return;
        }

        $original = $model->getOriginal();
        $new_status = $model->status;
        $old_status = $original['status'];

        if ( $new_status != $old_status ) {
            $this->update_sub_task_status( $model );
        }


        $task     = pm_get_task( $original['id'] );
        $task     = $task['data'];

        if ( !$this->has_permission( $task['project_id'], 'subTasks', 'update' ) ) {
            return;
        }

        $content = $this->has_task_update_content( $model );

        if ( empty( $content ) ) {
            return;
        }

        $parent = pm_get_task( $task['parent_id'] );
        $parent = $parent['data'];
        $parent_title = $parent['title'];
        $parent_url = pm_get_task_url( $parent['project_id'], $parent['task_list']['data']['id'], $parent['id'], $_POST['is_admin'] );

        $task_url          = $parent_url;
        $avatar            = $task['updater']['data']['avatar_url'];
        $display_name      = $task['updater']['data']['display_name'];
        $creator_url       = pm_get_user_url( $task['updater']['data']['id'], $_POST['is_admin'] );
        $title             = $task['title'];
        $description       = $task['description']['content'];
        $end               = $task['due_date']['date'];
        $assignees         = $task['assignees']['data'];
        $attachment_fields = [];
        $assginees_text    = [];
        $slack_title       = sprintf( "*<%s|%s>* has been updated", $task_url, $original['title'] );

        foreach ( $assignees as $key => $assignee ) {
            $user_url = pm_get_user_url( $assignee['id'], $_POST['is_admin'] );
            $assign_user_name = ucfirst( $assignee['display_name'] );

            $assginees_text[] = "<$user_url|$assign_user_name>";
        }

        if ( $assginees_text ) {
            $attachment_fields[] = [
                "title" => "",
                "value" => "*Assigned To*: _" . implode( ', ', $assginees_text) . "_",
                "short" => true
            ];
        }

        if ( $end ) {
            $due_date = date( 'Y-m-d', strtotime( $end ) );
            $due_date_str = strtotime( $due_date );

            $attachment_fields[] = [
                "title" => "",
                "value" => !empty( $content['due_date'] ) ? "*Due*: _<!date^$due_date_str^{date_short}^$task_url|$due_date>_" : '',
                "short" => true
            ];
        }

        if ( $description ) {
            $attachment_fields[] = [
                "title" => "",
                "value" => !empty( $content['description'] ) ? $description : '',
                "short" => false
            ];
        }

        $attachments = [
            [
                "author_name" => ucfirst( $display_name ),
                "author_link" => $creator_url,
                "author_icon" => $avatar,
                "title"       => !empty( $content['title'] ) ? ucfirst( $title ) : '',
                "title_link"  => !empty( $content['title'] ) ? $task_url : '',
                "mrkdwn_in"   => ["fields"],
                "fields"      => $attachment_fields,
                "footer_icon" => $this->icon,
                "ts" => current_time( 'timestamp', true )
            ]
        ];

        $this->send_notification( $attachments, $slack_title );
    }

    function new_subtask( $task, $params ) {
        $task  = $task['data'];

        if ( !$this->has_permission( $task['project_id'], 'subTasks', 'create' ) ) {
            return;
        }

        $parent = pm_get_task( $task['parent_id'] );
        $parent_title = $parent['data']['title'];
        $parent_url = pm_get_task_url( $task['project_id'], $task['task_list']['data']['id'], $task['parent_id'], $_POST['is_admin'] );

        $task_url          = pm_get_task_url( $task['project_id'], $task['task_list']['data']['id'], $task['parent_id'], $_POST['is_admin'] );
        $avatar            = $task['creator']['data']['avatar_url'];
        $display_name      = $task['creator']['data']['display_name'];
        $creator_url       = pm_get_user_url( $task['creator']['data']['id'], $_POST['is_admin'] );
        $title             = $task['title'];
        $description       = $task['description']['content'];
        $end               = $task['due_date']['date'];
        $assignees         = $task['assignees']['data'];
        $attachment_fields = [];
        $assginees_text    = [];
        $slack_title       = sprintf( "New sub task has been created in *<%s|%s>*", $parent_url, $parent_title );

        foreach ( $assignees as $key => $assignee ) {
            $user_url = pm_get_user_url( $assignee['id'], $_POST['is_admin'] );
            $assign_user_name = ucfirst( $assignee['display_name'] );

            $assginees_text[] = "<$user_url|$assign_user_name>";
        }

        if ( $assginees_text ) {
            $attachment_fields[] = [
                "title" => "",
                "value" => "*Assigned To*: _" . implode( ', ', $assginees_text) . "_",
                "short" => true
            ];
        }


        if ( $end ) {
            $due_date = date( 'Y-m-d', strtotime( $end ) );
            $due_date_str = strtotime( $due_date );

            $attachment_fields[] = [
                "title" => "",
                "value" => "*Due*: _<!date^$due_date_str^{date_short}^$task_url|$due_date>_",
                "short" => true
            ];
        }

        if ( $description ) {
            $attachment_fields[] = [
                "title" => "",
                "value" => $description,
                "short" => false
            ];
        }

        $attachments = [
            [
                "author_name" => ucfirst( $display_name ),
                "author_link" => $creator_url,
                "author_icon" => $avatar,
                "title"       => ucfirst( $title ),
                "title_link"  => $task_url,
                "mrkdwn_in"   => ["fields"],
                "fields"      => $attachment_fields,
                "footer_icon" => $this->icon,
                "ts" => current_time( 'timestamp', true )
            ]
        ];

        $this->send_notification( $attachments, $slack_title );
    }

    function has_permission( $project_id, $module, $key ) {
        //pmpr($module);
        $settings  = pm_get_setting( 'slack', $project_id );

        if ( ! $settings ) {
            return false;
        }

        $this->settings = $settings;
        $is_active = !empty( $settings['status'] ) && ( $settings['status'] == 'enable' ) ? true : false;

        if ( empty( $settings['options'] ) ) {
            $module = [];
        } else if ( !empty( $settings['options'][$module] ) ) {
            $module  = $settings['options'][$module];
        } else {
            $module  = [];
        }

        //pmpr( $module ); die();
       // $module    = empty( $settings['options'] ) ? [] : $settings['options'][$module];

        if ( ! $is_active ) {
            return false;
        }

        if ( ! empty( $module[$key] ) ) {
            return $module[$key] == 'false' ? false : true;
        } else {
            return true;
        }

        return false;
    }
    function user_delete( $model ) {

    }

    function user_assignee( $model ) {

    }

    function assignees_to( $new, $old, $task ) {

    }

    function update_task_status( $new, $old, $task ) {
        $task   = pm_get_task( $task->id );
        $task   = $task['data'];
        $status = $task['status'];

        if ( $status == 'complete' ) {
            if ( !$this->has_permission( $task['project_id'], 'tasks', 'complete' ) ) {
                return;
            }
        } else {
            if ( !$this->has_permission( $task['project_id'], 'tasks', 'incomplete' ) ) {
                return;
            }
        }

        $task_title     = $task['title'];
        $task_url       = pm_get_task_url( $task['project_id'], $task['task_list']['data']['id'], $task['id'], $_POST['is_admin'] );
        $creator_avatar = $task['updater']['data']['avatar_url'];
        $display_name   = $task['updater']['data']['display_name'];
        $creator_url    = pm_get_user_url( $task['updater']['data']['id'], $_POST['is_admin'] );
        $new_date       = date( 'F j, Y', strtotime( $new ) );
        $old_date       = date( 'F j, Y', strtotime( $old ) );

        if ( $status == 'complete' ) {
            $body = sprintf( "%s has been completed.", "~*<$task_url|$task_title>*~" );
        }

        if ( $status == 'incomplete' ) {
            $body = sprintf( "%s has been re-opened.", "*<$task_url|$task_title>*" );
        }

        $attachments = [
            [
                "author_name" => ucfirst( $display_name ),
                "author_link" => $creator_url,
                "author_icon" => $creator_avatar,
                "mrkdwn_in"   => ["fields"],
                "fields" => [
                    [
                        "title" => "",
                        "value" => $body,
                        "short" => false
                    ],
                ],
                "footer_icon" => $this->icon,
                "ts" => current_time( 'timestamp', true )
            ]
        ];

        $this->send_notification( $attachments, '' );
    }

    function task_due_date_update( $new, $old, $task ) {
        $task           = pm_get_task( $task->id );
        $task           = $task['data'];
        $task_title     = $task['title'];
        $task_url       = pm_get_task_url( $task['project_id'], $task['task_list']['data']['id'], $task['id'], $_POST['is_admin'] );
        $creator_avatar = $task['updater']['data']['avatar_url'];
        $display_name   = $task['updater']['data']['display_name'];
        $creator_url    = pm_get_user_url( $task['updater']['data']['id'], $_POST['is_admin'] );
        $new_date       = date( 'F j, Y', strtotime( $new ) );
        $old_date       = date( 'F j, Y', strtotime( $old ) );

        $attachments = [
            [
                "author_name" => ucfirst( $display_name ),
                "author_link" => $creator_url,
                "author_icon" => $creator_avatar,
                "mrkdwn_in"   => ["fields"],
                "fields" => [
                    [
                        "title" => "",
                        "value" => "Change the due date \"$old_date\" to $new_date for *<$task_url|$task_title>*",
                        "short" => false
                    ],
                ],
                "footer_icon" => $this->icon,
                "ts" => current_time( 'timestamp', true )
            ]
        ];

        $this->send_notification( $attachments, '' );
    }

    function task_description_update( $new, $old, $task ) {
        $task           = pm_get_task( $task->id );
        $task           = $task['data'];
        $content        = $this->parse_description( $task['description']['content'] );
        $task_title     = $task['title'];
        $task_url       = pm_get_task_url( $task['project_id'], $task['task_list']['data']['id'], $task['id'], $_POST['is_admin'] );
        $creator_avatar = $task['updater']['data']['avatar_url'];
        $display_name   = $task['updater']['data']['display_name'];
        $creator_url    = pm_get_user_url( $task['updater']['data']['id'], $_POST['is_admin'] );
        $slack_title    = "Updated the description of *<$task_url|$task_title>*";

        $attachments = [
            [
                "author_name" => ucfirst( $display_name ),
                "author_link" => $creator_url,
                "author_icon" => $creator_avatar,
                "mrkdwn_in"   => ["fields"],
                "fields" => [
                    [
                        "title" => "",
                        "value" => $content,
                        "short" => false
                    ],
                ],
                "footer_icon" => $this->icon,
                "ts" => current_time( 'timestamp', true )
            ]
        ];

        $this->send_notification( $attachments, $slack_title );
    }

    function task_title_update( $new, $old, $task ) {
        $task           = pm_get_task( $task->id );
        $task           = $task['data'];
        $task_url       = pm_get_task_url( $task['project_id'], $task['task_list']['data']['id'], $task['id'], $_POST['is_admin'] );
        $creator_avatar = $task['updater']['data']['avatar_url'];
        $display_name   = $task['updater']['data']['display_name'];
        $creator_url    = pm_get_user_url( $task['updater']['data']['id'], $_POST['is_admin'] );

        $attachments = [
            [
                "author_name" => ucfirst( $display_name ),
                "author_link" => $creator_url,
                "author_icon" => $creator_avatar,
                "mrkdwn_in"   => ["fields"],
                "fields" => [
                    [
                        "title" => "",
                        "value" => "Renamed the task \"$old\" to *<$task_url|$new>*",
                        "short" => false
                    ],
                ],
                "footer_icon" => $this->icon,
                "ts" => current_time( 'timestamp', true )
            ]
        ];

        $this->send_notification( $attachments, '' );
    }

    function update_task_comment( $comment, $params ) {
        $comment = $comment['data'];
        if ( $comment['commentable_type'] != 'task' ) {
            return;
        }

        $task = pm_get_task( $comment['commentable_id'] );
        $task = $task['data'];

        if ( !$this->has_permission( $task['project_id'], 'tasks', 'updateComment' ) ) {
            return;
        }

        $comment_content = $this->parse_description( $comment['content'] );

        $comment_files   = $comment['files']['data'];
        $task_title      = $task['title'];
        $task_url        = pm_get_task_url( $task['project_id'], $task['task_list']['data']['id'], $task['id'], $_POST['is_admin'] );
        $creator_name    = $comment['creator']['data']['display_name'];
        $creator_url     = pm_get_user_url( $task['creator']['data']['id'], $_POST['is_admin'] );
        $creator_avatar  = $comment['creator']['data']['avatar_url'];
        $creator_id      = $comment['creator']['data']['id'];
        $slack_title     = sprintf( "Update comment on %s", "*<$task_url|$task_title>*" );
        $attachments     = [];
        $attach_field    = [];

        foreach ( $comment_files as $key => $comment_file ) {
            $file_name    = $comment_file['name'];
            $project_id   = $comment_file['fileable']['project_id'];
            $file_id      = $comment_file['attachment_id'];
            $dwonload_url = pm_get_file_download_url( $project_id, $creator_id, $file_id );
            $attachments[] = "[$file_name](<$dwonload_url|$dwonload_url>)\n";
        }

        if ( $attachments ) {
            $attach_field = [
                "title" => "Attachments:",
                "value" => implode( '', $attachments ),
                "short" => false
            ];
        }

        $attachments = [
            [
                "author_name" => ucfirst( $creator_name ),
                "author_link" => $creator_url,
                "author_icon" => $creator_avatar,
                //"title"       => $task_title,
                //"title_link"  => $task_url,
                "mrkdwn_in"   => ["fields"],
                "fields" => [

                    [
                        "title" => "",
                        "value" => $comment_content,
                        "short" => false
                    ],

                    $attach_field

                ],
                "footer_icon" => $this->icon,
                "ts" => current_time( 'timestamp', true )
            ]
        ];

        $this->send_notification( $attachments, $slack_title );
    }

    function new_task_comment( $comment, $params ) {
        $comment = $comment['data'];
        if ( $comment['commentable_type'] != 'task' ) {
            return;
        }

        $task = pm_get_task( $comment['commentable_id'] );
        $task = $task['data'];

        if ( !$this->has_permission( $task['project_id'], 'tasks', 'createComment' ) ) {
            return;
        }

        $comment_content =  $this->parse_description( $comment['content'] );


        $comment_files   = $comment['files']['data'];
        $task_title      = $task['title'];
        $task_url        = pm_get_task_url( $task['project_id'], $task['task_list']['data']['id'], $task['id'], $_POST['is_admin'] );
        $creator_name    = $comment['creator']['data']['display_name'];
        $creator_url     = pm_get_user_url( $task['creator']['data']['id'], $_POST['is_admin'] );
        $creator_avatar  = $comment['creator']['data']['avatar_url'];
        $creator_id      = $comment['creator']['data']['id'];
        $slack_title     = sprintf( "New comment on %s", "*<$task_url|$task_title>*" );
        $attachments     = [];
        $attach_field    = [];

        foreach ( $comment_files as $key => $comment_file ) {
            $file_name    = $comment_file['name'];
            $project_id   = $comment_file['fileable']['project_id'];
            $file_id      = $comment_file['attachment_id'];
            $dwonload_url = pm_get_file_download_url( $project_id, $creator_id, $file_id );
            $attachments[] = "[$file_name](<$dwonload_url|$dwonload_url>)\n";
        }

        if ( $attachments ) {
            $attach_field = [
                "title" => "Attachments:",
                "value" => implode( '', $attachments ),
                "short" => false
            ];
        }

        $attachments = [
            [
                "author_name" => ucfirst( $creator_name ),
                "author_link" => $creator_url,
                "author_icon" => $creator_avatar,
                //"title"       => $task_title,
                //"title_link"  => $task_url,
                "mrkdwn_in"   => ["fields"],
                "fields" => [

                    [
                        "title" => "",
                        "value" => $comment_content,
                        "short" => false
                    ],

                    $attach_field

                ],
                "footer_icon" => $this->icon,
                "ts" => current_time( 'timestamp', true )
            ]
        ];

        $this->send_notification( $attachments, $slack_title );

    }

    function has_task_update_content( $model ) {

        $content = [];
        $original = $model->getOriginal();

        foreach ( $model->getDirty() as $key => $value ) {
            switch ( $key ) {
                case 'title':
                    $content['title'] = $value;
                    break;

                case 'description':
                    $content['description'] = $value;
                    break;

                case 'due_date':
                    $formated_due = format_date( $value );
                    $original_due = date( 'Y-m-d', strtotime( $original['due_date'] ) );
                    $updated_due  = date( 'Y-m-d', strtotime( $formated_due['date'] ) );

                    if ( $original_due != $updated_due ) {
                        $content['due_date'] = $formated_due['date'];
                    }
                    break;
            }
        }

        return $content;
    }

    function update_task( $model ) {
        $class_name = class_basename( $model );

        if ( $class_name != 'Task' ) {
            return;
        }

        $original = $model->getOriginal();
        $task     = pm_get_task( $original['id'] );
        $task     = $task['data'];

        if ( !$this->has_permission( $task['project_id'], 'tasks', 'update' ) ) {
            return;
        }

        $content = $this->has_task_update_content( $model );

        if ( empty( $content ) ) {
            return;
        }

        $is_admin          = $_POST['is_admin'];
        $task_url          = pm_get_task_url( $task['project_id'], $task['task_list']['data']['id'], $task['id'], $is_admin );
        $avatar            = $task['updater']['data']['avatar_url'];
        $display_name      = $task['updater']['data']['display_name'];
        $creator_url       = pm_get_user_url( $task['updater']['data']['id'], $_POST['is_admin'] );
        $title             = $task['title'];
        $description       = $task['description']['content'];
        $end               = $task['due_date']['date'];
        $assignees         = $task['assignees']['data'];
        $attachment_fields = [];
        $assginees_text    = [];
        $slack_title       = sprintf( "*<%s|%s>* has been updated", $task_url, $original['title'] );

        foreach ( $assignees as $key => $assignee ) {
            $user_url = pm_get_user_url( $assignee['id'], $_POST['is_admin'] );
            $assign_user_name = ucfirst( $assignee['display_name'] );

            $assginees_text[] = "<$user_url|$assign_user_name>";
        }

        if ( $assginees_text ) {
            $attachment_fields[] = [
                "title" => "",
                "value" => "*Assigned To*: _" . implode( ', ', $assginees_text) . "_",
                "short" => true
            ];
        }

        if ( $end ) {
            $due_date = date( 'Y-m-d', strtotime( $end ) );
            $due_date_str = strtotime( $due_date );

            $attachment_fields[] = [
                "title" => "",
                "value" => !empty( $content['due_date'] ) ? "*Due*: _<!date^$due_date_str^{date_short}^$task_url|$due_date>_" : '',
                "short" => true
            ];
        }


        $description = $this->parse_description( $description );

        if ( $description ) {
            $attachment_fields[] = [
                "title" => "",
                "value" => !empty( $content['description'] ) ?  $description : '',
                "short" => false
            ];
        }

        $attachments = [
            [
                "author_name" => ucfirst( $display_name ),
                "author_link" => $creator_url,
                "author_icon" => $avatar,
                "title"       => !empty( $content['title'] ) ? ucfirst( $title ) : '',
                "title_link"  => !empty( $content['title'] ) ? $task_url : '',
                "mrkdwn_in"   => ["fields"],
                "fields"      => $attachment_fields,
                "footer_icon" => $this->icon,
                "ts" => current_time( 'timestamp', true )
            ]
        ];

        $this->send_notification( $attachments, $slack_title );
    }

    function parse_description( $description ) {
        if ( empty( $description ) ) {
            return '';
        }

        $dom = new \DOMDocument();

        $dom->loadHTML( $description );
        $links = $dom->getElementsByTagName('a');

        $i = $links->length - 1;
        while ( $i > -1 ) {
            $element = $links->item($i);
            $ignore = false;

            $anchor = $element->getAttribute('href');
            $anchor_text = $element->textContent;

            $newelement = $dom->createTextNode( "<$anchor|$anchor_text>" );
            $element->parentNode->replaceChild($newelement, $element);
            $i--;
        }

        $description = $dom->saveHTML();

        $description = strip_tags( $description, '<br><strong><em><del><li><code><pre><a></a>' );
        $description = str_replace( array('<br />', '<br>'), "\n", $description );
        $description = str_replace( array('<strong>', '</strong>'), array(' *', '* '), $description );
        $description = str_replace( array('<em>', '</em>'), array(' _', '_ '), $description );
        $description = str_replace( array('<del>', '</del>'), array(' ~', '~ '), $description );
        $description = str_replace( array('<li>', '</li>'), array('•', ''), $description );
        $description = str_replace( array('<code>', '</code>'), array(' `', '` '), $description );
        $description = str_replace( array('<pre>', '</pre>'), array(' ```', '``` '), $description );

        return htmlspecialchars_decode( $description );
    }

    function new_task( $task, $params ) {
        $task  = $task['data'];

        if ( !$this->has_permission( $task['project_id'], 'tasks', 'create' ) ) {
            return;
        }

        $is_admin          = $_POST['is_admin'];
        $task_url          = pm_get_task_url( $task['project_id'], $task['task_list']['data']['id'], $task['id'], $is_admin );
        $list_url          = pm_get_list_url( $task['project_id'], $task['task_list']['data']['id'], $is_admin );
        $avatar            = $task['creator']['data']['avatar_url'];
        $display_name      = $task['creator']['data']['display_name'];
        $creator_url       = pm_get_user_url( $task['creator']['data']['id'], $_POST['is_admin'] );
        $title             = $task['title'];
        $description       = $task['description']['content'];
        $end               = $task['due_date']['date'];
        $assignees         = $task['assignees']['data'];
        $attachment_fields = [];
        $assginees_text    = [];
        $slack_title       = sprintf( "New task has been created in *<%s|%s>*", $list_url, $task['task_list']['data']['title'] );

        foreach ( $assignees as $key => $assignee ) {
            $user_url = pm_get_user_url( $assignee['id'], $_POST['is_admin'] );
            $assign_user_name = ucfirst( $assignee['display_name'] );

            $assginees_text[] = "<$user_url|$assign_user_name>";
        }

        if ( $assginees_text ) {
            $attachment_fields[] = [
                "title" => "",
                "value" => "*Assigned To*: _" . implode( ', ', $assginees_text) . "_",
                "short" => true
            ];
        }


        if ( $end ) {
            $due_date = date( 'Y-m-d', strtotime( $end ) );
            $due_date_str = strtotime( $due_date );

            $attachment_fields[] = [
                "title" => "",
                "value" => "*Due*: _<!date^$due_date_str^{date_short}^$task_url|$due_date>_",
                "short" => true
            ];
        }

        if ( $description ) {
            $attachment_fields[] = [
                "title" => "",
                "value" => $description,
                "short" => false
            ];
        }

        $attachments = [
            [
                "author_name" => ucfirst( $display_name ),
                "author_link" => $creator_url,
                "author_icon" => $avatar,
                "title"       => ucfirst( $title ),
                "title_link"  => $task_url,
                "mrkdwn_in"   => ["fields"],
                "fields"      => $attachment_fields,
                "footer_icon" => $this->icon,
                "ts" => current_time( 'timestamp', true )
            ]
        ];

        $this->send_notification( $attachments, $slack_title );
    }

    /**
     * Subscribe a user when a form is submitted
     *
     * @param  int $entry_id
     * @param  int $form_id
     * @param  int $page_id
     * @param  array $form_settings
     *
     * @return void
     */
    public function send_notification( $attachments, $slack_title ) {

        if ( empty( $this->settings['webhook'] ) ) {
            return;
        }

        $data = array(
            'payload' => json_encode(
                array (
                    "username"    => "WP Project Manager",
                    "icon_url"    => pm_config('frontend.assets_url') . '/images/pm-log.gif',
                    "text"        => ucfirst( $slack_title ),
                    'attachments' => $attachments
                )
            )
        );

        $posting_to_slack = wp_remote_post( $this->settings['webhook'], array(
            'method'      => 'POST',
            'timeout'     => 30,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => false,
            'headers'     => array(),
            'body'        => $data,
            'cookies'     => array()
        ) );
    }
}








