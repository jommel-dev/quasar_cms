<?php

namespace App\Models;

use CodeIgniter\Model;

class PathologyRequestModel extends Model
{
    protected $table = "tblspl_records"; 
    protected $primaryKey = 'id';

    // protected $createdField  = 'createdDate';
    public function getLastinsertedReference(){

        $query = $this->db->table($this->table)->orderBy('id', 'DESC')->limit(1)->get();
        $results = $query->getResult();
        return $results;

    }

    public function insertApplication($data){

        $query = $this->db->table($this->table)->insert($data);
        return $query ? true : false;

    }

    public function getDetails($where){

        $query = $this->db->table($this->table)->where($where)->get();
        $results = $query->getRow();

        return $results;
    }

    public function getAllApplicationAdmin($status){

        $query = $this->db->table($this->table)->whereIn('status', $status)->orderBy('id', 'DESC')->get();
        $results = $query->getResult();

        return $results;
    }

    public function getAllApplicationSearch($where){

        $query = $this->db->table($this->table)
                ->whereIn('status', $where["status"])
                ->where($where["searchKey"], $where["searchValue"])
                ->orderBy('id', 'DESC')
                ->get();
        $results = $query->getResult();

        return $results;
    }


    public function updateApplication($where, $setData){

        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;

    }

    

}