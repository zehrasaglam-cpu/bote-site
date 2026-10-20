<?php
session_start();

// Öğrenci dizisini ilk açılışta oluştur
if (!isset($_SESSION["ogrenciler"])) {
    $_SESSION["ogrenciler"] = [];
}

$mesaj = "";
$hata = "";

// Ekleme, güncelleme ve silme işlemleri
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $islem = $_POST["islem"] ?? "";

    if ($islem === "ekle" || $islem === "guncelle") {
        $okul_no = trim($_POST["okul_no"] ?? "");
        $ad = trim($_POST["ad"] ?? "");
        $soyad = trim($_POST["soyad"] ?? "");
        $bolum = trim($_POST["bolum"] ?? "");
        $telefon = trim($_POST["telefon"] ?? "");
        $email = trim($_POST["email"] ?? "");

        if (
            $okul_no === "" || $ad === "" ||
            $soyad === "" || $bolum === "" ||
            $telefon === "" ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            $hata = "Lütfen tüm alanları doğru doldurun.";
        } else {
            // Okul numarası benzersiz olsun
            $numaraVar = false;

            foreach ($_SESSION["ogrenciler"] as $ogrenci) {
                if (
                    $ogrenci["okul_no"] === $okul_no &&
                    (
                        $islem === "ekle" ||
                        $ogrenci["id"] != (int)($_POST["id"] ?? 0)
                    )
                ) {
                    $numaraVar = true;
                    break;
                }
            }

            if ($numaraVar) {
                $hata = "Bu okul numarası zaten kayıtlı.";
            } elseif ($islem === "ekle") {
                $_SESSION["ogrenciler"][] = [
                    "id" => time() . random_int(100, 999),
                    "okul_no" => $okul_no,
                    "ad" => $ad,
                    "soyad" => $soyad,
                    "bolum" => $bolum,
                    "telefon" => $telefon,
                    "email" => $email
                ];

                $mesaj = "Öğrenci başarıyla eklendi.";
            } else {
                $id = (string)($_POST["id"] ?? "");

                foreach ($_SESSION["ogrenciler"] as &$ogrenci) {
                    if ((string)$ogrenci["id"] === $id) {
                        $ogrenci["okul_no"] = $okul_no;
                        $ogrenci["ad"] = $ad;
                        $ogrenci["soyad"] = $soyad;
                        $ogrenci["bolum"] = $bolum;
                        $ogrenci["telefon"] = $telefon;
                        $ogrenci["email"] = $email;
                        break;
                    }
                }
                unset($ogrenci);

                $mesaj = "Öğrenci bilgileri güncellendi.";
            }
        }
    }

    if ($islem === "sil") {
        $id = (string)($_POST["id"] ?? "");

        foreach ($_SESSION["ogrenciler"] as $anahtar => $ogrenci) {
            if ((string)$ogrenci["id"] === $id) {
                unset($_SESSION["ogrenciler"][$anahtar]);
                break;
            }
        }

        $mesaj = "Öğrenci silindi.";
    }
}

// Düzenlenecek öğrenciyi bul
$duzenlenen = null;
$duzenlemeId = (string)($_GET["duzenle"] ?? "");

foreach ($_SESSION["ogrenciler"] as $ogrenci) {
    if ($duzenlemeId !== "" &&
        (string)$ogrenci["id"] === $duzenlemeId) {
        $duzenlenen = $ogrenci;
        break;
    }
}

// HTML çıktısında güvenli gösterim
function temizle($deger) {
    return htmlspecialchars(
        (string)$deger,
        ENT_QUOTES,
        "UTF-8"
    );
}

$sayfaBasligi = "Öğrenci Yönetimi | BÖTE";
include "header.php";
?>

<div class="container-fluid py-4">

    <h1 class="mb-4">Öğrenci Yönetimi</h1>

    <?php if ($mesaj !== ""): ?>
        <div class="alert alert-success">
            <?= temizle($mesaj) ?>
        </div>
    <?php endif; ?>

    <?php if ($hata !== ""): ?>
        <div class="alert alert-danger">
            <?= temizle($hata) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <strong>
                <?= $duzenlenen ? "Öğrenci Güncelle" : "Yeni Öğrenci Ekle" ?>
            </strong>
        </div>

        <div class="card-body">
            <form method="POST" action="ogrenciler.php">

                <input type="hidden" name="islem"
                    value="<?= $duzenlenen ? "guncelle" : "ekle" ?>">

                <?php if ($duzenlenen): ?>
                    <input type="hidden" name="id"
                        value="<?= temizle($duzenlenen["id"]) ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Okul Numarası</label>
                        <input type="text" name="okul_no"
                            class="form-control" maxlength="20" required
                            value="<?= temizle($duzenlenen["okul_no"] ?? "") ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Ad</label>
                        <input type="text" name="ad"
                            class="form-control" maxlength="50" required
                            value="<?= temizle($duzenlenen["ad"] ?? "") ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Soyad</label>
                        <input type="text" name="soyad"
                            class="form-control" maxlength="50" required
                            value="<?= temizle($duzenlenen["soyad"] ?? "") ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bölüm</label>
                        <input type="text" name="bolum"
                            class="form-control" maxlength="100" required
                            value="<?= temizle($duzenlenen["bolum"] ?? "") ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Telefon</label>
                        <input type="tel" name="telefon"
                            class="form-control" maxlength="20" required
                            value="<?= temizle($duzenlenen["telefon"] ?? "") ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">E-posta</label>
                        <input type="email" name="email"
                            class="form-control" maxlength="100" required
                            value="<?= temizle($duzenlenen["email"] ?? "") ?>">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <?= $duzenlenen ? "Güncelle" : "Öğrenci Ekle" ?>
                    </button>

                    <?php if ($duzenlenen): ?>
                        <a href="ogrenciler.php"
                            class="btn btn-secondary">Vazgeç</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Öğrenci Listesi</strong>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Okul No</th>
                            <th>Ad</th>
                            <th>Soyad</th>
                            <th>Bölüm</th>
                            <th>Telefon</th>
                            <th>E-posta</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($_SESSION["ogrenciler"])): ?>
                            <tr>
                                <td colspan="7" class="text-center">
                                    Henüz öğrenci kaydı yok.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($_SESSION["ogrenciler"] as $ogrenci): ?>
                                <tr>
                                    <td><?= temizle($ogrenci["okul_no"]) ?></td>
                                    <td><?= temizle($ogrenci["ad"]) ?></td>
                                    <td><?= temizle($ogrenci["soyad"]) ?></td>
                                    <td><?= temizle($ogrenci["bolum"]) ?></td>
                                    <td><?= temizle($ogrenci["telefon"]) ?></td>
                                    <td><?= temizle($ogrenci["email"]) ?></td>
                                    <td>
                                        <a class="btn btn-warning btn-sm mb-1"
                                            href="ogrenciler.php?duzenle=<?= urlencode((string)$ogrenci["id"]) ?>">
                                            Güncelle
                                        </a>

                                        <form method="POST"
                                            action="ogrenciler.php"
                                            class="d-inline"
                                            onsubmit="return confirm('Öğrenciyi silmek istiyor musunuz?');">
                                            <input type="hidden" name="islem"
                                                value="sil">
                                            <input type="hidden" name="id"
                                                value="<?= temizle($ogrenci["id"]) ?>">
                                            <button class="btn btn-danger btn-sm mb-1"
                                                type="submit">
                                                Sil
                                            </button>
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
