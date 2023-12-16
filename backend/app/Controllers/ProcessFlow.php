<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\ProcessFlowModel;
use App\Models\PathologyRequestModel;
use App\Models\RequestModel;
use App\Models\AuthModel;
use App\Models\HistoryModel;
use App\Models\PathoHistoryModel;

class ProcessFlow extends BaseController
{

    public function __construct(){
        $this->processModel = new ProcessFlowModel();
        $this->applicationModel = new RequestModel();
        $this->pathoModel = new PathologyRequestModel();
        $this->authModel = new AuthModel();
        $this->historyModel = new HistoryModel();
        $this->historyPathoModel = new PathoHistoryModel();
    }

    public function getProcessStatus(){
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

        //Select Query for finding User Information
        $status = $this->processModel->where(['status' => $data->status])->get()->getRow();

        if($status){

            $status->listAction = explode("|", $status->listAction);
            $status->nextStep = explode("|", $status->nextStep);
            $status->description = explode("|", $status->description);
            $status->currentStatus = $status->currentStatus == '' ? [] : explode("|", $status->currentStatus);
            // print_r(json_encode($status[0]));
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($status));
        } else {
            $response = [
                'title' => 'System Error',
                'message' => 'Please contact administrator.'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }  

    public function updateProcessStatus(){
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

        //GET the data of the user
        $userData = $this->authModel->where('id', $data->user)->get();
        $userData = $userData->getRow();

        //Variables
        $where = ['id' => $data->applicationId];


        $setData = [
            'status' => $data->nextStatus,
            'userId' => $this->ifHasUser($data->nextStatus) ? $data->user : 0,
            'statusDescription' => $data->curStatus,
            'testResult' => json_encode($data->testResult),
            'performedBy' => $data->performedBy,
            'verifiedBy' => $data->verifiedBy,
            'encodedBy' => $data->encodedBy,
            'approveBy' => $data->approveBy,
            'isRerun' => isset($data->rerun) ? 1 : 0,
        ];

        
        // $history = [
        //     'applicationId' => $data->applicationId,
        //     'requestData' => json_encode($data),
        //     'actionStatus' => str_replace(['<user>'], [$userData->firstName .' '. $userData->lastName .' '. $userData->suffix], $data->action),
        //     'createdBy' => $data->user,
        // ];

        // print_r($setData);
        // exit;

        //Update Query for finding User Information
        $updateApp = $this->applicationModel->updateApplication($where, $setData);


        if($updateApp){
            //save application history action
            // $this->historyModel->insert($history);

            //Return to user
            $response = [
                'title' => 'Update Specimen Status',
                'message' => 'Your data successfully updated!'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Update Request Status',
                'message' => 'Your application failed to update!'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

        
    } 

    public function updateProcessStatusMultiple(){
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
        $setData = [];
        $dataCompleted = 0;
        
        // print_r($data->selected);
        // exit();
        foreach($data->selected as $key => $value) {
            $performStr = "";
            $approveStr = "";
            $verifierStr = "";
            // Get Application Details
            $appDetails = $this->applicationModel->getDetails(['id'=>$value->key]);
            // Get the Status for the next process on each Application
            $status = $this->processModel->where(['status' => $value->appStatus])->get()->getRow();
            $status->listAction = explode("|", $status->listAction);
            $status->nextStep = explode("|", $status->nextStep);
            $status->description = explode("|", $status->description);
            $status->currentStatus = $status->currentStatus == '' ? [] : explode("|", $status->currentStatus);
            
            // Performer Signatories
            $performerStatuses = array("MLIS001","MLIS003","MLIS005","MLIS007");
            if(in_array($value->appStatus, $performerStatuses)){
                if($appDetails->performedBy != ""){
                    // explode existing performer to make it array separated by comma
                    $im = explode(",", $appDetails->performedBy);
                    array_push($im, $data->user);
                    
                    $performStr = implode(",", $im);
                } else {
                    $performStr = $data->user;
                }
            } else {
                $performStr = $appDetails->performedBy;
            }
            
            // Approver Signatories
            if($value->appStatus == "MLIS011A"){
                if($appDetails->approveBy != ""){
                    $ima = explode(",", $appDetails->approveBy);
                    array_push($ima, $data->user);
                    
                    $approveStr = implode(",", $ima);
                } else {
                    $status->nextStep[0] = $value->appStatus;
                    $status->currentStatus[0] = $appDetails->statusDescription;
                    $approveStr = $data->user;
                }
            } elseif($value->appStatus == "MLISAT001") {
                $approveStr = $data->user;
            } else {
                $approveStr = $appDetails->approveBy;
            }

            // Verifier Signatories
            if($value->appStatus == 'MLIS009'){
                $verifierStr = $data->user;
            } else {
                $verifierStr = $appDetails->verifiedBy;
            }
            

            $where = ['id' => $value->key];

            $setData = [
                'status' => $status->nextStep[0],
                'userId' => $this->ifHasUser($status->nextStep[0]) ? $data->user : 0,
                'statusDescription' => $status->currentStatus[0],
                // 'testResult' => ($value->appStatus == 'MLIS011A' || $value->appStatus == 'MLIS00R' || $value->appStatus == 'MLIS009') ?  $appDetails->testResult : json_encode($data->resultForm),
                'verifiedBy' => $verifierStr,
                'performedBy' => $performStr,
                'approveBy' => $approveStr,
            ];
            // print_r($where);
            // print_r($value->appStatus);
            // print_r($setData);
            $updateApp = $this->applicationModel->updateApplication($where, $setData);
            
            if($updateApp){
                $dataCompleted += 1;
            } else {
                $response = [
                    'title' => 'Update Request Status',
                    'message' => 'Selected specimen failed to update!'
                ];
    
                return $this->response
                        ->setStatusCode(400)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            }
            
        }
        // print_r($setData);
        // exit();

        if($dataCompleted == count($data->selected)){
            $response = [
                'title' => 'Update Specimen Status',
                'message' => 'Selected specimen successfully updated!'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    } 

    function ifHasUser($status){

        if($status == 'MLIS002' || $status == 'MLIS004' || $status == 'MLIS006' || $status == 'MLIS008'){
            return true;
        } 

        return false;
    }

}