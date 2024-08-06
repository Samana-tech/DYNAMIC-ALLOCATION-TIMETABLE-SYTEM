<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "timetabledb";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta name="description" content=""/>
    <meta name="author" content=""/>
    <title>Dynamic Allocation TimeTable System</title>
    <!-- BOOTSTRAP CORE STYLE CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet"/>
    <!-- FONT AWESOME CSS -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet"/>
    <!-- FLEXSLIDER CSS -->
    <link href="assets/css/flexslider.css" rel="stylesheet"/>
    <!-- CUSTOM STYLE CSS -->
    <link href="assets/css/style.css" rel="stylesheet"/>
    <!-- Google Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <style>
        body {
            position: relative;
            background-image: url('assets/img/bg/img5.jpeg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        table {
            margin-top: 10px;
            margin-bottom: 3px;
            font-family: Arial, sans-serif;
            border-collapse: collapse;
            width: 80%;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
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
            background-color: #5bc0de;
            border-color: #46b8da;
            color: #ffffff;
            padding: 10px 20px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn-info:hover {
            background-color: #31b0d5;
        }

        .custom-caption {
            color: #ffffff;
            font-weight: bold;
            padding: 10px;
            text-align: center;
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
                <li><a href="addlecturers.php">ADD LECTURERS</a></li>
                <li><a href="addunits.php">ADD UNITS</a></li>
                <li><a href="rooms.php">ADD ROOMS</a></li>
                <li><a href="addcourse_group.php">ADD COURSE_GROUPS</a></li>
                <li><a href="timeslots.php">TIMESLOTS</a></li>
                <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">TIMETABLE
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="generatetimetable.php">GENERATE TIMETABLE</a>
                        </li>
                        <li>
                            <a href="viewtimetable.php">VIEW TIMETABLE</a>
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

<div align="center">
    <br>
    <table id="timetabletable">
        <caption><strong>TIMETABLE</strong></caption>
        <thead>
            <tr>
                <th width="20">SNO.</th>
                <th width="50">Day</th>
                <th width="100">Lesson</th>
                <th width="100">Unit</th>
                <th width="100">Room</th>
                <th width="100">Lecturer</th>
                <th width="100">Course Group</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (isset($_SESSION['timetable'])) {
                foreach ($_SESSION['timetable'] as $index => $entry) {
                    echo "<tr>
                            <td>" . ($index + 1) . "</td>
                            <td>{$entry['day']}</td>
                            <td>{$entry['lesson']}</td>
                            <td>{$entry['unit']}</td>
                            <td>{$entry['room']}</td>
                            <td>{$entry['lecturer']}</td>
                            <td>{$entry['course']}</td>
                          </tr>";
                }
            }
            ?>
        </tbody>
    </table>
    <br>
    <button id="saveaspdf" class="btn btn-info btn-lg" onclick="generatePDF()">SAVE AS PDF</button>
    <br><br><br>
</div>

<!--  Jquery Core Script -->
<script src="assets/js/jquery-1.10.2.js"></script>
<!--  Core Bootstrap Script -->
<script src="assets/js/bootstrap.js"></script>
<!--  Flexslider Scripts -->
<script src="assets/js/jquery.flexslider.js"></script>
<!--  Scrolling Reveal Script -->
<script src="assets/js/scrollReveal.js"></script>
<!--  Scroll Scripts -->
<script src="assets/js/jquery.easing.min.js"></script>
<!--  Custom Scripts -->
<script src="assets/js/custom.js"></script>

<script>
    async function generatePDF() {
    const { jsPDF } = window.jspdf;
    const { autoTable } = window.jspdf;

    const doc = new jsPDF('p', 'mm', 'a4'); // 'mm' units and 'a4' size

    const table = document.getElementById('timetabletable');
    const rows = Array.from(table.querySelectorAll('tr')).map(tr => 
        Array.from(tr.querySelectorAll('th, td')).map(td => td.innerText)
    );

    // Define the header and body rows separately
    const head = [rows[0]]; // Header row
    const body = rows.slice(1); // Data rows

    // Add the table to the PDF
    doc.autoTable({
        head: head,
        body: body,
        startY: 20, // Initial Y position
        margin: { horizontal: 10 },
        styles: { fontSize: 10 },
        headStyles: { fillColor: [52, 73, 94] },
        bodyStyles: { 
            fillColor: [255, 255, 255], // Default fill color (white)
            lineColor: [0, 0, 0], // Black border color for cells
            lineWidth: 0.1 // Border width
        },
        alternateRowStyles: {
            fillColor: [255, 255, 0] // Yellow for alternate rows
        },
        tableWidth: 'auto', // Adjust width to fit the page
        theme: 'striped' // Adds striping (already handled in `autoTable`)
    });

    // Save the PDF
    doc.save('timetable.pdf');
}


</script>
</body>
</html>
