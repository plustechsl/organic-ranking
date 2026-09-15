# Agent guide — Winter CMS core

Winter core sits on top of [Winter Storm](https://github.com/wintercms/storm) (installed at `vendor/winter/storm/`), which sits on top of Laravel, which sits on top of Symfony components. The helper you want almost certainly already exists at one of those layers. **Search Storm → Laravel → Symfony before writing any "small utility"**, often with safer edge-case handling than a fresh implementation would have.

## Where to look (in this order)

1. **Storm itself** — each `vendor/winter/storm/src/<Module>/README.md` catalogues that module's public API:
   - `Filesystem/` — `PathResolver` (`resolve`, `within`, `join`, `standardize`), `Filesystem` (extends Illuminate's; adds `isAbsolutePath`, `symbolizePath`, `existsInsensitive`, `chmodRecursive`)
   - `Support/` — strings, arrays, class loading
   - `Network/`, `Html/`, `Parse/`, `Database/`, `Halcyon/`, `Auth/`, etc.
   - Path helpers (always loaded): `themes_path()`, `plugins_path()`, `media_path()`, `uploads_path()`, `temp_path()` — use these instead of `base_path('themes')` etc.

2. **Laravel (Illuminate)** — everything Laravel ships is available:
   - `Illuminate\Support\Str` — `Str::startsWith/endsWith/contains/before/after/between/slug/camel/snake/kebab/studly/random/uuid/limit/mask/finish/start/of/headline/title`. Use instead of regex one-liners.
   - `Illuminate\Support\Arr` — `Arr::get/set/has/forget/only/except/dot/undot/flatten/pluck/wrap/first/last/where`. Use instead of nested foreach.
   - `Illuminate\Support\Collection` (via `collect()`) — chainable map/filter/reduce.
   - `Illuminate\Filesystem\Filesystem` — `deleteDirectory()`, `cleanDirectory()`, `copyDirectory()`, `moveDirectory()`, `allFiles()`, `glob()`, `isDirectory()`, `prepend()`, `append()`, `replace()`, `hash()`.
   - Global helpers: `data_get/set/fill`, `value`, `tap`, `optional`, `transform`, `head`, `last`, `class_basename`, `now`, `today`, `e`, `__`/`trans`, `cache`, `config`, `env`, `app`, `resolve`, `route`, `url`, `report`, `rescue`, `retry`, `throw_if`/`unless`, `abort`/`abort_if`/`unless`.
   - Facades: `Cache`, `Config`, `DB`, `Event`, `File`, `Hash`, `Http`, `Lang`, `Log`, `Mail`, `Queue`, `Redis`, `Route`, `Schema`, `Session`, `Storage`, `URL`, `Validator`, `View`.

3. **Symfony components** at `vendor/symfony/`: `console`, `css-selector`, `error-handler`, `event-dispatcher`, `finder`, `http-foundation`, `http-kernel`, `mailer`, `mime`, `process`, `routing`, `string`, `translation`, `uid`, `var-dumper`, `yaml`. Most commonly reached for:
   - `Symfony\Component\Finder\Finder` — `Finder::create()->files()->name('*.less')->in($dir)` replaces RecursiveIteratorIterator chains.
   - `Symfony\Component\Filesystem\Filesystem` — `dumpFile()` (atomic write), `mirror()`, `mkdir()` (idempotent), `remove()`, `symlink()`.
   - `Symfony\Component\Process\Process` — safe external command execution instead of `exec()`/`shell_exec()`.
   - `Symfony\Component\Yaml\Yaml` — strict YAML.
   - `Symfony\Component\String\` — Unicode-aware strings.
   - `Symfony\Component\Uid\Uuid`/`Ulid` — UUID/ULID generation.

`grep -rl 'function <thing>' vendor/winter/storm/src/ vendor/laravel/framework/src/ vendor/symfony/` is a 10-second check.

## Concrete substitutions worth memorising

Paths and filesystem:

| If you reach for… | Use this instead |
|---|---|
| `realpath()` + null-check + slash-trim | `\Winter\Storm\Filesystem\PathResolver::resolve()` |
| `str_starts_with($path, $root)` to gate file access | `PathResolver::within($path, $root)` — separator-boundary safe |
| Manual `base_path('themes')` / `base_path('plugins')` | `themes_path()` / `plugins_path()` (Storm's autoloaded helpers) |
| Recursive `rmrf` in tests | `\File::deleteDirectory($path)` (Laravel facade) |
| Detect absolute path | `(new \Winter\Storm\Filesystem\Filesystem())->isAbsolutePath($path)` |
| Custom path-symbol resolution (`~/...`) | `(new \Winter\Storm\Filesystem\Filesystem())->symbolizePath($path)` |
| `str_replace('\\', '/', $path)` (cross-platform comparison) | `(new \Winter\Storm\Filesystem\Filesystem())->normalizePath($path)` |
| `str_replace('/', DIRECTORY_SEPARATOR, $path)` (handing to OS API) | `\Winter\Storm\Filesystem\PathResolver::standardize($path)` |
| Atomic file write (avoid partial-write races) | `(new \Symfony\Component\Filesystem\Filesystem())->dumpFile($path, $contents)` |
| Find files matching a pattern | `\Symfony\Component\Finder\Finder::create()->files()->name('*.ext')->in($dir)` |

Strings and arrays:

| If you reach for… | Use this instead |
|---|---|
| `preg_match('/^prefix/', $s)` | `Str::startsWith($s, 'prefix')` (accepts array of prefixes) |
| Manual `strpos !== false` | `Str::contains($s, $needle)` |
| `strtolower`-then-replace slug generation | `Str::slug($s)` |
| Random hex/string for tmp paths, tokens | `Str::random()` / `Str::uuid()` |
| Deep array key access with null safety | `data_get($array, 'a.b.c', $default)` |
| Pulling subset of array keys | `Arr::only($array, [...])` / `Arr::except($array, [...])` |
| Chained map / filter / reduce on array | `collect($array)->filter(...)->map(...)->values()->all()` |

Other:

| If you reach for… | Use this instead |
|---|---|
| `exec()` / `shell_exec()` / backticks | `(new \Symfony\Component\Process\Process([$cmd, ...$args]))->mustRun()` |
| Manual YAML parsing | `\Symfony\Component\Yaml\Yaml::parse()` |
| JSON parsing without strict error handling | `json_decode($s, true, 512, JSON_THROW_ON_ERROR)` |
| UUID generation by `random_bytes` + hex shuffle | `Str::uuid()` or `\Symfony\Component\Uid\Uuid::v7()` |
| HTTP request to external service | Laravel's `\Http::get(...)` facade |

## Layering boundaries

Storm depends on Laravel + Symfony pieces. It must **not** depend on Winter modules. Conversely, Winter core modules are free to use Storm. So:

- CMS / theme / plugin / system concerns live in `modules/` (backend, cms, system).
- Generic filesystem, path, parser, network primitives live in `vendor/winter/storm/`.
- When a Storm class needs a *policy* that's CMS-specific (e.g. "which directories count as theme asset roots"), expose a public setter on the Storm class and have the module-level caller supply the policy. Don't reach into module-level constants from Storm.

## Autoloading & file placement

Modules and plugins are autoloaded by `Winter\Storm\Support\ClassLoader` (see `ClassLoader::load()`), **not** plain PSR-4. Its convention: the namespace's **directory segments are lower-cased** to form the path, while the class file keeps its proper PascalCase name. So `System\Twig\SecurityPolicy\SafeCollection` resolves to `modules/system/twig/securitypolicy/SafeCollection.php` — note the lowercase `securitypolicy/` directory.

- **New sub-namespace directories must be lowercase on disk**, even though the namespace segment stays PascalCase: `System\Twig\Node` → `modules/system/twig/node/`, not `Node/`. The file name keeps its PascalCase and must match the class name exactly.
- This only bites on case-sensitive filesystems: a capitalized directory works on macOS/Windows (case-insensitive) and passes local tests, then fails Linux CI with `Class "…" not found`. **If Windows CI is green but every Ubuntu job fails to find a class, suspect a directory-case mismatch.**

## Tests

- Backend/CMS/system tests usually extend `System\Tests\Bootstrap\PluginTestCase` (boots Laravel, plugins, auth) or `System\Tests\Bootstrap\TestCase` (boots the framework but not plugins).
- Fixtures that flow through `Assetic\Asset\FileAsset` (e.g. `CombineAssets::combineToFile()`) must live under `base_path()`. `sys_get_temp_dir()` will fail with "source is not in the root directory". Use `base_path('storage/framework/cache/<unique>')` for temp dirs and clean up with `\File::deleteDirectory()` in `tearDown()`.
- A failing test that doesn't reproduce on a fresh clone is almost always a stale local `vendor/`. Run `composer update` in Storm (`~/Repositories/WinterCMS/Core/storm` or wherever you check it out) before claiming "environment issue".

## Working across Winter core + Storm

When a change touches both, work in the actual local checkouts (e.g. `~/Repositories/WinterCMS/Core/storm` and `~/Repositories/WinterCMS/Core/winter`) on parallel branches, and symlink `vendor/winter/storm` to the local Storm checkout so changes are visible in the live install. Avoid `/tmp` worktrees — the user can't test what they can't see.

Open both PRs concurrently; the maintainer handles merge order and Storm release tagging. The Winter core PR's `composer.json` constraint bump waits for the Storm tag.

---

# WSL (Windows Subsystem for Linux) Development Setup

## Quick Start on WSL

```bash
# 1. Clone or navigate to project directory
cd /path/to/organic_ranking

# 2. Ensure .env exists and is configured for Docker
# If missing, copy from .env.example or run: php artisan winter:env

# 3. Start Docker containers (NOT in /mnt/c — use WSL native filesystem)
docker-compose up -d

# 4. Install dependencies and set up database
docker exec organic_ranking_app composer install
docker exec organic_ranking_app php artisan winter:up

# 5. Access the app at http://localhost:8081
```

## Common WSL Issues & Fixes

### 🔴 Line Ending Problems (CRLF ↔ LF)

**Symptom**: `#!/bin/bash` scripts fail with "bad interpreter"; composer/vendor/bin/* become unexecutable.

**Fix**: Configure Git early:
```bash
git config --global core.autocrlf input
git config --global core.safecrlf warn
```

Then regenerate vendor scripts:
```bash
docker exec organic_ranking_app composer install --no-cache
```

**For the project repo**: Ensure `.gitattributes` contains:
```
* text=auto
*.php text eol=lf
*.sh text eol=lf
Dockerfile text eol=lf
docker-compose.yml text eol=lf
```

### 🔴 File Permissions & Docker Volume Mounts

**Symptom**: Permission denied writing to `storage/logs`, `storage/temp`, or `themes/` from inside container.

**Cause**: WSL-to-Windows volume mounts strip permission metadata. Fix in docker-compose.yml:
```yaml
services:
  app:
    # ... existing config ...
    volumes:
      - .:/var/www/html
    user: "33:33"  # www-data:www-data UID:GID inside container
    environment:
      # Ensure Laravel runs with correct permissions
      - APP_ENV=local
      - APP_DEBUG=true
```

Or fix permissions on first run:
```bash
docker exec organic_ranking_app chmod -R 775 storage/ bootstrap/cache/ themes/
docker exec organic_ranking_app chown -R 33:33 storage/ bootstrap/cache/
```

### 🔴 Slow File I/O on /mnt/c

**Symptom**: `docker-compose up`, migrations, or asset compilation are glacially slow.

**Cause**: Cross-filesystem performance (Windows → WSL → Docker volumes is two hops).

**Solutions (in order of preference)**:
1. **Store project in native WSL filesystem** — Clone to `~/projects/organic_ranking` (not `/mnt/c/Users/…`):
   ```bash
   # In WSL terminal:
   cd ~
   git clone <repo> organic_ranking
   cd organic_ranking
   docker-compose up -d
   ```
   Performance improves 10–50×.

2. **Enable WSL 2 & native containers** — Requires Windows 11 or Windows 10 21H2+:
   ```powershell
   # In PowerShell (admin):
   wsl --install
   wsl --set-default-version 2
   ```
   Then docker-compose runs in WSL's native kernel, not Hyper-V.

3. **If stuck on `/mnt/c`** — Add caching flags in docker-compose.yml:
   ```yaml
   volumes:
     - .:/var/www/html:cached  # Delays host-to-container sync
   ```
   ⚠️ Only safe for read-mostly directories; avoid for logs/uploads.

### 🔴 Database Connection Timeout

**Symptom**: `SQLSTATE[HY000]: General error: 1030 Got error 28 from storage engine` or connection refused.

**Cause**: MariaDB container can't allocate space or host networking conflict.

**Fix**:
```bash
# Check if db service is running
docker ps | grep db

# Inspect logs
docker logs organic_ranking_db

# If disk space issue:
docker system prune -a  # Clean up dangling volumes
docker volume ls
docker volume rm organic_ranking_db_data
docker-compose down -v && docker-compose up -d
```

### 🔴 PHP Composer Out of Memory

**Symptom**: `Allowed memory size of X bytes exhausted` during `composer install`.

**Fix** (in Dockerfile):
```dockerfile
ENV PHP_MEMORY_LIMIT=2G
RUN docker-php-ini-set memory_limit $PHP_MEMORY_LIMIT
```

Or override at runtime:
```bash
docker exec -e PHP_MEMORY_LIMIT=2G organic_ranking_app composer install
```

## WSL Terminal vs Code Terminal

- **Use WSL terminal for Docker commands** (consistency with Dockerfile context).
- VS Code's integrated terminal on WSL works fine if default shell is `/bin/bash`.
- Avoid PowerShell or cmd.exe for `docker-compose` — use WSL bash.

## Development Workflow on WSL

```bash
# Edit files in VS Code (or any editor)
# Terminal stays in WSL:

# Watch for asset changes
docker exec -it organic_ranking_app php artisan watch

# Tail logs from container
docker logs -f organic_ranking_app

# Access Laravel tinker (REPL)
docker exec -it organic_ranking_app php artisan tinker

# Run tests inside container
docker exec organic_ranking_app composer test

# SSH-like access to container
docker exec -it organic_ranking_app /bin/bash
```

## Recommended WSL Extensions for VS Code

- **Remote - WSL** (`ms-vscode-remote.remote-wsl`) — Open entire workspace in WSL, run integrated terminal there.
- **Docker** (`ms-azuretools.vscode-docker`) — Manage containers from sidebar.
- **Dev Containers** (`ms-vscode-remote.remote-containers`) — Open workspace *inside* container if desired.

**Quickstart**: Install "Remote - WSL", then in VS Code:
1. Press `Ctrl+Shift+P` → "Remote-WSL: Reopen in WSL"
2. Integrated terminal automatically becomes WSL bash
3. Docker commands and file access are native WSL speed

## Git Workflow on WSL

```bash
# Clone into WSL filesystem (~/organic_ranking, not /mnt/c)
git clone <repo> ~/organic_ranking
cd ~/organic_ranking

# Ensure line-ending config (one-time)
git config --global core.autocrlf input

# Pull, commit, push normally
git pull origin develop
git checkout -b feature/my-feature
git push origin feature/my-feature
```

Always commit from WSL terminal, not Windows PowerShell, to avoid mixed line endings.
