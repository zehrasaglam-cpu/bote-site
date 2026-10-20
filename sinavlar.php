<?php
session_start();

if (!isset($_SESSION["sinavlar"])) {
    $_SESSION["sinavlar"] = [];
}

$mesaj = "";
$hata = "";
$duzenlenen = null;

// Ekleme, güncelleme ve silme
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $islem = $_POST["islem"] ?? "";

    if ($islem === "sil") {
        $id = (string)($_POST["id"] ?? "");

        foreach ($_SESSION["sinavlar"] as $anahtar => $sinav) {
            if ((string)$sinav["id"] === $id) {
                unset($_SESSION["sinavlar"][$anahtar]);
                break;
            }
        }

        $mesaj = "Sınav kaydı silindi.";
    }

    if ($islem === "ekle" || $islem === "guncelle") {
        $ders = trim($_POST["ders"] ?? "");
        $ogrenci = trim($_POST["ogrenci"] ?? "");
        $sinav_tipi = $_POST["sinav_tipi"] ?? "";
        $not = $_POST["not"] ?? "";

        $gecerliTipler = ["Vize", "Final", "Bütünleme"];

        if (
            $ders === "" || $ogrenci === "" ||
            !in_array($sinav_tipi, $gecerliTipler, true) ||
            $not === "" || !is_numeric($not) ||
            (float)$not < 0 || (float)$not > 100
        ) {
            $hata = "Bilgileri kontrol edin. Sınav notu 0 ile 100 arasında olmalıdır.";
        } else {
            $duzenlemeId = (string)($_POST["id"] ?? "");

            $kayit = [
                "ders" => $ders,
                "ogrenci" => $ogrenci,
                "sinav_tipi" => $sinav_tipi,
                "not" => (float)$not
            ];

            if ($islem === "ekle") {
                $kayit["id"] = uniqid();
                $_SESSION["sinavlar"][] = $kayit;
                $mesaj = "Sınav kaydı eklendi.";
            } else {
                foreach ($_SESSION["sinavlar"] as &$sinav) {
                    if ((string)$sinav["id"] === $duzenlemeId) {
                        $kayit["id"] = $sinav["id"];
                        $sinav = $kayit;
                        break;
                    }
                }
                unset($sinav);

                $mesaj = "Sınav kaydı güncellendi.";
            }
        }
    }
}

// Düzenlenecek sınavı bul
$duzenlemeId = (string)($_GET["duzenle"] ?? "");

foreach ($_SESSION["sinavlar"] as $sinav) {
    if ($duzenlemeId !== "" &&
        (string)$sinav["id"] === $duzenlemeId) {
        $duzenlenen = $sinav;
        break;
    }
}

function sinavTemizle($deger) {
    return htmlspecialchars((string)$deger, ENT_QUOTES, "UTF-8");
}

$sayfaBasligi = "Sınav Yönetimi | BÖTE";
include "header.php";
?>

<div class="container-fluid py-4">
    <h1 class="mb-4">Sınav Yönetimi</h1>

    <?php if ($mesaj !== ""): ?>
        <div class="alert alert-success"><?= sinavTemizle($mesaj) ?></div>
    <?php endif; ?>

    <?php if ($hata !== ""): ?>
        <div class="alert alert-danger"><?= sinavTemizle($hata) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <strong><?= $duzenlenen ? "Sınav Kaydını Güncelle" : "Yeni Sınav Ekle" ?></strong>
        </div>

        <div class="card-body">
            <form method="POST" action="sinavlar.php">
                <input type="hidden" name="islem"
                       value="<?= $duzenlenen ? "guncelle" : "ekle" ?>">

                <?php if ($duzenlenen): ?>
                    <input type="hidden" name="id"
                           value="<?= sinavTemizle($duzenlenen["id"]) ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Ders</label>
                        <input type="text" name="ders"
                               class="form-control" maxlength="100" required
                               placeholder="Örn. Web Tasarımı"
                               value="<?= sinavTemizle($duzenlenen["ders"] ?? "") ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Öğrenci</label>
                        <input type="text" name="ogrenci"
                               class="form-control" maxlength="100" required
                               placeholder="Öğrencinin ad ve soyadı"
                               value="<?= sinavTemizle($duzenlenen["ogrenci"] ?? "") ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Sınav Tipi</label>
                        <select name="sinav_tipi" class="form-select" required>
                            <option value="">Sınav tipi seçin</option>
                            <?php foreach (["Vize", "Final", "Bütünleme"] as $tip): ?>
                                <option value="<?= sinavTemizle($tip) ?>"
                                    <?= ($duzenlenen["sinav_tipi"] ?? "") === $tip ? "selected" : "" ?>>
                                    <?= sinavTemizle($tip) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Sınav Notu (0-100)</label>
                        <input type="number" name="not"
                               class="form-control" min="0" max="100"
                               step="0.5" required
                               value="<?= sinavTemizle($duzenlenen["not"] ?? "") ?>">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <?= $duzenlenen ? "Değişiklikleri Kaydet" : "Sınav Ekle" ?>
                    </button>

                    <?php if ($duzenlenen): ?>
                        <a href="sinavlar.php" class="btn btn-secondary">Vazgeç</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header"><strong>Sınav Listesi</strong></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Ders</th>
                            <th>Öğrenci</th>
                            <th>Sınav Tipi</th>
                            <th>Not</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($_SESSION["sinavlar"])): ?>
                            <tr>
                                <td colspan="5" class="text-center">Henüz sınav kaydı yok.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($_SESSION["sinavlar"] as $sinav): ?>
                                <tr>
                                    <td><?= sinavTemizle($sinav["ders"]) ?></td>
                                    <td><?= sinavTemizle($sinav["ogrenci"]) ?></td>
                                    <td><?= sinavTemizle($sinav["sinav_tipi"]) ?></td>
                                    <td><?= sinavTemizle($sinav["not"]) ?></td>
                                    <td>
                                        <a class="btn btn-warning btn-sm mb-1"
                                           href="sinavlar.php?duzenle=<?= urlencode((string)$sinav["id"]) ?>">Güncelle</a>

                                        <form method="POST" action="sinavlar.php"
                                              class="d-inline"
                                              onsubmit="return confirm('Bu sınavı silmek istiyor musunuz?');">
                                            <input type="hidden" name="islem" value="sil">
                                            <input type="hidden" name="id"
                                                   value="<?= sinavTemizle($sinav["id"]) ?>">
                                            <button type="submit" class="btn btn-danger btn-sm mb-1">Sil</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
