# CAMS – Child Abuse Management System

<p align="center">
    <a href="https://laravel.com"><img src="https://img.shields.io/badge/Built%20With-Laravel-red.svg" alt="Built With Laravel"></a>
    <a href="#"><img src="https://img.shields.io/badge/License-MIT-blue.svg" alt="License"></a>
    <a href="#"><img src="https://img.shields.io/badge/PHP-%5E8.1-blueviolet" alt="PHP Version"></a>
    <a href="#"><img src="https://img.shields.io/badge/MySQL-Database-success" alt="Database"></a>
</p>

## 📖 About the Project

**CAMS (Child Abuse Management System)** is a web-based platform built with **Laravel** that enables streamlined reporting, management, and tracking of child abuse cases.

It connects **Admins**, **Social Workers**, and **Police Officers** in a centralized system to ensure child protection cases are properly documented, investigated, and resolved.

### 🎯 Key Features

- 🔐 **Role-based Access** (Admin, Social Worker, Police Officer).
- 📂 **Case Management**: Report, update, and track cases.
- 👶 **Child Information**: Record child details, medical conditions, and school data.
- 🕵️ **Offender Details**: Capture suspected offender information.
- 🧑‍🤝‍🧑 **Reporter Information**: Record guardian/teacher/community member who reports.
- 👮 **Assignment System**: Social Workers can assign Police Officers to cases.
- 📊 **Case Dashboard & Reports** for monitoring and decision-making.
- 🔎 **Search & Filters** for quick access to cases.

---

## 👥 User Roles & Responsibilities

- **Admin**

  - Manage users (Social Workers & Police Officers).
  - Assign roles & permissions.
  - Monitor and generate reports.
- **Social Worker**

  - Create and manage child abuse cases.
  - Assign Police Officers to cases.
  - Track progress and provide case updates.
- **Police Officer**

  - Receive assigned cases.
  - Conduct investigations and update case status.
  - Upload findings and close cases when resolved.

---

## 🛠️ Tech Stack

- **Framework:** [Laravel 10+](https://laravel.com)
- **Language:** PHP 8.1+
- **Database:** MySQL / MariaDB
- **Frontend:** Blade, Bootstrap 5, Tailwind (optional)
- **Others:** Composer, NPM, Artisan

---

## 🚀 Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/your-username/cams.git
   cd cams
   ```
2. Install dependencies:

   ```bash
   composer install
   npm install && npm run dev
   ```
3. Copy `.env` file and set up your database:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Run migrations:

   ```bash
   php artisan migrate
   ```
5. Start the development server:

   ```bash
   php artisan serve
   ```

---

## 📌 Usage

- Login as **Admin** to create Social Workers and Police Officers.
- Social Workers can create cases and assign officers.
- Police Officers can investigate and close cases.

---

## 📄 License

This project is open-source and available under the **MIT License**.
