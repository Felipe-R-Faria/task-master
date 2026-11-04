<?php

spl_autoload_register(function ($class) {
    $dirs = ['Model', 'Controller', 'View'];
    foreach ($dirs as $dir) {
        $file = __DIR__ . "/src/$dir/$class.php";
        if (file_exists($file)) {
            require_once $file;
        }
    }
});


$pdo = new PDO('sqlite:' . __DIR__ . '/tasks.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$controller = new TaskController($pdo);
$action = $_GET['action'] ?? 'index'; 

if (method_exists($controller, $action)) {
    $controller->$action(); 
} else {
    echo "Página não encontrada 404";
}
?>