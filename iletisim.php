<?php
$sayfaBasligi = "İletişim | BÖTE";

$ad = "";
$email = "";
$mesaj = "";
$hatalar = [];
$gonderildi = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ad = trim($_POST["ad"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mesaj = trim($_POST["mesaj"] ?? "");

    if ($ad === "" || strlen($ad) > 100) {
        $hatalar[] = "Ad alanını doldurmalısın.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $hatalar[] = "Geçerli bir e-posta adresi yazmalısın.";
    }

    if ($mesaj === "" || strlen($mesaj) > 3000) {
        $hatalar[] = "Mesaj alanı boş olamaz ve 3000 karakteri aşamaz.";
    }

    if (empty($hatalar)) {
        $gonderildi = true;
        $ad = "";
        $email = "";
        $mesaj = "";
    }
}

include "header.php";
?>

<section class="sayfa-baslik">
    <h1>İletişim</h1>
    <p>Görüş ve önerilerini bizimle paylaş.</p>
</section>

<div class="kartlar">
    <section class="kart">
        <h2 class="bolum-baslik">İletişim Bilgileri</h2>

        <p>
            <strong>Bölüm:</strong><br>
            Bilgisayar ve Öğretim Teknolojileri Eğitimi
        </p>

        <br>

        <p>
            <strong>Üniversite:</strong><br>
            İstanbul Üniversitesi-Cerrahpaşa
        </p>

        <br>

        <p>
            Resmî adres ve iletişim bilgilerini
            üniversitenin web sitesinden doğrulayabilirsin.
        </p>

        <br>

        <a class="buton"
           href="https://www.iuc.edu.tr/"
           target="_blank"
           rel="noopener noreferrer">
            Üniversite Sitesi
        </a>
    </section>

    <section class="kart">
        <h2 class="bolum-baslik">Mesaj Gönder</h2>

        <?php if ($gonderildi): ?>
            <div class="bilgi-kutusu" role="status">
                Form kontrolü başarılı! Demo sürümünde
                mesaj kaydedilmedi veya e-posta gönderilmedi.
            </div>
        <?php endif; ?>

        <?php if (!empty($hatalar)): ?>
            <div class="hata-kutusu" role="alert">
                <ul>
                    <?php foreach ($hatalar as $hata): ?>
                        <li><?= htmlspecialchars($hata, ENT_QUOTES, "UTF-8") ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="iletisim.php" method="post">
            <div class="form-grup">
                <label for="ad">Ad Soyad</label>
                <input
                    type="text"
                    id="ad"
                    name="ad"
                    maxlength="100"
                    required
                    value="<?= htmlspecialchars($ad, ENT_QUOTES, "UTF-8") ?>"
                >
            </div>

            <br>

            <div class="form-grup">
                <label for="email">E-posta</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="254"
                    required
                    value="<?= htmlspecialchars($email, ENT_QUOTES, "UTF-8") ?>"
                >
            </div>

            <br>

            <div class="form-grup">
                <label for="mesaj">Mesajın</label>
                <textarea
                    id="mesaj"
                    name="mesaj"
                    maxlength="3000"
                    required
                ><?= htmlspecialchars($mesaj, ENT_QUOTES, "UTF-8") ?></textarea>
            </div>

            <br>

            <button class="buton" type="submit">
                Mesajı Gönder
            </button>
        </form>
    </section>
</div>

<?php include "footer.php"; ?>
