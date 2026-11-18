<?php
class TaskPresenter {
    private $model;
    private $view;

    // INJEÇÃO DE DEPENDÊNCIA: O Presenter recebe o Model e a View prontos
    public function __construct(Task $model, TaskViewInterface $view) {
        $this->model = $model;
        $this->view = $view;
    }

    public function index() {
        try {
            $tasks = $this->model->getAll();
           
            // AQUI ESTÁ A REGRA: Passa por cada tarefa e bota o título em MAIÚSCULO
            foreach ($tasks as &$task) {
                $task['title'] = strtoupper($task['title']);
            }
           
            // O Presenter manda a View exibir a lista tratada
            $this->view->displayTasks($tasks);
        } catch (Exception $e) {
            // Se der erro, manda a View mostrar a mensagem de erro
            $this->view->showError($e->getMessage());
        }
    }

    public function create($title, $description, $dueDate) {
        try {
            $this->model->save($title, $description, $dueDate);
            header("Location: index.php");
        } catch (Exception $e) {
            $this->view->showError($e->getMessage());
        }
    }

    public function complete($id) {
        $this->model->complete($id);
        header("Location: index.php");
    }

    public function delete($id) {
        $this->model->delete($id);
        header("Location: index.php");
    }
}
?>