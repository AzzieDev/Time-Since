# Time Since

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)
![Tests](https://img.shields.io/github/actions/workflow/status/AzzieDev/Time-Since/tests.yml?branch=master&style=flat-square&label=tests)


**Time Since** is a Laravel-based application designed to track the time elapsed since specific events occurred. Think of it as a digital "Days since last injury" sign, but capable of tracking multiple custom events simultaneously.

Whether you're tracking habits, incident-free days, or the last time you performed a specific maintenance task, Time Since provides a simple interface and API to keep everything logged.

## Features

- **Multi-Event Tracking:** Track an unlimited number of custom events or tasks.
- **Flexible Logging:** Log an event as happening "Now" with a single click, or manually enter custom dates and times for past events.
- **Streak Tracking:**
  - **Longest Streak:** Automatically calculates and stores the longest duration between occurrences.
  - **Previous Streak Reversion:** Keeps track of the most recent streak range, allowing you to easily revert if an event was logged accidentally.
- **API First:** Built with integration in mind. easily retrieve or manipulate data via the built-in API.
- **Integration Ready:** Designed to be easily connected with external dashboards and smart home systems like Home Assistant and MagicMirror².

## Planned Integrations

- **Home Assistant:** Create sensors to display "time since" values directly on your smart home dashboards.
- **MagicMirror²:** A custom module to show your tracked events on your smart mirror.

## Data Model Concept

Instead of storing a bloated historical log of every occurrence, the application focuses on the metrics that matter. The core data model includes:

- `last_time`: The timestamp of the most recent occurrence.
- `longest_streak_range`: The date range representing the longest period between occurrences. Updated automatically when a new record is reached.
- `most_recent_streak_range`: The date range of the streak that just ended. This provides a safety net, allowing users to "undo" an accidental trigger and revert to the previous state.

## Getting Started

## API & Documentation

This application integrates **L5-Swagger** to auto-generate OpenAPI specifications and interactive documentation.
Once the application is running, simply navigate to `http://localhost:8000/api/documentation` to test all available tracker endpoints directly in your browser without needing Postman or cURL.

## Deployment Strategy

Time Since can be run quickly via Docker (recommended) or deployed manually to a bare-metal server using standard PHP dependencies.

### Option 1: Docker Compose (Recommended)
This repository includes a production-ready `Dockerfile` (utilizing `serversideup/php:8.3-fpm-nginx`) and a `docker-compose.yml` that seamlessly marries the application to an isolated MySQL database container. 

1. Clone the repository: `git clone https://github.com/AzzieDev/Time-Since`
2. Navigate to the project directory: `cd Time-Since`
3. Generate a secure application key out-of-band: `php artisan key:generate --show` (or generate a base64 string manually).
4. Edit the `docker-compose.yml` file and insert the generated key into the `APP_KEY=` environment variable.
5. Spin up the cluster:
   ```bash
   docker compose up -d
   ```
The container will automatically handle running composer dependencies, database connections, and migrations exactly once on startup. The app (and Swagger UI) will be immediately available on **Port 8000**.

### Option 2: Bare Metal / Manual Execution
If deploying straight to a web server running NGINX, Apache, or Laragon, you can spin it up using native path commands:

1. Clone the repository: `git clone https://github.com/AzzieDev/Time-Since`
2. Enter the directory: `cd Time-Since`
3. Copy the environment file and configure your database variables:
   ```bash
   cp .env.example .env
   # Edit .env with your MySQL/SQLite details
   ```
4. Install PHP dependencies and generate your system key:
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan key:generate
   ```
5. Migrate the database schema:
   ```bash
   php artisan migrate --force
   ```
6. Spin up the application (or link the path internally to NGINX/Apache):
   ```bash
   php artisan serve
   ```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
