# Books App

This project is a full-stack web application made of:

- a Laravel 12 backend API for authentication and book management
- a Vue 3 + Vite frontend for the user interface
- a MySQL database managed with Docker Compose

The application supports user registration/login and CRUD operations for books through a REST API.

## Project structure

- `backend/` — Laravel API backend
- `frontend/` — Vue frontend application
- `docker-compose.yml` — Docker setup for MySQL, backend, and frontend
- `Books API.postman_collection.json` — Postman collection for testing the API

## Tech stack

- Backend: PHP 8.3, Laravel 12, Sanctum, MySQL
- Frontend: Vue 3, Vite, Axios
- Infrastructure: Docker, Docker Compose

## Features

- User registration and login
- Protected API routes using Laravel Sanctum
- Book listing, creation, update, and deletion
- Dockerized local development environment

## API overview

Base API URL:

- Backend: `http://localhost:8000/api`
- Frontend: `http://localhost:3000`

Main endpoints:

- `POST /api/register` — register a new user
- `POST /api/login` — log in a user
- `POST /api/logout` — log out the authenticated user
- `GET /api/user` — fetch the authenticated user
- `GET /api/books` — list books
- `POST /api/books` — create a book
- `GET /api/books/{id}` — fetch a single book
- `PUT /api/books/{id}` — update a book
- `DELETE /api/books/{id}` — delete a book

## Quick start with Docker

1. Open a terminal in the project root.
2. Create the backend environment file if it does not exist:

   Windows PowerShell:
   ```powershell
   Copy-Item .\backend\.env.example .\backend\.env
   ```

   Linux/macOS:
   ```bash
   cp backend/.env.example backend/.env
   ```

3. Make sure the required environment variables are set in `backend/.env` or your shell. The Docker Compose file expects values such as:

   ```env
   APP_KEY=
   MYSQL_DATABASE=kepl_db
   MYSQL_USER=kepl_user
   MYSQL_PASSWORD=kepl_password
   MYSQL_ROOT_PASSWORD=root_password
   ```

4. Start the stack:

   ```bash
   docker compose up --build
   ```

5. Access the applications:

   - Frontend: `http://localhost:3000`
   - Backend API: `http://localhost:8000`

6. Stop the stack with:

   ```bash
   docker compose down
   ```

## Local development notes

The backend uses Docker for the database and web service, while the frontend is a separate Vite app. If you prefer to run them individually, use the project-specific instructions in the backend and frontend folders.

## Testing the API

A ready-to-import Postman collection is included at:

- `Books API.postman_collection.json`

Import it in Postman to test authentication and CRUD requests.

## Useful commands

From the backend directory:

```bash
cd backend
composer install
php artisan migrate
php artisan serve
```

From the frontend directory:

```bash
cd frontend
npm install
npm run dev
```

## License

This project is intended for UTS coursework and does not include a separate license file unless added later.
