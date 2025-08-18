<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model {
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
        'password' => 'label' => 'Password',
                      'rules' => 'required|min_length[12]'
                    . "|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])\S{12,}$/]"
                    . "|not_contains_user_fields[email,name,surnames,dni_nie,telephone]", //TODO CHECK MIN LENGTH AND OTHER VALIDATION RULES
        'name'     => 'required|min_length[3]',
        'surnames' => 'required|min_length[3]',
        'birthdate' => 'required|valid_date[Y-m-d]', // DD-MM-YYYY format
        'address'  => 'required',
        'dni_nie'  => 'permit_empty|alpha_numeric_space|min_length[8]|max_length[9]',
        'telephone' => 'required|check_phone_number', 
        'soft_skills' => 'permit_empty',
        'technical_skills' => 'permit_empty',
    ];

    protected $validationMessages = [
        'email' => [
            'required'    => 'The Email field is required.',
            'valid_email' => 'Please provide a valid email address.',
            'is_unique'   => 'Provide another email address.',
        ],
        'password' => [
            'required'   => 'The Password field is required.',
            'min_length' => 'The Password must be at least 8 characters long.',
            'regex_match' => 'Debe incluir minúscula, mayúscula, número y símbolo, y no contener espacios.',
        'not_contains_user_fields' => 'No puede contener tu email, nombre, apellidos, DNI/NIE ni teléfono.',
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
        ]
    ];    
    protected $skipValidation     = false;

    public function findByEmail(string $email) {
            return $this->where('email', $email)->first();
    }

    public function getRole(int $userId) {
        if ($this->db->table('Profile_Admin')->where('id_prof_admin', $userId)->countAllResults() > 0) {
            return 'Profile_Admin';
        }
        if ($this->db->table('Manager')->where('id_manager', $userId)->countAllResults() > 0) {
            return 'Manager';
        }
        if ($this->db->table('Head_Of_Team')->where('id_head_of_team', $userId)->countAllResults() > 0) {
            return 'Head_Of_Team';
        }
        if ($this->db->table('Worker')->where('id_worker', $userId)->countAllResults() > 0) {
            return 'Worker';
        }
        return null;
    }
}
