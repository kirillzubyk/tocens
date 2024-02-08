
<?php
session_start();

// Проверяем, если пользователь уже вошел в систему
if (!isset($_SESSION['username'])) {
    // Если не вошел, проверяем, если произведена попытка входа
    if (isset($_POST['login']) && $_POST['username'] === 'admin' && $_POST['password'] === 'Kirill2013!!!') {
        $_SESSION['username'] = 'admin';
        $_SESSION['password'] = 'Kirill2013!!!';
        $_SESSION['id'] = 'admin';
        $_SESSION['balance'] = 50000; // Баланс администратора
    }
}

// Создаем соединение с базой данных
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sqlmy";

$conn = new mysqli($servername = "localhost", $username = "root", $password = "root", $dbname = "sqlmy");

// Проверяем соединение
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Обработка регистрации нового пользователя
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Генерируем уникальный ID для нового пользователя
    $id = uniqid();

    // Вставляем новую запись в базу данных
    $sql = "INSERT INTO users (id, username, password, balance) VALUES ('$id', '$username', '$password', 500)";
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Обработка перевода токенов
if (isset($_POST['transfer'])) {
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];
    $amount = $_POST['amount'];

    // Получаем баланс отправителя
    $sender_balance_query = "SELECT balance FROM users WHERE id='$sender_id'";
    $sender_balance_result = $conn->query($sender_balance_query);
    $sender_balance_row = $sender_balance_result->fetch_assoc();
    $sender_balance = $sender_balance_row['balance'];

    // Получаем баланс получателя
    $receiver_balance_query = "SELECT balance FROM users WHERE id='$receiver_id'";
    $receiver_balance_result = $conn->query($receiver_balance_query);
    $receiver_balance_row = $receiver_balance_result->fetch_assoc();
    $receiver_balance = $receiver_balance_row['balance'];

    // Проверяем достаточность средств
    if ($sender_balance >= $amount) {
        // Вычитаем сумму из баланса отправителя
        $new_sender_balance = $sender_balance - $amount;

        // Добавляем сумму к балансу получателя
        $new_receiver_balance = $receiver_balance + $amount;

        // Обновляем балансы в базе данных
        $update_sender_balance_query = "UPDATE users SET balance=$new_sender_balance WHERE id='$sender_id'";
        $update_receiver_balance_query = "UPDATE users SET balance=$new_receiver_balance WHERE id='$receiver_id'";

        if ($conn->query($update_sender_balance_query) === TRUE && $conn->query($update_receiver_balance_query) === TRUE) {
            echo "Transfer successful";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        echo "Insufficient balance";
    }
}



$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Virtual Currency System</title>
    <style>
        /* CSS стили для красивого отображения */
        /* Можно изменить по вашему усмотрению */
    </style>
</head>
<body>
<?php if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin'): ?>
    <h2>Welcome Admin</h2>
    <p>Your balance: <?php echo $_SESSION['balance']; ?></p>
<?php else: ?>
    <h2>Admin Login</h2>
    <form method="post" action="">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" name="login" value="Login">
    </form>
<?php endif; ?>

<h2>Registration</h2>
<form method="post" action="">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" required><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required><br><br>
    <input type="submit" name="register" value="Register">
</form>

<h2>Transfer Tokens</h2>
<form method="post" action="">
    <label for="sender_id">Sender ID:</label><br>
    <input type="text" id="sender_id" name="sender_id" required><br>
    <label for="receiver_id">Receiver ID:</label><br>
    <input type="text" id="receiver_id" name="receiver_id" required><br>
    <label for="amount">Amount:</label><br>
    <input type="number" id="amount" name="amount" required><br><br>
    <input type="submit" name="transfer" value="Transfer">



</form>

<?php
// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sqlmy";

$conn = new mysqli($servername = "localhost", $username = "root", $password = "root", $dbname = "sqlmy");

// Проверяем соединение
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Указываем имя пользователя, для которого нужно вывести баланс
$username = "admin";

// Получаем баланс пользователя из базы данных
$sql = "SELECT balance FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Выводим баланс пользователя
    $row = $result->fetch_assoc();
    $balance = $row["balance"];
    echo "Баланс пользователя $username: $balance";
} else {
    echo "Пользователь с именем $username не найден";
}
?>

<?php
// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sqlmy";

$conn = new mysqli($servername = "localhost", $username = "root", $password = "root", $dbname = "sqlmy");

// Проверяем соединение
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получаем баланс каждого пользователя из базы данных
$sql = "SELECT id, username, balance FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Выводим баланс каждого пользователя
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"]. " - Имя пользователя: " . $row["username"]. " - Баланс: " . $row["balance"]. "<br>";
    }
} else {
    echo "Пользователи не найдены";
}
?>





</body>
</html>
