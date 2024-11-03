# Budget Manager Backend

A backend application to manage personal budgets, allowing users to track their incomes and expenses. This project is hosted locally using XAMPP.

## Table of Contents

1. [Project Overview](#project-overview)
2. [Features](#features)
3. [Technologies](#technologies)
4. [Installation and Setup](#installation-and-setup)

## Project Overview

The *Budget Manager Backend* is designed to provide RESTful services for managing users, incomes, and expenses. This backend works in conjunction with a frontend interface where users can visualize their financial data.

## Features

- User authentication and session management
- Tracking incomes and expenses by category
- Monthly and yearly financial summaries
- Custom date range reporting

## Technologies

- **PHP** - Backend logic
- **MySQL** - Database for storing user and financial data
- **Apache Server** - Provided by XAMPP for local hosting
- **JavaScript** - For frontend interactivity (when applicable)

## Installation and Setup

To run this project locally:

1. **Download and Install XAMPP**:
   - Download XAMPP from [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html) and follow the installation instructions.

2. **Set Up the Database**:
   - Start XAMPP and open **phpMyAdmin** at [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
   - Create a database (e.g., `budget_manager`).
   - Import the SQL file (`projekt_bazy_danych.sql`) provided with this project to set up the tables.

3. **Configure Project Files**:
   - Place your project files in the `htdocs` directory (e.g., `xampp/htdocs/budget_manager_backend`).
   - Update the database connection settings in `connect.php`:
     ```php
     $host = 'localhost';
     $db_user = 'root';
     $db_password = '';
     $db_name = 'budget_manager';
     ```

4. **Start Apache and MySQL**:
   - Start both **Apache** and **MySQL** from the XAMPP control panel.

5. **Access the Backend**:
   - Visit [http://localhost/budget_manager_backend](http://localhost/budget_manager_backend) in your browser.
