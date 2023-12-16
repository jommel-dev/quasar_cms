<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoryModel extends Model
{
    protected $table = "tblapplication_history";

    protected $allowedFields = ['applicationId', 'requestData', 'actionStatus', 'createdBy'];

}