# 🚀 cursor.style — Local Development Setup

Сursor & room-based chat experience powered by Docker & Laravel.

---

## 📦 Quick Start Guide

### 1. 🧱 Clone the repository

```bash
git clone https://github.com/wonchoe/cursor.style.git
cd cursor.style
```

---

### 2. 🖥 First-time setup (**Windows only**)

Run the initialization script as **Administrator**:

```bash
docker/init.bat
```

✅ This will:

- Install the Cloudflare Origin CA root certificate into Windows trusted root

> ⚠️ **Must be run as Administrator** — otherwise certificate installation will fail.

> ❗️ **Before starting the container**, make sure to stop or remove any other containers or services using port **443** (like Traefik, nginx-proxy, or local web servers), or `docker compose up` may fail due to port conflict.

---

### 3. 🛠 Build the containers

```bash
docker compose build --no-cache
```

---

### 4. 🚀 Start the services

```bash
docker compose up -d
```

> Use this same command every time you start the app.

---

### 5. ⏳ Wait for setup

Give it a few minutes to install dependencies, set permissions, and link storage.

You can follow logs with:

```bash
docker compose logs -f
```

---

### 6. 🌐 Access the app

Open in your browser:

```
https://dev.cursor.style
```

---

## 📌 Notes

- **Backend**: Laravel + PHP 8.3
- **Frontend**: Blade templates + JS effects
- **Services**:
  - `web`: Nginx + PHP-FPM + Laravel app
  - `mysql`: MySQL database (port 3306)
- **Ports**:
  - `443`: HTTPS for local app
  - `3306`: MySQL exposed for tools like DBeaver/Postico

---
