<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <?php include './static/header.php';?>
</head>
<body>
    <div style="display:flex; height: 100vh; align-items: center; justify-content: center;">
                <div class="row" style="background-color: white; border-radius: 10px; padding: 100px; display: flex; justify-content: flex-start; align-items: center;">
                    <div class="column" style="text-align: left;">
                        <h1 style="color: #000080; font-weight: bold;">OUR MISSION</h1>
                        <p style="text-align: justify;">
                            To enhance Bacolod City's traffic violation management by creating a secure and user-friendly database that allows authorized personnel to quickly and efficiently track and manage traffic violators.
                        </p>
                        <br>
                        <h1 style="color: #000080; font-weight: bold;">OUR VISION</h1>
                        <p style="text-align: justify; margin-bottom: 1px;"> 
                            To envision a safer future for Bacolod City by creating a platform that facilitates better decision-making through comprehensive analysis.
                        </p>
                        <br>
                        <h2 style="color: black;">Contact Us</h2>
                        <p><i class="fa fa-envelope"></i>Email: etrafickets@gmail.com</p>
                        <h2 style="color: black;">Locate Us</h2>
                        <p><i class="fa fa-building"></i>Office: Bacolod City, Negros Occidental</p>
                    </div>
                    <div class="column" style="text-align: left; margin-left:100px; display:block;">
                        <div class="image-container">
                            <img src="./images/paolo.png" alt="Paolo">
                            <h3>Paolo Juan Saturnino - Programmer</h3>
                            <img src="./images/loraine.png" alt="Loraine">
                            <h3>Loraine Sinadjan  - Project Manager</h3>
                            <img src="./images/windelle.png" alt="Windelle">
                            <h3>Windelle Reboquio  - Researcher/ Analyst</h3>
                            <!-- more images if needed -->
                        </div>
                </div>
            </div>
    </div>
</body>
<style>
.image-container {
    display: flex;
    flex-direction: column; /* Stack images vertically */
    align-items: center; /* Align images to center if container is wider */
    justify-content: space-around; /* Distribute space evenly */
}

.image-container img {
    max-width: 100%; /* Ensure images are responsive and fit within the container */
    margin-bottom: 10px; /* Space between images */
    width: 200px; 
    height: auto;
}
</style>
<?php include './static/footer.php';?>
</html>
