<?php
namespace WeDevs\PM_Pro\Integrations\Controllers;

use WP_REST_Request;
use WeDevs\PM\Task\Controllers\Task_Controller;
use WeDevs\PM\Comment\Controllers\Comment_Controller;
use WeDevs\PM_Pro\Integrations\Helpers\Intg_helper as Intg_helper;


class Integrations_Controller {

    public function index( WP_REST_Request $request ) {

        $token = $request->get_param('token');
        $project_id = $request->get_param('project_id');
        $project_token = get_option('projectId_git_bit_hash_'.$project_id);
        if($token == $project_token){
            $project_setting_gitbit = pm_get_setting( 'git_bit', $project_id );
            if($project_setting_gitbit == null || $project_setting_gitbit['status'] != 'enable' || empty($project_setting_gitbit) ){
                return [
                    'msg' => 'Settings is not enabled'
                ] ;
            }
            $request_header = $request->get_header('X-GitHub-Delivery');
            if(!empty($request_header) && isset($request_header)){
                $request_body = json_decode($request->get_body()) ;
                if(property_exists($request_body, 'issue')) {
                    $task_controller = new Task_Controller();
                    $comment_controller = new Comment_Controller();
                    $request_body->source_from = 'Github' ;
                    if($request_body->action == 'opened') {
                        return  Intg_helper::issue_created($request,$request_body,$task_controller);
                    }
                    if($request_body->action == 'deleted'){
                        if($request->get_header('X-GitHub-Event') == 'issues') {
                            Intg_helper::issue_delete($request,$request_body,$task_controller);
                        }
                        if($request->get_header('X-GitHub-Event') == 'issue_comment') {
                            Intg_helper::comment_delete($request,$request_body,$comment_controller);
                        }
                    }
                    if($request_body->action == 'closed'){
                        Intg_helper::issue_closed($request,$request_body,$task_controller);
                    }
                    if($request_body->action == 'reopened'){
                        Intg_helper::issue_reopened($request,$request_body,$task_controller);
                    }
                    if($request_body->action == 'created'){
                        Intg_helper::comment_created($request,$request_body,$comment_controller);
                    }
                    if($request_body->action == 'edited'){
                        if($request->get_header('X-GitHub-Event') == 'issues'){
                            Intg_helper::issue_updated($request,$request_body,$task_controller);
                        }
                        if($request->get_header('X-GitHub-Event') == 'issue_comment'){
                            Intg_helper::comment_updated($request,$request_body,$comment_controller);
                        }
                    }
                }
            }
            $request_header_bitbucket = $request->get_header('X-Hook-UUID');
            if(!empty($request_header_bitbucket) && isset($request_header_bitbucket)){
                $request_type = $request->get_header('X-Event-Key');
                $request_body = json_decode($request->get_body()) ;
                if(property_exists($request_body, 'issue')) {
                    $task_controller = new Task_Controller();
                    $comment_controller = new Comment_Controller();
                    $request_body->source_from = 'Bitbucket' ;
                    if($request_type == 'issue:created') {
                        $request_body->issue->body = $request_body->issue->content->html ;
                        $request_body->issue->id = ($request_body->issue->id).ord($request_body->actor->username);
                        $request_body->sender->login = $request_body->actor->username ;
                        return  Intg_helper::issue_created($request,$request_body,$task_controller);
                    }
                    if($request_type == 'issue:updated') {
                        $request_body->issue->body = $request_body->issue->content->html ;
                        $request_body->issue->id = ($request_body->issue->id).ord($request_body->actor->username);
                        $request_body->sender->login = $request_body->actor->username ;
                        Intg_helper::issue_updated($request,$request_body,$task_controller);
                    }
                    if($request_type == 'issue:comment_created') {
                        $request_body->comment->body = $request_body->comment->content->html ;
                        $request_body->issue->id = ($request_body->issue->id).ord($request_body->actor->username);
                        $request_body->sender->login = $request_body->actor->username ;
                        Intg_helper::comment_created($request,$request_body,$comment_controller);
                    }
                }
            }
        }else{
            return [
                'msg' => 'Wrong Url'
            ] ;
        }
    }
}