<?php

namespace App\Controllers;

use App\Models\TaskModel;

class Tasks extends BaseController {
    protected $taskModel;

    public function __construct() {
        $this->taskModel = new TaskModel();
    }

    public function myDay() {
        //$data['tasks'] = $this->taskModel
        $userId = session()->get('id_user');
        $tasks = $this->taskModel
        ->select('Task.*')
        ->join('tasks_users', 'Task.id_task = tasks_users.id_task')
        ->where('tasks_users.id_user', $userId)
        ->where('limit_date >=', date('Y-m-d 00:00:00'))
        ->where('limit_date <=', date('Y-m-d 23:59:59'))
        ->findAll();

        $total = count($tasks);
        $completed = 0;
        foreach ($tasks as $t) {
            if ($t['state'] === 'Done') {
                $completed++;
            }
        }

        $data = [
            'tasks'   => $tasks,
            'today'   => date('Y-m-d'),
            'percent' => $total > 0 ? round(($completed / $total) * 100) : 0,
        ];

        return view('tasks/my_day', $data);
    }

    public function myTasks() {
        $userId = session()->get('id_user');
        $data['tasks'] = $this->taskModel
            ->select('Task.*')
            ->join('tasks_users', 'Task.id_task = tasks_users.id_task')
            ->where('tasks_users.id_user', $userId)
            ->findAll();
        return view('tasks/my_tasks', $data);
    }

    public function show($id) {
        $task = $this->taskModel->find($id);
        if (!$task) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('tasks/show', ['task' => $task]);
    }

    public function create() {
        return view('tasks/create');
    }

    public function store() {
        if (!$this->validate([
            'name' => 'required|min_length[3]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->taskModel->save([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'limit_date'  => $this->request->getPost('limit_date'),
            'state'       => $this->request->getPost('state'),
            'priority'    => $this->request->getPost('priority')
        ]);

        return redirect()->to('/Tasks/MyTasks')->with('message', 'Tarea creada');
    }
}
