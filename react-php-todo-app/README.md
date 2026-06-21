
# React & PHP/MySQL REST API Task Manager

A simple full-stack To-Do application built with a React frontend and a clean PHP REST API backend.

## Project Structure
- `/backend`: Contains the PHP API logic (`api.php`) and the SQL schema (`database.sql`).
- `/frontend`: Contains the React/Vite source code.

## Setup Instructions

### Backend Setup
1. Import the `database.sql` file into your MySQL database (phpMyAdmin).
2. Place the `backend` folder files into your local server environment (e.g., XAMPP `htdocs`).
3. Adjust the database configuration ports/passwords inside `api.php` if necessary.

### Frontend Setup
1. Navigate to the `frontend` directory.
2. Run `npm install` to install all necessary dependencies.
3. Run `npm run dev` to start the local Vite development server.
