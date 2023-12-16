<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\MiscModel;

class Misc extends BaseController
{

    public function __construct(){
        $this->miscModel = new MiscModel();
    }

    public function getUserTypes(){
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

        //Select Query for finding User Information
        $categories = [];
        $categories['list'] = $this->miscModel->getTypeList();

        //Set Api Response return to the FE
        if($categories){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($categories));
        } else {
            $response = [
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }   

    public function getBranches(){
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

        //Select Query for finding User Information
        $unitareas = [];
        $unitareas['list'] = $this->miscModel->getBranchList();

        //Set Api Response return to the FE
        if($unitareas){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($unitareas));
        } else {
            $response = [
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }   

}