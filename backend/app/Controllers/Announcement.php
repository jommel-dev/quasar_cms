<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\AnnounceModel;

class Announcement extends BaseController
{

    public function __construct(){
        $this->announceModel = new AnnounceModel();
    }

    public function saveAnnouncement(){
        // Check Auth header bearer
        $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        if(!$authorization){
            $response = [
                'message' => 'Unauthorized Access'
            ];

            return $this->response
                    ->setStatusCode(401)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            exit();
        }

        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 
        $data->subject = json_encode($data->subject);
        $data->tags = json_encode($data->tags);
        
        $query = $this->announceModel->insert($data);

        if($query){

            $response = [
                'title' => 'Announcement Posted',
                'message' => 'Announcement succesfully posted.'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            
        } else {
            $response = [
                'title' => 'Registration Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        // print_r($data);
        // exit();
        
    }
    public function getAnnouncementList(){
        // Check Auth header bearer
        $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        if(!$authorization){
            $response = [
                'message' => 'Unauthorized Access'
            ];

            return $this->response
                    ->setStatusCode(401)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            exit();
        }

        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 
        
        $list = [];
        $where = [
            "status" => 1,
        ];
        // $list['list'] = $this->userModel->getAllUserInfo($where);
        $query = $this->announceModel->getAllAnnouncement($where);
        foreach ($query as $key => $value) {
            $list['list'][$key] = [
                "id" => $value->id,
                "subject" => json_decode($value->subject),
                "tags" => json_decode($value->tags),
                "title" =>  $value->title,
                "content" =>  $value->description,
                "createdBy" =>  $value->createdBy,
                "createdDate" =>  $value->createdDate,
            ];
        }
        
        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Fetch Failed!',
                'message' => 'No Data Found'
            ];
 
            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }
    public function getAnnouncementListPublic(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 
        
        $list = [];
        $where = [
            "status" => 1,
        ];
        // $list['list'] = $this->userModel->getAllUserInfo($where);
        $query = $this->announceModel->getAllAnnouncement($where);
        foreach ($query as $key => $value) {
            $list['list'][$key] = [
                "id" => $value->id,
                "subject" => json_decode($value->subject),
                "tags" => json_decode($value->tags),
                "title" =>  $value->title,
                "content" =>  $value->description,
                "createdBy" =>  $value->createdBy,
                "createdDate" =>  $value->createdDate,
                "image" =>  '/imgs/cardHeading.png',
                
            ];
        }
        
        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Fetch Failed!',
                'message' => 'No Data Found'
            ];
 
            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }
    

}