<?php
namespace App\Models;

use CodeIgniter\Model;

class WorkerModel extends Model
{
    protected $table      = 'Worker';
    protected $primaryKey = 'id_worker';
    protected $allowedFields = ['id_worker'];

    public function isWorker(int $userId): bool
    {
        return $this->where('id_worker', $userId)->countAllResults() > 0;
    }
}
