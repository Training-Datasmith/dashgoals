# Architecture: dashgoals

## Purpose

A PrestaShop native module providing the "Goals" dashboard widget — allows Back Office users to set monthly sales/visitor targets and displays a progress gauge comparing actual results against goals.

## Directory Structure

```
dashgoals.php                         — Module entry point: hook registration, goal data, widget rendering
controllers/admin/
  AdminDashgoalsController.php         — Admin controller: handles goal configuration form submission
views/
  templates/hook/                      — Smarty template for the dashboard goals widget
  js/                                  — Frontend JavaScript for gauge/chart rendering
translations/                          — Module translation strings
tests/phpstan/                         — PHPStan static analysis configuration
```

## Key Design Decisions

- **Per-month goals** — goal values are stored as PrestaShop configuration entries keyed by month/year, allowing different targets each month.
- **Admin controller** — `AdminDashgoalsController` extends PrestaShop's `AdminController` to provide the goal-setting form in the Back Office.
- **Progress calculation** — compares actual order values/visitor counts against stored goals and calculates percentage completion for display.

## Extension Points

- Override the Smarty template to add new KPI columns or change the visualisation style.
