<?php
session_start();

// Veritabanı bağlantısını düzenleyin
$host = "localhost";
$username = "root";
$password = "";
$database = "Yeni"; // Veritabanı adı

$conn = new mysqli($host, $username, $password, $database);

// Bağlantı hatasını kontrol edin
if ($conn->connect_error) {
    die("Veritabanına bağlanılamadı: " . $conn->connect_error);
}

// Kullanıcı giriş yapmamışsa yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: loginn.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Kullanıcının satın alımlarını çekmek için SQL sorgusu
$sql = "
    SELECT b.name AS book_name, p.total_price
    FROM purchases p
    INNER JOIN books b ON p.book_id = b.id
    WHERE p.user_id = ?
";

// Sorguyu hazırla ve çalıştır
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Sorgu hazırlama hatası: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Veritabanı sonuçlarını kontrol et
if ($result->num_rows > 0) {
    $purchases = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $message = "Hiçbir alışveriş yapılmamış.";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatura</title>
</head>
<body>
    <h1>Fatura</h1>

    <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php else: ?>
        <table border="1">
            <tr>
                <th>Kitap Adı</th>
                <th>Toplam Fiyat</th>
            </tr>
            <?php foreach ($purchases as $purchase): ?>
                <tr>
                    <td><?php echo htmlspecialchars($purchase['book_name']); ?></td>
                    <td><?php echo htmlspecialchars($purchase['total_price']); ?> TL</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <br>
    <a href="shopping.php">Alışverişe devam et</a>
</body>
</html>
