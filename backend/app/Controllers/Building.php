<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\BuildingModel;
use App\Models\RequestModel;
use App\Models\UsersModel;
use \Firebase\JWT\JWT;

class Building extends BaseController
{
    public function __construct(){
        //Models
        $this->buildingModel = new BuildingModel();
        $this->usersModel = new UsersModel();
        $this->reqModel = new RequestModel();
    }
    
    public function getAllBuilding(){
        
        $list = [];
        $list['list'] = $this->buildingModel->getAllUnits();

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }   
    public function getAllBuildingAvailable(){
        
        $list = [];
        $list['list'] = $this->buildingModel->where('userId', 0)->findAll();

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }   
    public function submitAssignUnit(){
        
        //Get API Request Data from NuxtJs
        $payload = $this->request->getJSON();
        $payload = json_decode(json_encode($payload), true);
        $payload['usersData']['password'] = 'password';
        $payload['usersData']['startDate'] = date('j-M-y', strtotime($payload['usersData']['startDate']));
        $payload['usersData']['endDate'] = date('j-M-y', strtotime($payload['usersData']['endDate']));
        // print_r($payload);
        // exit;
 
        //INSERT QUERY TO APPLICATION
        $query = $this->usersModel->insert($payload['usersData']);
        
        if($query){
            $lastInserted = $this->usersModel->insertID();
            
            //Update the Building Info
            $where = ['id' => $payload['buildingInfo']['id']];
            $updateData = [
                'userId' => $lastInserted,
                'status' => 1,
            ];

            $update = $this->buildingModel->updateBuildingInfo($where, $updateData);
            
            if($update){
                $response = [
                    'title' => 'Unit Assignment Complete',
                    'message' => 'Your application has been submitted.'
                ];
     
                return $this->response
                        ->setStatusCode(200)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            } else {
                $response = [
                    'title' => 'Assignment Failed!',
                    'message' => 'Please check your data.'
                ];
     
                return $this->response
                        ->setStatusCode(400)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            }
            
        } else {
            $response = [
                'title' => 'Assignment Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }   

    public function getUnitHistoryById($appId){

        // $data = $this->reqModel->where(['id'=>$appId])->find();
        $list = [];
        $list['list'] = $this->reqModel->where('buildingId', $appId)->findAll();

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'Something went wrong!'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }


    }

}