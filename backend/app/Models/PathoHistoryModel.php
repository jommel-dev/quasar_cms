<?php

namespace App\Models;

use CodeIgniter\Model;

class PathoHistoryModel extends Model
{
    protected $table = "tbl_history";

    protected $allowedFields = ['appId', 'requestData', 'actionStatus', 'userId', 'actionTaken'];

}