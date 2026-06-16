# The project

</br>

# Development details

### Installation

Clone the repository.

```bash
git clone git@github.com:lfsc09/kdongs.git
cd kdongs
```

Install dependencies, creates .env file and generates APP_KEY.

```bash
composer clone-setup
```

Configure git hooks.

```bash
composer configure-dev-githooks
```

Start the development containers (Postgres, Redis, Mailpit).

```bash
docker compose -f docker/compose.local.yaml --env-file .env up -d
```

Run database migrations and seeders.

```bash
php artisan migrate --seed
```

### Maintainance

#### Useful commands

##### Bring down development containers.

```bash
docker compose -f docker/compose.local.yaml --env-file .env down -v
```

##### Check DB

```bash
# Monitor connections
php artisan db:monitor --databases=pgsql

# Show database tables and views with counts
php artisan db:show --counts --views
```

##### Run tests

```bash
# Run all tests
php artisan test

# Run specific test class
php artisan test --filter=TestClassName

# Run specific test method
php artisan test --filter=TestClassName::testMethodName
```

#### Refactoring

Run Rector to automatically refactor code.

```bash
composer rector
```
Or on specific file or directory.

```bash
composer rector path/to/file.php
```

