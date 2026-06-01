# Changelog

All notable changes to `rconfig/laravel-zabbix` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [1.1.0] - 2026-05-26

### Added
- Support for Laravel 13 (`illuminate/support` and `illuminate/http` `^13.0`).
- Support for Orchestra Testbench 11 and Pest 4 in dev dependencies.

## [1.0.1] - 2025-10-24

### Fixed
- Removed "testing" keyword from composer.json that was causing Composer to suggest --dev installation
- Package is now properly recognized as a production dependency

## [1.0.0] - 2025-10-24

### Added
- 🎯 **Intuitive fluent API** with chainable query methods ending in `.get()`
- 🚀 **Dramatically improved developer experience** - no more double method calls
- ⚡ **Direct method chaining**: `ZabbixApi::hosts()->limit(5)->withGroups()->get()`
- 🔢 **Count functionality**: `count()` method and `countOnly()->get()` support
- 🔐 **Enhanced authentication** with login-based approach and advanced options
- 🛡️ **SSL configuration support** with `sslVerifyPeer`, `sslVerifyHost` options
- ⏱️ **Timeout controls** with `timeout`, `connectTimeout`, and retry settings
- 🐛 **Debug mode** with detailed logging for troubleshooting
- 📦 **Compression support** with `useGzip` option
- 🧪 **Comprehensive testing** with 82 tests and 119 assertions
- 📚 **Complete documentation** with examples and best practices
- 🔌 **Full resource coverage** for all major Zabbix API endpoints

### Changed
- **BREAKING**: Main facade renamed from `Zabbix` to `ZabbixApi`
- **BREAKING**: Authentication now uses `ZabbixApi::login()` method instead of config-only
- **Improved**: Fluent API no longer requires double method calls
- **Enhanced**: Error handling with specific exception types
- **Modernized**: Package architecture with clean separation of concerns

### Technical Details
- Enhanced `ZabbixConnector` with comprehensive options support
- Improved `Hosts` and `HostGroups` resources with fluent method delegation
- Updated fake client to properly handle `countOutput` parameter
- SSL options integration with Laravel HTTP client
- Legacy options compatibility for existing codebases

### Migration Guide
**Before (v0.x):**
```php
$hosts = Zabbix::hosts()->get(
    Zabbix::hosts()->query()->limit(5)->withGroups()
);
```

**After (v1.0):**
```php
$zabbix = ZabbixApi::login('https://zabbix.example.com', 'user', 'pass');
$hosts = $zabbix->hosts()->limit(5)->withGroups()->get();
```

## [0.1.0] - 2025-08-09
### Added
- Initial public release of the Laravel Zabbix client.