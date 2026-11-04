<?php
class Task {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM tasks ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save($title, $description, $dueDate, $assignee) {
        if (empty(trim($title)) || empty(trim($dueDate)) || empty(trim($assignee))) {
            throw new Exception("Título, Data e Responsável são obrigatórios.");
        }
        
        $stmt = $this->pdo->prepare("INSERT INTO tasks (title, description, due_date, assignee) VALUES (:title, :description, :due_date, :assignee)");
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':due_date', $dueDate);
        $stmt->bindValue(':assignee', $assignee);
        return $stmt->execute();
    }

    public function complete($id) {
        return $this->pdo->exec("UPDATE tasks SET done = 1 WHERE id = " . (int)$id);
    }

    public function delete($id) {
        return $this->pdo->exec("DELETE FROM tasks WHERE id = " . (int)$id);
    }
}
?>