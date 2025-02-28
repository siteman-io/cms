# SITEMAN CMS DEVELOPMENT GUIDE

## Build/Test/Lint Commands
- **Install/Setup**: `composer install && composer prepare`
- **Build**: `composer build` 
- **Serve dev environment**: `composer serve`
- **Run all tests**: `composer test`
- **Run single test**: `vendor/bin/pest tests/Path/To/Test.php --filter=testMethodName`
- **Code formatting**: `composer lint`
- **Static analysis**: `composer lint`
- **Lint code**: `composer lint`

## Code Style Guidelines
- Use strict typing with `<?php declare(strict_types=1);` at the top of files
- Follow Laravel naming conventions (PascalCase for classes, camelCase for methods)
- Use full type declarations in method signatures and return types
- Prefer dependency injection over Facades when possible (except in views)
- Namespaces follow PSR-4: src/ → Siteman\Cms\
- Place interfaces with implementations (not in separate directory)
- Use return type declarations (`public function example(): Type`)
- Throw exceptions with descriptive messages for error handling
- Follow Laravel Pint style preset with custom rules in pint.json
- PHPStan level 4 for static analysis
