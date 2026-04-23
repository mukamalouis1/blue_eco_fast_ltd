# Blue Eco Fast - Admin System Documentation

## Admin Features Implemented

### 1. **Admin Dashboard** (`admin/dashboard.php`)
- View key statistics:
  - Total enquiries
  - Pending enquiries
  - Active users
  - Cars in fleet
- View recent enquiries table
- Quick links to manage all resources

### 2. **Enquiry Management** (`admin/enquiries.php`)
- View all enquiries in a table format
- Click on any enquiry to view full details
- Update enquiry status (Pending → Responded → Completed)
- Add admin response notes
- Track response dates

### 3. **Car Management** (`admin/cars.php`)
- View all cars in the fleet
- Add new cars with details:
  - Emoji, Name, Category, Type
  - Range, Seats, Price, Fuel Type
- Edit existing cars
- Delete cars from the fleet

### 4. **User Management** (`admin/users.php`)
- View all registered users
- See user details (email, name, phone, role)
- View enquiry count per user
- Identify admin vs regular users

### 5. **Admin Settings** (`admin/settings.php`)
- Admin profile information
- Change password (with current password verification)
- System information display

## Default Admin Credentials

**Email:** `admin@blueeco.rw`  
**Password:** `Admin123`

⚠️ **IMPORTANT:** Change this password immediately after first login!

## User Roles

### Admin
- Full access to all admin features
- Can manage enquiries, cars, users, and settings
- Receives automatic redirect to admin dashboard on login

### Regular User
- Can register and view personal dashboard
- Can submit enquiries
- Can view their enquiry history

## Database Changes

The admin system created/modified the following:

### New Tables/Columns:
1. **users table** - Added `role` column (enum: 'user', 'admin')
2. **enquiries table** - Added:
   - `status` (pending, responded, completed)
   - `response` (admin notes)
   - `response_date` (timestamp)

### Default Admin User
Email: `admin@blueeco.rw`
Password: `Admin123` (hashed with PASSWORD_DEFAULT)

## Enquiry System Updates

### Key Features:
- ✅ **No User Login Required** - Any visitor can submit enquiries
- ✅ **Optional User Association** - Enquiries linked to user if logged in
- ✅ **Admin Tracking** - All enquiries visible to admin panel
- ✅ **Status Management** - Admins can mark enquiries as pending/responded/completed
- ✅ **Response Notes** - Admins can add personalized responses to enquiries
- ✅ **Email Integration** - Auto-reply sent to users + notification to company

## Navigation

### For Admins:
1. Log in with admin credentials → Redirect to `admin/dashboard.php`
2. Use sidebar navigation to access:
   - Dashboard
   - Enquiries
   - Cars
   - Users
   - Settings

### For Regular Users:
1. Log in with user credentials → Redirect to `dashboard.php`
2. View profile and enquiry history
3. Submit new enquiries from home page

## Security Features

- ✅ Session-based authentication
- ✅ Password hashing (PASSWORD_DEFAULT)
- ✅ Admin-only page protection via `requireAdmin()` function
- ✅ SQL injection prevention (prepared statements)
- ✅ Input sanitization via `clean()` function
- ✅ XSS protection (htmlspecialchars)

## Access Control

```php
// Admin pages check:
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}
```

## File Structure

```
blue_eco_fast_ltd/
├── admin/
│   ├── dashboard.php       # Admin dashboard with statistics
│   ├── enquiries.php       # Manage all enquiries
│   ├── cars.php            # Manage fleet
│   ├── users.php           # View all users
│   └── settings.php        # Admin settings & password change
├── includes/
│   └── config.php          # Updated with isAdmin() and requireAdmin()
├── login.php               # Updated with admin role detection
├── register.php            # User registration
├── dashboard.php           # User dashboard
└── index.php               # Home page with updated enquiry form
```

## Helper Functions

### In `includes/config.php`:
```php
isAdmin()        // Check if current user is admin
requireAdmin()   // Redirect if not admin
getDB()          // Get PDO database connection
```

## Tips for Admins

1. **First Time Setup:**
   - Log in with default credentials
   - Change password in Settings
   - Add/update cars in Cars section
   - Start managing enquiries

2. **Enquiry Workflow:**
   - Review pending enquiries in dashboard
   - Click "View" to see full details
   - Write response and change status
   - Enquiry history is automatically saved

3. **Best Practices:**
   - Respond to enquiries within 24 hours
   - Keep response notes professional
   - Regularly review user activity
   - Monitor fleet inventory

## Future Enhancements

- Email notifications for new enquiries
- Enquiry export (PDF/Excel)
- Advanced analytics and reporting
- User activity logs
- Multiple admin users with role-based permissions
- Custom admin theme/branding
