<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
include 'functions.php';
// Establish database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "Module5";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set userId to 1 for this example
$userId = 1;

// Function that accepts multiple arguments and returns data
// function get_user_savings($conn, $userId) {
//     $sql = "SELECT userMoneyData.moneyId, users.firstName, users.lastName, userMoneyData.savingsAmount, userMoneyData.dateSaved FROM userMoneyData JOIN users ON userMoneyData.userId = users.id WHERE userMoneyData.userId = $userId";
//     $result = $conn->query($sql);

//     $savings = array();
//     if ($result->num_rows > 0) {
//         while($row = $result->fetch_assoc()) {
//             $savings[] = $row;
//         }
//     }
//     return $savings;
// }

// Handle form submission for INSERT
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["insert"])) {
    $savingsAmount = $_POST["savingsAmount"];
    $dateSaved = $_POST["dateSaved"];

    // Insert into database
    $sql = "INSERT INTO userMoneyData (userId, savingsAmount, dateSaved)
    VALUES ($userId, $savingsAmount, '$dateSaved')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle GET parameters for UPDATE and DELETE
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["moneyId"]) && isset($_GET["action"])) {
        $moneyId = $_GET["moneyId"];
        if ($_GET["action"] == "update" && isset($_GET["newSavingsAmount"])) {
            $newSavingsAmount = $_GET["newSavingsAmount"];

            // Update database
            $sql = "UPDATE userMoneyData SET savingsAmount = $newSavingsAmount WHERE moneyId = $moneyId";

            if ($conn->query($sql) === TRUE) {
                echo "Record updated successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } elseif ($_GET["action"] == "delete") {
            // Delete from database
            $sql = "DELETE FROM userMoneyData WHERE moneyId = $moneyId";

            if ($conn->query($sql) === TRUE) {
                echo "Record deleted successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}

// Fetch data from database
$savings = get_user_savings($conn, $userId);

// Close the connection
$conn->close();

?>

<!-- Form for adding savings -->
<h2>Add Savings</h2>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
  Savings Amount: <input type="number" name="savingsAmount" step="0.01"><br>
  Date Saved: <input type="date" name="dateSaved"><br>
  <input type="submit" name="insert" value="Add Savings">
</form>

<!-- Display user savings -->
<h2>Your Savings</h2>
<?php 
$totalSavings = 0; 
foreach($savings as $saving): 
  $totalSavings += $saving['savingsAmount'];
  $dateSaved = new DateTime($saving['dateSaved']);
  $now = new DateTime();

  $interval = $now->diff($dateSaved);

  $timeAgo = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
?>
  <p>
    The user <?php echo $saving['firstName'] . " " . $saving['lastName']; ?> saved <?php echo $saving['savingsAmount']; ?> <?php echo $timeAgo; ?>
    <a href="#" onclick="updateSavings(<?php echo $saving['moneyId']; ?>)">Edit</a>, 
    <a href="<?php echo $_SERVER['PHP_SELF'];?>?action=delete&moneyId=<?php echo $saving['moneyId']; ?>">Delete</a>
  </p>
<?php endforeach; ?>

<p>Total Savings: <?php echo $totalSavings; ?></p>


<script>
  function updateSavings(moneyId) {
    var newSavingsAmount = prompt("Please enter new savings amount:");
    if (newSavingsAmount != null) {
      window.location.href = "<?php echo $_SERVER['PHP_SELF'];?>?action=update&moneyId=" + moneyId + "&newSavingsAmount=" + newSavingsAmount;
    }
  }
</script>


</body>
</html>

