# Contributing on Windows Subsystem for Linux (WSL)

Quickstart checklist to avoid common WSL development gotchas with this Winter CMS Docker project.

## ✅ Initial Setup (One-Time)

- [ ] **Use WSL 2, not WSL 1**: WSL 1 has severe Docker performance penalties.
  ```powershell
  # Run in PowerShell (admin) on Windows:
  wsl --set-default-version 2
  ```

- [ ] **Clone into WSL filesystem** (not `/mnt/c`):
  ```bash
  cd ~
  git clone <repo> organic_ranking
  cd organic_ranking
  ```

- [ ] **Configure Git line endings**:
  ```bash
  git config --global core.autocrlf input
  git config --global core.safecrlf warn
  ```

- [ ] **Install Remote - WSL extension** in VS Code:
  - Search for `ms-vscode-remote.remote-wsl`
  - Press `Ctrl+Shift+P` → "Remote-WSL: Reopen in WSL"

- [ ] **Copy `.env` for Docker** (if missing):
  ```bash
  cp .env.example .env
  # Edit DB_HOST=db, DB_USERNAME=organic_user, DB_PASSWORD=organic_pass
  ```

## ✅ Every Time You Start Development

1. **Start Docker services**:
   ```bash
   docker-compose up -d
   ```

2. **Check if containers are running**:
   ```bash
   docker ps
   ```
   Should show `organic_ranking_app` (Apache/PHP) and `organic_ranking_db` (MariaDB).

3. **Fix database permissions** (first run only):
   ```bash
   docker exec organic_ranking_app composer install
   docker exec organic_ranking_app php artisan winter:up
   docker exec organic_ranking_app chmod -R 775 storage/ bootstrap/cache/
   docker exec organic_ranking_app chown -R 33:33 storage/
   ```

4. **Access the app**:
   - Backend: http://localhost:8081/backend
   - Admin user: `admin` (password generated during `winter:up`, check terminal output)

## ✅ Common Commands

| Task | Command |
|------|---------|
| **View app logs** | `docker logs -f organic_ranking_app` |
| **Run tests** | `docker exec organic_ranking_app composer test` |
| **Lint PHP** | `docker exec organic_ranking_app composer sniff` |
| **SSH to app container** | `docker exec -it organic_ranking_app /bin/bash` |
| **MySQL CLI** | `docker exec -it organic_ranking_db mysql -u organic_user -p organic_ranking` |
| **Restart all services** | `docker-compose restart` |
| **Stop all services** | `docker-compose down` |
| **Purge & rebuild** | `docker-compose down -v && docker-compose up -d` |

## ❌ Common Pitfalls

| Issue | Symptom | Fix |
|-------|---------|-----|
| **Bad line endings** | Composer/shell scripts fail with "bad interpreter" | `git config --global core.autocrlf input` then `composer install` |
| **Permission denied** on `storage/` | `docker exec organic_ranking_app chmod -R 775 storage/ bootstrap/cache/` |
| **Slow Docker** | Glacial build times; if on `/mnt/c`, clone to `~/organic_ranking` instead |
| **DB connection refused** | `docker logs organic_ranking_db` then `docker-compose down -v && docker-compose up -d` |
| **Composer out of memory** | `docker exec -e PHP_MEMORY_LIMIT=2G organic_ranking_app composer install` |

## 🔗 Full WSL Guide

For detailed troubleshooting, see [AGENTS.md](../AGENTS.md#wsl-windows-subsystem-for-linux-development-setup) — WSL Development Setup section.

## Need Help?

- Check Docker logs: `docker logs <container_name>`
- Rebuild from scratch: `docker-compose down -v && docker-compose up -d`
- Clear Composer cache: `docker exec organic_ranking_app composer clear-cache`
- Verify VS Code is in WSL mode: Bottom-left corner should show "WSL: <distro>"
