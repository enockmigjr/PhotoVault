# Docker development environment

This environment is intended for local PhotoVault validation with Nginx, WordPress PHP-FPM, MariaDB, Mailpit, WP-CLI and a cron runner.

## Services

- `nginx`: serves WordPress on `http://localhost:8080`.
- `wordpress`: PHP-FPM container with WP-CLI installed.
- `db`: MariaDB with a persistent volume.
- `mailpit`: development SMTP UI on `http://localhost:8025`.
- `cron`: runs due WordPress cron events every minute through WP-CLI.

## Usage

```bash
cp .env.example .env
docker compose up --build
```

The Docker config mounts `docker/wp-config-docker.php` inside the container. It does not overwrite the host `wp-config.php`.

## Security notes

- `wp-content/photovault-private/` is explicitly denied in Nginx.
- `.env`, `.git` and common package metadata are denied by Nginx.
- The `.env.example` file contains placeholders only; do not commit a real `.env`.
- WP-Cron is disabled in the Docker WordPress config and delegated to the `cron` service.

## WP-CLI examples

```bash
docker compose run --rm wordpress wp plugin list --allow-root
docker compose run --rm wordpress wp photovault secure-originals --limit=100 --allow-root
```