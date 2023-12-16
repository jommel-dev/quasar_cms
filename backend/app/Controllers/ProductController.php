<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\ProductsModel;
use App\Models\HistoryModel;
use \Firebase\JWT\JWT;

class ProductController extends BaseController
{
    public function __construct(){
        //Models
        $this->productModel = new ProductsModel();
        $this->historyModel = new HistoryModel();
    }
    
    public function addProduct(){
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

        //Get API Request Data from Frontend
        $payload = $this->request->getJSON();

        // conversion of dateTime
        // $payload->scheduleDate = date('c', strtotime($payload->scheduleDate));
        $payload->costGroup = json_encode($payload->costGroup);
        $payload->category = json_encode($payload->category);
        $payload->unit = json_encode($payload->unit);
        $payload = json_decode(json_encode($payload), true);

        
        // Insert the data
        $query = $this->productModel->insertProduct($payload);

        if($query){

            $response = [
                'title' => 'Product added',
                'message' => 'Product added successfully',
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

    public function getProductList(){
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

        // $where = [
        //     "userType" => 15,
        //     "isDeleted" => 0,
        //     "status" => 1,
        // ];
        $list = [];
        $list['list'] = $this->productModel->getProductList();

        if($list){
            $list['message'] = "successfully fetch product list";
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


}