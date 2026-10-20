
<?php
if (!isset($sayfaBasligi)) {
    $sayfaBasligi = "BÖTE | Eğitim Teknolojileri";
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($sayfaBasligi, ENT_QUOTES, "UTF-8") ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Mevcut tasarımın -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="ust-menu">
    <a class="logo" href="index.php">
        <span class="logo-simge">B</span>
        <span>
            BÖTE
            <small>Eğitim Teknolojileri</small>
        </span>
    </a>

    <nav>
        <a href="index.php">Ana Sayfa</a>
        <a href="hakkimizda.php">Hakkımızda</a>
        <a href="dersler.php">Dersler</a>
        <a href="iletisim.php">İletişim</a>

        <!-- Yeni sayfalar -->
        <a href="ogrenciler.php">Öğrenciler</a>
        <a href="ogretim_elemanlari.php">Öğretim Elemanları</a>
        <a href="sinavlar.php">Sınavlar</a>
    </nav>
</header>

<main class="icerik">
