# Homi Platform: Multi-Tenant Residential Management

Homi is a modern, high-fidelity residential management platform designed with a multi-tenant, multi-database architecture. It allows multiple housing complexes (Perumahan) to operate on the same platform while keeping their data strictly isolated in separate databases.

## 🚀 Key Features
- **Multi-Database Tenancy**: Strict data isolation per housing complex.
- **Centralized Admin**: Super Admin dashboard to manage and sync staff across tenants.
- **Resident Services**: Digitalized complaint management, fee tracking, and notification systems.
- **Mobile-First Design**: Seamless integration with the Homi Resident App (Android/Compose).

## 🛠️ Tech Stack
- **Backend**: Laravel 12.x + MySQL
- **Frontend**: Blade + Tailwind CSS (Admin/Dashboard)
- **Mobile**: Kotlin Jetpack Compose (Modern Material Design)
- **Authentication**: Custom Guard supporting Central and Tenant contexts.

## 🏁 Getting Started

### Prerequisites
- PHP 8.3+
- Composer
- MySQL 8.0+
- Node.js & NPM

### Installation

1. **Clone the project**
   ```bash
   git clone https://github.com/your-username/homi-project.git
   cd homi-project
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Edit `.env` and set your `CENTRAL_DB_*` credentials.

4. **First-Time Database Setup**
   - Create a central database manually (e.g., named `homi`).
   - Run central migrations and seed initial data:
     ```bash
     php artisan migrate --path=database/migrations/landlord
     php artisan db:seed
     ```
   - Initialize the default tenant (creates the tenant DB and migrates it):
     ```bash
     php artisan tenant:initialize hawaii-garden
     ```

5. **Synchronization & Maintenance**
   To sync admin accounts from the central DB to all tenant DBs:
   ```bash
   php artisan homi:sync-admins
   ```

## 🏗️ Architecture Note
This project uses a custom `TenantManager` to resolve connections dynamically based on the request.
- **Central Connection**: Used for global state (Tenants, SuperAdmin).
- **Tenant Connection**: Switched at runtime based on the `X-Tenant-Code` header or domain.

## 📄 License
The Homi platform is proprietary software. All rights reserved.
