---
outline: deep
---

# Contributing

Thank you for considering contributing to Siteman CMS! This guide will help you get started with contributing code, documentation, bug reports, and feature requests.

## Code of Conduct

Please be respectful and constructive in all interactions. We strive to create a welcoming environment for all contributors.

## Ways to Contribute

### Reporting Bugs

Found a bug? Please [open an issue](https://github.com/siteman-io/cms/issues) with:

- **Clear title**: Describe the issue concisely
- **Steps to reproduce**: How to trigger the bug
- **Expected behavior**: What should happen
- **Actual behavior**: What actually happens
- **Environment details**: PHP version, Laravel version, OS
- **Screenshots/logs**: If applicable

### Suggesting Features

Have an idea? [Open a discussion](https://github.com/siteman-io/cms/discussions) or issue with:

- **Use case**: Why is this feature needed?
- **Proposed solution**: How should it work?
- **Alternatives considered**: Other approaches you've thought about
- **Examples**: Similar features in other CMS platforms

### Improving Documentation

Documentation improvements are always welcome:

- Fix typos or clarify explanations
- Add missing examples
- Improve code snippets
- Translate documentation (future)

Documentation is in the `/docs` directory and built with Vitepress.

### Contributing Code

See the development workflow below.

## Development Setup

### Prerequisites

- **PHP**: 8.3 or higher
- **Composer**: 2.x
- **Node.js**: 18.x or higher (for docs)
- **Git**: Latest version

### Fork and Clone

1. Fork the repository on GitHub
2. Clone your fork:
   ```bash
   git clone https://github.com/YOUR-USERNAME/cms.git
   cd cms
   ```

3. Add upstream remote:
   ```bash
   git remote add upstream https://github.com/siteman-io/cms.git
   ```

### Install Dependencies

```bash
# Install PHP dependencies
composer install

# Prepare testbench environment
composer prepare
```

### Running the Development Server

Siteman uses Orchestra Testbench for development:

```bash
composer serve
```

This starts a development server at `http://localhost:8000`.

### Running Tests

```bash
# Run all tests
composer test

# Run only Pest tests
vendor/bin/pest

# Run specific test
vendor/bin/pest tests/Feature/PageTest.php

# Run tests with coverage
composer test-coverage
```

### Code Quality

```bash
# Format code (Laravel Pint)
composer format

# Static analysis (PHPStan)
composer analyse

# Run all checks
composer all
```

## Development Workflow

### 1. Create a Branch

Create a descriptive branch name:

```bash
git checkout -b feature/add-video-block
git checkout -b fix/page-slug-validation
git checkout -b docs/improve-installation-guide
```

**Branch naming**:
- `feature/` - New features
- `fix/` - Bug fixes
- `docs/` - Documentation
- `refactor/` - Code refactoring
- `test/` - Test improvements

### 2. Make Changes

- Write clean, readable code
- Follow existing code style (enforced by Pint)
- Add/update tests for your changes
- Update documentation if needed

### 3. Commit Changes

Write clear, descriptive commit messages:

```bash
git add .
git commit -m "Add video block with URL validation"
```

**Commit message format**:
```
<type>: <description>

[optional body]

[optional footer]
```

**Types**:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation
- `style`: Formatting
- `refactor`: Code restructuring
- `test`: Tests
- `chore`: Maintenance

**Examples**:
```
feat: add video block support

Adds a new VideoBlock that supports YouTube and Vimeo embeds.
Includes URL validation and responsive iframe rendering.

Closes #123
```

```
fix: prevent circular references in page hierarchy

Validates parent_id before saving to prevent pages from becoming
their own ancestors.
```

### 4. Push and Create PR

```bash
git push origin feature/add-video-block
```

Then create a Pull Request on GitHub with:

- **Clear title**: Describe what the PR does
- **Description**: Explain the changes and why
- **Link issues**: Reference related issues (Closes #123)
- **Screenshots**: If UI changes
- **Checklist**: Confirm tests pass, docs updated, etc.

## Code Style

### PHP Code Style

Siteman follows Laravel conventions enforced by Laravel Pint:

```php
<?php declare(strict_types=1);

namespace App\Blocks;

use Siteman\Cms\Blocks\BaseBlock;

class VideoBlock extends BaseBlock
{
    public function getId(): string
    {
        return 'video';
    }

    public function getLabel(): string
    {
        return 'Video';
    }
}
```

**Key conventions**:
- Strict types declaration
- PSR-12 style
- Type hints for all parameters and return values
- Descriptive variable names
- No abbreviations (except common ones like `$id`)

### Documentation Style

- Use clear, concise language
- Provide code examples
- Include both simple and advanced use cases
- Link to related documentation
- Test all code examples

## Testing Requirements

All code contributions must include tests:

### Required Tests

- **Feature Tests**: Test the full feature workflow
- **Unit Tests**: Test individual classes/methods
- **Filament Tests**: Test admin panel functionality

### Test Coverage

- Aim for >80% coverage on new code
- Critical features should have >90% coverage
- Don't sacrifice test quality for coverage percentage

### Writing Good Tests

```php
// Good: Descriptive, focused test
it('creates page with computed slug from parent hierarchy', function () {
    $parent = Page::factory()->create(['slug' => '/blog']);
    $child = Page::factory()->create([
        'parent_id' => $parent->id,
        'slug' => '/my-post',
    ]);

    expect($child->computed_slug)->toBe('/blog/my-post');
});

// Bad: Vague, tests too much
it('works', function () {
    $page = Page::factory()->create();
    expect($page)->not->toBeNull();
    // ... 20 more assertions
});
```

## Documentation Guidelines

### When to Update Docs

Update documentation when:
- Adding new features
- Changing existing behavior
- Adding configuration options
- Updating dependencies with breaking changes

### Documentation Structure

```markdown
---
outline: deep
---

# Feature Name

Brief description of the feature.

## Basic Usage

Simple example showing the most common use case.

## Advanced Usage

More complex examples and edge cases.

## Configuration

Available options and their defaults.

## Related Features

- [Link to related docs](/path/to/docs)
```

### Running Docs Locally

```bash
cd docs
npm install
npm run docs:dev
```

Visit `http://localhost:5173` to preview.

## Pull Request Process

### Before Submitting

- [ ] Tests pass locally (`composer test`)
- [ ] Code is formatted (`composer format`)
- [ ] Static analysis passes (`composer analyse`)
- [ ] Documentation is updated
- [ ] CHANGELOG is updated (if applicable)
- [ ] Commit messages are clear

### Review Process

1. **Automated checks**: CI must pass
2. **Code review**: Maintainer reviews code
3. **Feedback**: Address review comments
4. **Approval**: Maintainer approves
5. **Merge**: Maintainer merges

### After Merge

- Pull request is merged to `main`
- Changes included in next release
- You're credited in CHANGELOG
- Consider joining as a regular contributor!

## Project Structure

Understanding the codebase structure helps with contributions:

```
cms/
├── config/              # Configuration files
├── database/            # Migrations, factories, seeders
├── docs/                # Vitepress documentation
├── resources/           # Views, assets
├── src/                 # Source code
│   ├── Blocks/          # Block system
│   ├── Commands/        # Artisan commands
│   ├── Http/            # Controllers, middleware
│   ├── Models/          # Eloquent models
│   ├── PageTypes/       # Page type implementations
│   ├── Resources/       # Filament resources
│   ├── Theme/           # Theme system
│   └── ...
├── tests/               # Test suite
└── workbench/           # Testbench workspace
```

## Common Tasks

### Adding a New Block

1. Create block class in `src/Blocks/`
2. Implement `BlockInterface` or extend `BaseBlock`
3. Register in theme's `configure()` method
4. Create Blade view in `resources/views/blocks/`
5. Add tests in `tests/Feature/Blocks/`
6. Document in `/docs/features/blocks.md`

### Adding a New Page Type

1. Create page type class in `src/PageTypes/`
2. Implement `PageTypeInterface`
3. Register in `Siteman` class `$pageTypes` array
4. Create view in theme
5. Add tests
6. Document in `/docs/features/page-types.md`

### Adding Configuration Option

1. Update `config/siteman.php`
2. Add validation if needed
3. Update configuration docs
4. Add test coverage

## Release Process

Maintainers handle releases:

1. Update CHANGELOG.md
2. Update version in composer.json
3. Create Git tag
4. Push to GitHub
5. Publish release notes

## Getting Help

- **Questions**: [GitHub Discussions](https://github.com/siteman-io/cms/discussions)
- **Bugs**: [GitHub Issues](https://github.com/siteman-io/cms/issues)
- **Security**: Email security@siteman.io (private disclosure)

## Recognition

Contributors are recognized in:
- CHANGELOG.md
- GitHub contributors page
- Release notes

Significant contributors may be invited as collaborators.

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

## Thank You!

Every contribution helps make Siteman CMS better. Thank you for being part of the community!

## Related Documentation

- [Testing Guide](/contributing/testing) - Writing and running tests
- [Code Style Guide](https://laravel.com/docs/contributions#coding-style) - Laravel conventions
