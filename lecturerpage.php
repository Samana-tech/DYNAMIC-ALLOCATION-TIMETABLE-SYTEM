<?php
session_start();
if (!isset($_SESSION['loggedin_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta name="description" content=""/>
    <meta name="author" content=""/>
    <title>Dynamic Allocation TimeTable System</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <!-- BOOTSTRAP CORE STYLE CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet"/>
    <!-- FONT AWESOME CSS -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet"/>
    <!-- FLEXSLIDER CSS -->
    <link href="assets/css/flexslider.css" rel="stylesheet"/>
    <!-- CUSTOM STYLE CSS -->
    <link href="assets/css/style.css" rel="stylesheet"/>
    <!-- Google Fonts -->
    <style>
        .scrollable-form {
            max-height: 500px; /* Adjust height as needed */
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #ccc;
        }

        .navbar-nav > .dropdown:hover > .dropdown-menu {
            display: block;
        }
        .dropdown-menu > li > a:hover {
            background-color: #f5f5f5; /* Change the color as needed */
        }
        body {
            position: relative;
            background-image: url('assets/img/bg/img5.jpeg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0; /* Remove default margin to cover the entire viewport */
            padding: 0; /* Remove default padding */
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Dark overlay color */
            z-index: -1;
        }
        table {
            margin-top: 20px;
            margin-bottom: 3px;
            font-family: Arial, sans-serif;
            border-collapse: collapse;
            width: 80%;
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent white background */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* More pronounced shadow */
            border-radius: 10px; /* Rounded corners */
            overflow: hidden; /* Ensures the shadow doesn't overflow */
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: center;
            padding: 10px;
        }

        tr:nth-child(even) td {
            background-color: yellow;
        }

        tr:nth-child(odd) td {
            background-color: white;
        }

        .btn-info {
            background-color: #5bc0de; /* Blue color for info button */
            border-color: #46b8da;
            color: #ffffff; /* White text color */
            padding: 10px 20px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn-info:hover {
            background-color: #31b0d5; /* Darker shade on hover */
        }

        .center-text {
            text-align: center; /* Center-align text */
            margin-bottom: 20px; /* Spacing below the centered text */
        }
        .custom-caption {
            /* background-color: #ffffff; White background */
            color: #ffffff; /* Text color */
            font-weight: bold; /* Bold text */
            padding: 10px; /* Padding around the caption */
            text-align: center; /* Center-align text */
        }
    </style>
</head>
<body>

<div class="navbar navbar-inverse navbar-fixed-top " id="menu">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="navbar-collapse collapse move-me">
            <ul class="nav navbar-nav navbar-left">
                <li><a href="#">Hello <?php echo $_SESSION['loggedin_name']; ?></a></li>
                <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">TIMETABLE
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="changelesson.php">CHANGE LESSON</a>
                        </li>
                        <li>
                            <a href="makeup.php">BOOK MAKE_UP LESSON</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="index.php">LOGOUT</a></li>
            </ul>
        </div>
    </div>
</div>
<!--NAVBAR SECTION END-->
<br>
<!--Algorithm Implementation-->
<div>
    <br>
    <div id="timetableContainer">
        <div id="recurringTimetable">
            <table border="2" cellspacing="3" align="center" id="timetable">
                <caption class="custom-caption"><strong><br><br>
                    <?php echo $_SESSION['loggedin_name']; ?>'s Recurring classes Timetable
                    </strong></caption>
                <tr>
                    <th style="text-align:center">S.No</th>
                    <th style="text-align:center">Day</th>
                    <th style="text-align:center">Lesson</th>
                    <th style="text-align:center">Unit</th>
                    <th style="text-align:center">Room</th>
                </tr>
                <?php
                $conn = mysqli_connect("localhost", "root", "", "timetabledb");

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $staff_no = $_SESSION['loggedin_id'];
                $q = mysqli_query($conn, "SELECT sno, day, lesson, unit, room FROM timetable WHERE staff_no = '$staff_no'");

                while ($row = mysqli_fetch_assoc($q)) {
                    echo "
                    <tr>
                        <td style=\"text-align:center\">{$row['sno']}</td>
                        <td style=\"text-align:center\">{$row['day']}</td>
                        <td style=\"text-align:center\">{$row['lesson']}</td>
                        <td style=\"text-align:center\">{$row['unit']}</td>
                        <td style=\"text-align:center\">{$row['room']}</td>
                    </tr>\n";
                }

                echo '</table>';
                ?>
        </div>
        <br>
        <div id="makeupTimetable">
            <table border="2" cellspacing="3" align="center" id="makeupTimetable">
                <caption class="custom-caption"><strong><br><br>
                    <?php echo $_SESSION['loggedin_name']; ?>'s Make_up classes Timetable
                    </strong></caption>
                <tr>
                    <th style="text-align:center">S.No</th>
                    <th style="text-align:center">Day</th>
                    <th style="text-align:center">Lesson</th>
                    <th style="text-align:center">Unit</th>
                    <th style="text-align:center">Room</th>
                </tr>
                <?php
                $conn = mysqli_connect("localhost", "root", "", "timetabledb");

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $staff_no = $_SESSION['loggedin_id'];
                $q = mysqli_query($conn, "SELECT sno, day, lesson, unit_code, room FROM make_up WHERE staff_no = '$staff_no'");

                while ($row = mysqli_fetch_assoc($q)) {
                    echo "
                    <tr>
                        <td style=\"text-align:center\">{$row['sno']}</td>
                        <td style=\"text-align:center\">{$row['day']}</td>
                        <td style=\"text-align:center\">{$row['lesson']}</td>
                        <td style=\"text-align:center\">{$row['unit_code']}</td>
                        <td style=\"text-align:center\">{$row['room']}</td>
                    </tr>\n";
                }

                echo '</table>';
                ?>
        </div>
    </div>
    <br>
    <div class="center-text">
        <button onclick="generatePDF()" class="btn btn-info">SAVE AS PDF</button>
    </div>
</div>

<script>
    function generatePDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        html2canvas(document.getElementById('timetableContainer')).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 190; // Maximum width for PDF (in mm)
            const pageHeight = 295; // A4 page height (in mm)
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;
            let position = 0;

            doc.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                doc.addPage();
                doc.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            doc.save('timetable.pdf');
        });
    }
</script>
</body>
</html>
