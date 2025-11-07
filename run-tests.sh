#!/bin/bash

# Test Runner Script for Laravel POS Application
# Usage: ./run-tests.sh [option]

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Function to setup test environment
setup_test_env() {
    print_status "Setting up test environment..."
    
    # Check if .env.testing exists, if not create it
    if [ ! -f .env.testing ]; then
        print_warning ".env.testing not found, creating from .env..."
        cp .env .env.testing
        # Update .env.testing for testing
        sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env.testing
        sed -i 's/DB_DATABASE=.*/DB_DATABASE=:memory:/' .env.testing
        sed -i 's/CACHE_DRIVER=.*/CACHE_DRIVER=array/' .env.testing
        sed -i 's/SESSION_DRIVER=.*/SESSION_DRIVER=array/' .env.testing
        sed -i 's/QUEUE_CONNECTION=.*/QUEUE_CONNECTION=sync/' .env.testing
        print_success ".env.testing created"
    fi
    
    # Clear caches
    php artisan cache:clear --env=testing
    php artisan config:clear --env=testing
    php artisan route:clear --env=testing
    php artisan view:clear --env=testing
    
    print_success "Test environment setup complete"
}

# Function to run all tests
run_all_tests() {
    print_status "Running all tests..."
    php artisan test
}

# Function to run tests with coverage
run_tests_with_coverage() {
    print_status "Running tests with coverage..."
    if command_exists xdebug; then
        php artisan test --coverage
    else
        print_warning "Xdebug not found. Install Xdebug for coverage reports."
        php artisan test
    fi
}

# Function to run tests in parallel
run_tests_parallel() {
    print_status "Running tests in parallel..."
    php artisan test --parallel
}

# Function to run specific test suite
run_test_suite() {
    local suite=$1
    print_status "Running $suite tests..."
    php artisan test --testsuite=$suite
}

# Function to run specific test file
run_test_file() {
    local file=$1
    print_status "Running test file: $file"
    php artisan test $file
}

# Function to run tests with filter
run_tests_filter() {
    local filter=$1
    print_status "Running tests with filter: $filter"
    php artisan test --filter="$filter"
}

# Function to run tests with verbose output
run_tests_verbose() {
    print_status "Running tests with verbose output..."
    php artisan test -v
}

# Function to show help
show_help() {
    echo "Laravel POS Test Runner"
    echo ""
    echo "Usage: $0 [option]"
    echo ""
    echo "Options:"
    echo "  all              Run all tests"
    echo "  coverage         Run tests with coverage report"
    echo "  parallel         Run tests in parallel"
    echo "  verbose          Run tests with verbose output"
    echo "  api              Run API tests only"
    echo "  web              Run Web tests only"
    echo "  models           Run Model tests only"
    echo "  middleware       Run Middleware tests only"
    echo "  helpers          Run Helper tests only"
    echo "  management       Run Management tests only"
    echo "  setup            Setup test environment"
    echo "  help             Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 all           # Run all tests"
    echo "  $0 api           # Run only API tests"
    echo "  $0 coverage      # Run tests with coverage"
    echo "  $0 parallel      # Run tests in parallel"
}

# Main script logic
case "${1:-all}" in
    "all")
        setup_test_env
        run_all_tests
        ;;
    "coverage")
        setup_test_env
        run_tests_with_coverage
        ;;
    "parallel")
        setup_test_env
        run_tests_parallel
        ;;
    "verbose")
        setup_test_env
        run_tests_verbose
        ;;
    "api")
        setup_test_env
        run_test_suite "API"
        ;;
    "web")
        setup_test_env
        run_test_suite "Web"
        ;;
    "models")
        setup_test_env
        run_test_suite "Models"
        ;;
    "middleware")
        setup_test_env
        run_test_suite "Middleware"
        ;;
    "helpers")
        setup_test_env
        run_test_suite "Helpers"
        ;;
    "management")
        setup_test_env
        run_test_suite "Management"
        ;;
    "setup")
        setup_test_env
        ;;
    "help"|"-h"|"--help")
        show_help
        ;;
    *)
        print_error "Unknown option: $1"
        show_help
        exit 1
        ;;
esac

print_success "Test execution completed!"
