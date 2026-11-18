
class TaskViewModel {
    constructor() {
        this.tasks = []; 
        this.init();
    }

    async init() {
        
        this.listElement = document.getElementById('taskList');
        this.titleInput = document.getElementById('taskTitle');
        this.descInput = document.getElementById('taskDescription');
        this.dateInput = document.getElementById('taskDueDate');
        this.addBtn = document.getElementById('addBtn');

       
        this.addBtn.onclick = () => this.addTask();

       
        await this.fetchTasks();
    }

   
    async fetchTasks() {
        const response = await fetch('api.php?action=list');
        this.tasks = await response.json();
        this.render(); // Atualiza a View
    }

    async addTask() {
        const title = this.titleInput.value;
        const description = this.descInput.value;
        const dueDate = this.dateInput.value;

        if (!title) return alert("Digite o título da tarefa!");

        await fetch('api.php?action=create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                title: title, 
                description: description, 
                due_date: dueDate 
            })
        });

        // Limpa os campos
        this.titleInput.value = '';
        this.descInput.value = '';
        this.dateInput.value = '';

        await this.fetchTasks();
    }

    async deleteTask(id) {
        await fetch(`api.php?action=delete&id=${id}`);
        await this.fetchTasks();
    }

    
    render() {
        this.listElement.innerHTML = '';
        this.tasks.forEach(task => {
            const li = document.createElement('li');
            li.className = task.done ? 'task-item done' : 'task-item';
            
            li.innerHTML = `
                <div class="task-info">
                    <strong>${task.title}</strong>
                    <p>${task.description || ''}</p>
                    <small>Vencimento: ${task.due_date || 'N/A'}</small>
                </div>
                <div class="task-actions">
                    <button class="delete-btn" onclick="vm.deleteTask(${task.id})">Excluir</button>
                </div>
            `;
            this.listElement.appendChild(li);
        });
    }
}


const vm = new TaskViewModel();