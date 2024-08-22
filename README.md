# ATM System

This project is a Laravel-based ATM system that allows users to deposit funds, withdraw funds, and view transaction history. The system enforces a minimum balance requirement and only allows withdrawals in multiples of 50,000 IDR.

## Features

- Deposit funds
- Withdraw funds (in multiples of 50,000 IDR)
- Minimum balance enforcement (50,000 IDR)
- SweetAlert for confirmation and error pop-ups
- AJAX for seamless user experience

## Requirements

- Docker
- Docker Compose
- Git

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/mini-atm.git
cd mini-atm
```

### 2. Set Up Environment Variables

Copy the .env.example file to .env to create your environment configuration. This file contains the environment variables required to run the Laravel application.

```bash
cp .env.example .env
```

Next, update the .env file with your database credentials and other necessary configurations. The default setup is configured for Docker, so you may not need to change much.

Key variables to check:

```bash
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=mini-atm
DB_USERNAME=root
DB_PASSWORD=root
```

### 3. Start Docker Containers

Use Docker Compose to build and start the containers for the application and database:

```bash
docker compose up -d
```

This will start the containers in detached mode. The application will be running in the app container, and the database will be running in the db container.

### 4. Access the Application

http://localhost:8045
