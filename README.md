# Time Since

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)
![Coverage Badge](badges/coverage.svg)


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

*(Installation instructions to be added)*

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
