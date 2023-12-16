<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table    = 'tblinvoice';
    protected $clientsTable    = 'tblclients';
    protected $usersTable    = 'tblusers';

    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['invoiceNo', 'agentId', 'syncId','customerId', 'otherDetails','termsOfPayment', 'orderList', 'gross', 'discount', 'netAmount', 'status', 'createdBy'];

    // protected $useTimestamps = false;
    // protected $createdField  = 'createdDate';
    // protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function checkGeneratedInvoice($where){
        $query = $this->db->table($this->table)->where($where)->get();
        $results = $query->getRow();
        return $results;
    }
    public function updateInvoiceData($where, $setData){
        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;
    }
    public function getAllInvoice(){
        $query = $this->db->table($this->table)->get();
        $results = $query->getResult();
        $all = array_map(function($el){

            foreach($el as $key => $val){
                $clientInfo = $this->db->table($this->clientsTable)->where('id', $el->customerId)->get();
                $agentInfo = $this->db->table($this->usersTable)->where('id', $el->agentId)->get();
                $creatorInfo = $this->db->table($this->usersTable)->where('id', $el->createdBy)->get();
                $el->customerInfo = $clientInfo->getRow();
                $el->agentInfo = $agentInfo->getRow();
                $el->issueInfo = $creatorInfo->getRow();
            }
            return $el;
        }, $results);

        return $all;
    }
    public function getInvoiceDetails($where){
        $query = $this->db->table($this->table)->where($where)->get();
        $result = $query->getRow();
        $clientInfo = $this->db->table($this->clientsTable)->where('id', $result->customerId)->get();
        $agentInfo = $this->db->table($this->usersTable)->where('id', $result->agentId)->get();
        $creatorInfo = $this->db->table($this->usersTable)->where('id', $result->createdBy)->get();
        // Modify Results
        $result->orderList = json_decode($result->orderList);
        $result->otherDetails = json_decode($result->otherDetails);
        $result->termsOfPayment = json_decode($result->termsOfPayment);
        $result->customerInfo = $clientInfo->getRow();
        $result->agentInfo = $agentInfo->getRow();
        $result->issueInfo = $creatorInfo->getRow();
        

        return $result;
    }
}