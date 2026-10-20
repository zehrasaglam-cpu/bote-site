<?php
session_start();

if (!isset($_SESSION["ogretim_elemanlari"])) {
    $_SESSION["ogretim_elemanlari"] = [];
}

$mesaj = "";
$hata = "";
$duzenlenen = null;

// Ekleme, güncelleme ve silme
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $islem = $_POST["islem"] ?? "";

    if ($islem === "sil") {
        $id = (string)($_POST["id"] ?? "");

        foreach ($_SESSION["ogretim_elemanlari"] as $anahtar => $kayit) {
            if ((string)$kayit["id"] === $id) {
                unset($_SESSION["ogretim_elemanlari"][$anahtar]);
                break;
            }
        }

        $mesaj = "Öğretim elemanı silindi.";
    }

    if ($islem === "ekle" || $islem === "guncelle") {
        $sicil = trim($_POST["sicil"] ?? "");
        $ad = trim($_POST["ad"] ?? "");
        $soyad = trim($_POST["soyad"] ?? "");
        $email = trim($_POST["email"] ?? "");

        if (
            $sicil === "" || $ad === "" || $soyad === "" ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            $hata = "Lütfen tüm alanları doğru doldurun.";
        } else {
            $sicilVar = false;
            $duzenlemeId = (string)($_POST["id"] ?? "");

            foreach ($_SESSION["ogretim_elemanlari"] as $kayit) {
                if (
                    $kayit["sicil"] === $sicil &&
                    ($islem === "ekle" ||
                     (string)$kayit["id"] !== $duzenlemeId)
                ) {
                    $sicilVar = true;
                    break;
                }
            }

            if ($sicilVar) {
                $hata = "Bu sicil numarası zaten kayıtlı.";
            } elseif ($islem === "ekle") {
                $_SESSION["ogretim_elemanlari"][] = [
                    "id" => uniqid(),
                    "sicil" => $sicil,
                    "ad" => $ad,
                    "soyad" => $soyad,
                    "email" => $email
                ];

                $mesaj = "Öğretim elemanı eklendi.";
            } else {
                foreach ($_SESSION["ogretim_elemanlari"] as &$kayit) {
                    if ((string)$kayit["id"] === $duzenlemeId) {
                        $kayit["sicil"] = $sicil;
                        $kayit["ad"] = $ad;
                        $kayit["soyad"] = $soyad;
                        $kayit["email"] = $email;
                        break;
                    }
                }
                unset($kayit);

                $mesaj = "Öğretim elemanı güncellendi.";
            }
        }
    }
}

// Güncellenecek kaydı bul
$duzenlemeId = (string)($_GET["duzenle"] ?? "");

foreach ($_SESSION["ogretim_elemanlari"] as $kayit) {
    if ($duzenlemeId !== "" &&
        (string)$kayit["id"] === $duzenlemeId) {
        $duzenlenen = $kayit;
        break;
    }
}

function ogretimTemizle($deger) {
    return htmlspecialchars((string)$deger, ENT_QUOTES, "UTF-8");
}

$sayfaBasligi = "Öğretim Elemanları | BÖTE";
include "header.php";
?>

<div class="container-fluid py-4">
    <h1 class="mb-4">Öğretim Elemanı Yönetimi</h1>

    <?php if ($mesaj !== ""): ?>
        <div class="alert alert-success"><?= ogretimTemizle($mesaj) ?></div>
    <?php endif; ?>

    <?php if ($hata !== ""): ?>
        <div class="alert alert-danger"><?= ogretimTemizle($hata) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <strong><?= $duzenlenen ? "Öğretim Elemanı Güncelle" : "Yeni Öğretim Elemanı Ekle" ?></strong>
        </div>

        <div class="card-body">
            <form method="POST" action="ogretim_elemanlari.php">
                <input type="hidden" name="islem"
                       value="<?= $duzenlenen ? "guncelle" : "ekle" ?>">

                <?php if ($duzenlenen): ?>
                    <input type="hidden" name="id"
                           value="<?= ogretimTemizle($duzenlenen["id"]) ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Kurum Sicil Numarası</label>
                        <input class="form-control" name="sicil"
                               maxlength="30" required
                               value="<?= ogretimTemizle($duzenlenen["sicil"] ?? "") ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Ad</label>
                        <input class="form-control" name="ad"
                               maxlength="50" required
                               value="<?= ogretimTemizle($duzenlenen["ad"] ?? "") ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Soyad</label>
                        <input class="form-control" name="soyad"
                               maxlength="50" required
                               value="<?= ogretimTemizle($duzenlenen["soyad"] ?? "") ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">E-posta</label>
                        <input class="form-control" type="email" name="email"
                               maxlength="100" required
                               value="<?= ogretimTemizle($duzenlenen["email"] ?? "") ?>">
                    </div>
                </div>

                <div class="mt-4">
                    <button class="btn btn-primary" type="submit">
                        <?= $duzenlenen ? "Değişiklikleri Kaydet" : "Öğretim Elemanı Ekle" ?>
                    </button>

                    <?php if ($duzenlenen): ?>
                        <a href="ogretim_elemanlari.php"
                           class="btn btn-secondary">Vazgeç</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header"><strong>Öğretim Elemanı Listesi</strong></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Sicil No</th>
                            <th>Ad</th>
                            <th>Soyad</th>
                            <th>E-posta</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($_SESSION["ogretim_elemanlari"])): ?>
                            <tr><td colspan="5" class="text-center">Henüz kayıt yok.</td></tr>
                        <?php else: ?>
                            <?php foreach ($_SESSION["ogretim_elemanlari"] as $kayit): ?>
                                <tr>
                                    <td><?= ogretimTemizle($kayit["sicil"]) ?></td>
                                    <td><?= ogretimTemizle($kayit["ad"]) ?></td>
                                    <td><?= ogretimTemizle($kayit["soyad"]) ?></td>
                                    <td><?= ogretimTemizle($kayit["email"]) ?></td>
                                    <td>
                                        <a class="btn btn-warning btn-sm mb-1"
                                           href="ogretim_elemanlari.php?duzenle=<?= urlencode((string)$kayit["id"]) ?>">Güncelle</a>

                                        <form method="POST" class="d-inline"
                                              action="ogretim_elemanlari.php"
                                              onsubmit="return confirm('Bu kaydı silmek istiyor musunuz?');">
                                            <input type="hidden" name="islem" value="sil">
                                            <input type="hidden" name="id"
                                                   value="<?= ogretimTemizle($kayit["id"]) ?>">
                                            <button class="btn btn-danger btn-sm mb-1" type="submit">Sil</button>
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
