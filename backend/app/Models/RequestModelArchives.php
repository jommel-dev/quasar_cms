<?php

namespace App\Models;

use CodeIgniter\Model;

class RequestModelArchives extends Model
{
    protected $table = "tblapplications_1";
    protected $tableUser = "tblusers";
    protected $tableProcess = "tblprocessflow";
    protected $tableBuilding = "tblbuilding_info";

    // protected $createdField  = 'createdDate';
    public function getLastinsertedReference(){

        $query = $this->db->table($this->table)->orderBy('id', 'DESC')->limit(1)->get();
        $results = $query->getResult();
        return $results;

    }

    public function getTenantRequests($id){

        $query = $this->db->table($this->table)->where(['tenantId'=> $id])->get();
        $results = $query->getResult();
        return $results;

    }
    
    public function insertApplication($data){

        $query = $this->db->table($this->table)->insert($data);
        return $query ? true : false;

    }

    public function updateApplication($where, $setData){

        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;

    }

    public function getAllApplication($status, $id, $table){

        $query = $this->db->table($table)->whereIn('status', $status)->where('userId', $id)->orderBy('id', 'DESC')->get();
        $results = $query->getResult();

        return $results;
    }
    public function getAllApplicationApproverMD($status, $id){

        $query = $this->db->table($this->table)->whereIn('status', $status)->where(['userId' => $id, "approveBy" => ""])->orderBy('id', 'DESC')->get();
        $results = $query->getResult();

        return $results;
    }
    public function getAllApplicationApproverHead($status, $id){

        $query = $this->db->table($this->table)->whereIn('status', $status)->where(['userId' => $id, "approveBy !=" => ""])->orderBy('id', 'DESC')->get();
        $results = $query->getResult();

        return $results;
    }
    public function getAllApplicationBranch($status, $id, $branch, $table){

        $query = $this->db->table($table)->whereIn('status', $status)->where(['userId' => $id, 'branchId' => $branch])->orderBy('id', 'DESC')->get();
        $results = $query->getResult();

        return $results;
    }

    public function getAllApplicationAdmin($status, $table){

        $query = $this->db->table($table)->whereIn('status', $status)->orderBy('id', 'DESC')->get();
        $results = $query->getResult();

        return $results;
    }

    public function getDetails($where, $table){

        $query = $this->db->table($table)->where($where)->get();
        $results = $query->getRow();

        return $results;
    }

    public function getDetailsByPatientName($where, $table){

        $query = $this->db->table($table)->where($where)->get();
        $results = $query->getResult();

        return $results;
    }




    public function getRequisitioner($where){

        $query = $this->db->table($this->tableUser)->select('lastName, firstName, middleName, suffix')->where($where)->get();
        $results = $query->getRow();

        return $results;
    }
    public function getUserSignature($where){

        $query = $this->db->table($this->tableUser)->select('lastName, firstName, middleName, suffix, eSignature, prcNumber')->where($where)->get();
        $results = $query->getRow();

        return $results;
    }


    public function getDashboardResults(){
        $query = $this->db->table($this->table)->get();
        $results = $query->getResult();

        return $results;
    }
    public function getDashboardResultsBranch($where){

        $query = $this->db->table($this->table)->where($where)->get();
        $results = $query->getResult();

        return $results;
    }

    public function getCIFReportRange($params){

        $sql = "SELECT * FROM `tblapplications` WHERE status = :status: AND DATE_FORMAT(createdAt, '%Y-%m-%d') BETWEEN :dateFrom: AND :dateTo:";
       
        $query = $this->db->query($sql, $params);
        $results = $query->getResult();

        return $results;
    }


    // Archive Applications
    

}