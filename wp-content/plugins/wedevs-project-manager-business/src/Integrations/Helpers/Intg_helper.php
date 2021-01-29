<?php

namespace WeDevs\PM_Pro\Integrations\Helpers;

use WeDevs\PM_Pro\Integrations\Models\Integrations ;
use WeDevs\PM\Activity\Models\Activity ;
use WeDevs\PM\Comment\Models\Comment;
use WeDevs\PM\User\Models\User_Role;
use WeDevs\PM\Task\Models\Task;


class Intg_helper {

        public function __construct(){

        }

        public static function issue_created($request,$request_body,$task_controller){
            $data = [];
            $project_id = $request->get_param('project_id'); // [project_id] => 39
            $data['assignees'] = array(0); // [assignees] => Array([0] => 0)
            $data['title'] = $request_body->issue->title; // [title] => hahaha
            $data['description'] = $request_body->issue->body; // [description] => hahaha
            $data['estimation'] = 0; //[estimation] => 0
            $data['board_id'] = ''; // [board_id] => 39
            $data['list_id'] = $project_id; // [list_id] => 39
            $data['privacy'] = false; // [privacy] => false
            $data['is_admin'] = 1; // [is_admin] => 1
            $request->set_param('action', '');
            $request->set_param('issue', '');
            $request->set_param('repository', '');
            $request->set_param('sender', '');
            $request->set_query_params($data);
            $task_contoller_response =  $task_controller->store($request);
            $super_admin = get_super_admins()[0] ;
	        $super_admin = get_user_by('login',$super_admin);
            Task::where('id',$task_contoller_response['data']['id'])
                ->update([
                	'created_by' => $super_admin->ID,
	                'updated_by' => $super_admin->ID
                ]);
            $activity_data = $task_contoller_response['activity']['data'] ;
            $activity_data['remote_user_id'] = self::if_remote_user_has_permission($request_body->sender->login,$project_id,$request_body->source_from);
            self::update_activity_data($activity_data,$request_body->source_from,$request_body->sender->login);
            return self::set_integration_data(
                $request_body->action,
                $task_contoller_response['data']['project_id'],
                $request_body->issue->id,
                $task_contoller_response['data']['id'],
                'issues',
                $request_body->source_from,
                $request_body->sender->login
            );
        }

        public static function issue_delete($request,$request_body,$task_controller){
            $task = Integrations::where('primary_key', $request_body->issue->id)
                ->where('type','issues')
                ->first();
            $request->set_param('project_id', $task->project_id);
            $request->set_param('task_id', $task->foreign_key);
            $task_contoller_response = $task_controller->destroy($request);
            $activity_data = $task_contoller_response['activity']['data'] ;
            $activity_data['remote_user_id'] = self::if_remote_user_has_permission($request_body->sender->login,$task->project_id,$request_body->source_from);
            self::update_activity_data($activity_data,$request_body->source_from,$request_body->sender->login);
        }

        public static function issue_closed($request,$request_body,$task_controller){
            $task = Integrations::where( 'primary_key', $request_body->issue->id )
                ->where('type','issues')
                ->first();
            sleep(3);
            $request->set_param('task_id',$task->foreign_key);
            $request->set_param('status', 1);
            $task_controller_response = $task_controller->change_status($request);
            $activity_data = $task_controller_response['activity']['data'] ;
            $activity_data['remote_user_id'] = self::if_remote_user_has_permission($request_body->sender->login,$task->project_id,$request_body->source_from);
            self::update_activity_data($activity_data,$request_body->source_from,$request_body->sender->login);
        }

        public static function issue_reopened($request,$request_body,$task_controller){
            $task = Integrations::where( 'primary_key', $request_body->issue->id )
                ->where('type','issues')
                ->first();
            $request->set_param('task_id',$task->foreign_key);
            $request->set_param('status', 0);
            $task_controller_response = $task_controller->change_status($request);
            $activity_data = $task_controller_response['activity']['data'] ;
            $activity_data['remote_user_id'] = self::if_remote_user_has_permission($request_body->sender->login,$task->project_id,$request_body->source_from);
            self::update_activity_data($activity_data,$request_body->source_from,$request_body->sender->login);
        }

        public static function issue_updated($request,$request_body,$task_controller){
            $task = Integrations::where( 'primary_key', $request_body->issue->id )
                ->where('type','issues')
                ->first();
            $request->set_param('project_id',$task->project_id);
            $request->set_param('task_id',$task->foreign_key);
            $request->set_param('title',$request_body->issue->title);
            $request->set_param('description',$request_body->issue->body);
            $task_controller_response = $task_controller->update($request);

            $activity_data = $task_controller_response['activity']['data'] ;
            $activity_data['remote_user_id'] = self::if_remote_user_has_permission($request_body->sender->login,$task->project_id,$request_body->source_from);
            self::update_activity_data($activity_data,$request_body->source_from,$request_body->sender->login);
        }

        public static function comment_created($request,$request_body,$comment_controller){
            $task = Integrations::where( 'primary_key', $request_body->issue->id )
                ->where('type','issues')
                ->first();
            $request->set_param('project_id',$task->project_id);
            $request->set_param('commentable_id',$task->foreign_key);
            $request->set_param('content','<p>'.$request_body->comment->body.'</p>');
            $request->set_param('commentable_type','task');
            $comment_contoller_response = $comment_controller->store($request);
            $activity_data = $comment_contoller_response['activity']['data'] ;
            $activity_data['remote_user_id'] = self::if_remote_user_has_permission($request_body->sender->login,$task->project_id,$request_body->source_from);
            self::update_activity_data($activity_data,$request_body->source_from,$request_body->sender->login);
            return self::set_integration_data(
                $request_body->action,
                $task->project_id,
                $request_body->comment->id,
                $comment_contoller_response['data']['id'],
                'issues_comments',
                $request_body->source_from,
                $request_body->sender->login
            );
        }

        public static function comment_delete($request,$request_body,$comment_controller){
            $comment = Integrations::where( 'primary_key', $request_body->comment->id )
                ->where('type','issues_comments')
                ->first();
            $request->set_param('comment_id',$comment->foreign_key);
            $comment_controller_response = $comment_controller->destroy($request);
            $activity_data = $comment_controller_response['activity']['data'] ;
            $activity_data['remote_user_id'] = self::if_remote_user_has_permission($request_body->sender->login,$comment->project_id,$request_body->source_from);
            self::update_activity_data($activity_data,$request_body->source_from,$request_body->sender->login);
        }

        public static function comment_updated($request,$request_body,$comment_controller){

            $integrations = Integrations::where( 'primary_key', $request_body->comment->id )
                ->where('type','issues_comments')
                ->first();
            $comment = Comment::where( 'id', $integrations->foreign_key )->first();
            $request->set_param('project_id',$comment->project_id);
            $request->set_param('comment_id',$comment->id);
            $request->set_param('content',$request_body->comment->body);
            $request->set_param('commentable_id',$comment->commentable_id);
            $request->set_param('commentable_type','task');
            $comment_controller_response = $comment_controller->update($request);
            $activity_data = $comment_controller_response['activity']['data'] ;
            $activity_data['remote_user_id'] = self::if_remote_user_has_permission($request_body->sender->login,$integrations->project_id,$request_body->source_from);
            self::update_activity_data($activity_data,$request_body->source_from,$request_body->sender->login);
        }

        public static function if_remote_user_has_permission($username,$projecId,$from){
            error_reporting(0);
            $user = reset(
                get_users(
                    array(
                        'meta_key' => strtolower($from),
                        'meta_value' => $username,
                        'number' => 1,
                        'count_total' => false
                    )
                )
            );
            //print_r($user->id);
            //$user_id = username_exists($username);
            $user_id = $user->id;
            $project_id = $projecId ;
            $user_permission = User_Role::where('user_id',$user_id)
                ->where('project_id',$project_id)
                ->first();
            if($user_permission){
                return $user_permission->user_id ;
            }else{
                return 0 ;
            }
        }

        public static function update_activity_data($activity_data,$from,$username){
            $activity_data['meta']['int_source'] = $from ;
            $activity_data['meta']['username'] = $username ;
            Activity::where('id',$activity_data['id'] )
                ->update([
                    'meta' => serialize($activity_data['meta']),
                    'actor_id'=> $activity_data['remote_user_id']
                ]);
        }

        public static function set_integration_data($action,$project_id,$primary_key,$foreign_key,$type,$source,$username){
        $result = [
            'action' => 	$action,
            'project_id' => 	$project_id,
            'primary_key' =>  	$primary_key,
            'foreign_key' => $foreign_key,
            'type' => $type,
            'source' => $source,
            'username' => $username
        ];
        $integrations = Integrations::create( $result );
        return $integrations ;
    }

        public static function modify_activity_response($response){
            for($i=0; $i < count($response['data']);$i++){
                if(isset($response['data'][$i]['meta']['int_source']) && empty($response['data'][$i]['actor']['data']) ){
                    $response['data'][$i]['actor']['data']['display_name'] = '['.ucfirst($response['data'][$i]['meta']['int_source']).'] '. $response['data'][$i]['meta']['username']  ;
                    $response['data'][$i]['actor']['data']['avatar_url'] = "http://2.gravatar.com/avatar/2ce274bc61d00731e73c033d90cb0d73?s=96&d=mm&r=g";
                }
            }
            return $response;
        }

        public static function set_integrated_creator($response){

            $project_id = $response['data']['project_id'] ;
            $intg_comments = Integrations::where('project_id', $project_id)
                ->where('type','issues_comments')
                ->get();
            $intg_comments_blank = [];
            foreach($intg_comments as $intgc){
                $intg_comments_blank[$intgc['foreign_key']] = $intgc ;
            }
            $project_comments = $response['data']['comments']['data'] ;
            $comments_blank = [];
            foreach($project_comments as $cmnt){
                if(empty($cmnt['creator']['data'])){
                    $cmnt['creator']['data']['username'] = $intg_comments_blank[$cmnt['id']]['source'];
                    $cmnt['creator']['data']['nicename'] = $intg_comments_blank[$cmnt['id']]['source'];
                    $cmnt['creator']['data']['email'] = '';
                    $cmnt['creator']['data']['display_name'] = '['.$intg_comments_blank[$cmnt['id']]['source'].'] ' . $intg_comments_blank[$cmnt['id']]['username'];
                    $cmnt['creator']['data']['manage_capability'] = 1;
                    $cmnt['creator']['data']['create_capability'] = 1;
                    $cmnt['creator']['data']['avatar_url'] = "http://2.gravatar.com/avatar/2ce274bc61d00731e73c033d90cb0d73?s=96&d=mm&r=g";
                    $cmnt['creator']['data']['roles'] = [];
                    $comments_blank[] = $cmnt ;
                }else{
                    $comments_blank[] = $cmnt ;
                }
            }
            return  $comments_blank ;
        }
}