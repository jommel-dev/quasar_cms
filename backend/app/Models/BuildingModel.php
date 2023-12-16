<?php

namespace App\Models;

use CodeIgniter\Model;

class BuildingModel extends Model
{
    protected $table      = 'tblbuilding_info';
    protected $usersTable = 'tblusers';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [''];

    protected $useTimestamps = false;
    protected $createdField  = 'createdDate';
    protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function updateBuildingInfo($where, $setData){

        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;

    }

    public function getAllUnits(){

        $query = $this->db->table($this->table)->get();
        (array)$results = $query->getResult();

        // print_r($results);
        // exit;

        $all = array_map(function($el){

            foreach($el as $key => $val){
                $info = $this->db->table($this->usersTable)->where('id', $el->userId)->get();
                $el->tenantInfo = $info->getRow();
            }
            return $el;
        }, $results);

        return $all;
    }

}