# Laravel Zabbix by rConfig

[![Tests](https://github.com/rConfig/laravel-zabbix/actions/workflows/tests.yml/badge.svg)](https://github.com/rConfig/laravel-zabbix/actions/workflows/tests.yml)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue?logo=php)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-10.x%20%7C%2011.x%20%7C%2012.x-red?logo=laravel)](https://laravel.com)

A Laravel-first, developer-friendly client for the [Zabbix JSON-RPC API](https://www.zabbix.com/documentation/current/en/manual/api) that makes monitoring integrations a breeze, developed by rConfig.

## ✨ Features

- 🎯 **Intuitive fluent API** with chainable query methods ending in `.get()`
- 🚀 **First-class developer experience** with expressive, readable syntax  
- ⚡ **No double method calls** - just chain directly: `hosts()->limit(5)->get()`
- 🧪 **Local testing without a live Zabbix server** via HTTP fakes
- 🔧 **Optional real server integration** for end-to-end testing
- 📦 **Laravel package best practices** with auto-discovery
- 🔌 **Extensible design** for additional Zabbix endpoints

---

## 📦 Installation

Install via Composer:

```bash
composer require rconfig/laravel-zabbix
```

The service provider will be auto-discovered. Optionally publish the config:

```bash
php artisan vendor:publish --tag=zabbix-config
```

---

## ⚙️ Configuration

### Environment Variables

```env
# Server connection
ZABBIX_BASE_URL=https://zabbix.example.com
ZABBIX_ENDPOINT=/api_jsonrpc.php

# Authentication (use token when possible)
ZABBIX_API_TOKEN=your_api_token_here

# Legacy fallback
ZABBIX_USERNAME=Admin
ZABBIX_PASSWORD=zabbix

# HTTP client settings
ZABBIX_TIMEOUT=15
ZABBIX_RETRIES=2

# Testing mode
ZABBIX_FAKE_BY_DEFAULT=true
```

### Config File

The config file at `config/zabbix.php` provides sensible defaults:

```php
return [
    'base_url' => env('ZABBIX_BASE_URL', 'http://localhost'),
    'endpoint' => env('ZABBIX_ENDPOINT', '/api_jsonrpc.php'),
    
    // Prefer API token (Zabbix 5.4+)
    'token' => env('ZABBIX_API_TOKEN'),
    
    // Legacy authentication
    'username' => env('ZABBIX_USERNAME'),
    'password' => env('ZABBIX_PASSWORD'),
    
    // HTTP settings
    'timeout' => (int) env('ZABBIX_TIMEOUT', 15),
    'retries' => (int) env('ZABBIX_RETRIES', 2),
    'retry_sleep_ms' => (int) env('ZABBIX_RETRY_SLEEP_MS', 250),
    
    // Testing
    'fake_by_default' => env('ZABBIX_FAKE_BY_DEFAULT', true),
];
```

---

## 🚀 Quick Start

```php
use Rconfig\Zabbix\Exceptions\UnsupportedOperationException;

// Connect with credentials
$zabbix = ZabbixApi::login('https://zabbix.example.com', 'Admin', 'zabbix');

// Or use API token (recommended)
$zabbix = ZabbixApi::login('https://zabbix.example.com', null, null, [
    'token' => 'your_api_token_here'
]);

// Get API version
$version = $zabbix->apiVersion(); // "7.0.0"

// List all hosts
$hosts = $zabbix->hosts()->all();

// Find active Linux servers with fluent syntax
$activeLinux = $zabbix->hosts()
    ->where(['status' => 0, 'name' => 'linux-server'])
    ->get();

// Create a new host
$newHost = $zabbix->hosts()->create([
    'host' => 'app-01',
    'interfaces' => [[
        'type' => 1, 'main' => 1, 'useip' => 1,
        'ip' => '10.0.0.50', 'port' => '10050'
    ]],
    'groups' => [['groupid' => '2']],
]);
```

---

## 🔍 Fluent Queries

Build complex queries with an intuitive fluent interface:

### Hosts

```php
$zabbix = ZabbixApi::login($url, null, null, ['token' => $token]);

// Simple fluent API - much cleaner!
$hosts = $zabbix->hosts()
    ->limit(50)
    ->select(['hostid', 'host', 'status'])
    ->withInterfaces()
    ->withGroups()
    ->where(['status' => 0])
    ->sort('host')
    ->get();

// Get count of hosts
$totalHosts = $zabbix->hosts()->count();
// or
$totalHosts = $zabbix->hosts()->countOnly()->get();
```

### Host Groups

```php
$groups = $zabbix->hostGroups()
    ->limit(20)
    ->select(['groupid', 'name'])
    ->get();
```

### Advanced Query Builder (Alternative)

For complex queries, you can still use the explicit query builder:

```php
$hosts = $zabbix->hosts()->get(
    $zabbix->hosts()->query()
        ->select(['hostid', 'host', 'status'])
        ->withInterfaces()
        ->withGroups()
        ->where(['status' => 0])
        ->limit(50)
        ->sort('host')
);
```
 
---

## 📊 Version Matrix & Compatibility

| Zabbix Version | Tested Minor | Notes                                                                 |
|----------------|-------------|-----------------------------------------------------------------------|
| 6.0 LTS        | 6.0.29       | Fully supported. `apiinfo.version` requires auth in some setups.      |
| 7.x            | 7.0.2        | Fully supported. Some methods differ slightly in param requirements. |

We recommend running your integration tests against **both LTS and current major** to catch method or parameter changes early.


## 🚫 Unsupported Operations

Some Zabbix API resources do not implement all CRUD operations.  
For example, certain system resources cannot be created or deleted.

In these cases:

- Methods like `create()`, `update()`, or `delete()` will throw an `UnsupportedOperationException`
- This makes it clear the operation isn’t available for that resource, rather than failing silently.

Example:

```php
use Rconfig\Zabbix\Exceptions\ZabbixException;

try {
    Zabbix::apiinfo()->delete(['id' => 1]);
} catch (UnsupportedOperationException $e) {
    echo $e->getMessage(); // "Delete is not supported for Apiinfo resource"
}
 

---

## 🛡️ Lightweight Parameter Validation

An **optional** guard can warn or throw if obviously invalid parameters are passed.
This is not a full schema validator — just common-sense checks to help catch mistakes early.

* Wrong data types for known fields
* Mutually exclusive params used together
* Missing required params for certain calls

Enable warnings in `.env`:

```env
ZABBIX_PARAM_VALIDATION=true
```

Example:

```php
// Will trigger a warning about 'limit' being a string instead of an integer
Zabbix::hosts()->get(['limit' => 'fifty']);
```

---
  

## 🔐 Authentication

### API Token (Recommended)

For Zabbix 5.4+ with API tokens:

```php
$zabbix = ZabbixApi::login('https://zabbix.example.com', null, null, [
    'token' => 'your_api_token_here'
]);
```

### Username/Password

Legacy authentication:

```php
$zabbix = ZabbixApi::login('https://zabbix.example.com', 'Admin', 'zabbix');
```

### Advanced Options

SSL and timeout configuration:

```php
$zabbix = ZabbixApi::login('https://zabbix.example.com', 'Admin', 'zabbix', [
    // SSL options
    'sslVerifyPeer' => false,
    'sslVerifyHost' => false,
    // Timeout settings
    'timeout' => 30,
    'connectTimeout' => 10,
    // Debug and compression
    'debug' => true,
    'useGzip' => true
]);
```

### Environment Configuration

You can still use environment variables for default values:

```env
ZABBIX_BASE_URL=https://zabbix.example.com
ZABBIX_API_TOKEN=your_api_token_here
ZABBIX_USERNAME=Admin
ZABBIX_PASSWORD=zabbix
```

Then use in your code:

```php
$zabbix = ZabbixApi::login([
    'url' => config('zabbix.base_url'),
    'token' => config('zabbix.token'),
]);
```

---

## 🧪 Testing

### Fake Mode (Default)

Perfect for local development and unit testing - no real server needed:

```php
// All calls return realistic mock data
$zabbix = ZabbixApi::login('fake://localhost');
$hosts = $zabbix->hosts()->all(); // Returns mock data
```

### Integration Testing

Test against a real Zabbix server:

```php
$zabbix = ZabbixApi::login($url, null, null, ['token' => $token]);
```

### Example Test

```php
it('fetches hosts via fluent query', function () {
    $zabbix = ZabbixApi::login('fake://localhost');
    $hosts = $zabbix->hosts()->all();
    expect($hosts)->toBeArray()->not->toBeEmpty();
});
```

## ⚠️ Error Handling

The package provides specific exceptions for different error types:

```php
use Rconfig\Zabbix\Exceptions\ZabbixException;
use Rconfig\Zabbix\Exceptions\ZabbixHttpException;
use Rconfig\Zabbix\Facades\ZabbixApi;

try {
    $zabbix = ZabbixApi::login($url, null, null, ['token' => $token]);
    $hosts = $zabbix->hosts()->all();
} catch (ZabbixException $e) {
    // API-level errors (invalid params, auth issues, etc.)
    logger()->error('Zabbix API error: ' . $e->getMessage());
} catch (ZabbixHttpException $e) {
    // HTTP transport errors (timeouts, connectivity, etc.)
    logger()->error('Zabbix HTTP error: ' . $e->getMessage());
}
```
## 🛠️ Local Development

### Package Development Setup

For developing this package alongside a Laravel application:

```
/code
  ├─ myapp/                   # Your Laravel app
  └─ rconfig-laravel-zabbix/  # This package
```

#### 1. Link the Package

In your Laravel app's `composer.json`:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../rconfig-laravel-zabbix",
      "options": { "symlink": true }
    }
  ]
}
```

#### 2. Install the Package

```bash
cd myapp
composer require rconfig/laravel-zabbix:"dev-main"
php artisan vendor:publish --tag=zabbix-config
```

#### 3. Test in Tinker

```bash
php artisan tinker
>>> use Rconfig\Zabbix\Facades\ZabbixApi;
>>> $zabbix = ZabbixApi::login('fake://localhost');
>>> $zabbix->apiVersion()      // "7.0.0" (fake)
>>> $zabbix->hosts()->all()    // Mock data
```

#### 4. Quick Test Route

```php
// routes/web.php
Route::get('/zabbix-test', function () {
    $zabbix = ZabbixApi::login(['url' => 'fake://localhost']);
    return [
        'version' => $zabbix->apiVersion(),
        'hosts' => $zabbix->hosts()->all(5),
    ];
});
```

Here’s a clean way to add it to your main README so contributors (or future you) know exactly how to cut a release.
I’d suggest adding a **"Releases"** or **"Publishing"** section at the bottom:

## 📦 Publishing a New Release

We use [Semantic Versioning](https://semver.org/) for all releases.

### 1. Update the Changelog

* Edit `CHANGELOG.md`
* Add a new section at the top for the version you’re releasing:

```md
## [0.1.0] - 2025-08-09
### Added
- Initial public release of the Laravel Zabbix client.
```

* Commit the changes:

```bash
git add CHANGELOG.md
git commit -m "docs: update changelog for v0.1.0"
git push origin main
```

### 2. Tag the Release

Create an annotated Git tag and push it:

```bash
git tag -a v0.1.0 -m "v0.1.0 initial public release"
git push origin v0.1.0
```

### 3. Create the GitHub Release

1. Go to the [GitHub Releases](../../releases) page.
2. Click **Draft a new release**.
3. Select the tag you just pushed (`v0.1.0`).
4. Set the title to match the tag (e.g. `v0.1.0`).
5. Paste the changelog notes for that version.
6. Publish the release.

> 💡 **Tip:** If the Packagist webhook is active, the new version will appear there automatically within minutes.
 
---

## 🗺️ Roadmap

Planned features for future releases:

- Pagination Support
- More fluent convenience methods

## 🤝 Contributing

We welcome contributions! Here's how to get started:

### Development Setup

1. **Fork and clone** the repository
2. **Install dependencies:**
   ```bash
   composer install
   ```
3. **Run the test suite:**
   ```bash
   vendor/bin/pest
   ```
4. **Make your changes** in a feature branch
5. **Submit a pull request** with a clear description

### Coding Standards

- Follow **PSR-12** for PHP code style
- Use **Pint** for automatic formatting. Push to main will fail if there are formatting issues.
  ```bash
  vendor/bin/pint
  ```
- Keep methods **small and expressive**
- Add **tests** for new features and bug fixes
- Update **documentation** when applicable
- Use **meaningful commit messages**

### Running Tests

```bash
# Run all tests with fakes
vendor/bin/pest

# Run integration tests (requires real Zabbix server)
RUN_ZABBIX_INTEGRATION=1 \
ZABBIX_BASE_URL=https://your-zabbix.com \
ZABBIX_API_TOKEN=your_token \
vendor/bin/pest
```

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).


## Code of Conduct

While we aren't directly affiliated with Zabbix or Laravel, but we follow their respective Codes of Conduct. We expect you to abide by these guidelines as well.

## Authors

This library is created by rConfig Developers.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.


<div align="center">
<strong>Built with ❤️ by rConfig for the Zabbix and Laravel communities</strong>
</div>
