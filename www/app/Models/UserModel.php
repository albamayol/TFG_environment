<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model {
    protected $profileAdminModel;
    protected $managerModel;
    protected $headOfTeamModel;
    protected $workerModel;

    public function __construct()
    {
        parent::__construct();
        $this->profileAdminModel = new ProfileAdminModel();
        $this->managerModel = new ManagerModel();
        $this->headOfTeamModel = new HeadOfTeamModel();
        $this->workerModel = new WorkerModel();
    }

    protected $table      = 'Usuario';
    protected $primaryKey = 'id_user';
    
    protected $useAutoIncrement = true;
    
    //The fields allowed for insert and update operations are specified (`$allowedFields`). 
    protected $allowedFields = [
        'name', 'surnames', 'email', 'password', 'birthdate', 'address',
        'dni_nie', 'telephone', 'soft_skills', 'technical_skills', 'simulated'
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data): array {
        if (isset($data['data']['password'])) {
            // Evita doble hash si te llega ya hasheada por cualquier motivo
            $info = password_get_info($data['data']['password']);
            if (($info['algo'] ?? 0) === 0) {
                $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
            }
        }
        // No guardes repeated_password (ya no está en $allowedFields, pero por si acaso)
        unset($data['data']['repeated_password']);
        
        log_message('debug', 'hashPassword callback fired');
        
        return $data;
    }

    protected $useTimestamps = false;

    protected $validationRules = [
        'email'    => 'required|valid_email|is_unique[Usuario.email,id_user,{id_user}]',
        'password' => [
            'label' => 'Password',
            'rules' => 'required|min_length[12]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z0-9])\\S{12,}$/]|not_contains_user_fields[email,name,surnames,dni_nie,telephone]',
        ],
        'name'     => 'required|min_length[3]',
        'surnames' => 'required|min_length[3]',
        'birthdate' => 'required|valid_date[Y-m-d]', // DD-MM-YYYY format
        'address'  => 'required',
        'dni_nie'  => 'permit_empty|alpha_numeric_space|min_length[8]|max_length[9]',
        'telephone' => 'required|check_phone_number', 
        'soft_skills' => 'permit_empty',
        'technical_skills' => 'permit_empty',
        'Role' => 'required|in_list[Profile_Admin,Manager,Head_Of_Team,Worker]', // Only for IAM user creation
    ];

    protected $validationMessages = [
        'email' => [
            'required'    => 'The Email field is required.',
            'valid_email' => 'Please provide a valid email address.',
            'is_unique'   => 'Provide another email address.',
        ],
        'password' => [
            'required'   => 'The Password field is required.',
            'min_length' => 'The Password must be at least 12 characters long.',
            'regex_match' => 'The password format is not valid.',
            'not_contains_user_fields' => 'The password can not contain email, name, surnames, DNI/NIE or telephone.',
        ],
        'repeated_password' => [
            'required' => 'The Repeated Password field is required.',
            'matches'  => 'The Repeated Password does not match the Password.',
        ],
        'name' => [
            'required'   => 'The Name field is required.',
            'min_length' => 'The Name must be at least 3 characters long.',
        ],
        'surnames' => [
            'required'   => 'The Surname field is required.',
            'min_length' => 'The Surname must be at least 3 characters long.',
        ],
        'birthdate' => [
            'required' => 'The Birthdate field is required.',
            'valid_date' => 'The Birthdate must be a valid date in the format DD-MM-YYYY.',
        ],
        'address' => [
            'required' => 'The Address field is required.',
        ],
        'dni_nie' => [
            'alpha_numeric_space' => 'The DNI/NIE must contain only alphanumeric characters and spaces.',
            'min_length'          => 'The DNI/NIE must be at least 8 characters long.',
            'max_length'          => 'The DNI/NIE must not exceed 9 characters.',
        ],
        'telephone' => [
            'required' => 'The Telephone field is required.',
            'check_phone_number' => 'The Telephone number must be a valid phone number.',
        ],
        'soft_skills' => [
            'permit_empty' => 'Soft Skills can be left empty.',
        ],
        'technical_skills' => [
            'permit_empty' => 'Technical Skills can be left empty.',
        ],
        'Role' => [
            'required' => 'The Role field is required.',
            'in_list'  => 'The Role must be one of: Profile Admin, Manager, Head Of Team, Worker.',
        ],
    ];    
    protected $skipValidation     = false;

    public function findByEmail(string $email) {
            return $this->where('email', $email)->first();
    }

    public function getUsersForIAM() {
        $users = $this->select('id_user, name, surnames, email, simulated')->findAll();
        foreach ($users as &$user) {
            $user['role'] = $this->getRole($user['id_user']) ?? null;
        }
        return $users;
    }

    public function getRole (int $userId) {
        if ($this->profileAdminModel->isProfileAdmin($userId)) {
            return 'Profile_Admin';
        }
        if ($this->managerModel->isManager($userId)) {
            return 'Manager';
        }
        if ($this->headOfTeamModel->isHeadOfTeam($userId)) {
            return 'Head_Of_Team';
        }
        if ($this->workerModel->isWorker($userId)) {
            return 'Worker';
        }
        return null;
    }

    public function getAllHeadsOfTeam(): array {
        $hotIDs = $this->headOfTeamModel->findColumn('id_head_of_team'); 
        if (empty($hotIDs)) {
            return [];
        }
        $hots = $this->whereIn('id_user', $hotIDs)->orderBy('name', 'ASC')->findAll();
        return $hots;
    }

    public function getAllWorkers(): array {
        $workerIDs = $this->workerModel->findColumn('id_worker'); 
        if (empty($workerIDs)) {
            return [];
        }
        $workers = $this->whereIn('id_user', $workerIDs)->orderBy('name', 'ASC')->findAll();
        return $workers;
    }

    public function getAllManagers(): array {
        $managerIDs = $this->managerModel->findColumn('id_manager'); 
        if (empty($managerIDs)) {
            return [];
        }
        $managers = $this->whereIn('id_user', $managerIDs)->orderBy('name', 'ASC')->findAll();
        return $managers;
    }

    public function deleteUser(int $userId): bool {
        $userToDeleteRole = $this->getRole($userId);
        switch ($userToDeleteRole) {
            case 'Profile_Admin':
                $this->profileAdminModel->where('id_prof_admin', $userId)->delete();
                break;
            case 'Manager':
                $this->managerModel->where('id_manager', $userId)->delete();
                break;
            case 'Head_Of_Team':
                $this->headOfTeamModel->where('id_head_of_team', $userId)->delete();
                break;
            case 'Worker':
                $this->workerModel->where('id_worker', $userId)->delete();
                break;
        }
        return $this->delete($userId);
    }
}
