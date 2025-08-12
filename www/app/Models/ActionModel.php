<?php
namespace App\Models;

use CodeIgniter\Model;

class ActionModel extends Model
{
    protected $table      = 'Action';
    protected $primaryKey = 'id_actions';

    protected $allowedFields = ['name', 'description', 'simulated'];

    public function findByName(string $name)
    {
        return $this->where('name', $name)->first();
    }

    public function getAllActions(): array
    {
        return $this->findAll();
    }

    public function getActionById(int $id): ?array
    {
        return $this->find($id);
    }

    public function addAction(array $data): bool
    {
        return $this->insert($data) !== false;
    }

    public function updateAction(int $id, array $data): bool
    {
        return $this->update($id, $data) !== false;
    }

    public function deleteAction(int $id): bool
    {
        return $this->delete($id) !== false;
    }
}