<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\RequestModel;
use App\Models\AuthModel;
use \Firebase\JWT\JWT;

class Dashboard extends BaseController
{
    public function __construct(){
        //Models
        $this->reqModel = new RequestModel();
        $this->authModel = new AuthModel();
    }

    public function getDashboard(){
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
        $positive = 0;
        $negative = 0;
        $reruns = 0;
        $invalids = 0;
        $payload = $this->request->getJSON();

        if($payload->branch != 1){
            //All Dashboard from branch display in Main branch
            $query = $this->reqModel->getDashboardResultsBranch(['branchId' => $payload->branch]);
        } else {
            //here is the per branch Dashboard
            $query = $this->reqModel->getDashboardResults();
        }

        
        foreach ($query as $key => $value) { 

            //Getting the positive and negative
            $data = json_decode($value->testResult, true);
            if(!empty($data)){
                if(isset($data['testResult']) && $data['testResult'] == 'SARS-CoV-viral RNA Detected'){
                    $positive += 1;
                } else if (isset($data['testResult']) && $data['testResult'] == 'SARS-CoV-viral RNA: Not Detected'){
                    $negative += 1;
                }
            }

            //Getting the Rerun
            if($value->isRerun == 1 && $value->status == 'MLIS001'){
                $reruns += 1;
            }

            //Getting the Invalids
            if($value->status == 'MLIS00D'){
                $invalids += 1;
            }



        }


        $list = [
            'activeCases' => sizeof($query),
            'totalPositive' => $positive,
            'totalNegative' => $negative,
            'totalRerun' => $reruns,
            'totalInvalids' => $invalids,
        ];

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

    public function getMonthlyResult(){
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

        $result = [];
        $series = [
            (object)[
                'name' => 'Postive',
                'type' => 'column',
                'data' => [],
            ],
            (object)[
                'name' => 'Negative',
                'type' => 'column',
                'data' => [],
            ],
            (object)[
                'name' => 'Total',
                'type' => 'line',
                'data' => [],
            ]
        ];
        $categories = [];
        $totalDaysinMonth  = date('t');
        $payload = $this->request->getJSON();

        if($payload->branch != 1){
            //All Dashboard from branch display in Main branch
            $query = $this->reqModel->getDashboardResultsBranch(['branchId' => $payload->branch]);
        } else {
            //here is the per branch Dashboard
            $query = $this->reqModel->getDashboardResults();
        }

        // Get the Result X Axis Categories 
        $positive = [];
        $negative = [];
        $totalEachDate = [];
        for ($i=1; $i < $totalDaysinMonth + 1; $i++) {
            $eachDay = date('m-'. str_pad($i, 2, '0', STR_PAD_LEFT) .'-Y');
            // push to categories array
            array_push($categories, $i);
            $positive[$eachDay] = [];
            $negative[$eachDay] = [];
            $totalEachDate[$eachDay] = 0;
        }
       

        foreach ($query as $key => $value) { 
            //Getting the positive and negative
            $testRes = json_decode($value->testResult, true);
            if(isset($testRes['dateTimeResult']) && isset($testRes['testResult'])){
                $dateResult = date('m-d-Y', strtotime($testRes['dateTimeResult']));
                if($testRes['testResult'] == 'SARS-CoV-viral RNA Detected'){
                    array_push($positive[$dateResult], +1);
                } else if ($testRes['testResult'] == 'SARS-CoV-viral RNA: Not Detected'){
                    array_push($negative[$dateResult], +1);
                }
            
            }
        }

        // Mapping
        foreach ($positive as $key => $value) {
            $count = sizeof($value);
            array_push($series[0]->data, $count);
            $totalEachDate[$key] += $count;
        }
        foreach ($negative as $key => $value) {
            $count = sizeof($value);
            array_push($series[1]->data, $count);
            $totalEachDate[$key] += $count;
        }
        foreach ($totalEachDate as $key => $value) {
            array_push($series[2]->data, $value);
        }

        
        // Finalization of Results
        $result = [
            'categories' => $categories,
            'series' => $series,
        ];
        

        // print_r($result);
        // exit();

        return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($result));
    }

    public function getWeeklyResult(){
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

        $result = [];
        $series = [
            (object)[
                'name' => 'Postive',
                'data' => [],
            ],
            (object)[
                'name' => 'Negative',
                'data' => [],
            ]
        ];
        $weeks = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $getWeek  = 7;

        $payload = $this->request->getJSON();

        if($payload->branch != 1){
            //All Dashboard from branch display in Main branch
            $query = $this->reqModel->getDashboardResultsBranch(['branchId' => $payload->branch]);
        } else {
            //here is the per branch Dashboard
            $query = $this->reqModel->getDashboardResults();
        }
        // $query = $this->reqModel->getDashboardResults();

        // Get the Result X Axis Categories 
        $positive = [];
        $negative = [];
        for ($i=0; $i < $getWeek; $i++) {
            $day = $weeks[$i] .'0 week';
            $eachDay = date('m-d-Y', strtotime($day));

            $positive[$eachDay] = [];
            $negative[$eachDay] = [];
        }
        

        foreach ($query as $key => $value) { 
            //Getting the positive and negative
            $testRes = json_decode($value->testResult, true);
            if(isset($testRes['dateTimeResult']) && isset($testRes['testResult'])){
                $dateResult = date('m-d-Y', strtotime($testRes['dateTimeResult']));
                if($testRes['testResult'] == 'SARS-CoV-viral RNA Detected' && array_key_exists($dateResult, $positive)){
                    array_push($positive[$dateResult], +1);
                } else if ($testRes['testResult'] == 'SARS-CoV-viral RNA: Not Detected' && array_key_exists($dateResult, $negative)){
                    array_push($negative[$dateResult], +1);
                }
            
            }
        }

        // Mapping
        foreach ($positive as $key => $value) {
            $count = sizeof($value);
            array_push($series[0]->data, $count);
        }
        foreach ($negative as $key => $value) {
            $count = sizeof($value);
            array_push($series[1]->data, $count);
        }

        
        // Finalization of Results
        $result = [
            'series' => $series,
        ];
        

        // print_r($result);
        // exit();

        return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($result));
    }
    

}