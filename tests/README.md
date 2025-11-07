# Test Suite Documentation

## Overview

This test suite provides comprehensive testing for the Laravel POS application. The tests cover API endpoints, web controllers, models, middleware, and helper functions.

## Test Structure

### Feature Tests

#### API Tests (`tests/Feature/Api/`)
- **AuthControllerTest.php** - Tests for API authentication endpoints
  - Login with username, email, and phone number
  - Registration with company creation
  - Logout functionality
  - Password change
  - Access PIN management
  - User availability checks
  - Company availability checks

#### Web Tests (`tests/Feature/Web/`)
- **AuthControllerTest.php** - Tests for web authentication
  - Login/logout functionality
  - Registration process
  - Company selection
  - Access PIN setup and validation
  - Screen lock/unlock functionality

#### Management Tests (`tests/Feature/Man/`)
- **CustomerCompanyControllerTest.php** - Tests for customer company management
  - CRUD operations for companies
  - Data table functionality with search and pagination
  - Role-based access control
  - Company profile management

#### Model Tests (`tests/Feature/Models/`)
- **UserTest.php** - Tests for User model
  - Model creation and relationships
  - Role associations
  - Soft delete functionality
  - Hidden fields
  - User scopes

#### Middleware Tests (`tests/Feature/Middleware/`)
- **AuthorizationTest.php** - Tests for authorization middleware
  - Authentication checks
  - Role-based access control
  - Company selection requirements
  - Access PIN validation
  - Session handling

#### Helper Tests (`tests/Feature/Helpers/`)
- **HelpersTest.php** - Tests for helper functions
  - Phone number formatting
  - Currency formatting
  - Date/time formatting
  - Email validation
  - String manipulation utilities

### Unit Tests

#### Basic Application Tests
- **ApplicationTest.php** - Basic application functionality tests
  - Route accessibility
  - Authentication requirements
  - Database connectivity
  - Model relationships
  - Session management

## Running Tests

### Prerequisites

1. Ensure you have PHP and Composer installed
2. Set up your `.env.testing` file for test database configuration
3. Run database migrations for testing: `php artisan migrate --env=testing`

### Running All Tests

```bash
# Run all tests
php artisan test

# Run tests with coverage (requires Xdebug)
php artisan test --coverage

# Run tests in parallel
php artisan test --parallel
```

### Running Specific Test Suites

```bash
# Run only API tests
php artisan test --filter=Api

# Run only web tests
php artisan test --filter=Web

# Run only model tests
php artisan test --filter=Models

# Run only middleware tests
php artisan test --filter=Middleware

# Run only helper tests
php artisan test --filter=Helpers
```

### Running Individual Test Files

```bash
# Run specific test file
php artisan test tests/Feature/Api/AuthControllerTest.php

# Run specific test method
php artisan test --filter=test_user_can_login_with_valid_credentials
```

### Running Tests with Verbose Output

```bash
# Run tests with detailed output
php artisan test -v

# Run tests with very verbose output
php artisan test -vv
```

## Test Database Configuration

Create a `.env.testing` file with the following configuration:

```env
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_DRIVER=sync
```

## Test Data Factories

The test suite uses Laravel factories to generate test data. Key factories include:

- **UserFactory** - Creates test users
- **CustomerCompanyFactory** - Creates test companies
- **AppRoleFactory** - Creates application roles
- **CustomerRoleFactory** - Creates customer roles
- **BusinessTypeFactory** - Creates business types

## Test Coverage Areas

### Authentication & Authorization
- ✅ User login/logout (API & Web)
- ✅ User registration
- ✅ Role-based access control
- ✅ Company selection
- ✅ Access PIN management
- ✅ Session management

### Customer Company Management
- ✅ CRUD operations
- ✅ Data table functionality
- ✅ Search and pagination
- ✅ Role-based filtering
- ✅ Company profile management

### Models & Relationships
- ✅ User model functionality
- ✅ Role associations
- ✅ Company relationships
- ✅ Soft delete operations
- ✅ Model scopes

### Middleware
- ✅ Authentication checks
- ✅ Authorization rules
- ✅ Company selection requirements
- ✅ Access PIN validation
- ✅ Session handling

### Helper Functions
- ✅ Phone number formatting
- ✅ Currency formatting
- ✅ Date/time formatting
- ✅ Email validation
- ✅ String utilities

## Best Practices

### Writing Tests

1. **Use descriptive test names** - Test method names should clearly describe what is being tested
2. **Follow AAA pattern** - Arrange, Act, Assert
3. **Test both success and failure cases** - Ensure error handling is tested
4. **Use factories for test data** - Avoid hardcoding test data
5. **Test edge cases** - Include boundary conditions and error scenarios

### Test Organization

1. **Group related tests** - Use test classes to organize related functionality
2. **Use setUp methods** - Initialize common test data in setUp()
3. **Clean up after tests** - Use RefreshDatabase trait for clean test state
4. **Use meaningful assertions** - Choose the most specific assertion for the test

### Performance Considerations

1. **Use in-memory database** - SQLite in-memory for faster tests
2. **Minimize database calls** - Use factories efficiently
3. **Run tests in parallel** - Use --parallel flag for faster execution
4. **Use appropriate test data** - Don't create unnecessary test records

## Troubleshooting

### Common Issues

1. **Database connection errors** - Ensure `.env.testing` is configured correctly
2. **Factory errors** - Check that all required factories exist
3. **Permission errors** - Ensure test database is writable
4. **Session errors** - Clear application cache: `php artisan cache:clear`

### Debugging Tests

```bash
# Run tests with debug output
php artisan test --verbose

# Run specific failing test
php artisan test --filter=test_name --verbose

# Check test database
php artisan tinker --env=testing
```

## Continuous Integration

The test suite is designed to work with CI/CD pipelines. Ensure your CI environment:

1. Has PHP and required extensions installed
2. Uses the `.env.testing` configuration
3. Runs `php artisan test` as part of the build process
4. Reports test results and coverage

## Contributing

When adding new features:

1. **Write tests first** - Follow TDD principles
2. **Update this documentation** - Keep test documentation current
3. **Ensure all tests pass** - Don't commit failing tests
4. **Add appropriate test coverage** - Aim for high coverage of new code
