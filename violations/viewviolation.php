<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/../static/header.php';?>
    <title>Welcome, <?php echo $_SESSION["username"];?></title>
</head>
<body>
<?php
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Get the violation_id from the URL parameter.wwwwwww
$violation_id = isset($_GET['violation_id']) ? intval($_GET['violation_id']) : 0;
$qr = $_GET['qr'];
// Fetch the violation details if violation_id is provided.
if ($violation_id) {
    $query = "SELECT * 
    FROM violations
    INNER JOIN users ON users.user_id = violations.user_id
    INNER JOIN user_information ON users.info_id = user_information.info_id 
    INNER JOIN officer ON officer.officer_id = violations.officer_id  
    WHERE violation_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $violation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $violationDetails = $result->fetch_assoc();
}

if($_SESSION['user_id'] != $violationDetails['user_id']) {
    if ($qr == $violationDetails['qr']) {
        echo '<p style="padding: 20px;">Do not share your QR code or this link!</p>';
    } else {
        header("Location: {$base}capstone/dashboard/home.php");
        exit();
    }
}

?>
<div class="login-container">
    <div class="loginform">
    <?php
    // Check if violation details are available
    if (isset($violationDetails)) {
        // Display violation details
        echo '<img src="' . $base . "capstone/qrcodes/" . htmlspecialchars($violationDetails['qr']) . ".png" . '" alt="QR Code" width="150" height="150" />';
        echo "<h1 style='font-weight: bold;'>Violation Details</h1>";
        echo "<p>Violation ID: " . htmlspecialchars($violationDetails['violation_id']) . "</p>";
        echo "<p>Name of Violator: " . htmlspecialchars($violationDetails['first_name'] . " " . $violationDetails['last_name']) . "</p>";
        echo "<p>Officer ID: " . htmlspecialchars($violationDetails['officer_id']) . "</p>";
        echo "<p>Issued By: " . htmlspecialchars($violationDetails['fname'] . " " . $violationDetails['lname']) . "</p>";
        echo "<p>Violation: " . htmlspecialchars($violationDetails['violation']) . "</p>";
        echo "<p>Date: " . htmlspecialchars($violationDetails['date']) . "</p>";
        echo "<p>Status: " . htmlspecialchars($violationDetails['status']) . "</p>";
        echo "<p>Fine: ₱" . htmlspecialchars($violationDetails['fine']) . "</p>";
        if (!$violationDetails['seminar'] == NULL) {
            echo "Seminar must be attended.";
        }
        if (!$violationDetails['dispute'] == NULL) {
            echo "<p>Dispute: " . htmlspecialchars($violationDetails['dispute']) . "</p>";
        }
        if (!$violationDetails['due_date'] == NULL) {
            echo "<p>Due: " . htmlspecialchars($violationDetails['due_date']) . "</p>";
        }
    } else {
        echo "<p>No violation found.</p>";
    }
    ?>
    <?php if ($violationDetails['dispute'] == NULL) {
        echo '
        <div class="dispute-form">
            <form id="disputeForm" action="" method="post">
                <button style="border-radius: 5px;" class="button" id="disputeButton" type="button" onclick="showInput()">Dispute</button>
                <div id="disputeInput" style="display:none;">
                    <input type="text" name="disputeText" id="disputeText" placeholder="Enter your dispute here...">
                    <input type="submit" value="Submit" onclick="return confirmSubmit()">
                    <p id="disputeError" style="color: red; display: none;">Please enter your dispute text.</p>
                    <p style="color: red;">Note: You can only submit one dispute.</p>
                </div>
            </form>
        </div>';
    }
    ?>
        <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $violationId = $violationDetails['violation_id'];
    $disputeText = $_POST['disputeText'];

    // Check connection
     if ($conn->connect_error) {
         die("Connection failed: " . $conn->connect_error);
     }

     //Prepare and bind
     $stmt = $conn->prepare("UPDATE violations SET dispute = ? WHERE violation_id = ?");
     $stmt->bind_param("si", $disputeText, $violationId);

     //Execute the query
     $stmt->execute();
     echo "<h3 class='color: green;'>";
     echo "Dispute submitted for violation ID: " . htmlspecialchars($violationId);
     echo "</h3>";
     //Close statement and connection
     $stmt->close();
     $conn->close();
}
?>
    </div>
</div>
<script>
    document.getElementById('disputeForm').onsubmit = function(event) {
        var disputeText = document.getElementById('disputeText').value.trim();
        var disputeError = document.getElementById('disputeError');

        if (disputeText === "") {
            disputeError.style.display = 'block';
            disputeError.textContent = 'Please enter your dispute text.';
            event.preventDefault(); // Prevent form submission
        } else {
            disputeError.style.display = 'none';
        }
    };
    function showInput() {
        document.getElementById('disputeButton').onclick = function() {
            var inputDiv = document.getElementById('disputeInput');
            if (inputDiv.style.display === 'none') {
                inputDiv.style.display = 'block';
            } else {
                inputDiv.style.display = 'none';
            }
        };
    }
    function confirmSubmit() {
        var agree = confirm("Are you sure you want to submit this information?");
        return agree;
    }

</script>
</body>
<?php include __DIR__ . '/../static/footer.php';?>
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
    }
    .login-container {
        width: 60%;
        margin: 0 auto;
        padding: 20px;
    }
    .dispute-form input[type="text"], .dispute-form input[type="submit"] {
        width: 100%;
        margin: 10px 0;
        padding: 10px;
    }
    .dispute-form input[type="submit"] {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .dispute-form input[type="submit"]:hover {
        background-color: #0056b3;
    }

    @media screen and (max-width: 600px) {
        .login-container {
            width: 100%;
            padding: 10px;
        }
    }

</style>
</html>
