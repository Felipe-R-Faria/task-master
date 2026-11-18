<?php

spl_autoload_register(function ($class) {
    $dirs = ['Model', 'Presenter', 'View'];
    foreach ($dirs as $dir) {
        $file = __DIR__ . "/src/$dir/$class.php";
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

$pdo = new PDO('sqlite:' . __DIR__ . '/tasks.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$model = new Task($pdo);

$view = new TaskHtmlView();

$presenter = new TaskPresenter($model, $view);


$action = $_GET['action'] ?? 'index';

if ($action === 'create') {
    $presenter->create($_POST['title'] ?? '', $_POST['description'] ?? '', $_POST['due_date'] ?? '');
} elseif ($action === 'complete') {
    $presenter->complete($_GET['id']);
} elseif ($action === 'delete') {
    $presenter->delete($_GET['id']);
} else {
    $presenter->index();
}
?>