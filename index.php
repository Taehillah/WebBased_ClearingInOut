<?php

$connectionString = "Data Source=clearing-in-outdbserver.database.windows.net;Initial Catalog=Clearing_In_Out_db;User ID=Taehillah;Password=Lehlohonolo@01;Connect Timeout=30;Encrypt=True;TrustServerCertificate=False;ApplicationIntent=ReadWrite;MultiSubnetFailover=False";

function escapeInput($input)
{
    // Function to escape user inputs to prevent SQL injection
    // You can implement this using your preferred database library or PDO
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

<!-- HTML Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Form</title>
</head>
<body>
    <form method="post">
        <!-- Your HTML form elements here -->
        <!-- For example:
        <input type="text" name="txtForceNum" placeholder="Force Number">
        <input type="text" name="txtName" placeholder="Name">
        <select name="cmbRank">
            <option value="Rank1">Rank1</option>
            <option value="Rank2">Rank2</option>
            <!-- Add other rank options here -->
        </select>
        <!-- Add other form elements here -->
        <input type="submit" name="btnSubmit" value="Submit">
    </form>
</body>
</html>
