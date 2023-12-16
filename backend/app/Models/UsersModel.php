<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table      = 'tblusers';
    protected $typeTable  = 'tblusertypes';
    protected $branchTable= 'tblbranches';
    protected $patientTable= 'tblpatient_info';
    protected $checkupTable= 'tblcheckups';
    protected $scheduleTable= 'tblschedule';
    protected $wellnessTable= 'tblwellness';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['username', 'password', 'firstName', 'lastName', 'middleName', 'suffix','userType', 'contact', 'address', 'branchId', 'profilePhoto', 'eSignature', 'status'];

    protected $useTimestamps = false;
    protected $createdField  = 'createdDate';
    protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;


    public function getUserInfo($id){

        $query = $this->db->table($this->table)->where('id', $id)->get();
        $results = $query->getRow();
        return $results;
    }

    public function getAllUserInfo($where){

        $query = $this->db->table($this->table)->where($where)->get();
        $results = $query->getResult('array');

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $type = $this->db->table($this->typeTable)->where('id', $el['userType'])->get()->getRow();
                $el['userTypeDescription'] = $type->description;
                $branch = $this->db->table($this->branchTable)->where('id', $el['branchId'])->get()->getRow();
                $el['branch'] = $branch->branchName;
            }
            return $el;
        }, $results);

        return $all;
    }
    public function getAllUserAgentInfo($where, $utypes){

        $query = $this->db->table($this->table)->where($where)->whereIn('userType', $utypes)->get();
        $results = $query->getResult('array');

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $type = $this->db->table($this->typeTable)->where('id', $el['userType'])->get()->getRow();
                $el['userTypeDescription'] = $type->description;
                $branch = $this->db->table($this->branchTable)->where('id', $el['branchId'])->get()->getRow();
                $el['branch'] = $branch->branchName;
            }
            return $el;
        }, $results);

        return $all;
    }
    public function getAllPtientInfo($where){

        $query = $this->db->table($this->patientTable)->where($where)->get();
        $results = $query->getResult('array');
        return $results;
    }

    public function updateTenantInfo($where, $setData){

        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;

    }
    
    public function updatePassword($where, $setData){

        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;

    }


    // Get PAtients Data
    public function getClientPatients($where){

        $query = $this->db->table($this->patientTable)->where($where)->get();
        $results = $query->getResult('array');

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $owner = $this->db->table($this->table)->where('id', $el['clientId'])->get()->getRow();
                $el['patientOwner'] = $owner;
            }
            return $el;
        }, $results);

        return $all;
    }
    public function insertPetDetails($data){
        $query = $this->db->table($this->patientTable)->insert($data);
        return $query ? true : false;
    }
    public function insertScheduleDetails($data){
        $query = $this->db->table($this->scheduleTable)->insert($data);
        return $query ? true : false;
    }
    public function insertCheckupDetails($data){
        $query = $this->db->table($this->checkupTable)->insert($data);
        return $query ? true : false;
    }
    
    public function updateScheduleStatus($setData, $where){
        $query = $this->db->table($this->scheduleTable)->set($setData)->where($where)->update();
        return $query ? true : false;
    }


    // Get Patients Schedules
    public function getAllSchedules() {

        $query = $this->db->table($this->scheduleTable)->get();
        $results = $query->getResult('array');

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $owner = $this->db->table($this->table)->where('id', $el['clientId'])->get()->getRow();
                $el['patientOwner'] = $owner;

                $pet = $this->db->table($this->patientTable)->where('id', $el['patientId'])->get()->getRow();
                $el['patientDetails'] = $pet;
            }
            return $el;
        }, $results);

        return $all;
    }

    public function getPatientsSchedule($where) {

        $query = $this->db->table($this->scheduleTable)->where($where)->get();
        $results = $query->getResult('array');

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $owner = $this->db->table($this->table)->where('id', $el['clientId'])->get()->getRow();
                $el['patientOwner'] = $owner;

                $pet = $this->db->table($this->patientTable)->where('id', $el['patientId'])->get()->getRow();
                $el['patientDetails'] = $pet;
            }
            return $el;
        }, $results);

        return $all;
    }

    public function getScheduleDetails() {

        $query = $this->db->table($this->scheduleTable)->get();
        $results = $query->getRow();
        $results->patientOwner = $this->db->table($this->table)->where('id', $results->clientId)->get()->getRow();
        $results->patientDetails = $this->db->table($this->table)->where('id', $results->patientId)->get()->getRow();

        return $results;
    }

    public function getPatientCheckups($where) {

        $query = $this->db->table($this->checkupTable)->where($where)->get();
        $results = $query->getResult('array');

        $all = array_map(function($el){
            foreach($el as $key => $val){
                if(isset($el['clientId'])){
                    $owner = $this->db->table($this->table)->where('id', $el['clientId'])->get()->getRow();
                    $el['patientOwner'] = $owner;
                }
                
                $pet = $this->db->table($this->patientTable)->where('id', $el['patientId'])->get()->getRow();
                $el['patientDetails'] = $pet;
            }
            return $el;
        }, $results);

        return $all;
    }

    public function getPatientWelness($where) {

        $query = $this->db->table($this->wellnessTable)->where($where)->get();
        $results = $query->getResult('array');

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $owner = $this->db->table($this->table)->where('id', $el['clientId'])->get()->getRow();
                $el['patientOwner'] = $owner;

                $pet = $this->db->table($this->patientTable)->where('id', $el['patientId'])->get()->getRow();
                $el['patientDetails'] = $pet;
            }
            return $el;
        }, $results);

        return $all;
    }
}