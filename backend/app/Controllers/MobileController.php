<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\MobileModel;

class MobileController extends BaseController
{

    public function __construct(){
        $this->mobModel = new MobileModel();
    }

    public function syncAgentData(){
        try {
            // get the data
            $payload = $this->request->getJSON();
            $dataList = json_decode($payload->listData);
            $clientList = [];
            $attendaceList = [];
            $bookingList = [];
            $fileList = [];

            // Check if there was an existing Sync
            $where = [
                "agentId" => $payload->agentId,
                "syncDate" => $payload->currDate
            ];
            $check = $this->mobModel->getAllAgentSyncCalls($where);
            // Set of Data List Save
            foreach ($dataList as $key => $value) {

                // First Check Client saved to db and migrate
                $clientList[$key] = [
                    "id" => (int)$value->client->id,
                    "storeName" => $value->client->storeName,
                    "address" => $value->client->address,
                    "addressDetails" => $value->client->addressDetails,
                    "branch" => $value->client->branch,
                    "categoryId" => $value->client->categoryId,
                    "geoLocation" => $value->client->geoLocation,
                    "type" => $value->client->type,
                    "contactPerson" => $value->client->contactPerson,
                    "remarks" => $value->remarks,
                    "activity" => $value->activity
                ];

                $attendaceList[$key] = [
                    "clientId" => $key,
                    "attendance" => $value->attendance
                ];

                $bookingList[$key] = [
                    "clientId" => $key,
                    "booking" => $value->booking
                ];

                $fileList[$key] = [
                    "clientId" => $key,
                    "imgSrc" => $value->files
                ];
            }

            if($check){
                $data = [
                    "client" => json_encode($clientList),
                    "attendance" => json_encode($attendaceList),
                    "booking" => json_encode($bookingList),
                    "files" => json_encode($fileList)
                ];
                $query = $this->mobModel->updateSyncData($where, $data);
            } else {
                $data = [
                    "agentId" => $payload->agentId,
                    "category" => $payload->category,
                    "client" => json_encode($clientList),
                    "attendance" => json_encode($attendaceList),
                    "booking" => json_encode($bookingList),
                    "files" => json_encode($fileList)
                ];
                $query = $this->mobModel->insert($data);
            }
            
            if($query){

                $response = [
                    'title' => 'Sync Complete',
                    'message' => 'data hasbeen successfully sync to database'
                ];
    
                return $this->response
                        ->setStatusCode(200)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
                
            } else {
                $response = [
                    'title' => 'Sync Failed!',
                    'message' => 'Something is went wrong',
                    'error' => $query
                ];
    
                return $this->response
                        ->setStatusCode(400)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    } 

    public function agentClientList(){
        try {
            // get the data
            $payload = $this->request->getJSON();
            $list = [];
            $where = [
                "agentId" => $payload->aid,
                "syncDate" => $payload->date
            ];

            $query = $this->mobModel->getAllAgentSyncCalls($where);
            
            if($query){
                $client = json_decode($query->client);
                $attendaces = json_decode($query->attendance);
                // print_r(gettype($client));
                // print_r($bookings);
                // print_r($query);
                // exit();
                foreach ($client as $key => $value) {
                    // print_r($value);
                    $attendace = $attendaces[$key];
                    // print_r($booking);

                    $list['list'][$key] = [
                        "key" => $key,
                        "id" => (int)$query->id,
                        "syncId" => (int)$query->id,
                        "storeName" => $value->storeName,
                        "address" => $value->address,
                        "branch" => $value->branch,
                        "categoryId" => $value->categoryId,
                        "geoLocation" => $value->geoLocation,
                        "contactPerson" => $value->contactPerson->name,
                        "contactNumber" => $value->contactPerson->contactNum,
                        "attendance" => $attendace,
                        "activity" => $value->activity,
                        "remarks" => $value->remarks,
                    ];
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

        } catch (\Throwable $th) {
            print_r($th);
            throw $th;
        }
    }

    public function clientList(){
        try {
            // get the data
            $list = [];

            $query = $this->mobModel->getAllClients();
            
            if($query){
                foreach ($query as $key => $value) {
                    $contact = json_decode($value->contactPerson);

                    $list['list'][$key] = [
                        "key" => $value->id,
                        "storeName" => $value->storeName,
                        "address" => $value->address,
                        "addressInfo" => json_decode($value->addressInfo),
                        "geoLocation" => json_decode($value->geoLocation),
                        "contactPerson" => $contact->name,
                        "contactNumber" => $contact->contactNum,
                        "status" => $value->status,
                        "details" => $value
                    ];
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

        } catch (\Throwable $th) {
            print_r($th);
            throw $th;
        }
    }

    public function agentIdClientList(){
        try {
            // get the data
            $payload = $this->request->getJSON();
            $where = [
                "createdBy" => $payload->aid
            ];
            $list = [];

            $query = $this->mobModel->getAllClientsByCondition($where);
            if($query){
                foreach ($query as $key => $value) {
                    $addInfo = json_decode($value->addressInfo);
                    $list['list'][$key]['client'] = [
                        "id" => (int)$value->id,
                        "storeName" => $value->storeName,
                        "address" => $value->address,
                        "addressInfo" => $addInfo,
                        "geoLocation" => json_decode($value->geoLocation),
                        "contactPerson" => json_decode($value->contactPerson),
                        "branch" => $addInfo->region->label,
                        "categoryId" => (int)$value->category,
                        "adminAssigned" => (object)[
                            "label" => "",
                            "value" => $value->createdBy,
                        ],
                        "isAdmin" => boolval((int)$value->isAdminCreated),
                        "icon" => "play_circle",
                        "status" => "pending",
                        "loading" => false,
                        "color" => "green",
                        "type" => "client"
                    ];
                    $list['list'][$key]['isAdmin'] = (int)$value->isAdminCreated;
                    $list['list'][$key]['adminAssigned'] = (int)$value->createdBy;
                    $list['list'][$key]['files'] = "";
                    $list['list'][$key]['remarks'] = "";
                    $list['list'][$key]['booking'] = [];
                    $list['list'][$key]['attendance'] = [
                        "endCall" => "",
                        "startCall" => "",
                        "geoLocation" => [
                            "timeIn" => "",
                            "timeOut" => "",
                            "coorIn" => (object)[],
                            "coorOut" => (object)[]
                        ]
                    ];
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

        } catch (\Throwable $th) {
            print_r($th);
            throw $th;
        }
    }
    public function adminClientList(){
        try {
            // get the data
            $list = [];

            $query = $this->mobModel->getAllClients();
            if($query){
                foreach ($query as $key => $value) {
                    $addInfo = json_decode($value->addressInfo);
                    $list['list'][$key]['client'] = [
                        "id" => (int)$value->id,
                        "storeName" => $value->storeName,
                        "address" => $value->address,
                        "addressDetails" => $addInfo,
                        "geoLocation" => json_decode($value->geoLocation),
                        "contactPerson" => json_decode($value->contactPerson),
                        "branch" => $addInfo->region->label,
                        "categoryId" => (int)$value->category,
                        "adminAssigned" => (object)[
                            "label" => "",
                            "value" => $value->createdBy,
                        ],
                        "isAdmin" => boolval((int)$value->isAdminCreated),
                        "icon" => "play_circle",
                        "status" => "pending",
                        "loading" => false,
                        "color" => "green",
                        "type" => "client"
                    ];
                    $list['list'][$key]['isAdmin'] = boolval((int)$value->isAdminCreated);
                    $list['list'][$key]['adminAssigned'] = (int)$value->createdBy;
                    $list['list'][$key]['files'] = "";
                    $list['list'][$key]['remarks'] = "";
                    $list['list'][$key]['booking'] = [];
                    $list['list'][$key]['attendance'] = [
                        "endCall" => "",
                        "startCall" => "",
                        "geoLocation" => [
                            "timeIn" => "",
                            "timeOut" => "",
                            "coorIn" => (object)[],
                            "coorOut" => (object)[]
                        ]
                    ];
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

        } catch (\Throwable $th) {
            print_r($th);
            throw $th;
        }
    }

    public function agentClientPhoto(){
        try {
            // get the data
            $payload = $this->request->getJSON();
            $where = [
                "agentId" => $payload->aid,
                "syncDate" => $payload->date
            ];
            $res = [];

            $query = $this->mobModel->getAllAgentSyncCalls($where);
            
            if($query){
                $file = json_decode($query->files);
                $res = $file[$payload->idx];
            }
            
            if($res){
                return $this->response
                        ->setStatusCode(200)
                        ->setContentType('application/json')
                        ->setBody(json_encode($res));
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

        } catch (\Throwable $th) {
            print_r($th);
            throw $th;
        }
    }

    public function agentClientBooking(){
        try {
            // get the data
            $payload = $this->request->getJSON();
            $where = [
                "agentId" => $payload->aid,
                "syncDate" => $payload->date
            ];
            $res = [];

            $query = $this->mobModel->getAllAgentSyncCalls($where);
            
            if($query){
                $book = json_decode($query->booking);
                $res = $book[$payload->idx];
            }
            
            if($res){
                return $this->response
                        ->setStatusCode(200)
                        ->setContentType('application/json')
                        ->setBody(json_encode($res));
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

        } catch (\Throwable $th) {
            print_r($th);
            throw $th;
        }
    }

    public function agentHistoryList(){
        try {
            // get the data
            $payload = $this->request->getJSON();
            $list = [];
            $where = [
                "agentId" => $payload->aid
            ];

            $query = $this->mobModel->getAllHistoryCalls($where);
            
            if($query){
                $client = json_decode($query->client);
                $bookings = json_decode($query->booking);

                foreach ($client as $key => $value) {
                    // print_r($value);
                    $booking = $bookings[$key]->booking;
                    // print_r($booking);

                    $list['list'][$key] = [
                        "storeName" => $value->storeName,
                        "address" => $value->address,
                        "branch" => $value->branch,
                        "categoryId" => $value->categoryId,
                        "geoLocation" => $value->geoLocation,
                        "contactPerson" => $value->contactPerson,
                        "attendance" => $value->attendance,
                        "remarks" => $value->remarks,
                        "books" => $booking,
                        "files" => $value->files
                    ];
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

        } catch (\Throwable $th) {
            print_r($th);
            throw $th;
        }
    }

    public function agentCallSummaryList(){
        try {
            // get the data
            $payload = $this->request->getJSON();
            $list = [];
            $params = [
                "aid" => $payload->aid,
                "dateFrom" => $payload->dateFrom,
                "dateTo" => $payload->dateTo,
            ];

            $query = $this->mobModel->getAllSummaryCalls($params);

            if($query){
                $marr = [];
                foreach ($query as $key => $value) {
                    
                    $clients = $value->client;
                    $clist = $this->segregateClient($clients, $value);
                    $marr = array_merge($marr, $clist['list']);
                }

                $list['list'] = $marr;
                $list['count'] = sizeof($marr);

                // print_r($list);

                // exit();
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

        } catch (\Throwable $th) {
            print_r($th);
            throw $th;
        }
    }

    public function segregateClient($arr, $val){
        $clist = [];
        $client = json_decode($arr);
        foreach($client as $ckey => $cvalue){
            $start = strtotime($cvalue->attendance->startCall);
            $end = strtotime($cvalue->attendance->endCall);
            $callDuration = ($end - $start) / 60;

            $clist['list'][$ckey] = [
                "date" => date('m/d/Y', strtotime($val->syncDate)),
                "storeName" => $cvalue->storeName,
                "address" => $cvalue->address,
                "timeIn" => date('h:i:s A', strtotime($cvalue->attendance->startCall)),
                "timeOut" => date('h:i:s A', strtotime($cvalue->attendance->endCall)),
                "duration" => $callDuration .'min(s)',
                "remarks" => $this->callReamrks($callDuration),
            ];
        }

        return $clist;
    }

    public function callReamrks($mins){
        $rem = 'SHORT CALL';

        if($mins >= 5){
            $rem = 'GOOD';
        }

        return $rem;
    }

    // public function migrateClient(){
    //     try {
    //         // get the data
    //         $payload = $this->request->getJSON();
    //         $list = [];
    //         $i = 0;
    //         $params = [
    //             "aid" => $payload->aid,
    //             "dateFrom" => $payload->dateFrom,
    //             "dateTo" => $payload->dateTo,
    //         ];

    //         $query = $this->mobModel->getAllSummaryCalls($params);

    //         if($query){
    //             $marr = [];
    //             foreach ($query as $key => $value) {
    //                 $clients = $value->client;
    //                 $clist = $this->generateClientData($clients, $value);
    //                 $marr = array_merge($marr, $clist['list']);
    //             }

    //             $result = $this->unique_multidim_array($marr, 'storeName');
    //             // print_r($result);
    //             // Do Inserting of client data
    //             if($result){
    //                 foreach ($result as $rvalue) {
    //                     # code...
    //                     $insert = $this->mobModel->insertMigrateClient($rvalue);
    //                     $i++;
    //                 }
    //             }
    //         }
    //         // exit();
    //         if($i === sizeof($result)){
    //             $response = [
    //                 'title' => 'Success',
    //                 'message' => 'Client migration done!'
    //             ];

    //             return $this->response
    //                     ->setStatusCode(200)
    //                     ->setContentType('application/json')
    //                     ->setBody(json_encode($response));
    //         } else {
    //             $response = [
    //                 'title' => 'Error',
    //                 'message' => 'Something is not right, please check to your administrator'
    //             ];
    
    //             return $this->response
    //                     ->setStatusCode(404)
    //                     ->setContentType('application/json')
    //                     ->setBody(json_encode($response));
    //         }

    //     } catch (\Throwable $th) {
    //         print_r($th);
    //         throw $th;
    //     }
    // }
    
    // New Integration
    public function migrateClient(){
        try {
            // get the data
            $payload = $this->request->getJSON();
            $list = [];
            $i = 0;
            // print_r($payload);

            foreach ($payload->clients as $key => $value) {
                // check if is from Admin
                $clients = $value->client;
                $generated = $this->generateClientData($clients, $payload->aid);
                $list[$key] = $generated;
            }

            foreach ($list as $key => $value){
                $validate = $this->mobModel->getClientData(["storeName" => $value['storeName']]);

                if($validate){
                    $this->mobModel->updateClientData(["id" => $validate->id], $value);
                } else {
                    $this->mobModel->insertMigrateClient($value);
                }

                $i++;
            }
            
            // exit();
            if($i === sizeof($list)){
                $response = [
                    'title' => 'Success',
                    'message' => 'Client migration done!'
                ];

                return $this->response
                        ->setStatusCode(200)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            } else {
                $response = [
                    'title' => 'Error',
                    'message' => 'Something is not right, please check to your administrator'
                ];
    
                return $this->response
                        ->setStatusCode(404)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            }

        } catch (\Throwable $th) {
            print_r($th);
            throw $th;
        }
    }

    public function updateClient(){
        try {
            // get the data
            $payload = $this->request->getJSON();
            $mod = (array)$payload->modified;
            foreach ($mod as $key => $value) {
                # code...
                $mod[$key] = json_encode($value);
            }
            // print_r($mod);
            // exit();
            $update = $this->mobModel->updateClientData(["id" => $payload->id], $mod);
            
            // exit();
            if($update){
                $response = [
                    'title' => 'Success',
                    'message' => 'Client Update done!'
                ];

                return $this->response
                        ->setStatusCode(200)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            } else {
                $response = [
                    'title' => 'Error',
                    'message' => 'Something is not right, please check to your administrator'
                ];
    
                return $this->response
                        ->setStatusCode(404)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            }

        } catch (\Throwable $th) {
            print_r($th);
            throw $th;
        }
    }

    public function generateClientData($arr, $id){
        
        $agentId = $id;
        $isAdmin = 0;
        if(isset($arr->isAdmin) && isset($arr->adminAssigned)){
            $assined = $arr->adminAssigned;
            $agentId = $arr->isAdmin === true ? (int)$assined->value : $id;
            $isAdmin = $arr->isAdmin ? 1 : 0;
        }
        
        $res = [
            "storeName" => $arr->storeName,
            "address" => $arr->address,
            "addressInfo" => json_encode($arr->addressDetails),
            "category" => $arr->categoryId,
            "geoLocation" => json_encode($arr->geoLocation),
            "contactPerson" => json_encode($arr->contactPerson),
            "status" => 1,
            "createdBy" => $agentId,
            "isAdminCreated" => $isAdmin
        ];

        return $res;
    }

    // public function generateClientData($arr, $val){
    //     $clist = [];
    //     $client = json_decode($arr);

    //     // Validation for client if already exist
    //     foreach($client as $ckey => $cvalue){
    //         $clist['list'][$ckey] = [
    //             "storeName" => $cvalue->storeName,
    //             "address" => $cvalue->address,
    //             "addressInfo" => json_encode($cvalue->addressDetails),
    //             "geoLocation" => json_encode($cvalue->geoLocation),
    //             "contactPerson" => json_encode($cvalue->contactPerson),
    //             "status" => 1,
    //             "createdBy" => $val->agentId
    //         ];
    //     }

    //     return $clist;
    // }

    public function unique_multidim_array($array, $key) {

        $temp_array = array();
    
        $i = 0;
    
        $key_array = array();
    
        
    
        foreach($array as $val) {
    
            if (!in_array($val[$key], $key_array)) {
    
                $key_array[$i] = $val[$key];
    
                $temp_array[$i] = $val;
    
            }
    
            $i++;
    
        }
    
        return $temp_array;
    
    }

    // Registering Client Online





    public function migrateProducts(){
        try {
            // get the data
            $payload = $this->request->getJSON();
            $payload = json_decode(json_encode($payload), true);
            $list = $payload['products'];
            $i = 0;
            
            foreach ($list as $listval) {
                if($listval['hasPriceGroup']){
                    $listval['costGroup'] = json_encode($listval['costGroup']);
                }
                $this->mobModel->insertMigrateProduct($listval);
                $i++;
            } 
            // print_r($list);

            // exit();

            if($i === sizeof($list)){
                $response = [
                    'title' => 'Success',
                    'message' => 'Product migration done!'
                ];

                return $this->response
                        ->setStatusCode(200)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            } else {
                $response = [
                    'title' => 'Error',
                    'message' => 'Something is not right, please check to your administrator'
                ];
    
                return $this->response
                        ->setStatusCode(404)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            }

        } catch (\Throwable $th) {
            print_r($th);
            throw $th;
        }
    }

    public function migrateProductsToMobile(){
        try {

            $query = $this->mobModel->getAllProducts();
            
            if($query){
                $response = [
                    'title' => 'Success',
                    'message' => 'Products has been updated',
                    'list' => $query
                ];
                return $this->response
                        ->setStatusCode(200)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
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
        } catch (\Throwable $th) {
            print_r($th);
            throw $th;
        }
    }


}