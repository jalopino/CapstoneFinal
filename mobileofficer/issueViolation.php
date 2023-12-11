<?php include __DIR__ . '/../static/header.php';?>
<?php
require("conn.php");
require __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\Alignment\LabelAlignmentLeft;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

use Infobip\configuration;
use Infobip\Api\SmsApi;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
use Infobip\Model\SmsAdvancedTextualRequest;

$response = array();

// Check if all required POST data is set
if (isset($_POST["officername"]) && isset($_POST["drivername"]) &&  isset($_POST["licensenum"]) &&
    isset($_POST["tov"]) && isset($_POST["date"]) && isset($_POST["phone"])) {

    $officername = mysqli_real_escape_string($connect, $_POST["officername"]);
    $drivername = mysqli_real_escape_string($connect, $_POST["drivername"]);
    $licensenum = mysqli_real_escape_string($connect, $_POST["licensenum"]);
    $tov = mysqli_real_escape_string($connect, $_POST["tov"]);
    $date = mysqli_real_escape_string($connect, $_POST["date"]);
    $phone_no = mysqli_real_escape_string($connect, $_POST["phone"]);
    $seminar = NULL;
    
    // Fetch officer_id based on officername
    $sql = "SELECT officer_id FROM officer WHERE username = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("s", $officername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $officer_id = $row['officer_id'];

        // Fetch info_id based on licensenum
        // Assuming licensenum is related to user_information
        $sql = "SELECT info_id FROM user_information WHERE license_no = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("s", $licensenum);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $info_id = $row['info_id'];

            // Fetch user_id based on info_id
            // Assuming user_id is related to users
            $sql = "SELECT user_id FROM users WHERE info_id = ?";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("i", $info_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $user_id = $row['user_id'];
                $status = "Pending";
                $qr_id = uniqid();
                // Insert data into violations table
                $sql = "INSERT INTO violations (user_id, officer_id, name, violation, date, phone_no, qr, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("iissssss", $user_id, $officer_id, $drivername, $tov, $date, $phone_no, $qr_id, $status);
                if ($stmt->execute()) {
                    $violation_id = $connect->insert_id;
                    $response["success"] = 1;
                    $response["message"] = "Violation recorded successfully";
                    $qr_url = "capstone/violations/viewviolation.php?violation_id=$violation_id&qr=$qr_id";
                    $sql = "UPDATE violations SET qr_url = ? WHERE violation_id = ?";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("si", $qr_url, $violation_id);
                    $stmt->execute();
                } else {
                    $response["error"] = "Error recording violation: " . $stmt->error;
                }
            } else {
                $response["error"] = "User ID not found for given license number";
            }
        } else {
            $response["error"] = "Info ID not found for given license number";
        }
    } else {
        $response["error"] = "Officer not found for given username";
    }
} else {
    $response["error"] = "Missing required fields";
}
    echo json_encode($response);
    //SQL check violation count
    $sql = "SELECT COUNT(*) as violation_count FROM violations WHERE user_id = ? AND violation = ?";
    if ($stmt = $connect->prepare($sql)) {
        $stmt->bind_param("is", $user_id, $tov); // "i" for integer (user_id), "s" for string (violation)
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        $violationCount = $row['violation_count'];
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
    
    // Check fine
    switch ($tov) {
        case 'Disregarding Traffic Signs':
            $fine = 1000;
            break;
        case 'Illegal Parking (Attended)':
            $fine = 1000;
            break;
        case 'Illegal Parking (Unattended)':
            $fine = 2000;
            break;
        case 'Unified Vehicle Reduction Program':
            $fine = 500;
            break;
        case 'Truck Ban':
            $fine = 3000;
            break;
        case 'Truck Ban (Light)':
            $fine = 2000;
            break;
        case 'Reckless Driving':
            if ($violationCount == 1) {
                $fine = 1000;
            } else if ($violationCount == 2) {
                $fine = 2000;
            } else if ($violationCount == 3) {
                $fine = 3000;
                $seminar = 1;
            } else {
                $fine = 1000;
            }
            break;
        case 'Tricycle Ban':
            $fine = 500;
            break;
        case 'Obstruction':
            $fine = 1000;
            break;
        case 'Dress Code for Motorcycle Riders':
            if ($violationCount == 1) {
                $fine = 500;
            } else if ($violationCount == 2) {
                $fine = 700;
            } else if ($violationCount == 3) {
                $fine = 1000;
            } else {
                $fine = 500;
            }
            break;
        case 'Overloading':
            $fine = 1000;
            break;
        case 'Defective Motor Vehicles Accessories':
            $fine = 1000;
            break;
        case 'Unauthorized Modification':
            $fine = 2000;
            break;
        case 'Arrogance/ Discourteous Conduct':
            $fine = 500;
            break;
        case 'Loading/Unloading in Prohibited Zone':
            $fine = 1000;
            break;
        case 'Illegal Counterflow':
            if ($violationCount == 1) {
                $fine = 2000;
            } else if ($violationCount == 2) {
                $fine = 5000;
            } else if ($violationCount == 3) {
                $fine = 5000;
            } else {
                $fine = 2000;
            }
            break;
        case 'Over Speeding':
            $fine = 1000;
            break;
        case 'Failure to use Seatbelt':
            $fine = 1000;
            break;
        case 'Failure to use Child Restraint System':
            if ($violationCount == 1) {
                $fine = 1000;
            } else if ($violationCount == 2) {
                $fine = 2000;
            } else if ($violationCount == 3) {
                $fine = 5000;
            } else {
                $fine = 1000;
            }
            break;
        case 'Use of Substandard Child Restraint System':
            if ($violationCount == 1) {
                $fine = 1000;
            } else if ($violationCount == 2) {
                $fine = 3000;
            } else if ($violationCount == 3) {
                $fine = 5000;
            } else {
                $fine = 1000;
            }
            break;
        case 'No Motorcycle Helmet':
            if ($violationCount == 1) {
                $fine = 3000;
            } else if ($violationCount == 2) {
                $fine = 5000;
            } else if ($violationCount == 3) {
                $fine = 5000;
            } else if ($violationCount == 4) {
                $fine = 10000;
            } else {
                $fine = 3000;
            }
            break;
        case 'Use of Helmet with no ICC Sticker/Mark':
            if ($violationCount == 1) {
                $fine = 3000;
            } else if ($violationCount == 2) {
                $fine = 5000;
            } else if ($violationCount == 3) {
                $fine = 5000;
            } else {
                $fine = 3000;
            }
            break;
        case 'Violation of Children Safety in Motorcycle Act':
            if ($violationCount == 1) {
                $fine = 3000;
            } else if ($violationCount == 2) {
                $fine = 5000;
            } else if ($violationCount == 3) {
                $fine = 10000;
            } else {
                $fine = 3000;
            }
            break;
        case 'Anti-Distracted Driving Act (Private)':
            if ($violationCount == 1) {
                $fine = 5000;
            } else if ($violationCount == 2) {
                $fine = 10000;
            } else if ($violationCount == 3) {
                $fine = 15000;
            } else {
                $fine = 1000;
            }
            break;
        case 'Anti-Distracted Driving Act (PUV/PUJ)':
            if ($violationCount == 1) {
                $fine = 5000;
            } else if ($violationCount == 2) {
                $fine = 10000;
            } else if ($violationCount == 3) {
                $fine = 15000;
            } else if ($violationCount == 4) {
                $fine = 20000;
            } else {
                $fine = 5000;
            }
            break;
        default:
            $fine = 1000;
            break;
    }
    //Insert Fine
    $sql = "UPDATE violations SET fine = ? WHERE violation_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("di", $fine, $violation_id); // "d" for double (fine), "i" for integer (violation_id)
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo "Fine updated successfully for violation ID $violation_id.";
        } else {
            echo "No record updated. Please check the violation ID.";
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    if (isset($seminar)) {
        //Insert Seminar
        $sql = "UPDATE violations SET seminar = ? WHERE violation_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ii", $seminar, $violation_id); 
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                echo "Fine updated successfully for violation ID $violation_id.";
            } else {
                echo "No record updated. Please check the violation ID.";
            }
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }

    //QR step!
    if ($response["success"]) {
        $url = $base . $qr_url;
        $qr_code = QrCode::create($url)
                        ->setSize(600)
                        ->setMargin(40)
                        ->setForegroundColor(new Color(0, 0, 0))
                        ->setBackgroundColor(new Color(204, 229, 255))
                        ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh);

        $writer = new PngWriter;
        $result = $writer->write($qr_code);
        // Save the image to a file
        $uniqueqr = __DIR__ . "/../qrcodes/" . $qr_id . ".png";
        $result->saveToFile($uniqueqr);

        //SMS step!
        $message = "E-Trafickets violation notification.

Name: $drivername
Issued By: $officername
Violation: $tov
No. of offense: $violationCount
Fine: ₱$fine
Date Issued: $date
        
For more details you may visit this link: $url

Do not share your link and qr code to anyone!";

        $base_url = "https://1vxzen.api.infobip.com";
        $api_key = "a71eff0621e210796e943821435f599f-76c8272e-eb42-4d68-b9dc-488a3d496302";
        $configuration = new Configuration(host: $base_url, apiKey: $api_key);
        $api = new SmsApi(config: $configuration);
        $destination = new SmsDestination(to: $phone_no);
        $message = new SmsTextualMessage(
            destinations: [$destination],
            text: $message
        );
        $request = new SmsAdvancedTextualRequest(messages: [$message]);
        $response = $api->sendSmsMessage($request);
    }
$stmt->close();
$connect->close();
?>
