# Siteman CMS Project Guidelines

## Project Overview
Siteman is a Content Management System (CMS) that leverages Laravel and Filament to provide a straightforward content management solution. It serves as a robust foundation for building custom applications, offering a seamless and efficient development experience.

## Project Structure
- `/src`: Contains the main source code of the CMS
- `/resources`: Contains views, assets, and other frontend resources
- `/config`: Configuration files
- `/database`: Database migrations and seeders
- `/tests`: Test files using Pest framework
- `/routes`: Route definitions
- `/docs`: Documentation files
- `/stubs`: Template files used for code generation

## Testing Guidelines
When implementing changes, Junie should run tests to verify the correctness of the solution:

```bash
composer test
```

The project uses Pest as its testing framework. Make sure all tests pass before submitting any changes.

## Development Setup
For local development, the following commands should be used:

```bash
composer install
composer prepare
composer serve
```

## Code Style
The project follows Laravel's coding standards. Code style issues can be fixed with:

```bash
composer format
```

## Building the Project
Before submitting changes, ensure that the project builds successfully:

```bash
composer build
```

## Documentation
The official documentation can be found at [siteman.io](https://siteman.io).

## Additional Notes
- The project is licensed under the MIT License
- When making changes, ensure backward compatibility is maintained
- Follow the existing architecture patterns when adding new features
