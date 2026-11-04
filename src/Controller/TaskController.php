<?php
class TaskController {
    private $model;

    public function __construct(PDO $pdo) {
        $this->model = new Task($pdo); 
    }

    public function index($error = null) {
        $tasks = $this->model->getAll();
        require __DIR__ . '/../View/list.php'; 
    }

    public function create() {
        try {
            $this->model->save($_POST['title'], $_POST['description'], $_POST['due_date']);
            header("Location: index.php");
        } catch (Exception $e) {
            $this->index($e->getMessage()); 
        }
    }

    public function complete() {
        if (isset($_GET['id'])) {
            $this->model->complete($_GET['id']);
        }
        header("Location: index.php");
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $this->model->delete($_GET['id']);
        }
        header("Location: index.php");
    }
}
?>