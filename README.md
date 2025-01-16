
# Movie Rentals API and Frontend

This project is a simple Movie Rentals application that provides a backend API built with Laravel and a frontend built with React using Vite. The application includes functionality for managing movies, placing orders, and calculating total costs based on movie tags.

---

## Prerequisites
- PHP >= 8.1
- Composer
- Node.js and npm
- SQLite (or another supported database)

---

## Setup Instructions

### 1. Clone the Repository
```bash
git clone git@github.com:MyTek/movie_rentals.git
cd movie_rentals
```

### 2. Install Dependencies
- Install PHP dependencies:
  ```bash
  composer install
  ```
- Install JavaScript dependencies:
  ```bash
  npm install
  ```

### 3. Set Up the Database
- Run migrations to create the necessary tables:
  ```bash
  php artisan migrate
  ```
- Seed the database with sample data:
  ```bash
  php artisan db:seed
  ```

### 4. Start the Development Servers
- Start the Laravel backend:
  ```bash
  php artisan serve --port=8550
  ```
- Start the Vite development server for the frontend:
  ```bash
  npm run dev
  ```

### 5. Access the Application
- Open your browser and navigate to:
  - **Frontend**: [http://localhost:8550](http://localhost:8550) (or the port shown by `npm run dev`)
  - **Backend API**: [http://localhost:8550/api](http://localhost:8550/api)

---

## Running Tests
### Backend Tests
- Run the PHPUnit tests to ensure everything works as expected:
  ```bash
  php artisan test
  ```

---

## Features
- Manage movies with adjustable pricing based on tags.
- Place orders for movies with automatic total cost calculation.
- API documentation (optional) for available endpoints.

---

## Notes
- Make sure to update the `.env` file for your specific environment, such as database credentials.
- For production deployment, ensure proper configurations for environment variables and use optimized commands:
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  npm run build
  ```

Enjoy working with the Movie Rentals project! 🎥🍿
