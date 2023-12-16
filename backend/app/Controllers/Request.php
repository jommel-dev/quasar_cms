<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\RequestModel;
use App\Models\BuildingModel;
use App\Models\UsersModel;
use App\Models\HistoryModel;
use App\Models\ProcessFlowModel;

class Request extends BaseController
{

    public function __construct(){
        $this->reqModel = new RequestModel();
        $this->buildingModel = new BuildingModel();
        $this->userModel = new UsersModel();
        $this->historyModel = new HistoryModel();
        $this->processModel = new ProcessFlowModel();
    }

    public function createRequest(){

        //Check the Last Created Reference
        $suffix = "SR";
        $numberSequence = date('Ymd') .'0'. 101 ;
        $reference = '';

        $checkReference = $this->reqModel->getLastinsertedReference();
        // print_r($checkReference);
        // exit();
        if($checkReference){
            //if has exists generate addition 

            // print_r($checkReference[0]['referenceId']);
            $lastCountDigit = substr($checkReference[0]->referenceId, -3);
            $numberSequence = date('Ymd') .'0'. $lastCountDigit + 1 ;
            $reference = $suffix . $numberSequence;
            // print_r($reference);
            // exit();
        } else {
            $reference = $suffix . $numberSequence;
        }
            
        //Get API Request Data from NuxtJs
        $payload = $this->request->getJSON();
        $payload = json_decode(json_encode($payload), true);
        $payload['referenceId'] = $reference;
        $payload['status'] = 'SR001';

        //INSERT QUERY TO APPLICATION
        $query = $this->reqModel->insertApplication($payload);

        if($query){
            $lastInserted = $this->reqModel->insertID();
            $history = [
                'applicationId' => $lastInserted,
                'appRequestData' => json_encode($payload),
                'actionStatus' => 'Service Request Submitted.',
                'createdBy' => $payload['tenantId'],
            ];

            $this->historyModel->insert($history);


            $response = [
                'title' => 'Insert Complete',
                'message' => 'Your application has been submitted.'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Insert Failed',
                'message' => 'Please check your data'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

        // print_r($payload);

    }   

    public function updateRequestSchedule(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 

         // print_r($payload);
        $where = ['id' => $data->applicationId];

        $setData = [
            'plannedDate' => $data->plannedDate
        ];

        //Update Query for finding User Information
        $updateApp = $this->reqModel->updateApplication($where, $setData);

        if($updateApp){

            $history = [
                'applicationId' => $data->applicationId,
                'appRequestData' => json_encode($data),
                'actionStatus' => 'Service request has been re-scheduled to '. $data->plannedDate,
                'createdBy' => $data->user,
            ];

            $this->historyModel->insert($history);

            $response = [
                'title' => 'Update Request Schedule',
                'message' => 'Resident request successfully re-schedule!'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Update Request Schedule',
                'message' => 'Application failed to update!'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }   

    public function getRequestList($id){
        
        $list = [];
        $list['list'] = $this->reqModel->where(['tenantId'=>$id])->orderBy('id', 'DESC')->findAll();

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

    public function getOngoingList($id){
        
        $list = [];
        $list['list'] = $this->reqModel->where(['evaluatorId'=>$id])->orderBy('id', 'DESC')->findAll();

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
    
    
    public function getAllRequestList(){
        
        $list = [];
        $list['list'] = $this->reqModel->where(['status'=>'SR001'])->orderBy('id', 'DESC')->findAll();

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

    public function getAllList(){
        
        $list = [];
        $list['list'] = $this->reqModel->getAllServices(); 

        if($list['list']){
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

    public function getRequestById($appId){

        // $data = $this->reqModel->where(['id'=>$appId])->find();
        $data = $this->reqModel->getRequestDetails($appId);

        if($data){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($data));
        } else {
            $response = [
                'title' => 'Error',
                'message' => $data
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }


    }

}