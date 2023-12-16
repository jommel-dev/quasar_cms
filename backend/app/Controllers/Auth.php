<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\AuthModel;
use App\Models\MiscModel;
use App\Models\UsersModel;

use CodeIgniter\RESTful\ResourceController;
use \Firebase\JWT\JWT;

class Auth extends BaseController
{
    public function __construct(){
        //Models
        $this->authModel = new AuthModel();
        $this->miscModel = new MiscModel();
        $this->userModel = new UsersModel();
    }

    public function privateKey(){
        $privateKey = <<<EOD
        -----BEGIN RSA PRIVATE KEY-----
        MIICXAIBAAKBgQC8kGa1pSjbSYZVebtTRBLxBz5H4i2p/llLCrEeQhta5kaQu/Rn
        vuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL9
        5+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4ehde/zUxo6UvS7UrBQIDAQAB
        AoGAb/MXV46XxCFRxNuB8LyAtmLDgi/xRnTAlMHjSACddwkyKem8//8eZtw9fzxz
        bWZ/1/doQOuHBGYZU8aDzzj59FZ78dyzNFoF91hbvZKkg+6wGyd/LrGVEB+Xre0J
        Nil0GReM2AHDNZUYRv+HYJPIOrB0CRczLQsgFJ8K6aAD6F0CQQDzbpjYdx10qgK1
        cP59UHiHjPZYC0loEsk7s+hUmT3QHerAQJMZWC11Qrn2N+ybwwNblDKv+s5qgMQ5
        5tNoQ9IfAkEAxkyffU6ythpg/H0Ixe1I2rd0GbF05biIzO/i77Det3n4YsJVlDck
        ZkcvY3SK2iRIL4c9yY6hlIhs+K9wXTtGWwJBAO9Dskl48mO7woPR9uD22jDpNSwe
        k90OMepTjzSvlhjbfuPN1IdhqvSJTDychRwn1kIJ7LQZgQ8fVz9OCFZ/6qMCQGOb
        qaGwHmUK6xzpUbbacnYrIM6nLSkXgOAwv7XXCojvY614ILTK3iXiLBOxPu5Eu13k
        eUz9sHyD6vkgZzjtxXECQAkp4Xerf5TGfQXGXhxIX52yH+N2LtujCdkQZjXAsGdm
        B2zNzvrlgRmgBrklMTrMYgm1NPcW+bRLGcwgW2PTvNM=
        -----END RSA PRIVATE KEY-----
        EOD;

        return $privateKey;
    }

    public function login(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 
        $options = [
            'cost' => 12 // the default cost is 10
        ];
        $hasPass = sha1($data->password);

        //Select Query for finding User Information
        $user = $this->authModel->where(['username' => $data->username, 'password' => $hasPass])->get()->getRow();
        
        //Set Api Response return to the FE
        if($user){

            if($user->status == 1){
                // $userModules = $user[0]['userType'] == 0 ? ['101', '103', '104', '105'] : ['101', '102'];

                $user->userType = $this->miscModel->getUserType($user->userType);
                $user->branch = $this->miscModel->getBranch($user->branchId);
                // $userModules =;
                // $user[0]['usersMod'] = $userModules;
                // print_r($user);

                // //Set JWT Authorization
                $secretKey = $this->privateKey();
                $issueTimeClaim = time();
                $notBeforeClaim = $issueTimeClaim + 10;
                $expiryClaim = $issueTimeClaim + 3600;

                // //Generate Token
                $token = [
                    "fullName" => $user->firstName .' '. $user->lastName,
                    "iss" => $user->username,
                    "aud" => $user->username,
                    "uui" => $user->userInterface,
                    "iat" => $issueTimeClaim,
                    "nbf" => $notBeforeClaim,
                    "exp" => $expiryClaim,
                    "userId" => $user->id,
                    "userType" => $user->userType->id,
                    "branchId" => $user->branchId,
                    "modules" =>  $user->userType->modules
                ];

                $jwt = JWT::encode($token, $secretKey, 'RS256');

                $result = [
                    "fullName" => $user->firstName .' '. $user->lastName,
                    "userId" => $user->id,
                    "uui" => $user->userInterface,
                    "jwt" => $jwt,
                    // "userData" => $user
                ];


                return $this->response 
                        ->setHeader('jwt', $jwt)
                        ->setStatusCode(200)
                        ->setContentType('application/json')
                        ->setBody(json_encode($result));
            } else {
                $response = [
                    'error'=>404,
                    'title' => 'Account Deactivated',
                    'message' => 'Please contact your adminitrator for more information'
                ];
    
                return $this->response
                        ->setStatusCode(200)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            }
            
        } else {
            $response = [
                'error' => 404,
                'title' => 'Invalid Credentials',
                'message' => 'Please check your username or password, If its forgotten please contact your adminitrator for more information.'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }


        // print_r(json_encode($data));
        
    } 

    public function validateAppVersion(){
        $payload = $this->request->getJSON();
        $currentVersion = "2.10";

        if($payload->appVersion !== $currentVersion){
            $response = [
                'error' => 401,
                'currVersion' => $currentVersion,
                'title' => 'Invalid Credentials',
                'message' => 'Please check your username or password'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'currVersion' => $currentVersion,
                'title' => 'Valid Credentials',
                'message' => 'Version matched'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    public function reconnect($uid){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 

        //Select Query for finding User Information
        $user = $this->authModel->where(['id' => $uid])->find();
        $user[0]['buildingInfo'] = $this->buildingModel->where(['premiseNumber' => $user[0]['premise']])->find();

        //Set Api Response return to the FE
        if($user){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($user));
        } else {
            $response = [
                'title' => 'Invalid Credentials',
                'message' => 'Please check your username or password'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }


        // print_r(json_encode($data));
        
    }   

    public function firstLoginChangePassword(){

        //Get API Request Data from NuxtJs
        $payload = $this->request->getJSON();

        $where = ['id' => $payload->id];
        $updateData = ['password' => $payload->password, 'isFirstLogin' => 1];

        $updatePassword =  $this->authModel->updateBuildingInfo($where, $updateData);

        if($updatePassword){
            $response = [
                'title' => 'Change Password',
                'message' => 'Your successfully change password.'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Change Password Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }
}