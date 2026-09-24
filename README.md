# RevPanda Casino & Offers Management Platform

A containerized Web application built with **Yii2 Framework**, **PHP 8.3 Apache**, **MySQL 8.0**, and **Bootstrap 5**.

---

## 🚀 Quick Start (Only 2 Commands)

### 1. Start the Containers
```bash
docker compose up -d --build
```
> The application will be available at: **http://localhost:8080**

### 2. Run Database Migrations
```bash
docker compose exec app php yii migrate --interactive=0
```

### 3. (Optional) Run Initial Seed Data
To populate demo casinos and 30 realistic offers (covering welcome bonuses, free spins, no deposit bonuses, active/draft/expired statuses):
```bash
docker compose exec app php yii seed --interactive=0
```
*(To rollback seed data at any time: `docker compose exec app php yii seed/down 1 --interactive=0`)*

---

## 🧪 Testing & Code Quality

### 1. Automated Test Suite
Complete test coverage across all models, behaviors, search filters, N+1 query prevention, and **every single public, auth, and admin endpoint**:

```bash
# Run all tests
docker compose exec app composer test

# Run with human-readable specification output
docker compose exec app vendor/bin/phpunit --testdox


```

### 2. Static Analysis: PHPStan (Level 5)
Static analysis configured at Level 5 with zero errors across controllers, models, config, and tests:

```bash
docker compose exec app composer stan
# or: docker compose exec app vendor/bin/phpstan analyse
```

---

## 🔐 Admin Authentication & Credentials

Admin sections are strictly guarded behind authentication (`yii\filters\AccessControl`). Unauthenticated visits are automatically redirected to `/login`.

- **Login URL:** [http://localhost:8080/login](http://localhost:8080/login)
- **Username:** `admin`
- **Password:** `admin123`

---
