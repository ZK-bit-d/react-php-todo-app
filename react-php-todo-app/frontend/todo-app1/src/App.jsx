import React, { useState, useEffect } from 'react';
import './App.css';

function App() {
  const [tasks, setTasks] = useState([]);
  const [taskText, setTaskText] = useState('');
  
  // Точната патека до вашето PHP REST API во XAMPP
  const API_URL = 'http://localhost/todo-api/api.php';

  // 1. Автоматски ги зема сите задачи од базата при вчитување
  useEffect(() => {
    fetchTasks();
  }, []);

  const fetchTasks = () => {
    fetch(API_URL)
      .then(response => response.json())
      .then(data => {
        if (Array.isArray(data)) {
          setTasks(data);
        } else {
          console.error('API did not return an array:', data);
        }
      })
      .catch(error => console.error('Error fetching tasks:', error));
  };

  // 2. Функција за додавање нова задача (POST)
  const handleAddTask = (e) => {
    e.preventDefault();
    if (!taskText.trim()) return;

    fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ task_text: taskText })
    })
      .then(response => response.json())
      .then(() => {
        setTaskText(''); // Го чисти полето за внес
        fetchTasks();   // Ја освежува листата со задачи
      })
      .catch(error => console.error('Error adding task:', error));
  };

  // 3. Функција за бришење задача (DELETE)
  const handleDeleteTask = (id) => {
    fetch(`${API_URL}?id=${id}`, {
      method: 'DELETE'
    })
      .then(response => response.json())
      .then(() => {
        fetchTasks(); // Ја освежува листата по бришењето
      })
      .catch(error => console.error('Error deleting task:', error));
  };

  return (
    <div className="todo-container">
      <h2>My React & PHP Task Manager</h2>
      
      {/* Форма за внесување */}
      <form onSubmit={handleAddTask} className="todo-form">
        <input 
          type="text" 
          placeholder="What needs to be done?" 
          value={taskText}
          onChange={(e) => setTaskText(e.target.value)}
          required 
          autoComplete="off"
        />
        <button type="submit">Add Task</button>
      </form>

      {/* Листа на задачи */}
      <ul className="task-list">
        {tasks.length > 0 ? (
          tasks.map(task => (
            <li key={task.id} className="task-item">
              <span>{task.task_text}</span>
              <button onClick={() => handleDeleteTask(task.id)} className="delete-btn">
                Delete
              </button>
            </li>
          ))
        ) : (
          <p className="no-tasks">No tasks found. Add your first task above!</p>
        )}
      </ul>
    </div>
  );
}

export default App;
