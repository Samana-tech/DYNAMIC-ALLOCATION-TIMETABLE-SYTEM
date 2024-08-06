<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta name="description" content=""/>
    <meta name="author" content=""/>
    <title>Dynamic Allocation TimeTable System</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.16/jspdf.plugin.autotable.min.js"></script>
    <!-- BOOTSTRAP CORE STYLE CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet"/>
    <!-- FONT AWESOME CSS -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet"/>
    <!-- CUSTOM STYLE CSS -->
    <link href="assets/css/style.css" rel="stylesheet"/>
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
    <div class="container">
        <?php
        // Database connection
        $conn = mysqli_connect("localhost", "root", "", "timetabledb");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Initialize an array to store timetable data
        $timetable_data = array();

        if (isset($_POST['course_group_code'])) {
            $course_group_code = $_POST['course_group_code'];
            echo "<div align='center' style='color:white;'><p>{$course_group_code}'s Group Timetable</p></div>";

            // Query timetable for entries where course matches the provided course_group_code
            $timetable_query = "SELECT sno, day, lesson, unit, room, lecturer FROM timetable WHERE course = '$course_group_code'";
            $timetable_result = mysqli_query($conn, $timetable_query);

            if (mysqli_num_rows($timetable_result) > 0) {
                while ($timetable_row = mysqli_fetch_assoc($timetable_result)) {
                    $timetable_data[] = $timetable_row;
                }
            } else {
                echo "<p>No timetable found for the course group code: $course_group_code</p>";
            }

            mysqli_close($conn);
        } else {
            echo "<p>Course group code not provided.</p>";
        }
        ?>
        <div>
            <table border="2" cellspacing="3" align="center" id="timetable">
                <caption class="custom-caption"><strong><br><br>Timetable</strong></caption>
                <tr>
                    <th style="text-align:center">S.No</th>
                    <th style="text-align:center">Day</th>
                    <th style="text-align:center">Lesson</th>
                    <th style="text-align:center">Unit</th>
                    <th style="text-align:center">Room</th>
                    <th style="text-align:center">Lecturer</th>
                </tr>
                <?php foreach ($timetable_data as $row): ?>
                    <tr>
                        <td style="text-align:center"><?php echo $row['sno']; ?></td>
                        <td style="text-align:center"><?php echo $row['day']; ?></td>
                        <td style="text-align:center"><?php echo $row['lesson']; ?></td>
                        <td style="text-align:center"><?php echo $row['unit']; ?></td>
                        <td style="text-align:center"><?php echo $row['room']; ?></td>
                        <td style="text-align:center"><?php echo $row['lecturer']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div align="center" style="margin-top: 10px">
            <button id="saveaspdf" class="btn btn-info btn-lg" onclick="generatePDF()">SAVE AS PDF</button>
        </div>
    </div>
    <script>
        function generatePDF() {
            const { jsPDF } = window.jspdf;
            const { autoTable } = window.jspdf;

            const doc = new jsPDF('p', 'mm', 'a4'); // 'mm' units and 'a4' size

            // Get table data
            const table = document.getElementById('timetable');
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
                headStyles: { fillColor: [52, 73, 94] }, // Dark color for header
                alternateRowStyles: { fillColor: [255, 255, 0] }, // Yellow for alternate rows
                tableWidth: 'auto', // Adjust width to fit the page
                theme: 'striped' // Adds striping (already handled in `autoTable`)
            });

            // Save the PDF
            doc.save('timetable.pdf');
        }
    </script>
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
</body>
</html>
