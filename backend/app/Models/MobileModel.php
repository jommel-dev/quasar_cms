<?php

namespace App\Models;

use CodeIgniter\Model;

class MobileModel extends Model
{
    protected $table      = 'tblmobile_sync';
    protected $clientTable = 'tblclients';
    protected $productTable = 'tblproducts';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['agentId', 'category', 'client', 'attendance', 'booking', 'files', 'syncDate'];

    protected $useTimestamps = false;
    // protected $createdField  = 'syncDate';
    // protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;
    

    public function getAllAgentSyncCalls($where){

        $query = $this->db->table($this->table)->where("agentId", $where['agentId'])->like("syncDate", $where['syncDate'])->get();
        $results = $query->getRow();
        return $results;
    }
    public function updateSyncData($where, $setData){
        $query = $this->db->table($this->table)->set($setData)->where("agentId", $where['agentId'])->like("syncDate", $where['syncDate'])->update();
        return $query ? true : false;
    }

    public function getAllProducts(){
        $query = $this->db->table($this->productTable)->where("status", 1)->get();
        $results = $query->getResult();
        return $results;
    }
    public function getAllClients(){
        $query = $this->db->table($this->clientTable)->where("status", 1)->get();
        $results = $query->getResult();
        return $results;
    }

    public function getAllSummaryCalls($params){

        // $query = $this->db->table($this->table)->where("agentId", $where['agentId'])->get();
        // $results = $query->getResult();
        // return $results;
        $sql = "SELECT * FROM `tblmobile_sync` WHERE agentId = :aid: AND DATE_FORMAT(syncDate, '%Y-%m-%d') BETWEEN :dateFrom: AND :dateTo: ORDER BY syncDate ASC";
       
        $query = $this->db->query($sql, $params);
        $results = $query->getResult();

        return $results;
    }

    
    public function insertMigrateProduct($params){
        $query = $this->db->table($this->productTable)->insert($params);
        return $query ? true : false;
    }


    // Sync Client Add and Update
    public function getClientData($where){
        $query = $this->db->table($this->clientTable)->like("storeName", $where['storeName'])->get();
        $results = $query->getRow();
        return $results;
    }
    public function updateClientData($where, $setData){
        $query = $this->db->table($this->clientTable)->set($setData)->where($where)->update();
        return $query ? true : false;
    }
    public function insertMigrateClient($params){
        $query = $this->db->table($this->clientTable)->insert($params);
        return $query ? true : false;
    }
    public function getAllClientsByCondition($where){
        $query = $this->db->table($this->clientTable)->where($where)->get();
        $results = $query->getResult();
        return $results;
    }

}