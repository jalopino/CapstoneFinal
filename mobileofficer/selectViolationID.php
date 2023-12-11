<?php
require("conn.php");

$username = $_REQUEST['code']; // The officer's username

// SQL to get officer_id from the officer table
$sqlOfficer = "SELECT officer_id FROM officer WHERE username = ?";
$response = array();

try {
    $stmt = $connect->prepare($sqlOfficer);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $resultOfficer = $stmt->get_result();

    if ($resultOfficer->num_rows > 0) {
        $rowOfficer = $resultOfficer->fetch_assoc();
        $officerId = $rowOfficer['officer_id'];

        // Fetching detailed violations for this officer_id
        $sqlViolations = "SELECT * FROM violations WHERE officer_id = ?";
        $stmt = $connect->prepare($sqlViolations);
        $stmt->bind_param("i", $officerId);
        $stmt->execute();
        $resultViolations = $stmt->get_result();

        $violations = array();
        if ($resultViolations->num_rows > 0) {
            while ($rowViolation = $resultViolations->fetch_assoc()) {
                array_push($violations, $rowViolation);
            }
            $response["success"] = 1;
            $response["violations"] = $violations;
        } else {
            $response["success"] = 0;
            $response["message"] = "No violations found for this officer.";
        }
    } else {
        $response["success"] = 0;
        $response["message"] = "Officer not found.";
    }
} catch (Exception $e) {
    $response["success"] = 0;
    $response["message"] = "Database Error: " . $e->getMessage();
}

echo json_encode($response);
?>
