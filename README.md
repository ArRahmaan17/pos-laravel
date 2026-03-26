# DPOS - Point of Sale System 
A comprehensive, multi-company Point of Sale (POS) system built with the Laravel framework. This system provides complete inventory management, transaction processing, user management, and reporting capabilities for businesses of all sizes.

## 🚀 Features

### Core POS Features
- **Multi-Company Support**: Manage multiple companies/outlets within a single system
- **Inventory Management**: Complete product and stock management with real-time tracking
- **Transaction Processing**: Sales, returns, and payment processing with detailed tracking
- **Warehouse Management**: Multi-warehouse support with rack management and location tracking
- **User Management**: Role-based access control with customizable permissions
- **Reporting**: Comprehensive sales and inventory reports with PDF export
- **Discount Management**: Flexible discount system for products and transactions
- **Temporary Product Management**: Pending product approval system for inventory changes

### Business Management
- **Company Management**: Multi-company setup with individual configurations
- **Role-Based Access**: Granular permission system for different user roles
- **Subscription Management**: Subscription-based feature access
- **Task Management**: Internal task tracking and management with detailed workflows
- **Stocktaking**: Inventory counting and reconciliation with approval workflows
- **Payment Methods**: Multiple payment method support
- **Product Categories**: Hierarchical product categorization system
- **Unit Management**: Flexible product unit management

### Advanced Features
- **Theme Support**: Premium Dark and Light modes with smooth transitions
- **Temporary Product Approval**: Pending inventory changes require manager approval
- **Stock Movement Tracking**: Complete audit trail for all inventory movements
- **Order Code Generation**: Automatic order code generation with company prefixes
- **Real-time Stock Validation**: Prevents overselling with real-time stock checks
- **PDF Receipt Generation**: Automatic receipt generation for transactions
- **Data Export**: Multiple export formats (PDF, Excel, CSV)
- **Search and Filter**: Advanced search and filtering capabilities
- **Bulk Operations**: Support for bulk product and inventory operations

### Technical Features
- **Laravel 10**: Built on the latest Laravel framework
- **RESTful API**: Complete API for mobile and third-party integrations
- **PDF Generation**: Built-in PDF report generation using DomPDF
- **Real-time Updates**: Live data updates and notifications
- **Responsive Design**: Mobile-friendly interface
- **Security**: Advanced authentication and authorization
- **Database Optimization**: Optimized queries and indexing
- **Caching**: Built-in caching for improved performance

## 📋 Requirements

- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (for frontend assets)
- Web server (Apache/Nginx/FrankenPHP)

## 🛠️ Installation

This project is fully dockerized, providing a seamless setup experience.

### 1. Clone the Repository
```bash
git clone https://github.com/ArRahmaan17/DPOS
cd DPOS
```

### 2. Environment Setup
Create a `.env` file in the root directory (you can copy from `.env.example` if available) and configure your database and application variables.

### 3. Start the Environment
```bash
docker-compose up -d --build
```

This command will automatically:
- Start the web server (FrankenPHP), queue workers, Reverb, MySQL, Redis, and Nginx.
- Create the configured database if it doesn't exist.
- Run database migrations on startup.

For more detailed information on managing the Docker environment and running artisan commands, please refer to the [Docker Management Manual (DOCKER_MAN.md)](DOCKER_MAN.md).
## 🏗️ Project Structure

```text
DPOS/
├── application/          # Laravel application source code
│   ├── app/              # Application logic (Controllers, Models, etc.)
│   ├── database/         # Migrations and seeders
│   ├── resources/        # Views, CSS, and JS
│   ├── routes/           # Web and API routes
│   └── public/           # Public assets
├── build/                # Docker configuration files and entrypoint scripts
├── docker-compose.yml    # Docker services configuration
├── DOCKER_MAN.md         # Detailed Docker documentation
└── .env                  # Environment variables
```

## 🔧 Configuration

### Key Configuration Files
- `.env` - Environment variables
- `config/app.php` - Application configuration
- `config/database.php` - Database configuration
- `config/auth.php` - Authentication configuration

### Important Environment Variables
```env
APP_NAME="DPOS"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

REDIS_HOST=127.0.0.1
REDIS_USERNAME=
REDIS_PASSWORD=
REDIS_PREFIX=
REDIS_DB=
REDIS_PORT=
REDIS_TTL=
```

## 👥 User Management

### Default Roles
- **Super Admin**: Full system access
- **Company Admin**: Company-level management
- **Manager**: Store/outlet management with approval rights
- **Cashier**: Basic POS operations
- **Stock Clerk**: Inventory management
- **Developer**: System development and maintenance

### User Permissions
- Permission access control
- Feature-level permissions
- Data access restrictions
- Action-based permissions
- Approval workflows

## 🏢 Multi-Company Setup

### Company Configuration
- Company profile management
- Address and contact information
- Business type classification
- Subscription plan assignment
- Custom order code prefixes

### Warehouse Management
- Multiple warehouse support
- Rack and shelf management
- Stock location tracking
- Inventory movement tracking
- Warehouse-specific permissions

## 📦 Inventory Management

### Product Management
- Product creation and categorization
- Unit management (pieces, kg, liters, etc.)
- Stock level tracking
- Product variants and attributes
- Bulk product operations

### Stock Operations
- **Stock In**: New product arrivals
- **Restock**: Replenishing existing stock
- **Stock Out**: Sales and consumption
- **Stock Removal**: Damaged/expired products
- **Stock Transfer**: Between warehouses

### Temporary Product System
- Pending product approval workflow
- Manager approval for inventory changes
- Transaction grouping by date
- Approval status tracking
- Automatic order code generation

## 💰 Transaction Processing

### Sales Operations
- Real-time product search
- Discount application
- Payment method selection
- Receipt generation
- Transaction history

### Order Management
- Automatic order code generation
- Transaction validation
- Stock availability checking
- Discount code validation
- Payment processing

## 📊 Reporting

### Available Reports

#### Sales Reports
- **Sales Summary**: Summary of sales grouped by day, week, or month with total sales, orders, and average order value
- **Sales by Product**: Detailed report of sales per product with quantities and revenue
- **Sales by Category**: Sales report grouped by product categories with top products
- **Sales by Cashier**: Displays sales made by each cashier or staff member
- **Complete Transaction List**: A complete list of all transactions made in the system
- **Discount Usage Report**: Report showing how and when discounts are applied by cashiers or customers
- **Transaction Receipt**: Individual transaction receipt for specific orders

#### Product Reports
- **Stock On Hand**: Current available stock quantities for each product
- **Stock Movement Report**: Report of all stock movements: stock in, stock out, and adjustments
- **Stock Opname**: Comparison between physical stock count and system stock records
- **Product Performance Report**: Analysis of fast and slow moving products based on sales trends

#### Finance Reports
- **Cash Flow Summary**: Summary of all incoming and outgoing cash transactions
- **Income vs Expense Report**: Comparison report between total income and total expenses over a period

### Report Features
- **Date Range Selection**: Custom date ranges with preset options (Today, Week, Month)
- **Cashier Filtering**: Filter reports by specific cashiers or staff members
- **Real-time Data**: Reports generated with current system data
- **Company-specific**: All reports are filtered by the logged-in company
- **Export Options**: PDF and Excel export formats available

### Export Options
- **PDF Export**: Professional PDF reports with custom templates
- **Excel Export**: Data analysis ready Excel files
- **Print-friendly Formats**: Optimized for printing
- **Custom Styling**: Company branding and watermarks

## 🔌 API Integration

### RESTful API Endpoints
- Authentication endpoints
- Product management
- Transaction processing
- Inventory operations
- User management
- Company management

### API Features
- Rate limiting (100 requests per minute)
- User availability checking
- Company type retrieval
- Company availability validation
- Registration endpoints

### API Documentation
API documentation is available at `/api/documentation` when running in development mode.

## 🚀 Deployment

The recommended deployment method for this application is using **Docker Compose**.

### Production Deployment
1. Ensure `.env` is configured with `APP_ENV=production` and strong credentials.
2. Run the Docker Compose stack:
```bash
docker-compose up -d --build
```
3. The Docker container automatically caches configuration, routes, and views on startup for optimal performance.
4. For HTTPS, configure a reverse proxy (e.g., Nginx, Traefik, or Caddy) in front of the application to handle SSL certificates.
5. Set up external database backups mapping from your Docker volume as needed.

### Server Requirements
- **Minimum**: 2GB RAM, 1 CPU core
- **Recommended**: 4GB RAM, 2 CPU cores
- **Storage**: 20GB minimum
- **OS**: Ubuntu 20.04+, CentOS 8+, or similar

## 🧪 Testing

### Run Tests
```bash
php artisan test
```

### Code Quality
```bash
composer pint
```

### Performance Testing
```bash
php artisan test --filter=PerformanceTest
```

## 🔒 Security

### Security Features
- **Authentication**: Multi-factor authentication support
- **Authorization**: Role-based access control
- **Data Encryption**: Sensitive data encryption
- **SQL Injection Protection**: Laravel's built-in protection
- **XSS Protection**: Cross-site scripting prevention
- **CSRF Protection**: Cross-site request forgery protection

### Security Best Practices
- Regular security updates
- Strong password policies
- Session management
- Input validation
- Output sanitization
- HTTPS enforcement

## ⚡ Performance Optimization

### Database Optimization
- Indexed queries for faster searches
- Query optimization for large datasets
- Database connection pooling
- Caching frequently accessed data

### Application Optimization
- Route caching for faster routing
- View caching for compiled templates
- Configuration caching
- Asset minification and compression

### Monitoring
- Application performance monitoring
- Database query monitoring
- Error tracking and logging
- Resource usage monitoring

## 🐛 Troubleshooting

### Common Issues

#### Database Connection Issues
```bash
# Check database connection
php artisan tinker
DB::connection()->getPdo();
```

#### Permission Issues
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Cache Issues
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### Asset Issues
```bash
# Rebuild assets
npm run build
php artisan vendor:publish --tag=laravel-assets
```

### Debug Mode
Enable debug mode in `.env`:
```env
APP_DEBUG=true
```

### Log Files
Check log files for errors:
```bash
tail -f storage/logs/laravel.log
```

## 📝 Contributing

### Development Guidelines
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Follow PSR-12 coding standards
4. Add tests for new features
5. Update documentation
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

### Code Standards
- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Add comments for complex logic
- Write unit tests for new features
- Update documentation when needed

### Pull Request Process
1. Ensure all tests pass
2. Update documentation if needed
3. Provide clear commit messages
4. Include screenshots for UI changes
5. Describe the changes in detail

### 📝 Guideline commit message 
- **`feat:`** A new feature
- **`fix:`** A bug fix
- **`docs:`** Documentation only changes
- **`style:`** Changes that don't affect code meaning (formatting, missing semicolons, etc.)
- **`refactor:`** Code change that neither fixes a bug nor adds a feature
- **`perf:`** Code change that improves performance
- **`test:`** Adding missing tests or correcting existing tests
- **`chore:`** Changes to build process, dependencies, or tooling

## ⚖️ Licensing

This software is licensed under the **Academic-Commercial Dual License**.

* **Free Use:** You may use, copy, and modify this software **free of charge** for **Educational, Research, or Personal Learning purposes only.**
* **Commercial Use:** **Commercial use is strictly prohibited** unless a separate commercial license is purchased from the Copyright Owner.

For the full terms and conditions, please see the [LICENSE](LICENSE) file in this repository.

For Commercial Licensing inquiries, please contact: <a href="mailto:ardrah17@gmail.com">ardrah17@gmail.com</a>

## 🆘 Support

### Getting Help
- **Documentation**: Check this README and Laravel docs
- **Issues**: Create an issue in the repository
- **Discussions**: Use GitHub Discussions for questions
- **Email**: Contact the development team

### Community
- **GitHub Issues**: Bug reports and feature requests
- **GitHub Discussions**: General questions and discussions
- **Contributing**: See contributing guidelines above

## 🔄 Updates

### Updating the Application
```bash
# Backup your database first
php artisan backup:run

# Update code
git pull origin education

# Update dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Run migrations
php artisan migrate

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Version Compatibility
- **Laravel**: 10.x
- **PHP**: 8.1+
- **MySQL**: 8.0+ or PostgreSQL 12+
- **Redis**: 7.2+

## 📚 Additional Resources

### Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [Bootstrap Documentation](https://getbootstrap.com/docs/)
- [DomPDF Documentation](https://github.com/barryvdh/laravel-dompdf)

### Learning Resources
- [Laravel Bootcamp](https://bootcamp.laravel.com)
- [Laracasts](https://laracasts.com)
- [Laravel News](https://laravel-news.com)

### Tools
- [Laravel Telescope](https://laravel.com/docs/telescope) - Debugging
- [Laravel Horizon](https://laravel.com/docs/horizon) - Queue monitoring
- [Laravel Sanctum](https://laravel.com/docs/sanctum) - API authentication

## 📞 Contact

- **Project Maintainer**: Ardhi Rahmaan MS
- **Email**: ardrah17@gmail.com
- **Website**: https://www.rahmaanms.my.id
- **GitHub**: https://github.com/ArRahmaan17
---
*Last updated: Nov 2025*