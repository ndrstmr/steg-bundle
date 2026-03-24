# Changelog

All notable changes to `ndrstmr/steg-bundle` are documented here.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).
This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- `StegBundle` — Symfony Bundle class
- `StegExtension` — DI registration of StegClient services per connection
- `Configuration` — TreeBuilder with DSN/base_url validation and timeout > 0 constraint
- `StegDataCollector` — Symfony Profiler data collector (request count, duration, tokens)
- `ProfilingClient` — Decorating client that records complete() calls to the DataCollector
- Profiler panel template (`templates/data_collector/steg.html.twig`)
- 27 unit tests (ConfigurationTest, StegExtensionTest, StegDataCollectorTest, ProfilingClientTest)
- GitHub Actions CI (PHP 8.2–8.4, PHPUnit, PHPStan Level 9, CS-Fixer)
