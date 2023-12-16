<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\UsersModel;
use App\Models\AuthModel;
use App\Models\MiscModel;
use \Firebase\JWT\JWT;

class Users extends BaseController
{
    public function __construct(){
        //Models
        $this->userModel = new UsersModel();
        $this->authModel = new AuthModel();
        $this->miscModel = new MiscModel();
    }

    public function getUserDetails(){
        // Check Auth header bearer
        // $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        // if(!$authorization){
        //     $response = [
        //         'message' => 'Unauthorized Access'
        //     ];

        //     return $this->response
        //             ->setStatusCode(401)
        //             ->setContentType('application/json')
        //             ->setBody(json_encode($response));
        //     exit();
        // }

        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 

        //Select Query for finding User Information
        $user = $this->authModel->where(['id' => $data->id])->get()->getRow();
        
        //Set Api Response return to the FE
        if($user){

            if($user->status == 1){
                $user->userType = $this->miscModel->getUserType($user->userType);
                $user->branch = $this->miscModel->getBranch($user->branchId);
             
          
                return $this->response
                        ->setStatusCode(200)
                        ->setContentType('application/json')
                        ->setBody(json_encode($user));
            } else {
                $response = [
                    'title' => 'Account Deactivated',
                    'message' => 'Please contact your adminitrator for more information'
                ];
    
                return $this->response
                        ->setStatusCode(404)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            }
            
        } else {
            $response = [
                'title' => 'Please contact admin',
                'message' => 'Please check your data.'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }


        // print_r(json_encode($data));
        
    }

    public function registerUser(){
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
        $data->password = sha1($data->password);
        
        $query = $this->userModel->insert($data);

        if($query){

            $response = [
                'title' => 'Registration Complete',
                'message' => 'User data has been successfully submitted.'
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

    public function ChangePassword(){
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
        $payload = $this->request->getJSON();
        $payload->confirmPassword = sha1($payload->confirmPassword);

        $where = ['id' => $payload->id];
        $updateData = ['password' => $payload->confirmPassword];

        $updatePassword =  $this->userModel->updatePassword($where, $updateData);

        if($updatePassword){
            $response = [
                'title' => 'Change Password',
                'message' => 'Your successfully change password.'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Change Password Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }
    
    public function getAllUserList(){
        // Check Auth header bearer
        // $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        // if(!$authorization){
        //     $response = [
        //         'message' => 'Unauthorized Access'
        //     ];

        //     return $this->response
        //             ->setStatusCode(401)
        //             ->setContentType('application/json')
        //             ->setBody(json_encode($response));
        //     exit();
        // }

        // $header = $this->request->getHeader("");
        
        $where = ['userInterface'=>'DSIS'];
        $list = [];
        $list['list'] = $this->userModel->getAllUserInfo($where);

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
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function getAgentUsers(){
        // Check Auth header bearer
        // $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        // if(!$authorization){
        //     $response = [
        //         'message' => 'Unauthorized Access'
        //     ];

        //     return $this->response
        //             ->setStatusCode(401)
        //             ->setContentType('application/json')
        //             ->setBody(json_encode($response));
        //     exit();
        // }

        // $header = $this->request->getHeader("");
        
        $list = [];
        $where = [
            "status" => 1,
        ];
        // $list['list'] = $this->userModel->getAllUserInfo($where);
        $query = $this->userModel->getAllUserAgentInfo($where, [3, 1]);
        foreach ($query as $key => $value) {
            $list['list'][$key] = [
                "key" => $value['id'],
                "username" => $value['username'],
                "name" => $value['firstName'] .' '. $value['middleName'] .' '. $value['lastName'] .' '. $value['suffix'],
                "userType" =>  $value['userType'],
                "desc" =>  $value['userTypeDescription'],
            ];
        }
        

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
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }


    public function getAllInactiveUserList(){
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

        $list = [];
        $list['list'] = $this->userModel->getAllUserInfo(['userType' => 1, 'status' => 0]);

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
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    } 

    public function updateTenantStatus(){
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

        $where = ['id' => $data->userId];
        
        if($data->action == 'ACTIVE'){
            $setData = [
                'status' => 1,
                'reasonVacancy' => $data->reason,
            ];
        } else if ($data->action == 'DEACTIVE') {
            $setData = [
                'status' => 0,
                'reasonVacancy' => $data->reason,
            ];
        } else if ($data->action == 'VACATE') {
            $setData = [
                'status' => 2,
                'isFirstLogin' => 0,
                'premise' => '',
                'password' => '',
                'reasonVacancy' => $data->reason
            ];
        } else if($data->action == 'RENEW'){
            $setData = [
                'status' => 1,
                'startDate' =>  date('j-M-y', strtotime($data->startDate)),
                'endDate' => date('j-M-y', strtotime($data->endDate)),
                'reasonVacancy' => $data->reason
            ];
        } else if($data->action == 'RESET'){
            $setData = [
                'status' => 1,
                'isFirstLogin' => 0,
                'password' => 'password',
                'reasonVacancy' => $data->reason
            ];
        } else {
            $setData = [
                'reasonVacancy' => 'Something went wrong',
            ];
        }

        $update = $this->userModel->updateTenantInfo($where, $setData);

        if($update){
            if($data->action == 'VACATE'){
                $whereU = ['id' => $data->buildingId];
                $updateData = ['userId' => 0];
                $this->buildingModel->updateBuildingInfo($whereU, $updateData);
            }
            $response = [
                'title' => 'Tenant Information',
                'message' => 'Tenant details successfully updated'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Update Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function assignExistingTenant(){
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

        $where = ['id' => $data->userId];
        
        $setData = [
            'status' => 1,
            'isFirstLogin' => 0,
            'premise' => $data->premise,
            'password' => 'password',
            'startDate' => $data->startDate,
            'endDate' => $data->endDate,
        ];

        $update = $this->userModel->updateTenantInfo($where, $setData);

        if($update){
            if($data->action == 'VACANT'){
                $whereU = ['id' => $data->buildingId];
                $updateData = ['userId' => $data->userId];
                $this->buildingModel->updateBuildingInfo($whereU, $updateData);
            }
            $response = [
                'title' => 'Tenant Information',
                'message' => 'Tenant details successfully updated'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Update Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

}