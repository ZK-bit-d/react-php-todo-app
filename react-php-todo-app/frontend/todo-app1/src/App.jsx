import React, { useState, useEffect } from 'react';
import './App.css';

function App() {
  const [tasks, setTasks] = useState([]);
  const [taskText, setTaskText] = useState('');
  
  // Endpoint URL pointing to the local PHP REST API
  const API_URL = 'http://localhost/todo-api/api.php';

  // Fetch task operations on initial component mount lifecycle
  useEffect(() => {
    fetchTasks();
  }, []);

  // 1. Fetch data operation - GET query request to PHP
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

  // 2. Form submission handler - POST query request to add tasks
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
        setTaskText(''); // Clear input fields
        fetchTasks();   // Reload application data stream
      })
      .catch(error => console.error('Error adding task:', error));
  };

  // 3. User interaction handler - DELETE request to clear specific items
  const handleDeleteTask = (id) => {
    fetch(`${API_URL}?id=${id}`, {
      method: 'DELETE'
    })
      .then(response => response.json())
      .then(() => {
        fetchTasks(); // Refresh app layout view sync
      })
      .catch(error => console.error('Error deleting task:', error));
  };

  return (
    <div className="todo-container">
      <h2>My React & PHP Task Manager</h2>
      
      {/* Component layout form container view */}
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

      {/* Rendered dynamic dataset array tracking task entries */}
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
