<?php
// Function that accepts multiple arguments and returns data
function get_user_savings($conn, $userId) {
    $sql = "SELECT userMoneyData.moneyId, users.firstName, users.lastName, userMoneyData.savingsAmount, userMoneyData.dateSaved FROM userMoneyData JOIN users ON userMoneyData.userId = users.id WHERE userMoneyData.userId = $userId";
    $result = $conn->query($sql);

    $savings = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $savings[] = $row;
        }
    }
    return $savings;
}
?>
