<?php
$sayfaBasligi = "Dersler | BÖTE";
include "header.php";

$dersler = [
    ["BOTE101", "Eğitimde Bilişim Teknolojileri", 1, "Güz", "Zorunlu", 5],
    ["BOTE103", "Programlamaya Giriş", 1, "Güz", "Zorunlu", 6],
    ["BOTE105", "Eğitim Psikolojisi", 1, "Bahar", "Zorunlu", 5],
    ["BOTE107", "Web Tasarımına Giriş", 1, "Bahar", "Seçmeli", 4],
    ["BOTE201", "Öğretim Tasarımı", 2, "Güz", "Zorunlu", 5],
    ["BOTE203", "Veri Tabanı Yönetimi", 2, "Güz", "Zorunlu", 6],
    ["BOTE205", "Eğitimde Materyal Tasarımı", 2, "Bahar", "Zorunlu", 5],
    ["BOTE207", "Grafik Tasarım", 2, "Bahar", "Seçmeli", 4],
    ["BOTE301", "Eğitimde Araştırma Yöntemleri", 3, "Güz", "Zorunlu", 5],
    ["BOTE303", "Çoklu Ortam Tasarımı", 3, "Bahar", "Seçmeli", 5],
    ["BOTE401", "Öğretim Teknolojileri ve Proje", 4, "Güz", "Zorunlu", 6],
    ["BOTE403", "Bitirme Projesi", 4, "Bahar", "Zorunlu", 8]
];

$sinif = $_GET["sinif"] ?? "";
$donem = $_GET["donem"] ?? "";
$tur = $_GET["tur"] ?? "";
$akts = $_GET["akts"] ?? "";
$arama = trim($_GET["arama"] ?? "");

if (!in_array($sinif, ["", "1", "2", "3", "4"], true)) {
    $sinif = "";
}
if (!in_array($donem, ["", "Güz", "Bahar"], true)) {
    $donem = "";
}
if (!in_array($tur, ["", "Zorunlu", "Seçmeli"], true)) {
    $tur = "";
}
if ($akts !== "" &&
    (!ctype_digit($akts) || (int)$akts < 1 || (int)$akts > 30)) {
    $akts = "";
}
$arama = mb_substr($arama, 0, 100, "UTF-8");

$sonuclar = [];

foreach ($dersler as $ders) {
    if ($sinif !== "" && $ders[2] !== (int)$sinif) {
        continue;
    }

    if ($donem !== "" && $ders[3] !== $donem) {
        continue;
    }

    if ($tur !== "" && $ders[4] !== $tur) {
        continue;
    }

    if ($akts !== "" && $ders[5] !== (int)$akts) {
        continue;
    }

    if ($arama !== "" &&
        mb_stripos($ders[0], $arama, 0, "UTF-8") === false &&
        mb_stripos($ders[1], $arama, 0, "UTF-8") === false) {
        continue;
    }

    $sonuclar[] = $ders;
}
?>

<section class="sayfa-baslik">
    <h1>Ders Seçim Sistemi</h1>
    <p>
        Dersleri filtrele, istediğin dersleri seç
        ve toplam AKTS değerini hesapla.
    </p>
</section>

<form action="dersler.php" method="get" class="filtre-alani">
    <h2 class="bolum-baslik">Ders Filtreleri</h2>

    <div class="form-grid">
        <div class="form-grup">
            <label for="sinif">Sınıf</label>
            <select id="sinif" name="sinif">
                <option value="">Tüm sınıflar</option>
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <option value="<?= $i ?>"
                        <?= $sinif === (string)$i ? "selected" : "" ?>>
                        <?= $i ?>. Sınıf
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="form-grup">
            <label for="donem">Dönem</label>
            <select id="donem" name="donem">
                <option value="">Tüm dönemler</option>
                <option value="Güz" <?= $donem === "Güz" ? "selected" : "" ?>>Güz</option>
                <option value="Bahar" <?= $donem === "Bahar" ? "selected" : "" ?>>Bahar</option>
            </select>
        </div>

        <div class="form-grup">
            <label for="tur">Ders türü</label>
            <select id="tur" name="tur">
                <option value="">Tüm türler</option>
                <option value="Zorunlu" <?= $tur === "Zorunlu" ? "selected" : "" ?>>Zorunlu</option>
                <option value="Seçmeli" <?= $tur === "Seçmeli" ? "selected" : "" ?>>Seçmeli</option>
            </select>
        </div>

        <div class="form-grup">
            <label for="akts">AKTS</label>
            <select id="akts" name="akts">
                <option value="">Tüm AKTS değerleri</option>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>"
                        <?= $akts === (string)$i ? "selected" : "" ?>>
                        <?= $i ?> AKTS
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="form-grup">
            <label for="arama">Ders adı veya kodu</label>
            <input
                id="arama"
                name="arama"
                type="text"
                maxlength="100"
                placeholder="Örn. Programlama"
                value="<?= htmlspecialchars($arama, ENT_QUOTES, "UTF-8") ?>"
            >
        </div>
    </div>

    <br>
    <button class="buton" type="submit">Dersleri Filtrele</button>
    <a class="buton" href="dersler.php" style="background:#64748b">
        Filtreleri Temizle
    </a>
</form>

<h2 class="bolum-baslik">Ders Listesi</h2>
<p>Bulunan ders sayısı: <strong><?= count($sonuclar) ?></strong></p>

<form id="dersSecimFormu">
    <div class="tablo-kapsayici">
        <table>
            <thead>
                <tr>
                    <th>Seç</th>
                    <th>Ders Kodu</th>
                    <th>Ders Adı</th>
                    <th>Sınıf</th>
                    <th>Dönem</th>
                    <th>Tür</th>
                    <th>AKTS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$sonuclar): ?>
                    <tr>
                        <td colspan="7">Uygun ders bulunamadı.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sonuclar as $ders): ?>
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    class="ders-sec"
                                    value="<?= $ders[5] ?>"
                                    data-kod="<?= htmlspecialchars($ders[0], ENT_QUOTES, "UTF-8") ?>"
                                    data-ad="<?= htmlspecialchars($ders[1], ENT_QUOTES, "UTF-8") ?>"
                                    aria-label="<?= htmlspecialchars($ders[1], ENT_QUOTES, "UTF-8") ?> dersini seç"
                                >
                            </td>
                            <td><?= htmlspecialchars($ders[0], ENT_QUOTES, "UTF-8") ?></td>
                            <td><?= htmlspecialchars($ders[1], ENT_QUOTES, "UTF-8") ?></td>
                            <td><?= $ders[2] ?>. sınıf</td>
                            <td><?= htmlspecialchars($ders[3], ENT_QUOTES, "UTF-8") ?></td>
                            <td><span class="etiket"><?= htmlspecialchars($ders[4], ENT_QUOTES, "UTF-8") ?></span></td>
                            <td><?= $ders[5] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <section class="filtre-alani">
        <h3>Seçtiğin Dersler</h3>
        <p id="seciliDersler">Henüz ders seçmedin.</p>
        <p>Seçilen ders sayısı: <strong id="dersSayisi">0</strong></p>
        <p>Toplam AKTS: <strong id="toplamAkts">0</strong></p>

        <button class="buton" type="button" id="secimleriTemizle">
            Seçimleri Temizle
        </button>
    </section>
</form>

<script>
const kutular = document.querySelectorAll(".ders-sec");
const liste = document.getElementById("seciliDersler");
const sayi = document.getElementById("dersSayisi");
const toplam = document.getElementById("toplamAkts");

function hesapla() {
    let aktsToplami = 0;
    const secilenler = [];

    kutular.forEach(function(kutu) {
        if (kutu.checked) {
            aktsToplami += Number(kutu.value);
            secilenler.push(
                kutu.dataset.kod + " - " +
                kutu.dataset.ad + " (" + kutu.value + " AKTS)"
            );
        }
    });

    sayi.textContent = secilenler.length;
    toplam.textContent = aktsToplami;
    liste.textContent = secilenler.length
        ? secilenler.join(" | ")
        : "Henüz ders seçmedin.";
}

kutular.forEach(function(kutu) {
    kutu.addEventListener("change", hesapla);
});

document.getElementById("secimleriTemizle")
    .addEventListener("click", function() {
        kutular.forEach(function(kutu) {
            kutu.checked = false;
        });
        hesapla();
    });
</script>

<?php include "footer.php"; ?>
