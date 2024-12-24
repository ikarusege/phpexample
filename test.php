<?php
// Veritabanı bağlantısı
$host = 'localhost';
$dbname = 'vulnerable_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Kullanıcı giriş kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Güvensiz sorgu (SQL Injection'a açık)
    $sql = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo "Hoş geldiniz, " . htmlspecialchars($result['username']) . "!";
    } else {
        echo "Hatalı kullanıcı adı veya şifre.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Login</title>
</head>
<body>
    <h1>Giriş Yap</h1>
    <form method="post">
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password">Şifre:</label>
        <input type="password" name="password" id="password" required>
        <br>
        <button type="submit">Giriş</button>
    </form>
</body>
</html>
