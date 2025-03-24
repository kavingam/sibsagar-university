<?php

class SeatAllocation extends BaseModel {
    
    // 🔥 Assign Seats Automatically
    public function allocateSeats() {
        return "hello"; // Return instead of echo
    }

    // ✅ Get All Allocations
    public function getAllAllocations() {
        return "hello";
    }
}

// ✅ Usage Example
$seatAlloc = new SeatAllocation();
$result = $seatAlloc->allocateSeats();
print_r($result); // Output: hello
?>
