# 🏘️ Homi: Smart Multi-Tenant Housing Ecosystem

[![Laravel](https://img.shields.io/badge/Backend-Laravel%2012.x-red.svg)](https://laravel.com)
[![Kotlin](https://img.shields.io/badge/Mobile-Kotlin%20Compose-blue.svg)](https://kotlinlang.org)
[![MySQL](https://img.shields.io/badge/Database-MySQL-blue.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/License-Proprietary-black.svg)](#)

**Homi** is a premium, enterprise-grade residential management platform designed to bridge the gap between housing management and residents. Built with a high-performance **Multi-Tenant, Multi-Database** architecture, Homi ensures absolute data isolation and massive scalability for modern property developers and township managers.

---

## ✨ Core Value Propositions
- **🚀 Automation First:** From automated billing and WhatsApp reminders to self-service digital document requests.
- **🛡️ Multi-Database Security:** Each housing cluster (Tenant) has its own dedicated database, ensuring 100% data privacy and isolation.
- **🧠 AI-Ready:** Built-in Naive Bayes predictive models to analyze delinquency risks and payment behavior.
- **📱 Premium Mobile Experience:** A native Android resident app built with **Jetpack Compose** for a silky-smooth, modern interface.

---

## 🛠️ Tech Stack & Architecture

### Backend (The Engine)
- **Framework:** Laravel 12.x (PHP 8.3+)
- **Architecture:** Multi-Tenancy (Isolated DBs) + Central Landlord DB.
- **Key Services:**
    - **PDF Engine:** Dynamic letter generation via Browsershot.
    - **Notification Hub:** Unified FCM (Push), In-App, and WhatsApp messaging.
    - **Job Scheduler:** Redis-powered background processing for massive task execution.

### Mobile (The Interface)
- **Language:** Kotlin
- **UI Framework:** Jetpack Compose (Modern Declarative UI)
- **Navigation:** Animated AppNavHost
- **Persistence:** Jetpack DataStore

---

## 📸 Key Features & Preview

| **Resident App** | **Admin Dashboard** |
| :--- | :--- |
| **Interactive Onboarding:** Professional 3D illustrations for first-time users. | **Tenancy Control:** One-click tenant synchronization and database migration. |
| **Smart Billing:** Pay fees via QRIS and track payment history instantly. | **Automated Reminders:** AI-driven scanning for overdue invoices with auto-reminders. |
| **Document Portal:** Request administrative letters (Domisili, etc.) and get PDFs. | **Announcement Master:** Exclusive single-active announcement system for focused communication. |

---

## ⚙️ Quick Start

### 1. Backend Setup
```bash
git clone https://github.com/your-username/homi-project.git
cd backend-homi
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
```

### 2. Database Initialization
1. Create a central database named `homi`.
2. Run migrations for the landlord/central context:
   ```bash
   php artisan migrate --path=database/migrations/landlord
   php artisan db:seed
   ```
3. Initialize your first tenant (Hawaii Garden):
   ```bash
   php artisan tenant:initialize hawaii-garden
   ```

### 3. Syncing All Tenants
Whenever you update schemas across the platform:
```bash
php artisan homi:tenants-migrate
```

---

## 🏢 System Ecosystem
- **Central Context:** Manages the subscription of tenants, super admin accounts, and global settings.
- **Tenant Context:** Handles the specific day-to-day operations of a housing cluster (announcements, fees, residents, staff).

---

## 📜 Academic & Business Context
Homi was developed as a comprehensive solution for local township management issues, addressing high delinquency rates and the lack of transparency in fund management within residential areas.

---

**Developed with ❤️ by the Homi Team.**
*Proprietary Software - All Rights Reserved.*
