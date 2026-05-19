<?php
// 1. I-set ang password na gusto mong i-test
$password_na_input = "testadmin123"; 

// 2. I-set ang Hash na galing sa database mo (Copy-paste mo dito yung nasa phpMyAdmin)
$hash_sa_database = '$2y$10$K0WkP3X4S.6uHjY5gK8mre9YVz4mU2M.6S9S7fP6K5S5vY7g6L7hG';

echo "<h3>Password Hashing Test</h3>";
echo "Password Input: <b>" . $password_na_input . "</b><br>";
echo "Hash sa DB: <b>" . $hash_sa_database . "</b><br><br>";

// 3. Dito natin iche-check kung MATCH sila
if (password_verify($password_na_input, $hash_sa_database)) {
    echo "<h2 style='color: green;'>✅ MATCH! Gumagana ang Login mo.</h2>";
    echo "Ito ang dapat mong itype sa login form para makapasok.";
} else {
    echo "<h2 style='color: red;'>❌ HINDI MATCH!</h2>";
    echo "Kaya ka nakakakuha ng 'Wrong Password' alert dahil hindi tugma ang hash sa database.";
}

echo "<hr>";

// 4. Kung gusto mong gumawa ng BAGONG hash para sa ibang password:
$new_password = "admin123"; // Palitan mo ito kung gusto mo ng ibang pass
$new_hash = password_hash($new_password, PASSWORD_BCRYPT);

echo "<h4>Gusto mo ng bagong password?</h4>";
echo "Bagong Password: <b>" . $new_password . "</b><br>";
echo "I-copy ito at i-paste sa phpMyAdmin: <br>";
echo "<input type='text' value='" . $new_hash . "' style='width: 100%; padding: 10px; margin-top: 5px;'>";
?>