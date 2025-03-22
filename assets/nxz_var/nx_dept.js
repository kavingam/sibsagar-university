
// class DepartmentManager {
//     constructor(apiUrl) {
//         this.apiUrl = apiUrl;
//         this.init();
//     }

//     // Initialize event listeners
//     init() {
//         $(document).ready(() => {
//             this.fetchDepartments();

//             $("#departmentForm").on("submit", (e) => this.addDepartment(e));
//             $("#editDepartmentForm").on("submit", (e) => this.updateDepartment(e));
//         });
//     }

//     // Fetch Departments and populate table
//     fetchDepartments() {
//         $.get(`${this.apiUrl}/nxo_dept.php`, (response) => {
//             $("#departmentTableBody").html(response);
//             this.attachEventListeners();
//         });
//     }

//     // Attach event listeners to dynamically generated buttons
//     attachEventListeners() {
//         $(".edit-btn").on("click", (e) => this.editDepartment(e));
//         $(".delete-btn").on("click", (e) => this.deleteDepartment($(e.currentTarget).data("id")));
//     }

//     // Add a new department
//     addDepartment(event) {
//         event.preventDefault();
//         let departmentName = $("#departmentName").val().trim();
        
//         if (!departmentName) {
//             alert("⚠️ Department name cannot be empty.");
//             return;
//         }

//         $.post(`${this.apiUrl}/nxa_dept.php`, { department_name: departmentName }, (response) => {
//             let data = this.safeJsonParse(response);
//             if (data.status === "success") {
//                 alert("✅ Department added successfully.");
//                 $("#departmentName").val("");
//                 this.fetchDepartments();
//             } else {
//                 alert("⚠️ " + data.message);
//             }
//         });
//     }

//     // Open edit modal and pre-fill form
//     editDepartment(event) {
//         let btn = $(event.currentTarget);
//         $("#editDepartmentId").val(btn.data("id"));
//         $("#editDepartmentName").val(btn.data("name"));
//         $("#editDepartmentModal").modal("show");
//     }

//     // Update an existing department
//     updateDepartment(event) {
//         event.preventDefault();
        
//         let departmentId = $("#editDepartmentId").val();
//         let departmentName = $("#editDepartmentName").val().trim();

//         if (!departmentName) {
//             alert("⚠️ Department name cannot be empty.");
//             return;
//         }

//         $.post(`${this.apiUrl}/edit_department.php`, { department_id: departmentId, department_name: departmentName }, (response) => {
//             let data = this.safeJsonParse(response);
//             if (data.status === "success") {
//                 alert("✅ Department updated successfully.");
//                 $("#editDepartmentModal").modal("hide");
//                 this.fetchDepartments();
//             } else {
//                 alert("⚠️ " + data.message);
//             }
//         });
//     }

//     // Delete a department with confirmation
//     deleteDepartment(departmentId) {
//         if (!confirm("❌ Are you sure you want to delete this department?")) return;

//         fetch(`${this.apiUrl}/nxr_dept.php`, {
//             method: "POST",
//             headers: { "Content-Type": "application/x-www-form-urlencoded" },
//             body: new URLSearchParams({ department_id: departmentId })
//         })
//         .then(response => response.text())
//         .then(text => {
//             let data = this.safeJsonParse(text);
//             if (data.status === "success") {
//                 alert("✅ " + data.message);
//                 this.fetchDepartments();
//             } else {
//                 alert("⚠️ " + (data.message || "Unknown error occurred."));
//             }
//         })
//         .catch(error => console.error("❌ Fetch error:", error));
//     }

//     // Utility function: Safe JSON parsing
//     safeJsonParse(text) {
//         try {
//             return JSON.parse(text);
//         } catch (error) {
//             console.error("❌ JSON Parse Error:", error, "Response:", text);
//             alert("⚠️ Server returned an invalid response.");
//             return { status: "error", message: "Invalid server response." };
//         }
//     }
// }

// // Initialize the DepartmentManager class with API path
// const departmentManager = new DepartmentManager("xyz/dept");


class DepartmentManager {
    constructor(apiUrl) {
        this.apiUrl = apiUrl;
        this.init();
    }

    init() {
        $(document).ready(() => {
            this.fetchDepartments();

            $("#departmentForm").on("submit", (e) => this.addDepartment(e));
            $("#editDepartmentForm").on("submit", (e) => this.updateDepartment(e));
        });
    }

    fetchDepartments() {
        $.get(`${this.apiUrl}/nxo_dept.php`, (response) => {
            $("#departmentTableBody").html(response);
            this.attachEventListeners();
        });
    }

    attachEventListeners() {
        $(".edit-btn").on("click", (e) => this.editDepartment(e));
        $(".delete-btn").on("click", (e) => this.deleteDepartment($(e.currentTarget).data("id")));
    }

    addDepartment(event) {
        event.preventDefault();
        let departmentName = $("#departmentName").val().trim();

        if (!departmentName) {
            Swal.fire("⚠️ Error", "Department name cannot be empty.", "warning");
            return;
        }

        $.post(`${this.apiUrl}/nxa_dept.php`, { department_name: departmentName }, (response) => {
            let data = this.safeJsonParse(response);
            if (data.status === "success") {
                Swal.fire("✅ Success", "Department added successfully.", "success");
                $("#departmentName").val("");
                this.fetchDepartments();
            } else {
                Swal.fire("⚠️ Error", data.message, "error");
            }
        });
    }

    editDepartment(event) {
        let btn = $(event.currentTarget);
        $("#editDepartmentId").val(btn.data("id"));
        $("#editDepartmentName").val(btn.data("name"));
        $("#editDepartmentModal").modal("show");
    }

    updateDepartment(event) {
        event.preventDefault();

        let departmentId = $("#editDepartmentId").val();
        let departmentName = $("#editDepartmentName").val().trim();

        if (!departmentName) {
            Swal.fire("⚠️ Error", "Department name cannot be empty.", "warning");
            return;
        }

        $.post(`${this.apiUrl}/edit_department.php`, { department_id: departmentId, department_name: departmentName }, (response) => {
            let data = this.safeJsonParse(response);
            if (data.status === "success") {
                Swal.fire("✅ Success", "Department updated successfully.", "success");
                $("#editDepartmentModal").modal("hide");
                this.fetchDepartments();
            } else {
                Swal.fire("⚠️ Error", data.message, "error");
            }
        });
    }

    deleteDepartment(departmentId) {
        Swal.fire({
            title: "❌ Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`${this.apiUrl}/nxr_dept.php`, {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({ department_id: departmentId })
                })
                .then(response => response.text())
                .then(text => {
                    let data = this.safeJsonParse(text);
                    if (data.status === "success") {
                        Swal.fire("✅ Deleted!", data.message, "success");
                        this.fetchDepartments();
                    } else {
                        Swal.fire("⚠️ Error", data.message || "Unknown error occurred.", "error");
                    }
                })
                .catch(error => console.error("❌ Fetch error:", error));
            }
        });
    }

    safeJsonParse(text) {
        try {
            return JSON.parse(text);
        } catch (error) {
            console.error("❌ JSON Parse Error:", error, "Response:", text);
            Swal.fire("⚠️ Error", "Server returned an invalid response.", "error");
            return { status: "error", message: "Invalid server response." };
        }
    }
}

// Initialize
const departmentManager = new DepartmentManager("xyz/dept");

