<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <?php include __DIR__ . '/../admin2023/static/header.php';?>
    <?php include __DIR__ . '/../admin2023/templates/restrict_nonadmin.php';?>
</head>

<body style="margin: 0;">
    <div style="display:flex; height: 100vh; align-items: center; justify-content: center;">
        <div class="row" >
            <div class="column" style="flex: 25%;">
                <div style="display:flex; align-items: center; justify-content: center;">
                    <a href="<?php echo $base . "capstone/admin2023/analytics/analytics.php" ?>">
                        <div class="loginform button">
                            <h1>ANALYTICS<i class="fa fa-chart-simple" style="margin-left: 5px;"></i></h1>
                        </div>
                    </a>
                </div>
                <div style="text-align: center;">
                    <p style="padding: 0 20px;">Gain insights into traffic violations, track trends, and visualize data.</p>
                    <br>
                    <br>
                </div>
            </div>
            <div class="column" style="flex: 25%;">
                <div style="display:flex; align-items: center; justify-content: center;">
                    <a href="<?php echo $base . "capstone/admin2023/violations/manage_violations.php" ?>">
                        <div class="loginform button">
                            <h1>MANAGE VIOLATIONS<i class="fa fa-circle-info" style="margin-left: 5px;"></i></h1>
                        </div>
                    </a>
                </div>
                <div style="text-align: center;">
                    <p style="padding: 0 20px;">Effortlessly monitor and manage traffic infractions, oversee dispute resolutions, and maintain comprehensive records of violations.</p>
                </div>
            </div>
            <div class="column" style="flex: 25%;">
                <div style="display:flex; align-items: center; justify-content: center;">
                    <a href="<?php echo $base . "capstone/admin2023/officers/manageofficer.php" ?>">
                        <div class="loginform button">
                            <h1>MANAGE OFFICERS<i class="fa fa-user" style="margin-left: 5px;"></i></h1>
                        </div>
                    </a>
                </div>
                <div style="text-align: center;">
                    <p style="padding: 0 20px;">Empower administrative capabilities by efficiently managing officer accounts, creating new profiles, and maintaining officer information.</p>
                </div>
            </div>
            <div class="column" style="flex: 25%;">
                <div style="display:flex; align-items: center; justify-content: center;">
                    <a href="<?php echo $base . "capstone/admin2023/users/manageuser.php" ?>">
                        <div class="loginform button">
                            <h1>MANAGE USERS<i class="fa fa-user" style="margin-left: 5px;"></i></h1>
                        </div>
                    </a>
                </div>
                <div style="text-align: center;">
                    <p style="padding: 0 20px;">Efficiently monitor user profiles, ensuring management of all application users and its information.</p>
                    <br>
                </div>
            </div>
        </div>
    </div>
</body>
<br>
    <div style="margin-bottom: 200px;"></div>
</html>
<?php include __DIR__ . '/../static/footer.php';?>


<style>
    .loginform {
        height: auto;
        width: max-content;
        justify-content: center;
        align-items: center;
        margin: 10px;
        padding: 10px;
    }
    h1 {
        text-align: center;
    }
    .button {
        border-radius: 5px;
    }
</style>