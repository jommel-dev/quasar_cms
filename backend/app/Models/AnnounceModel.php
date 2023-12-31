<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnounceModel extends Model
{
    protected $table      = 'tblannouncement';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['subject', 'tags', 'title', 'description', 'createdBy', 'createdDate','status', 'type'];

    protected $useTimestamps = false;
    // protected $createdField  = 'createdDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function getAllAnnouncement($where){

        $query = $this->db->table($this->table)->where($where)->get();
        $results = $query->getResult();

        return $results;
    }

}