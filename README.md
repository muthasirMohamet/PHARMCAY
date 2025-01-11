# Pharmacy Management System

## Introduction
The **Pharmacy Management System** is a web-based application designed to streamline pharmacy operations. Built with PHP and a MySQL database, it offers efficient tools for managing inventory, prescriptions, customer records, and staff activities, ensuring a smooth workflow and better patient service.

---

## Features
- **User Authentication:** Secure login for admins, pharmacists, and staff.
- **Inventory Management:** Track medicines, stock levels, expiry dates, and suppliers.
- **Prescription Management:** Process prescriptions and maintain patient records.
- **Sales and Billing:** Generate invoices, manage payments, and track sales.
- **Reset Password with Approval:** A user-friendly password reset system requiring administrator approval.
- **Reports and Analytics:** View sales reports, stock usage, and financial summaries.

---

## Requirements
- **Server:** Apache/Nginx
- **Backend:** PHP (version 7.4 or higher)
- **Database:** MySQL
- **Frontend:** HTML, CSS (Bootstrap for responsive design)
- **Tools:** Composer (for dependency management)

---

## Installation Steps
1. **Clone the Repository:**
   ```bash
   git clone https://github.com/your-repo/pharmacy-management.git
   ```
2. **Set Up the Database:**
   - Import the `pharmacy_db.sql` file into your MySQL database.
   - Update `db.php` with your database credentials:
     ```php
     $servername = "localhost";
     $username = "root";
     $password = "your_password";
     $dbname = "pharmacy_db";
     ```
3. **Run the Application:**
   - Place the project in your server directory (e.g., `htdocs` for XAMPP).
   - Access the application at `http://localhost/pharmacy-management`.

---

## Usage
1. **Admin Panel:**
   - Manage users, approve password resets, and generate reports.
2. **Pharmacist Panel:**
   - Handle prescriptions, manage inventory, and process sales.
3. **Reset Password:**
   - Users can request a password reset. The reset will only take effect after admin approval.

---

## Contribution
We welcome contributions to improve the system. Feel free to fork the project and submit a pull request.

---

## License
This project is licensed under the MIT License. See the LICENSE file for details.

