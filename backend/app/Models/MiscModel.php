<?php

namespace App\Models;

use CodeIgniter\Model;

class MiscModel extends Model
{
    protected $usertypeTable = "tblusertypes";
    protected $branchTable = "tblbranches";


    public function getTypeList(){

        $query = $this->db->table($this->usertypeTable)->get();
        $results = $query->getResult();

        return $results;
    }

    public function getBranchList(){

        $query = $this->db->table($this->branchTable)->get();
        $results = $query->getResult();

        return $results;

    }

    public function getUserType($userType){

        $query = $this->db->table($this->usertypeTable)->where('id', $userType)->get();
        $results = $query->getRow();

        return $results;

    }
    public function getBranch($branchId){

        $query = $this->db->table($this->branchTable)->where('id', $branchId)->get();
        $results = $query->getRow();

        return $results;

    }

}