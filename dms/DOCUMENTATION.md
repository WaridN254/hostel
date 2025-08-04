# Kdrem Technologies - Property Management System Documentation

## Table of Contents
1. [System Architecture](#system-architecture)
2. [Database Schema](#database-schema)
3. [User Guide](#user-guide)
4. [Developer Documentation](#developer-documentation)
5. [Troubleshooting](#troubleshooting)
6. [FAQs](#faqs)

## System Architecture

### Directory Structure
```
spymvc/
├── application/
│   ├── config/      # Configuration files
│   ├── controllers/ # Application controllers
│   ├── models/      # Database models
│   ├── views/       # View templates
│   └── language/    # Language files
├── assets/
│   ├── css/         # Stylesheets
│   ├── js/          # JavaScript files
│   ├── uploads/     # Uploaded files
│   └── db/          # Database schema
└── system/          # Core framework files
```

## Database Schema

### Core Tables

#### 1. Users & Authentication
- `tbl_users` - System users (admin, staff, tenants)
- `tbl_branches` - Property branches/locations
- `tbl_user_types` - User role definitions

#### 2. Property Management
- `tbl_rooms` - Property units/rooms
- `tbl_beds` - Beds within rooms (for shared spaces)
- `tbl_room_types` - Room categories and specifications
- `tbl_room_features` - Amenities and features

#### 3. Tenant Management
- `tbl_tenants` - Tenant information
- `tbl_tenant_documents` - Tenant documents
- `tbl_lease_agreements` - Lease contracts
- `tbl_tenant_vehicles` - Vehicle information

#### 4. Financials
- `tbl_invoices` - Generated invoices
- `tbl_payments` - Payment records
- `tbl_expenses` - Property expenses
- `tbl_bank_accounts` - Bank account details

#### 5. Maintenance
- `tbl_maintenance_requests` - Service requests
- `tbl_maintenance_logs` - Maintenance history
- `tbl_vendors` - Service providers

## User Guide

### Admin Panel
1. **Dashboard**
   - Property overview
   - Financial summary
   - Maintenance alerts

2. **Property Management**
   - Add/Edit properties
   - Manage room allocations
   - Track occupancy

3. **Tenant Management**
   - Tenant registration
   - Lease management
   - Document storage

4. **Financial Management**
   - Generate invoices
   - Record payments
   - Track expenses
   - Financial reporting

### Common Tasks

#### Adding a New Tenant
1. Navigate to Tenants > Add New
2. Fill in personal details
3. Upload required documents
4. Assign property/room
5. Create lease agreement

#### Generating an Invoice
1. Go to Invoices > New Invoice
2. Select tenant and property
3. Add line items (rent, utilities, etc.)
4. Set due date
5. Save and send to tenant

## Developer Documentation

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Composer
- Git

### Setup Development Environment
1. Clone the repository
2. Run `composer install`
3. Import database schema from `assets/db/dhpms.sql`
4. Configure database settings in `application/config/database.php`
5. Set up base URL in `application/config/config.php`

### Code Structure
- **Controllers**: Handle HTTP requests
- **Models**: Database interactions
- **Views**: Presentation layer
- **Libraries**: Core functionality
- **Helpers**: Reusable functions

### API Endpoints

#### Authentication
```
```

#### Property Endpoints
- `GET /admin/rooms` - List all properties
- `POST /admin/newroom` - Add new property
- `GET /admin/room/{id}` - Get property details
- `POST /admin/updateroom/{id}` - Update property

## Troubleshooting

### Common Issues
1. **Login Issues**
   - Verify user credentials
   - Check user status in database
   - Clear browser cache

2. **Database Connection**
   - Verify database credentials
   - Check MySQL service status
   - Ensure database exists and is accessible

3. **File Uploads**
   - Check uploads directory permissions
   - Verify PHP upload settings
   - Check file size limits in php.ini

## FAQs

### How do I reset the admin password?
1. Access the database directly
2. Locate the admin user in `tbl_users`
3. Update the password field with MD5 hash of the new password

### How to backup the system?
1. Export the database using mysqldump
2. Backup the application directory
3. Store backups securely offsite

### How to update the system?
1. Backup current installation
2. Download latest version
3. Follow update instructions in the release notes
4. Run any database migrations

## Support
For additional support, please contact:
- Email: support@kdremtech.com
- Business Hours: Mon-Fri, 9AM-5PM EST
