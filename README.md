cd Medical_records
git pull --no-rebase
cat > README.md << 'EOF'
# 🏥 Medical Records Management System

![Symfony](https://img.shields.io/badge/Symfony-7-000000?logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?logo=postgresql)
![Auth](https://img.shields.io/badge/Auth-JWT-black)
![Docker](https://img.shields.io/badge/Docker-ready-2496ED?logo=docker)

A Symfony 7 REST API built to practise secure application design — role-based
access control, JWT authentication, rate limiting and full audit logging for
a hospital records system.

## 🌐 Live Demo

**[medical-records-1hu1.onrender.com/login](https://medical-records-1hu1.onrender.com/login)**

Deployed as a Docker web service on [Render](https://render.com), with a
[Neon](https://neon.tech) serverless PostgreSQL database. See [Test Users](#-test-users)
below for demo credentials.

> Note: the free Render instance spins down when idle, so the first request
> after a period of inactivity can take up to a minute to respond.

## 📸 Screenshots

| Login | Admin Dashboard | Audit Logs |
|---|---|---|
| ![Login](docs/screenshots/login.png) | ![Dashboard](docs/screenshots/dashboard.png) | ![Audit logs](docs/screenshots/audit-logs.png) |

## 🔐 Security Features

This project exists specifically to explore application security in Symfony:

- **Role-Based Access Control (RBAC)** — hierarchical roles: ADMIN > DOCTOR > NURSE > RECEPTIONIST
- **Symfony Voters** — granular, per-record access control based on role and ownership
- **JWT Authentication** — stateless auth for the REST API (1-hour token lifetime)
- **Rate Limiting** — max 5 login attempts every 15 minutes
- **Account Lockout** — account locked after 5 consecutive failed logins
- **Audit Logging** — every access logged to the database with IP, user and timestamp
- **CSRF Protection** — on all edit/delete forms
- **Suspicious Access Monitoring** — admin panel showing failed access attempts in the last 24h

## 🛠️ Tech Stack

PHP 8.3 · Symfony 7 · Doctrine ORM · PostgreSQL · Lexik JWT Bundle · Docker

## 🚀 Getting Started

The whole app (PHP + PostgreSQL) runs with a single command via Docker.
Database migrations, demo data and the JWT keypair are all generated
automatically on startup:

    git clone https://github.com/SoniaDL90/Medical_records.git
    cd Medical_records
    docker compose up -d --build

Then open http://localhost:8000/login in your browser.

## ☁️ Deployment

The app is deployed as a Docker web service on **Render**, with a
**Neon** serverless PostgreSQL database (Render's own free Postgres tier
expires after 30 days, so the database is hosted separately on Neon's
permanent free tier instead). All secrets (`APP_SECRET`, `JWT_PASSPHRASE`,
`DATABASE_URL`, etc.) are configured as environment variables and are never
committed to the repository. On every deploy, the container's entrypoint
runs pending migrations and seeds demo data before starting the app.

## 👤 Test Users

| Email | Password | Role | Permissions |
|-------|-----------|-----|---------|
| admin@hospital.com | Password123! | ROLE_ADMIN | Read, edit and delete all records |
| doctor@hospital.com | Password123! | ROLE_DOCTOR | Read and edit their own patients |
| nurse@hospital.com | Password123! | ROLE_NURSE | Read all records, limited editing |
| reception@hospital.com | Password123! | ROLE_RECEPTIONIST | Basic patient data only |

## 📡 REST API

Get a token:

    curl -X POST http://127.0.0.1:8000/api/login -H "Content-Type: application/json" -d '{"username":"admin@hospital.com","password":"Password123!"}'

Use the token:

    curl http://127.0.0.1:8000/api/medical-records/ -H "Authorization: Bearer <token>"

Main URLs:
- Web login: http://127.0.0.1:8000/login
- Admin panel: http://127.0.0.1:8000/admin
- Audit logs: http://127.0.0.1:8000/admin/logs

## 📝 What I Learned

Building this project helped me understand how to design layered authorization
(RBAC combined with Symfony Voters for per-record checks), implement stateless
JWT authentication for an API, and add defensive measures against brute-force
attacks — rate limiting, account lockout and audit logging.

Deploying it to production taught me just as much: keeping dev and prod
environments consistent (migrating the whole stack from MySQL to PostgreSQL),
handling database-specific quirks like Doctrine ORM quoting reserved SQL
keywords (`user`), separating build-time from runtime configuration in Docker
so secrets are never baked into an image, restricting Symfony bundles like
DoctrineFixturesBundle to run in the right environments, and managing secrets
purely through environment variables instead of committed `.env` files.
EOF
git add README.md
git commit -m "docs: update README for PostgreSQL/Neon and live Render deployment"
git push
