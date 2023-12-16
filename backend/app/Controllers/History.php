<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\ProcessFlowModel;
use App\Models\RequestModel;
use App\Models\UsersModel;
use App\Models\HistoryModel;

class History extends BaseController
{

    public function __construct(){
        $this->historyModel = new HistoryModel();
        $this->processModel = new ProcessFlowModel();
        $this->applicationModel = new RequestModel();
        $this->usersModel = new UsersModel();
    }

    public function getRequestHistory($appId){
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
        $list['list'] = $this->historyModel->where(['applicationId'=> $appId])->findAll();

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


}