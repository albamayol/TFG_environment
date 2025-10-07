<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class Timezone extends Controller
{
    public function setTimezone()
    {
        $tz = $this->request->getJSON(true)['timezone'] ?? null;
        if ($tz) {
            session()->set('user_timezone', $tz);
        }
        // If CSRF is enabled with regeneration, return the fresh token
        return $this->response->setJSON([
            'ok' => true,
            'csrf' => [
                'name' => csrf_token(),
                'hash' => csrf_hash(),
            ],
        ])->setStatusCode(200);
    }
}