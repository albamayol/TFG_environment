<?php
namespace App\Models;

use CodeIgniter\Model;

class HeadOfTeamModel extends Model {
    protected $table      = 'Head_of_Team';
    protected $primaryKey = 'id_head_of_team';
    protected $returnType     = 'array';
    protected $allowedFields = ['id_head_of_team'];

    public function isHeadOfTeam(int $userId): bool {
        return $this->where('id_head_of_team', $userId)->countAllResults() > 0;
    }
}
