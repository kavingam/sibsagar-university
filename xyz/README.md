# 📌 Student Seat Allocation System

## 📖 Overview
This project provides a **Student Seat Allocation System** that efficiently assigns seats to students based on various allocation strategies. It includes both **basic** and **advanced** seat allocation functions, ensuring flexibility and fairness in seat distribution.

## 🚀 Features
- **Batch-based seat allocation**
- **Even distribution of seats among departments**
- **Merit-based and random seat assignment**
- **Dynamic seat adjustments and validation**
- **Exportable seating charts for easy management**
- **User Authentication**: Secure login for admin users to manage the system.
- **Responsive Design**: Fully responsive UI using Bootstrap for easy access on all devices.
- **Export Data**: Download seat plans and duty allotments in CSV or PDF format.
- **Real-time Updates**: AJAX-based system for smooth and fast data updates without page reloads.

## 🎯 Seat Allocation Functions
### **Basic Seat Allocation Functions**
1. **`allocateSeatsByBatch()`** – Allocates seats based on student batches.
2. **`distributeSeatsEvenly()`** – Distributes available seats evenly among departments.
3. **`assignSeatsByMerit()`** – Allocates seats based on student rankings or merit.
4. **`allocateSeatsRandomly()`** – Assigns seats randomly to students.
5. **`generateSeatingArrangement()`** – Creates a seating plan for students.

### **Advanced Allocation Functions**
6. **`allocateSeatsByDepartment()`** – Allocates seats based on department quotas.
7. **`seatAllocationByExamHall()`** – Assigns students to seats in different exam halls.
8. **`calculateAvailableSeats()`** – Computes the remaining available seats after allocation.
9. **`rearrangeSeatAllocation()`** – Adjusts seat assignments dynamically.
10. **`validateSeatDistribution()`** – Ensures that seat allocation meets specific rules.

## 📂 File Structure
```

│── 📄 index.php                 # Main entry point
│── 📄 import-students.php       # 
│── 📄 export-students.php       # 
│── 📄 add-room.php              # 
│── 📄 view-room.php             # 
│── 📄 department.php            # 
│── 📄 README.md                 # 
│── 📂 assets/                   # Static assets (CSS, JS, Images)
│   │── 📂 nxz_var               # Javascript (JS) Main Functions
│   │── 📂 plugins               # External Resources Bootstarp 5.3.3
│   │── 📄 README.md             # Describe Internal Structure
│── 📂 includes/                 # Reusable PHP files
│   │── 📄 config.php            #
│   │── 📄 header.php            #
│   │── 📄 footer.php            #
│   │── 📄 modal.php             #
│   │── 📄 navbar.php            #
│   │── 📄 sidebar.php           #
│── 📂 seat_allocation/          # Core seat allocation logic
│   │── 📄 seat_allocation.php   # Main seat allocation logic
│   │── 📄 assign_seats.php      # Processes seat assignments
│   │── 📄 generate_seating_plan.php  # Generates seating arrangement
│   │── 📄 seat_distribution.php  # Manages seat distribution
│   │── 📄 exam_hall_allocation.php # Assigns students to exam halls
│── 📂 advanced_management/       # Advanced seat allocation features
│   │── 📄 validate_seat_allocation.php  # Ensures rules are followed
│   │── 📄 seat_availability.php  # Checks available seats
│   │── 📄 update_seat_arrangement.php  # Updates seating plan
│   │── 📄 reserve_seats.php      # Manages reserved seating
│   │── 📄 export_seating_chart.php  # Exports seating plan as PDF/CSV
│── 📂 authentication/            # User authentication
│   │── 📄 login.php              # Admin login page
│   │── 📄 logout.php             # Logout functionality
│   │── 📄 register.php           # Admin registration (if required)
│── 📂 database/                  # Database scripts
│   │── 📄 schema.sql             # Database structure
│   │── 📄 seed.sql               # Sample data for testing
│── 📂 api/                       # API endpoints for AJAX calls
│   │── 📄 fetch_seats.php        # Fetches seat allocation data
│   │── 📄 update_allocation.php  # Updates seat assignment dynamically
│── 📂 docs/                      # Documentation and Guides
│   │── 📄 README.md              # Project documentation
│   │── 📄 INSTALLATION.md        # Setup and installation guide
│── 📂 tests/                     # Testing files (Optional)
│   │── 📄 test_allocation.php    # Unit tests for seat allocation logic
│── 📄 .gitignore                 # Ignore unnecessary files in Git
│── 📄 LICENSE                    # Project license file


```
### **Basic Seat Allocation Files**
- **`seat_allocation.php`** – Main file handling seat allocation logic.
- **`assign_seats.php`** – Processes and assigns seats to students.
- **`generate_seating_plan.php`** – Generates a seating arrangement dynamically.
- **`seat_distribution.php`** – Manages the distribution of available seats.
- **`exam_hall_allocation.php`** – Allocates students to exam halls.

### **Advanced Seat Management Files**
- **`validate_seat_allocation.php`** – Ensures seat distribution follows rules.
- **`seat_availability.php`** – Checks and updates available seats.
- **`update_seat_arrangement.php`** – Modifies the seating plan when needed.
- **`reserve_seats.php`** – Handles reserved seating for special cases.
- **`export_seating_chart.php`** – Exports the seating plan as a PDF or Excel file.

## 🛠️ Technologies Used
- **Backend**: PHP (Core PHP, PDO for database interaction)
- **Frontend**: HTML, CSS, Bootstrap 5.3.3, JavaScript, jQuery (latest)
- **Icons & UI Enhancements**: Font Awesome (latest)
- **Database**: MySQL
- **Version Control**: Git & GitHub

## 📦 Installation & Usage
### **Requirements**
- PHP 7.4+
- MySQL (Optional, if storing seat allocations in a database)
- Web Server (Apache/Nginx)
- Composer (for dependency management)
- Git (optional but recommended)

### **Installation**
1. Clone the repository:
   ```sh
   git clone https://github.com/your-username/student-seat-allocation.git
   cd student-seat-allocation
   ```
2. Configure database settings in `config.php` (if applicable).
3. Run the application on a local server:
   ```sh
   php -S localhost:8000
   ```

## 📌 Usage
- Open `seat_allocation.php` in a browser to start allocating seats.
- Use `generate_seating_plan.php` to create a seating arrangement.
- Modify `update_seat_arrangement.php` to rearrange seats when needed.
- Export seating data using `export_seating_chart.php`.

## 🎨 Assets & Plugins
- **Bootstrap 5.3.3**: Provides a responsive and modern UI.
- **jQuery (latest)**: Enhances dynamic interactions and AJAX-based updates.
- **Font Awesome (latest)**: Adds icons for a visually appealing interface.
- **DataTables.js**: Enhances table functionalities for sorting and filtering.
- **SweetAlert2**: Provides modern and interactive alert popups.

## 🤝 Contributing
Contributions are welcome! Feel free to fork this repository and submit a pull request with improvements.

## 📝 License
This project is licensed under the [MIT License](LICENSE).

