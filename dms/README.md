# Kdrem Technologies - Property Management System (PMS)

## Overview
Kdrem Technologies PMS is a comprehensive property and tenant management system designed to streamline property rental operations. The system is built using PHP with the CodeIgniter framework and features a modern, responsive admin interface.

## Key Features

### Property Management
- Room and bed management
- Property details and specifications
- Room allocation and tracking
- Maintenance requests

### Tenant Management
- Tenant registration and profiles
- Lease agreement management
- Document management
- Communication logs

### Billing & Payments
- Rent collection and tracking
- Invoice generation
- Payment history
- Financial reporting

### Maintenance & Services
- Service request tracking
- Maintenance scheduling
- Vendor management
- Service history

### Reporting
- Financial reports
- Occupancy rates
- Payment status
- Custom report generation

## Technology Stack
- **Backend**: PHP 7.4+
- **Framework**: CodeIgniter 3
- **Frontend**: HTML5, CSS3, JavaScript, jQuery
- **Database**: MySQL 5.7+
- **UI Framework**: AdminLTE
- **Additional Libraries**:
  - Grocery CRUD
  - TCPDF
  - PHPMailer
  - Stripe Payment Gateway (optional)

## Installation

### Prerequisites
- Web Server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer

### Setup Instructions
1. Clone the repository
2. Import the database schema from `assets/db/dhpms.sql`
3. Configure database settings in `application/config/database.php`
4. Set up base URL in `application/config/config.php`
5. Set proper file permissions:
   ```bash
   chmod -R 755 ./
   chmod -R 777 ./uploads
   ```
6. Access the application through your web server

## Default Login Credentials
- **Admin Panel**: `/admin`
  - Username: admin
  - Password: admin123

## Documentation
For detailed documentation, please refer to [DOCUMENTATION.md](DOCUMENTATION.md)

## License
Proprietary - All rights reserved © Kdrem Technologies

## Support
For support, please contact support@kdremtech.com
