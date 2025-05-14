# HealthMantra - Health & Supplements Website

A comprehensive health and supplements website with user management, product catalog, nutritionist portal, and personalized diet plans.

## Project Structure

```
healthmantra/
├── index.html                  # Home page
├── css/                        # CSS files
│   ├── style.css               # Main styles
│   ├── home.css                # Home page specific styles
│   ├── supplements.css         # Supplements page styles
│   ├── auth.css                # Login/Signup styles
│   ├── health_profile.css      # Health profile styles
│   └── dashboard.css           # Dashboard styles
├── js/                         # JavaScript files
│   ├── main.js                 # Main script for all pages
│   ├── home.js                 # Home page specific scripts
│   ├── supplements.js          # Supplements page scripts
│   ├── auth.js                 # Authentication scripts
│   ├── health_profile.js       # Health profile scripts
│   └── dashboard.js            # Dashboard scripts
├── pages/                      # HTML pages
│   ├── supplements.html        # Supplements catalog
│   ├── about.html              # About us page
│   ├── contact.html            # Contact page
│   ├── login.html              # Login page
│   ├── signup.html             # Signup page
│   ├── health_profile.html     # Health profile form
│   ├── dashboard.html          # User dashboard
│   ├── diet_plan.html          # Diet plan page
│   ├── progress.html           # Progress tracking
│   ├── appointments.html       # Appointments management
│   ├── messages.html           # User messages
│   └── settings.html           # User settings
├── admin/                      # Admin portal
│   └── dashboard.php           # Admin dashboard
├── nutritionist/               # Nutritionist portal
│   └── dashboard.php           # Nutritionist dashboard
├── database.sql                # Database schema
└── README.md                   # Project documentation
```

## Database Setup

1. Create a MySQL database:
```sql
CREATE DATABASE healthmantra;
```

2. Import the database schema:
```
mysql -u root -p healthmantra < database.sql
```

3. Update the database configuration in `php/config.php` with your MySQL credentials.

## Features

- Responsive design for all devices
- User registration and authentication
- Health profile collection and personalized recommendations
- Supplements catalog with filtering
- Nutritionist portal for client management
- User dashboard for progress tracking
- Subscription-based business model
- Admin panel for site management

## Access Credentials

### Admin Dashboard
- URL: `/admin/dashboard.php`
- Email: admin@healthmantra.com
- Password: admin123

### Nutritionist Dashboard
- URL: `/nutritionist/dashboard.php`
- Email: jessica.miller@healthmantra.com
- Password: nutritionist123

### Test User Account
- Email: test@example.com
- Password: test123

## Available Pages

### User Dashboard
- Dashboard Overview: View health metrics and recent activities
- Diet Plan: Personalized nutrition plans and meal schedules
- Progress Tracking: Monitor weight, measurements, and goals
- Appointments: Schedule and manage nutritionist consultations
- Messages: Communication with assigned nutritionist
- Settings: Update profile and preferences

### Nutritionist Dashboard
- Client Management: View and manage assigned clients
- Diet Plans: Create and modify client nutrition plans
- Progress Reports: Track client progress and metrics
- Appointments: Manage consultation schedule
- Messages: Communicate with clients
- Profile: Update professional profile

### Admin Dashboard
- User Management: Manage users and nutritionists
- Supplement Management: Add/edit supplement catalog
- Content Management: Update website content
- Reports: View site statistics and analytics
- System Settings: Configure site parameters

## Note

This is a demonstration project. In a production environment, you would need to:

1. Set up proper security measures (HTTPS, CSRF protection, etc.)
2. Implement email verification
3. Set up payment processing
4. Add proper error handling
5. Implement a more robust user authentication system