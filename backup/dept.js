$(document).ready(function () {
    fetchDepartments();
});

// Fetch Departments
function fetchDepartments() {
    $.get("xyz/dept/nxo_dept.php", function (response) {
        $("#departmentTableBody").html(response);

        // Attach event listeners to dynamically generated buttons
        $(".edit-btn").on("click", function () {
            let deptId = $(this).data("id");
            let deptName = $(this).data("name");

            $("#editDepartmentId").val(deptId);
            $("#editDepartmentName").val(deptName);
            $("#editDepartmentModal").modal("show");
        });

        $(".delete-btn").on("click", function () {
            let deptId = $(this).data("id");
            deleteDepartment(deptId);
        });
    });
}

// Add Department
$("#departmentForm").on("submit", function (e) {
    e.preventDefault();

    let departmentName = $("#departmentName").val().trim();
    if (departmentName === "") {
        alert("Department name cannot be empty.");
        return;
    }

    $.post("xyz/dept/nxa_dept.php", { department_name: departmentName }, function (response) {
        let data = JSON.parse(response);
        if (data.status === "success") {
            alert("Department added successfully.");
            $("#departmentName").val("");
            fetchDepartments();
        } else {
            alert("Error: " + data.message);
        }
    });
});

// Handle Edit Form Submission
$("#editDepartmentForm").on("submit", function (e) {
    e.preventDefault();

    let departmentId = $("#editDepartmentId").val();
    let departmentName = $("#editDepartmentName").val().trim();

    if (departmentName === "") {
        alert("Department name cannot be empty.");
        return;
    }

    $.post("procs/edit_department.php", { department_id: departmentId, department_name: departmentName }, function (response) {
        let data = JSON.parse(response);
        if (data.status === "success") {
            alert("Department updated successfully.");
            $("#editDepartmentModal").modal("hide");
            fetchDepartments();
        } else {
            alert("Error: " + data.message);
        }
    });
});

function deleteDepartment(departmentId) {
    if (!confirm("Are you sure you want to delete this department?")) return;

    fetch("xyz/dept/nxr_dept.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ department_id: departmentId })
    })
    .then(response => response.text()) // ✅ Read response as text first
    .then(text => {
        try {
            let data = JSON.parse(text); // ✅ Convert text to JSON safely
            if (!data.status) throw new Error("Invalid response format"); // Handle missing JSON keys

            if (data.status === "success") {
                alert("✅ " + data.message);
                fetchDepartments(); // Refresh department list
            } else {
                alert("⚠️ " + (data.message || "Unknown error occurred."));
            }
        } catch (error) {
            console.error("❌ JSON Parse Error:", error, "Response:", text);
            alert("⚠️ Server returned an invalid response.");
        }
    })
    .catch(error => console.error("❌ Fetch error:", error));
} 