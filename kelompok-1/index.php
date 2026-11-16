<?php
session_start();

// =====================================
// 1. DATA AWAL PRODUK MENGGUNAKAN ARRAY
// =====================================

if (!isset($_SESSION["produk"])) {
    $_SESSION["produk"] = [
        1 => [
            "id" => 1,
            "nama" => "Laptop Pro 14",
            "harga" => 8500000,
            "kategori" => "Elektronik",
            "deskripsi" => "Laptop untuk belajar dan bekerja."
        ],
        2 => [
            "id" => 2,
            "nama" => "Mouse Wireless",
            "harga" => 175000,
            "kategori" => "Aksesoris",
            "deskripsi" => "Mouse nirkabel yang nyaman digunakan."
        ],
        3 => [
            "id" => 3,
            "nama" => "Keyboard Mechanical",
            "harga" => 425000,
            "kategori" => "Aksesoris",
            "deskripsi" => "Keyboard dengan tombol responsif."
        ],
        4 => [
            "id" => 4,
            "nama" => "Headset Stereo",
            "harga" => 275000,
            "kategori" => "Audio",
            "deskripsi" => "Headset untuk mendengarkan audio."
        ],
        5 => [
            "id" => 5,
            "nama" => "Monitor 24 Inci",
            "harga" => 1850000,
            "kategori" => "Elektronik",
            "deskripsi" => "Monitor untuk menunjang pekerjaan."
        ]
    ];

    $_SESSION["next_id"] = 6;
}

// =====================================
// 2. FUNGSI BANTUAN
// =====================================

function rupiah($angka)
{
    return "Rp" . number_format($angka, 0, ",", ".");
}

function e($teks)
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, "UTF-8");
}

// =====================================
// 3. PROSES TAMBAH, UBAH, DAN HAPUS
// =====================================

$pesan = "";
$jenisPesan = "success";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $aksi = $_POST["aksi"] ?? "";

    // CREATE: TAMBAH PRODUK
    if ($aksi === "tambah") {

        $nama = trim($_POST["nama"] ?? "");
        $harga = $_POST["harga"] ?? "";
        $kategori = trim($_POST["kategori"] ?? "");
        $deskripsi = trim($_POST["deskripsi"] ?? "");

        if ($nama === "" || $harga === "" || $kategori === "") {
            $pesan = "Nama, harga, dan kategori wajib diisi.";
            $jenisPesan = "error";
        } elseif (!is_numeric($harga) || $harga < 0) {
            $pesan = "Harga harus berupa angka yang valid.";
            $jenisPesan = "error";
        } else {
            $id = $_SESSION["next_id"];

            $_SESSION["produk"][$id] = [
                "id" => $id,
                "nama" => $nama,
                "harga" => (float) $harga,
                "kategori" => $kategori,
                "deskripsi" => $deskripsi
            ];

            $_SESSION["next_id"]++;

            $pesan = "Produk berhasil ditambahkan.";
        }
    }

    // UPDATE: UBAH PRODUK
    elseif ($aksi === "ubah") {

        $id = (int) ($_POST["id"] ?? 0);

        $nama = trim($_POST["nama"] ?? "");
        $harga = $_POST["harga"] ?? "";
        $kategori = trim($_POST["kategori"] ?? "");
        $deskripsi = trim($_POST["deskripsi"] ?? "");

        if (!isset($_SESSION["produk"][$id])) {
            $pesan = "Produk tidak ditemukan.";
            $jenisPesan = "error";
        } elseif ($nama === "" || $harga === "" || $kategori === "") {
            $pesan = "Nama, harga, dan kategori wajib diisi.";
            $jenisPesan = "error";
        } elseif (!is_numeric($harga) || $harga < 0) {
            $pesan = "Harga harus berupa angka yang valid.";
            $jenisPesan = "error";
        } else {
            $_SESSION["produk"][$id] = [
                "id" => $id,
                "nama" => $nama,
                "harga" => (float) $harga,
                "kategori" => $kategori,
                "deskripsi" => $deskripsi
            ];

            $pesan = "Produk berhasil diperbarui.";
        }
    }

    // DELETE: HAPUS PRODUK
    elseif ($aksi === "hapus") {

        $id = (int) ($_POST["id"] ?? 0);

        if (isset($_SESSION["produk"][$id])) {
            unset($_SESSION["produk"][$id]);

            $pesan = "Produk berhasil dihapus.";
        } else {
            $pesan = "Produk tidak ditemukan.";
            $jenisPesan = "error";
        }
    }

    // REDIRECT SETELAH POST
    header("Location: " . strtok($_SERVER["REQUEST_URI"], "?") .
        "?pesan=" . urlencode($pesan) .
        "&jenis=" . urlencode($jenisPesan));
    exit;
}

// =====================================
// 4. MENGAMBIL DATA UNTUK EDIT
// =====================================

$dataEdit = null;

if (isset($_GET["edit"])) {
    $idEdit = (int) $_GET["edit"];

    if (isset($_SESSION["produk"][$idEdit])) {
        $dataEdit = $_SESSION["produk"][$idEdit];
    }
}

// Pesan notifikasi
if (isset($_GET["pesan"])) {
    $pesan = $_GET["pesan"];
    $jenisPesan = $_GET["jenis"] ?? "success";
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Katalog Produk CRUD</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            color: #263238;
        }

        header {
            background: white;
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: auto;
        }

        header h2 {
            margin: 0;
            color: #df725f;
        }

        .hero {
            padding: 35px 0;
        }

        .hero h1 {
            margin-bottom: 8px;
        }

        .hero p {
            color: #68747b;
        }

        .layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 20px;
            align-items: start;
        }

        .panel {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e5e8eb;
        }

        .panel h3 {
            margin-top: 0;
        }

        label {
            display: block;
            margin: 12px 0 6px;
            font-weight: bold;
            font-size: 14px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 7px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        button,
        .btn {
            display: inline-block;
            padding: 9px 13px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
        }

        .btn-primary {
            background: #df725f;
            color: white;
        }

        .btn-edit {
            background: #fff0d5;
            color: #80551a;
        }

        .btn-delete {
            background: #ffeded;
            color: #bd3d3d;
        }

        .btn-cancel {
            background: #eee;
            color: #333;
        }

        .btn-submit {
            width: 100%;
            margin-top: 18px;
        }

        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 7px;
        }

        .success {
            background: #eaf7ee;
            color: #21633a;
        }

        .error {
            background: #fff0f0;
            color: #922f2f;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .product-card {
            background: white;
            padding: 18px;
            border-radius: 12px;
            border: 1px solid #e5e8eb;
        }

        .category {
            display: inline-block;
            background: #fff0ed;
            color: #bd5949;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .product-card h3 {
            margin-bottom: 5px;
        }

        .description {
            color: #68747b;
            font-size: 13px;
            min-height: 35px;
        }

        .price {
            color: #bd5949;
            font-size: 18px;
            font-weight: bold;
            margin: 12px 0;
        }

        .actions {
            display: flex;
            gap: 7px;
        }

        .actions form {
            margin: 0;
        }

        footer {
            margin-top: 40px;
            padding: 20px;
            text-align: center;
            background: white;
            color: #68747b;
        }

        @media (max-width: 800px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .product-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="container">
            <h2>Katalog Produk</h2>
        </div>
    </header>

    <main class="container">

        <section class="hero">
            <h1>Manajemen Katalog Produk</h1>
            <p>
                Aplikasi web dinamis menggunakan PHP,
                array asosiatif, dan foreach.
            </p>
        </section>

        <?php if ($pesan !== "") : ?>
            <div class="alert <?= e($jenisPesan); ?>">
                <?= e($pesan); ?>
            </div>
        <?php endif; ?>

        <div class="layout">

            <!-- FORM TAMBAH DAN EDIT -->
            <section class="panel">

                <h3>
                    <?= $dataEdit ? "Edit Produk" : "Tambah Produk"; ?>
                </h3>

                <form method="POST">

                    <input
                        type="hidden"
                        name="aksi"
                        value="<?= $dataEdit ? "ubah" : "tambah"; ?>">

                    <?php if ($dataEdit) : ?>
                        <input
                            type="hidden"
                            name="id"
                            value="<?= e($dataEdit["id"]); ?>">
                    <?php endif; ?>

                    <label>Nama Produk</label>

                    <input
                        type="text"
                        name="nama"
                        placeholder="Masukkan nama produk"
                        value="<?= e($dataEdit["nama"] ?? ""); ?>"
                        required>

                    <label>Harga Produk</label>

                    <input
                        type="number"
                        name="harga"
                        placeholder="Masukkan harga"
                        min="0"
                        value="<?= e($dataEdit["harga"] ?? ""); ?>"
                        required>

                    <label>Kategori</label>

                    <select name="kategori" required>
                        <option value="">Pilih kategori</option>

                        <?php
                        $kategoriProduk = [
                            "Elektronik",
                            "Aksesoris",
                            "Audio",
                            "Penyimpanan",
                            "Lainnya"
                        ];

                        foreach ($kategoriProduk as $kategori) :
                        ?>

                            <option
                                value="<?= e($kategori); ?>"
                                <?= (($dataEdit["kategori"] ?? "") === $kategori)
                                    ? "selected" : ""; ?>>
                                <?= e($kategori); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <label>Deskripsi</label>

                    <textarea
                        name="deskripsi"
                        rows="3"
                        placeholder="Deskripsi produk"><?= e($dataEdit["deskripsi"] ?? ""); ?></textarea>

                    <button
                        type="submit"
                        class="btn-primary btn-submit">
                        <?= $dataEdit ? "Simpan Perubahan" : "Tambah Produk"; ?>
                    </button>

                    <?php if ($dataEdit) : ?>
                        <a href="index.php" class="btn btn-cancel">
                            Batal
                        </a>
                    <?php endif; ?>

                </form>

            </section>

            <!-- DAFTAR PRODUK -->
            <section>

                <h2>
                    Daftar Produk
                    (<?= count($_SESSION["produk"]); ?>)
                </h2>

                <div class="product-grid">

                    <?php foreach ($_SESSION["produk"] as $item) : ?>

                        <article class="product-card">

                            <span class="category">
                                <?= e($item["kategori"]); ?>
                            </span>

                            <h3>
                                <?= e($item["nama"]); ?>
                            </h3>

                            <p class="description">
                                <?= e($item["deskripsi"]); ?>
                            </p>

                            <div class="price">
                                <?= rupiah($item["harga"]); ?>
                            </div>

                            <div class="actions">

                                <!-- UPDATE -->
                                <a
                                    href="?edit=<?= (int) $item["id"]; ?>"
                                    class="btn btn-edit">
                                    Edit
                                </a>

                                <!-- DELETE -->
                                <form
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus produk ini?');">

                                    <input
                                        type="hidden"
                                        name="aksi"
                                        value="hapus">

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $item["id"]; ?>">

                                    <button
                                        type="submit"
                                        class="btn-delete">
                                        Hapus
                                    </button>

                                </form>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            </section>

        </div>

    </main>

    <footer>
        <p>
            Praktik Pemrograman Web - PHP CRUD menggunakan Array
        </p>
    </footer>

</body>

</html>