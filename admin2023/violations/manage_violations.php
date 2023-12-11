<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/../../admin2023/static/header.php';?>
    <?php include __DIR__ . '/../../admin2023/templates/restrict_nonadmin.php';?>
    <title>Manage Violations</title>
</head>
<body>
    <div class="background-container" style="display: flex; flex-direction: column; height: 100vh;">
        <div style="display: flex; justify-content: flex-end; padding: 20px;">
            <form method="get" action="<?php echo $base; ?>capstone/admin2023/violations/manage_violations.php" style="text-align: center;">
                <h1 style="font-weight: bold; color: #000080;">Search violations<i class="fa fa-search" style="padding-left: 20px;"></i></h1>
                <input type="text" name="search" placeholder="Enter here" style="border: 1px solid black; width: 300px;">
                <input type="submit" value="Search" class="button">
            </form>
        </div>
        <div class="background-container" style="flex: 1;">
            <div class="row">
                <div class="column" style="padding: 20px;">
                <?php
                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Define search term
                $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

                // SQL with LIKE operator if the search term exists
                if ($searchTerm) {
                    $query = "SELECT violation_id, user_information.first_name, user_information.last_name, violations.user_id, violation, date, status, fine 
                    FROM violations
                    INNER JOIN users ON users.user_id = violations.user_id
                    INNER JOIN user_information ON users.info_id = user_information.info_id 
                    INNER JOIN officer ON officer.officer_id = violations.officer_id
                    WHERE 
                    violation LIKE ? OR 
                    user_information.first_name LIKE ? OR 
                    user_information.last_name LIKE ?";
                    $stmt = $conn->prepare($query);
                    $searchTermWithWildcards = '%' . $searchTerm . '%';
                    $stmt->bind_param("sss", $searchTermWithWildcards, $searchTermWithWildcards, $searchTermWithWildcards);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } else {
                    // If there's no search term, fetch all violations for the logged-in user.
                    $query = "SELECT violation_id, user_information.first_name, user_information.last_name, violations.user_id, violation, date, status, fine 
                    FROM violations
                    INNER JOIN users ON users.user_id = violations.user_id
                    INNER JOIN user_information ON users.info_id = user_information.info_id 
                    INNER JOIN officer ON officer.officer_id = violations.officer_id";
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                    $result = $stmt->get_result();
                }

                // Display results in a table if there are any matches.
                echo "<div class='table-container'>";
                echo "<table border='1'>";
                echo "<tr><th>Violation ID</th><th>Violator</th><th>Violation</th><th>Date</th><th>Status</th><th>Fine</th><th>Actions</th></tr>";
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['violation_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['violation']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                        echo "<td>₱" . htmlspecialchars($row['fine']) . "</td>";
                        echo "<td><a href='". $base ."capstone/admin2023/violations/viewviolation.php?violation_id=" . $row['violation_id'] . "' target='_blank' class='a-button'>View</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<h1>No related violations found from search.</h1>";
                }
                echo "</table>";
                echo "</div>";

                // Close the statement and connection if they were opened
                if (isset($stmt)) {
                    $stmt->close();
                }
                $conn->close();
                ?>

            </div>
        </div>
    </div>
</body>
</html>