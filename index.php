<?php

$connectionString = "Data Source=clearing-in-outdbserver.database.windows.net;Initial Catalog=Clearing_In_Out_db;User ID=Taehillah;Password=Lehlohonolo@01;Connect Timeout=30;Encrypt=True;TrustServerCertificate=False;ApplicationIntent=ReadWrite;MultiSubnetFailover=False";

function escapeInput($input)
{
    // Function to escape user inputs to prevent SQL injection
    // For simplicity, we'll use the mysqli library here
    // Please make sure to use prepared statements and bind parameters in production code
    global $connection;
    return mysqli_real_escape_string($connection, $input);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Form data has been submitted

    $forceNum = escapeInput($_POST["txtForceNum"]);
    $name = escapeInput($_POST["txtName"]);
    $rank = escapeInput($_POST["cmbRank"]);
    $homeUnit = escapeInput($_POST["txtHomeUnit"]);
    $clearingInOut = escapeInput($_POST["cmbClearingInOut"]);
    $authorityNum = escapeInput($_POST["txtAuthNum"]);
    $dateTime = escapeInput($_POST["dtpReturnDate"]);
    $unitVisiting = escapeInput($_POST["txtUnitVisiting"]);

    // Assuming ClbClearingSections is submitted as an array of checkbox values
    $checkedItems = implode(", ", $_POST["ClbClearingSections"]);

    // Write data to the database
    try {
        // Connect to the database using your preferred database library (e.g., mysqli, PDO)
        $connection = mysqli_connect('your_host', 'your_username', 'your_password', 'your_database');
        if (!$connection) {
            die("Database connection failed: " . mysqli_connect_error());
        }

        // Prepare and execute the SQL insert query using prepared statements to prevent SQL injection
        $sqlInsertQuery = "INSERT INTO dbo.FORM1 (ForceNum, Ranks, FullNames, HomeUnit, ClearingInOut, Authority, ReturnDate, UnitVisiting) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($connection, $sqlInsertQuery);
        mysqli_stmt_bind_param($stmt, "ssssssss", $forceNum, $rank, $name, $homeUnit, $clearingInOut, $authorityNum, $dateTime, $unitVisiting);
        mysqli_stmt_execute($stmt);

        // Close the statement and connection
        mysqli_stmt_close($stmt);
        mysqli_close($connection);

        // Display a success message to the user
        echo '<script>alert("Data has been exported to the database.");</script>';
    } catch (Exception $ex) {
        // Handle any exceptions that may occur during database operations
        echo '<script>alert("Error exporting data: ' . $ex->getMessage() . '");</script>';
    }

    // Write data to the text file
    $filePath = "data.txt";
    try {
        // Create or append to the text file and write the data
        $fileContent = "Force Number: $forceNum\nName: $name\nRank: $rank\nHome Unit: $homeUnit\nClearing: $clearingInOut\nAuthority Number: $authorityNum\nDate: $dateTime\nUnit (Visiting): $unitVisiting\nChecked Sections: $checkedItems\n----------\n";

        // Open the file in append mode to add new entries
        $fileHandle = fopen($filePath, "a");
        fwrite($fileHandle, $fileContent);
        fclose($fileHandle);

        // Display a success message to the user
        echo '<script>alert("Data has been exported to ' . $filePath . '");</script>';
    } catch (Exception $ex) {
        // Handle any exceptions that may occur during writing to the file
        echo '<script>alert("Error exporting data: ' . $ex->getMessage() . '");</script>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Clearing In Out</title>
    <style>
        body {
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 14px;
                    background-color: white;

                }
                div{
                    position: absolute;
                    width: 600px !important;
                    height: 600px;

                    top: 50%;
                    left: 50%;
                    margin-top: -250px;
                    margin-left: -300px;




                }
                form{
                    background-color: #282728;
                    width: 600px;
                    height: 600px;
                    border-radius: 10px;

                    margin:0;
                    padding: 0px;
                
     
                
                }
        
                table {
                    border-collapse: collapse;
                    width: 100%;
                }
        
                th, td {
                    text-align: left;
                    padding: 8px;
                }
        
                tr:nth-child(even) {
                    background-color: #f2f2f2;
                }
    </style>
</head>
<body>
    <h1 style="text-align: center;">HQ 46 SA BRIGADE</h1>
    <h2 style="text-align: center;">Clearing In | Out Form</h2>
    <div>
    <form>
        <label for="txtForceNum">Force Number:</label>
        <input type="text" id="txtForceNum" name="txtForceNum"><br>

        <label for="cmbRank">Rank:</label>
        <select id="cmbRank" name="cmbRank">
            <option value="Pte">Pte</option>
            <option value="L/Cpl">L/Cpl</option>
            <option value="Cpl">Sgt</option>
            <!-- Add other rank options here -->
        </select><br>

        <label for="txtName">Full Names:</label>
        <input type="text" id="txtName" name="txtName"><br>

        <label for="txtHomeUnit">Unit(Home):</label>
        <input type="text" id="txtHomeUnit" name="txtHomeUnit"><br>

        <label for="txtAuthNum">Authority No:</label>
        <input type="text" id="txtAuthNum" name="txtAuthNum"><br>

        <label for="cmbClearingInOut">Clearing In/Out:</label>
        <select id="cmbClearingInOut" name="cmbClearingInOut">
            <option value="In">In</option>
            <option value="Out">Out</option>
        </select><br>

        <label for="dtpReturnDate">Date(Return):</label>
        <input type="date" id="dtpReturnDate" name="dtpReturnDate"><br>

        <label for="txtUnitVisiting">Unit(Visiting):</label>
        <input type="text" id="txtUnitVisiting" name="txtUnitVisiting"><br>

        <label>Clearing Sections (Check/Uncheck the relevant ones):</label><br>
        <input type="checkbox" name="clearingSection" value="RSM">RSM
        <input type="checkbox" name="clearingSection" value="Adjudant">Adjudant
        <!-- Add other clearing section checkboxes here -->

        <br>
        <input type="submit" value="Submit">
    </form>
    </div>
</body>
</html>

