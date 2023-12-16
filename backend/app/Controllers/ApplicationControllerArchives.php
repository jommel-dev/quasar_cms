<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\RequestModelArchives;
use App\Models\AuthModel;
use App\Models\HistoryModel;
use \Firebase\JWT\JWT;

class ApplicationControllerArchives extends BaseController
{
    public function __construct(){
        //Models
        $this->reqModel = new RequestModelArchives();
        $this->authModel = new AuthModel();
        $this->historyModel = new HistoryModel();
    }

    public function sendCIFApplication(){
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
        $payload = json_decode(json_encode($payload), true);
        $caseForm =  $payload['caseForm'];
        $isAntigen = $caseForm['laboratoryInfo'][0]['typeOfTest'] != 'Antigen' ? true : false;

        //Check the Last Created Reference
        // $suffix = "MLIS";
        $suffix = $isAntigen ?  "MLIS" : "MLISAT";
        $numberSequence = date('Ymd') .'0'. 101 ;
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
            "status" => 'MLIS000',
            "status" => $suffix .'000',
            "statusDescription" => $isAntigen ? 'New specimen has ben submitted.' : 'New antigen specimen has ben submitted.',
            // "statusDescription" => 'New specimen has ben submitted.',
            "branchId" => $payload['branchId'],
            "contactTracing" => json_encode($payload['contactTracing']),
            "interviewForm" => json_encode($payload['interviewForm']),
            "patientForm" => json_encode($payload['patientForm']),
            "caseForm" => json_encode($payload['caseForm']),
            "createdBy" => $payload['userId'],
            "specimenNumber" => isset($payload['isDuplicate']) ?  $payload['specimenNumber'] : 1,
        ];

        //INSERT QUERY TO APPLICATION
        $query = $this->reqModel->insertApplication($spacimentApplicationData);

        if($query){
            $lastInserted = $this->reqModel->insertID();
            $history = [
                'applicationId' => $lastInserted,
                'requestData' => json_encode($spacimentApplicationData),
                'actionStatus' => 'New specimen has been submitted.',
                'createdBy' => $payload['userId'],
            ];

            $this->historyModel->insert($history);


            $response = [
                'title' => 'Case Investigation Form',
                'message' => 'Your application has been submitted.'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Data Submit Failed',
                'message' => 'Please contact the admin for concern'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    public function updateCIFApplication(){
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

        $payload = $this->request->getJSON();
        $where = ['id' => $payload->applicationId];
        $payload = json_decode(json_encode($payload), true);
        
        $spacimentApplicationData = [
            "contactTracing" => json_encode($payload['contactTracing']),
            "interviewForm" => json_encode($payload['interviewForm']),
            "patientForm" => json_encode($payload['patientForm']),
            "caseForm" => json_encode($payload['caseForm'])
        ];

        // Reset the process flow status 
        $type = $payload['caseForm']['laboratoryInfo'][0]['typeOfTest'];
        $status = $payload['currStatus'];
        $antigenStatuses = array("MLISAT000","MLISAT001","MLISAT00R");
        if($type !== 'Antigen' && in_array($status, $antigenStatuses)){
            // Change Statuss
            $spacimentApplicationData["status"] = "MLIS000";
            // $spacimentApplicationData["status"] = "MLIS000";
        }

        

        $updateApp = $this->reqModel->updateApplication($where, $spacimentApplicationData);

        if($updateApp){

            //Return to user
            $response = [
                'title' => 'Update Specimen Information',
                'message' => 'Specimen data successfully updated!'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Update Specimen Data',
                'message' => 'Your application failed to update!'
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
        $payload->keyword = base64_decode($payload->keyword);
        
        if(isset($payload->branch)){

            $query = $payload->branch == 1 ? 
            $this->reqModel->getAllApplicationAdmin($payload->status, $payload->keyword) : 
            $this->reqModel->getAllApplicationBranch($payload->status, $payload->user, $payload->branch, $payload->keyword);
        } else {
            if(isset($payload->approverType) && $payload->approverType == 1){
                $query = $this->reqModel->getAllApplicationApproverMD($payload->status, $payload->user);
            } else if(isset($payload->approverType) && $payload->approverType == 2) {
                $query = $this->reqModel->getAllApplicationApproverHead($payload->status, $payload->user);
            } else {
                $query = $this->reqModel->getAllApplication($payload->status, $payload->user, $payload->keyword);
            }
            
        }
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            $interviewInfo = json_decode($value->interviewForm);
            $caseInfo = json_decode($value->caseForm);
            $patientInfo = json_decode($value->patientForm);
            $LabInfoAvail = isset($caseInfo->laboratoryInfo) ? $caseInfo->laboratoryInfo[0]->typeOfTest == 'Antigen' ? true : false : false;
            
            
            if($value->testResult != '' && !$LabInfoAvail){
                $result = json_decode($value->testResult);
                $result = $result->testResult == 'SARS-CoV-viral RNA: Not Detected' ? 'NEGATIVE' : 'POSITIVE';
            } else {
                $result = 'N/A';
            }
            
            
            // print_r($value->id);
            // echo '<br>';

            
            $list['list'][$key] = [
                "key" => $value->id,
                "rerun" => $value->isRerun,
                "appStatus" =>  $value->status,
                "specimenNumber" => $this->ordinalNumber($value->specimenNumber),
                "performer" =>  $value->performedBy == "" ? [] : explode(",", $value->performedBy),
                "approver" =>  $value->approveBy == "" ? [] : explode(",", $value->approveBy),
                "verifier" =>  $value->verifiedBy,
                "encoder" =>  $value->encodedBy,
                "referenceId" => $value->referenceId,
                "name" => $patientInfo->firstName .' '. $patientInfo->middleName .' '. $patientInfo->lastName .' '. $patientInfo->suffix,
                "age" =>  $patientInfo->age,
                "gender" =>  $patientInfo->sex,
                "dateOfInterview" =>  date('Y-m-d', strtotime($interviewInfo->interviewDate)),
                "drUnit" =>  isset($interviewInfo->drUnit) ? $interviewInfo->drUnit : 'DR. PJGMRMC',
                "isAntigen" => $LabInfoAvail,
                "typeOfClient" =>  $interviewInfo->clientType,
                "resultStatus" =>  $result,
                "status" =>  $value->statusDescription,
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

    public function searchSpecimenApplication(){
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
        $query = $this->reqModel->getDetails(['referenceId' => $payload->search, 'branchId'=>$payload->branch]);
        // print_r($query);
        // exit;

        $interviewInfo = json_decode($query->interviewForm);
        $patientInfo = json_decode($query->patientForm);

        $list['list'] = [
            "key" => $query->id,
            "rerun" => $query->isRerun,
            "appStatus" =>  $query->status,
            "referenceId" => $query->referenceId,
            "name" => $patientInfo->firstName .' '. $patientInfo->lastName .' '. $patientInfo->suffix,
            "age" =>  $patientInfo->age,
            "gender" =>  $patientInfo->sex,
            "dateOfInterview" =>  date('Y-m-d', strtotime($interviewInfo->interviewDate)),
            "drUnit" =>  $interviewInfo->drUnit,
            "typeOfClient" =>  $interviewInfo->clientType,
            "status" =>  $query->statusDescription,
        ];
        
        // print_r($list);
        // exit;

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

    public function searchSpecimenApplicationByLastname(){
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
        $payload->keyword = base64_decode($payload->keyword);

        if(isset($payload->approverType) && $payload->approverType == 1){
            $query = $this->reqModel->getAllApplicationApproverMD($payload->status, 0);
        } else if(isset($payload->approverType) && $payload->approverType == 2) {
            $query = $this->reqModel->getAllApplicationApproverHead($payload->status, 0);
        } else {
            if($payload->branch == 1){
                $query = $this->reqModel->getAllApplicationAdmin($payload->status, $payload->keyword);
            } else {
                $query = $this->reqModel->getDetailsByPatientName(['branchId'=>$payload->branch], $payload->keyword);
            }
        }

        // print_r($query);
        // exit();
        
        $num = 0;
        foreach ($query as $key => $value) {
            $interviewInfo = json_decode($value->interviewForm);
            $patientInfo = json_decode($value->patientForm);
            $caseInfo = json_decode($value->caseForm);
            $lastFirst = substr($patientInfo->lastName, 0, 1);
            if(($payload->search == $patientInfo->lastName || $lastFirst == $payload->search) && in_array($value->status, $payload->status) ){
                $list['list'][$num] = [
                    "key" => $value->id,
                    "rerun" => $value->isRerun,
                    "appStatus" =>  $value->status,
                    "specimenNumber" => $this->ordinalNumber($value->specimenNumber),
                    "performer" =>  $value->performedBy == "" ? [] : explode(",", $value->performedBy),
                    "approver" =>  $value->approveBy == "" ? [] : explode(",", $value->approveBy),
                    "verifier" =>  $value->verifiedBy,
                    "encoder" =>  $value->encodedBy,
                    "referenceId" => $value->referenceId,
                    "name" => $patientInfo->firstName .' '. $patientInfo->lastName .' '. $patientInfo->suffix,
                    "age" =>  $patientInfo->age,
                    "gender" =>  $patientInfo->sex,
                    "dateOfInterview" =>  date('Y-m-d', strtotime($interviewInfo->interviewDate)),
                    "isAntigen" => $caseInfo->laboratoryInfo[0]->typeOfTest == 'Antigen' ? true : false,
                    "drUnit" =>  $interviewInfo->drUnit,
                    "typeOfClient" =>  $interviewInfo->clientType,
                    "status" =>  $value->statusDescription,
                ];

                $num += 1;
            } 

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

    public function searchSpecimenApplicationByFirstname(){
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
        $payload->keyword = base64_decode($payload->keyword);

        if(isset($payload->approverType) && $payload->approverType == 1){
            $query = $this->reqModel->getAllApplicationApproverMD($payload->status, 0);
        } else if(isset($payload->approverType) && $payload->approverType == 2) {
            $query = $this->reqModel->getAllApplicationApproverHead($payload->status, 0);
        } else {
            if($payload->branch == 1){
                $query = $this->reqModel->getAllApplicationAdmin($payload->status, $payload->keyword);
            } else {
                $query = $this->reqModel->getDetailsByPatientName(['branchId'=>$payload->branch], $payload->keyword);
            }
            // $query = $this->reqModel->getDetailsByPatientName(['branchId'=>$payload->branch]);
        }
        $num = 0;
        foreach ($query as $key => $value) {
            
            $interviewInfo = json_decode($value->interviewForm);
            $patientInfo = json_decode($value->patientForm);
            $caseInfo = json_decode($value->caseForm);
            $firstFirst = substr($patientInfo->firstName, 0, 1);
            if(($payload->search == $patientInfo->firstName || $firstFirst == $payload->search) && in_array($value->status, $payload->status)){
                $list['list'][$num] = [
                    "key" => $value->id,
                    "rerun" => $value->isRerun,
                    "specimenNumber" =>$this->ordinalNumber($value->specimenNumber),
                    "performer" =>  $value->performedBy == "" ? [] : explode(",", $value->performedBy),
                    "approver" =>  $value->approveBy == "" ? [] : explode(",", $value->approveBy),
                    "verifier" =>  $value->verifiedBy,
                    "encoder" =>  $value->encodedBy,
                    "appStatus" =>  $value->status,
                    "referenceId" => $value->referenceId,
                    "name" => $patientInfo->firstName .' '. $patientInfo->lastName .' '. $patientInfo->suffix,
                    "age" =>  $patientInfo->age,
                    "gender" =>  $patientInfo->sex,
                    "dateOfInterview" =>  date('Y-m-d', strtotime($interviewInfo->interviewDate)),
                    "isAntigen" => $caseInfo->laboratoryInfo[0]->typeOfTest == 'Antigen' ? true : false,
                    "drUnit" =>  $interviewInfo->drUnit,
                    "typeOfClient" =>  $interviewInfo->clientType,
                    "status" =>  $value->statusDescription,
                ];

                $num += 1;
            }

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

    public function getApplicationDetails(){
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

        $payload = $this->request->getJSON();
        $payload->keyword = base64_decode($payload->keyword);

        $query = $this->reqModel->getDetails(["id" => $payload->id], $payload->keyword);
        $query->interviewForm = json_decode($query->interviewForm);
        $query->patientForm = json_decode($query->patientForm);
        $query->contactTracing = json_decode($query->contactTracing);
        if($query->caseForm != ''){
            $query->caseForm = json_decode($query->caseForm);
        }
        if($query->testResult != ''){
            $query->testResult = json_decode($query->testResult);
        }
        


        if($query){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($query));
        } else {
            $response = [
                'title' => 'Error',
                'message' => $query
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function fixDataForms(){
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
        
        $payload = $this->request->getJSON();
        $payload = json_decode(json_encode($payload), true);


        $where = ['id' => $payload['appID']];

        $spacimentApplicationData = [
            "caseForm" => json_encode($payload['data'])
        ];
        // print_r($payload);
        print_r($spacimentApplicationData);
        // exit();
        $updateApp = $this->reqModel->updateApplication($where, $spacimentApplicationData);
    }

    public function ordinalNumber($num) {
        if (!in_array(($num % 100),array(11,12,13))){
          switch ($num % 10) {
            // Handle 1st, 2nd, 3rd
            case 1:  return $num.'st';
            case 2:  return $num.'nd';
            case 3:  return $num.'rd';
          }
        }
        return $num.'th';
      }

    

}