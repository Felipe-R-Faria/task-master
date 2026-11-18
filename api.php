<?php

require_once __DIR__ . '/src/Model/Task.php';


header('Content-Type: application/json');

$pdo = new PDO('sqlite:' . __DIR__ . '/tasks.sqlite');
$model = new Task($pdo);

$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {
        case 'list':
    
            echo json_encode($model->getAll());
            break;

        case 'create':
           
            $data = json_decode(file_get_contents('php://input'), true);
            $model->save($data['title'], $data['description'], $data['due_date']);
            echo json_encode(['status' => 'success']);
            break;

        case 'complete':
            $model->complete($_GET['id']);
            echo json_encode(['status' => 'success']);
            break;

        case 'delete':
            $model->delete($_GET['id']);
            echo json_encode(['status' => 'success']);
            break;
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>