<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\InvoiceModel;
use \Firebase\JWT\JWT;

class InvoiceController extends BaseController
{
    public function __construct(){
        //Models
        $this->invoiceModel = new InvoiceModel();
    }
    
    public function createInvoice(){
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

        //Get API Request Data from Frontend
        $payload = $this->request->getJSON();

        // conversion of dateTime
        // $payload->scheduleDate = date('c', strtotime($payload->scheduleDate));
        // $payload->costGroup = json_encode($payload->costGroup);
        // $payload->category = json_encode($payload->category);
        // $payload->unit = json_encode($payload->unit);
        $payload = json_decode(json_encode($payload), true);

        
        // Insert the data
        $query = $this->invoiceModel->insert($payload);

        if($query){

            $response = [
                'title' => 'Invoice',
                'message' => 'Invoice added successfully',
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

    }
    public function updateInvoice(){
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

        //Get API Request Data from Frontend
        $payload = $this->request->getJSON();
        $where = [
            "id" => $payload->invoiceId
        ];
        $payload->invoiceDetails->otherDetails = json_encode($payload->invoiceDetails->otherDetails);
        $payload->invoiceDetails->termsOfPayment = json_encode($payload->invoiceDetails->termsOfPayment);
        $data = json_decode(json_encode($payload->invoiceDetails), true);
        // print_r($data);
        // exit();
        // Insert the data
        $query = $this->invoiceModel->updateInvoiceData($where,$data);

        if($query){

            $response = [
                'title' => 'Invoice',
                'message' => 'Invoice updated successfully',
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            
        } else {
            $response = [
                'error' => 400,
                'title' => 'Invoice Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }
    public function createBookingInvoice(){
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


        //Get API Request Data from Frontend
        $payload = $this->request->getJSON();
        $where = [
            "syncId" => $payload->syncId,
            "agentId" => $payload->agentId,
            "customerId" => $payload->customerId,
        ];
        // conversion of dateTime
        $payload = json_decode(json_encode($payload), true);


        // check if there was an existing Created Invoice
        $check = $this->invoiceModel->checkGeneratedInvoice($where);
        if($check){
            $response = [
                'error' => 400,
                'title' => 'Generate Invoice Failed!',
                'message' => 'This booking already Exist please check the list of your invoice'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

        // Insert the data
        $query = $this->invoiceModel->insert($payload);

        if($query){
            $lastInserted = $this->invoiceModel->insertID();
            $response = [
                'title' => 'Invoice',
                'message' => 'Invoice added successfully',
                'invoiceId' => $lastInserted,
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
    }

    public function fetchInvoice(){
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
        // Insert the data
        $query = $this->invoiceModel->getAllInvoice();
        // print_r($query);
        // exit();
        if($query){
            foreach ($query as $key => $value) {
                $ordList = json_decode($value->orderList);
                $issuerName = $value->issueInfo->firstName .' '. $value->issueInfo->lastName;
                $list['list'][$key] = [
                    "key" => $value->id,
                    "invoiceNumber" => $value->invoiceNo,
                    "customer" => $value->customerInfo->storeName,
                    "orderDate" => $value->createdDate,
                    "itemCount" => sizeof($ordList),
                    "createdBy" => $issuerName
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
    }

    public function getInvoiceDetails(){
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
        $where = [
            "id" => $payload->invoiceId,
        ];
        // Insert the data
        $query = $this->invoiceModel->getInvoiceDetails($where);
        // print_r($query);
        // exit();

        if($query){
            return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setBody(json_encode($query));
            
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