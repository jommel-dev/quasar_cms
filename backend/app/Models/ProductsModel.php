<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductsModel extends Model
{
    protected $table      = 'tblproducts';

    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['productName', 'unit', 'description', 'category', 'sku', 'stock', 'productCost', 'productSRP', 'hasPriceGroup', 'costGroup', 'isSpecial', 'isSale', 'createdBy'];

    protected $useTimestamps = false;
    protected $createdField  = 'createdDate';
    protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;


    public function insertProduct($data){
        $query = $this->db->table($this->table)->insert($data);
        return $query ? true : false;
    }


    // Get Products
    public function getProductList() {

        $query = $this->db->table($this->table)->get();
        $results = $query->getResult('array');
        return $results;
    }

}