<?php include __DIR__ . '/../static/header.php';?>
<?php include __DIR__ . '/../templates/restrict_anon.php';?>
<?php 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $insert;
    $user_id = $_SESSION['user_id'];
    // Get user input from the registration form
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $mname = $_POST['mname'];
    $nationality = $_POST['nationality'];
    $sex = $_POST['sex'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $weight = $_POST['weight'];
    $height = $_POST['height'];
    $bloodtype = $_POST['bloodtype'];
    $eyecolor = $_POST['eyecolor'];
    // License
    $license = $_POST['license'];

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    //Check if user already has info_id 
    $sql = "SELECT info_id FROM users WHERE user_id = ?";
    $stmt_info_id_exist = $conn->prepare($sql);
    $stmt_info_id_exist ->bind_param("i", $user_id);
    if ($stmt_info_id_exist->execute()) {
        $result = $stmt_info_id_exist->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $info_id_exist = $row['info_id'];
            if ($info_id_exist === null) {
                echo "user_id exists but does not have an info_id (info_id is NULL)";
                $insert = true;
            } else {
                echo "user_id has info_id: $info_id_exist";
                $insert = false;
            }
        } else {
            echo "user_id doesn't exist in the users table.";
        }
    } else {
        echo "Error executing user_id query: " . $stmt_info_id_exist->error;
    }
    // Check license exists
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM user_information WHERE license_no = ?");
    $stmt->bind_param("s", $license);
    $stmt->execute();
    $result = $stmt->get_result();
    $row_license = $result->fetch_assoc();
    //Check if user 
    if ($row_license['count'] > 0) {
        // License in database already
        echo "License already in database";
        // Find info_id based on new_license_num
        if ($insert == true) {
            echo 'License is currently registered to another account!';
        } else if ($insert == false && isset($row['info_id'])) {    
            //check if other info_id has it
            // SQL to select the license number
            // Prepare SQL statement
            $stmt_check_other_info = $conn->prepare("SELECT info_id FROM user_information WHERE license_no = ?");
            if ($stmt_check_other_info === false) {
                die("Error preparing statement: " . $conn->error);
            }
            $stmt_check_other_info->bind_param("s", $license); // 's' indicates the type is a string
            $stmt_check_other_info->execute();
            $stmt_check_other_info->store_result();
            $stmt_check_other_info->bind_result($info_id);
            $stmt_check_other_info->fetch();
            $stored_info_id = $info_id;
             
            if ($stored_info_id != $row['info_id']) {
                echo "Did not update! license already in database!";
            } else {
                echo "Does not exist!";
                $update = true;
            }

            if ($update) {
                $update_stmt = "UPDATE user_information 
                SET first_name = ?, last_name = ?, middle_name = ?, nationality = ?, sex = ?, dob = ?, address = ?, license_no = ?, blood_type = ?, eye_color = ?, height = ?, weight = ? 
                WHERE info_id = ?";
                $update_stmt = $conn->prepare($update_stmt);
                $update_stmt->bind_param("ssssssssssddi", $fname, $lname, $mname, $nationality, $sex, $dob, $address, $license, $bloodtype, $eyecolor, $height, $weight, $info_id_exist);
                if ($update_stmt->execute()) {
                    echo "User information updated successfully.";
                } else {
                    echo "Error updating user information: " . $update_stmt->error;
                }
            }
        } else {
            $sql_find_info_id = "SELECT info_id FROM user_information WHERE license_no = ?";
            $stmt_find_info_id = $conn->prepare($sql_find_info_id);
            $stmt_find_info_id->bind_param("s", $license);
            if ($stmt_find_info_id->execute()) {
                $result = $stmt_find_info_id->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $info_id = $row['info_id'];
                    // If the user_information update was successful, update the users table info_id
                    $sql_users = "UPDATE users SET info_id = ? WHERE user_id = ?";
                    $stmt_users = $conn->prepare($sql_users);
                    $stmt_users->bind_param("ii", $info_id, $user_id);
                    if ($stmt_users->execute()) {
                        $conn->commit();
                        echo "License number and info_id updated successfully.";
                        $sql = "UPDATE user_information 
                        SET first_name = ?, last_name = ?, middle_name = ?, nationality = ?, sex = ?, dob = ?, address = ?, blood_type = ?, eye_color = ?, height = ?, weight = ? 
                        WHERE license_no = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sssssssssdds", $fname, $lname, $mname, $nationality, $sex, $dob, $address, $bloodtype, $eyecolor, $height, $weight, $license);
                        if ($stmt->execute()) {
                            echo "User information updated successfully.";
                        } else {
                            echo "Error updating user information: " . $stmt->error;
                        }
                    } else {
                        $conn->rollback();
                        echo "Error updating info_id in the users table: " . $stmt_users->error;
                    }
                } else {
                    echo "No matching info_id found for the provided new license number.";
                    // Handle the case where the new_license_num doesn't exist in the users table.
                }
            } else {
                echo "Error executing info_id query: " . $stmt_find_info_id->error;
            }
        }
    } else {
        if ($insert == true) {
       // insert the user's license number.
       $sql = "INSERT INTO user_information (first_name, last_name, middle_name, nationality, sex, dob, address, license_no, blood_type, eye_color, height, weight) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
       $stmt = $conn->prepare($sql);
       $stmt->bind_param("ssssssssssdd", $fname, $lname, $mname, $nationality, $sex, $dob, $address, $license, $bloodtype, $eyecolor, $height, $weight);
       if ($stmt->execute()) {
         // Find info_id based on new_license_num
            $sql_find_info_id = "SELECT info_id FROM user_information WHERE license_no = ?";
            $stmt_find_info_id = $conn->prepare($sql_find_info_id);
            $stmt_find_info_id->bind_param("s", $license);

            if ($stmt_find_info_id->execute()) {
                $result = $stmt_find_info_id->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $info_id = $row['info_id'];
                    // If the user_information update was successful, update the users table info_id
                    $sql_users = "UPDATE users SET info_id = ? WHERE user_id = ?";
                    $stmt_users = $conn->prepare($sql_users);
                    $stmt_users->bind_param("ii", $info_id, $user_id);
                    if ($stmt_users->execute()) {
                        $conn->commit();
                        echo "License number and info_id updated successfully.";
                    } else {
                        $conn->rollback();
                        echo "Error updating info_id in the users table: " . $stmt_users->error;
                    }
                } else {
                    echo "No matching info_id found for the provided new license number.";
                    // Handle the case where the new_license_num doesn't exist in the users table.
                }
            } else {
                echo "Error executing info_id query: " . $stmt_find_info_id->error;
            }
       } else {
           echo "Error updating license number: " . $stmt->error;
       }
    } else {
        $sql = "UPDATE user_information 
        SET first_name = ?, last_name = ?, middle_name = ?, nationality = ?, sex = ?, dob = ?, address = ?, license_no = ?, blood_type = ?, eye_color = ?, height = ?, weight = ? 
        WHERE info_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssddi", $fname, $lname, $mname, $nationality, $sex, $dob, $address, $license, $bloodtype, $eyecolor, $height, $weight, $info_id_exist);
        if ($stmt->execute()) {
            echo "User information updated successfully.";
        } else {
            echo "Error updating user information: " . $stmt->error;
        }
    }
}
    header("Location: {$base}capstone/dashboard/profile.php");
    exit();
    $stmt_find_info_id->close();
    $stmt->close();
    $conn->close();
}
?>
