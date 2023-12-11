<!DOCTYPE html>
<html lang="en">
<head>
    <title> E - Trafickets </title>
    <link rel="stylesheet" href="style.css">
    <?php include './static/header.php';?>
    <?php include './templates/restrict_user.php';?>

</head>
<body>
    <div class="background-container" style="display:flex; height: 100vh; align-items: center; justify-content: center;">
        <div class="row" style="display: flex; flex-direction: column; align-items: center; justify-content: space-around;">
            <div style="padding: 30px 50px; text-align: center;">
                <h1 style="font-weight: bold; color: #000080; margin-top: 50px; margin-bottom: 20px;">Welcome to E-TRAFICKETS!</h1>
                <p>
                    E-Trafickets is a centralized database for securely storing and managing traffic violation data in Bacolod City. 
                    <br>Designed to empower authorized personnel, boost efficiency, minimize manual work, and contribute to safer roads throughout the city.
                </p>
            </div>
            <div class="column" style="text-align: center; margin-bottom: 70px;">
                <h1 style="color: #000080; margin-bottom: 20px; font-weight: bold;">Services</h1>
                <div style="display: flex; justify-content: space-around;">
                    <div style="width: 30%; margin: 10px; display: flex; flex-direction: column;">
                        <div style="padding: 20px; color: white; border: 1px solid black; border-radius: 5px;">
                            <h4 style="background-color: transparent; margin: 0; padding: 5px 10px; color: black; border-radius: 3px; border: none;">FIND VIOLATOR</h4>
                        </div>
                        <div style="background-color: #0B0B45; padding: 20px; color: white; border-radius: 5px; margin-top: 10px;">
                            <p style="margin: 0; padding: 10px;">Easily track down violators by searching for specified driver records using their license information.</p>
                        </div>
                    </div>
                    <div style="width: 30%; margin: 10px; display: flex; flex-direction: column;">
                        <div style="padding: 20px; color: white; border: 1px solid black; border-radius: 5px;">
                            <h4 style="background-color: transparent; margin: 0; padding: 5px 10px; color: black; border-radius: 3px; border: none;">VIEW RECORDS</h4>
                        </div>
                        <div style="background-color: #0B0B45; padding: 20px; color: white; border-radius: 5px; margin-top: 10px;">
                            <p style="margin: 0; padding: 10px;">View and manage all traffic data, ensuring effective monitoring of the city's violators and enhancing road safety.</p>
                        </div>
                    </div>
                    <div style="width: 30%; margin: 10px; display: flex; flex-direction: column;">
                        <div style="padding: 20px; color: white; border: 1px solid black; border-radius: 5px;">
                            <h4 style="background-color: transparent; margin: 0; padding: 5px 10px; color: black; border-radius: 3px; border: none;">VIEW ANALYTICS</h4>
                        </div>
                        <div style="background-color: #0B0B45; padding: 20px; color: white; border-radius: 5px; margin-top: 10px;">
                            <p style="margin: 0; padding: 10px;">Gain valuable insights and make informed, strategic decisions with our comprehensive data analysis tools.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include './static/footer.php';?>
</html>

