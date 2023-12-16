<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\PathologyRequestModel;
use App\Models\AuthModel;
use App\Models\PathoHistoryModel;
use \Firebase\JWT\JWT;

class PathologyAppContoller extends BaseController
{
    public function __construct(){
        //Models
        $this->reqModel = new PathologyRequestModel();
        $this->authModel = new AuthModel();
        $this->historyPathoModel = new PathoHistoryModel();
    }

    public function createReportData(){
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

        //Check the Last Created Reference
        // $suffix = "MLIS";
        $suffix = "SPCR";
        $numberSequence = date('Ymd') .'0'. 001 ;
        $reference = '';

        $checkReference = $this->reqModel->getLastinsertedReference();
        
        if($checkReference){
            //if has exists generate addition 
            $lastCountDigit = substr($checkReference[0]->referenceId, -3);
            $latestRefID =  $lastCountDigit + 1;
            $numberSequence = date('Ymd') . '0' . $latestRefID;
            $reference = $suffix . $numberSequence;
        } else {
            $reference = $suffix . $numberSequence;
        }
        

        $spacimentApplicationData = [
            "referenceId" => $reference,
            "status" => $suffix .'000',
            "statusDescription" => 'New record has ben submitted.',
            "lastName" => $payload->lastName,
            "firstName" => $payload->firstName,
            "middleName" => $payload->middleName,
            "suffix" => $payload->suffix,
            "age" => $payload->age,
            "birthDate" => $payload->birthDate,
            "sex" => $payload->sex,
            "accessionNumber" => $payload->accessionNumber,
            "dateOfOperation" => $payload->dateOfOperation,
            "otherDetails" => json_encode($payload->otherDetails),
            "surgeryResult" => json_encode($payload->surgeryResult),
            "procedureDetails" => json_encode($payload->procedureDetails),
            "specimenCheckList" => json_encode($payload->specimenCheckList),
            "residentId" => $payload->userId,
        ];

        //INSERT QUERY TO APPLICATION
        $query = $this->reqModel->insertApplication($spacimentApplicationData);

        if($query){
            //save application history action
            $lastInserted = $this->reqModel->insertID();
            $history = [
                'appId' => $lastInserted,
                'requestData' => json_encode($spacimentApplicationData),
                'actionStatus' => "New record has ben submitted.",
                'userId' => $payload->userId,
                'actionTaken' => 'Insert Result',
            ];
            $this->historyPathoModel->insert($history);

            $response = [
                'title' => 'Patient Report',
                'message' => 'Your application has been submitted.'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Data Submission Failed',
                'message' => 'Please contact the admin for concern'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    public function getApplicationsRequest(){
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
        $payload = $this->request->getJSON();
        
        $query = $this->reqModel->getAllApplicationAdmin($payload->status);

        foreach ($query as $key => $value) {
            $otherDetails = json_decode($value->otherDetails);
            $checkList = json_decode($value->specimenCheckList);
            $surgeryResult = json_decode($value->surgeryResult);
            $procedureDetails = json_decode($value->procedureDetails);

            // print_r($value->id);
            // echo '<br>';

            
            $list['list'][$key] = [
                "key" => $value->id,
                "appStatus" =>  $value->status,
                "status" =>  $value->statusDescription,
                "signatory" =>  $value->signatory == "" ? [] : explode(",", $value->signatory),
                "referenceId" => $value->referenceId,
                "name" => $value->firstName .' '. $value->middleName .' '. $value->lastName .' '. $value->suffix,
                "firstName" => $value->firstName,
                "lastName" => $value->lastName,
                "middleName" => $value->middleName,
                "suffix" =>$value->suffix,
                "age" => $value->age,
                "birthDate" => $value->birthDate,
                "gender" => $value->sex,
                "accessionNumber" => $value->accessionNumber,
                "dateOfOperation" => date('Y-m-d', strtotime($value->dateOfOperation)),
                // Other Details
                "otherDetails" => $otherDetails,
                "checkList" => $checkList,
                "surgeryResult" => $surgeryResult,
                "procedureDetails" => $procedureDetails,
                "dateCreated" => date('Y-m-d', strtotime($value->createdDate)),
                "reportedDate" =>  $value->reportedDate == "" ? "" : date('Y-m-d', strtotime($value->reportedDate)),
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
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function getApplicationsSearch(){
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
        $where = [];
        $payload = $this->request->getJSON();
        
        if($payload->searchBy === "lastname"){
            $where = [
                "status" => $payload->data->status,
                "searchKey" => "lastName",
                "searchValue" =>  $payload->data->search
            ];
        } elseif($payload->searchBy === "firstname") {
            $where = [
                "status" => $payload->data->status,
                "searchKey" => "firstName",
                "searchValue" =>  $payload->data->search
            ];
        } else {
            $where = [
                "status" => $payload->data->status,
                "searchKey" => "referenceId",
                "searchValue" =>  $payload->data->search
            ];
        }

        $query = $this->reqModel->getAllApplicationSearch($where);

        foreach ($query as $key => $value) {
            $otherDetails = json_decode($value->otherDetails);
            $checkList = json_decode($value->specimenCheckList);
            $surgeryResult = json_decode($value->surgeryResult);
            $procedureDetails = json_decode($value->procedureDetails);

            
            $list['list'][$key] = [
                "key" => $value->id,
                "appStatus" =>  $value->status,
                "status" =>  $value->statusDescription,
                "signatory" =>  $value->signatory == "" ? [] : explode(",", $value->signatory),
                "referenceId" => $value->referenceId,
                "name" => $value->firstName .' '. $value->middleName .' '. $value->lastName .' '. $value->suffix,
                "firstName" => $value->firstName,
                "lastName" => $value->lastName,
                "middleName" => $value->middleName,
                "suffix" =>$value->suffix,
                "age" => $value->age,
                "birthDate" => $value->birthDate,
                "gender" => $value->sex,
                "accessionNumber" => $value->accessionNumber,
                "dateOfOperation" => date('Y-m-d', strtotime($value->dateOfOperation)),
                // Other Details
                "otherDetails" => $otherDetails,
                "checkList" => $checkList,
                "surgeryResult" => $surgeryResult,
                "procedureDetails" => $procedureDetails,
                "dateCreated" => date('Y-m-d', strtotime($value->createdDate)),
                "reportedDate" =>  $value->reportedDate == "" ? "" : date('Y-m-d', strtotime($value->reportedDate)),
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
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }


}