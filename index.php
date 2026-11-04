<?php
// ==========================================
// AULA 01: O CÓDIGO SPAGHETTI (Tudo misturado)
// ==========================================

// 1. CONEXÃO COM O BANCO DE DADOS E CRIAÇÃO DA TABELA
$dbFile = __DIR__ . '/tasks.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Tabela atualizada com os novos campos
$pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT,
    due_date TEXT NOT NULL,
    assignee TEXT NOT NULL,
    done INTEGER DEFAULT 0
)");

// 2. LÓGICA DE NEGÓCIO E CONTROLE DE REQUISIÇÕES MISTURADOS
$error = '';

// Criar nova tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description'] ?? '');
    $due_date = trim($_POST['due_date'] ?? '');
    $assignee = trim($_POST['assignee'] ?? '');
    
    // Validação dos campos obrigatórios
    if (empty($title) || empty($due_date) || empty($assignee)) {
        $error = "Título, Data de Vencimento e Responsável são obrigatórios!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, due_date, assignee) VALUES (:title, :description, :due_date, :assignee)");
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':due_date', $due_date);
        $stmt->bindValue(':assignee', $assignee);
        $stmt->execute();
        
        header("Location: index.php");
        exit;
    }
}

// Concluir ou excluir tarefa
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    if ($_GET['action'] === 'complete') {
        $pdo->exec("UPDATE tasks SET done = 1 WHERE id = $id");
    } elseif ($_GET['action'] === 'delete') {
        $pdo->exec("DELETE FROM tasks WHERE id = $id");
    }
    
    header("Location: index.php");
    exit;
}

// 3. BUSCA DE DADOS MISTURADA COM A VISUALIZAÇÃO
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY id DESC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Master - Spaghetti Expandido</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; color: #333; display: flex; justify-content: center; padding-top: 50px; }
        .container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 600px; }
        h1 { font-size: 1.5rem; text-align: center; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .error { color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 4px; font-size: 0.9rem; }
       
        .form-group { display: flex; flex-direction: column; gap: 10px; margin-top: 20px; margin-bottom: 20px; background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb; }
        input[type="text"], input[type="date"], textarea { padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; }
        button { background: #2563eb; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background: #1d4ed8; }
        
        ul { list-style: none; padding: 0; }
        li { display: flex; flex-direction: column; padding: 15px; border-bottom: 1px solid #eee; gap: 8px; }
        li.done .task-title, li.done .task-meta { text-decoration: line-through; color: #9ca3af; }
        
       
        .task-header { display: flex; justify-content: space-between; align-items: center; }
        .task-title { font-weight: bold; font-size: 1.1rem; }
        .task-meta { font-size: 0.85rem; color: #4b5563; display: flex; gap: 15px; }
        .task-meta span { background: #e5e7eb; padding: 2px 8px; border-radius: 12px; }
        .task-desc { font-size: 0.95rem; color: #6b7280; margin-top: 5px; }
        
        .actions a { text-decoration: none; margin-left: 10px; cursor: pointer; font-size: 1.2rem; }
    </style>
</head>
<body>

<div class="container">
    <h1>Task Master (Spaghetti Expandido)</h1>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php" class="form-group">
        <input type="text" name="title" placeholder="O que precisa ser feito? (Obrigatório)" autocomplete="off" required>
        <textarea name="description" placeholder="Descrição detalhada (Opcional)" rows="2"></textarea>
        
        <div style="display: flex; gap: 10px;">
            <input type="date" name="due_date" style="flex: 1;" required title="Data de Vencimento">
            <input type="text" name="assignee" placeholder="Responsável (Obrigatório)" style="flex: 2;" autocomplete="off" required>
        </div>
        
        <button type="submit">Adicionar Tarefa</button>
    </form>

    <ul>
        <?php foreach ($tasks as $task): ?>
            <li class="<?php echo $task['done'] ? 'done' : ''; ?>">
                <div class="task-header">
                    <span class="task-title"><?php echo htmlspecialchars($task['title']); ?></span>
                    <div class="actions">
                        <?php if (!$task['done']): ?>
                            <a href="?action=complete&id=<?php echo $task['id']; ?>" title="Concluir">✅</a>
                        <?php endif; ?>
                        <a href="?action=delete&id=<?php echo $task['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?');" title="Excluir">❌</a>
                    </div>
                </div>
                
                <div class="task-meta">
                    <span> Resp: <strong><?php echo htmlspecialchars($task['assignee']); ?></strong></span>
                    <span> Vence em: <strong><?php echo date('d/m/a', strtotime($task['due_date'])); ?></strong></span>
                </div>
                
                <?php if (!empty($task['description'])): ?>
                    <div class="task-desc">
                        <?php echo nl2br(htmlspecialchars($task['description'])); ?>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>
</html>