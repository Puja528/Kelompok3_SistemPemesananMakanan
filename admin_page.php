<?php
include 'config.php';

// Tambah produk
if (isset($_POST['add'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);

    $foto = '';
    if ($_FILES['foto']['name']) {
        $foto = basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto);
    }

    $insert = mysqli_query($conn, "INSERT INTO products (nama, harga, foto) VALUES ('$nama', '$harga', '$foto')");
    echo $insert ? "Produk berhasil ditambahkan." : "Error: " . mysqli_error($conn);
}

// Update produk
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);

    $query = "UPDATE products SET nama='$nama', harga='$harga'";
    if ($_FILES['foto']['name']) {
        $foto = basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto);
        $query .= ", foto='$foto'";
    }
    $query .= " WHERE product_id=$id";

    $update = mysqli_query($conn, $query);
    echo $update ? "Produk berhasil diperbarui." : "Error: " . mysqli_error($conn);
}

// Hapus produk
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $delete = mysqli_query($conn, "DELETE FROM products WHERE product_id=$id");
    echo $delete ? "Produk berhasil dihapus." : "Error: " . mysqli_error($conn);
}

// Ambil semua produk
$products = mysqli_query($conn, "SELECT * FROM products");

// Ambil data edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_result = mysqli_query($conn, "SELECT * FROM products WHERE product_id=$edit_id");
    $edit_data = mysqli_fetch_assoc($edit_result);
}
?>

<link rel="stylesheet" href="css/style.css">
<h2>Data Produk</h2>
<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Harga</th>
        <th>Foto</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($products)) { ?>
    <tr>
        <td><?= $row['product_id']; ?></td>
        <td><?= htmlspecialchars($row['nama']); ?></td>
        <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
        <td>
            <?php if (!empty($row['foto'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['foto']); ?>" width="80">
            <?php else: ?>
            Tidak ada
            <?php endif; ?>
        </td>
        <td>
            <a href="?edit=<?= $row['product_id']; ?>">Edit</a> |
            <a href="?delete=<?= $row['product_id']; ?>" onclick="return confirm('Hapus produk ini?')">Hapus</a>
        </td>
    </tr>
    <?php } ?>
</table>

<hr>

<h2><?= $edit_data ? 'Edit' : 'Tambah'; ?> Produk</h2>
<form method="post" enctype="multipart/form-data">
    <?php if ($edit_data) { ?>
    <input type="hidden" name="id" value="<?= $edit_data['product_id']; ?>">
    <?php } ?>

    <label>Nama Produk:</label>
    <input type="text" name="nama" required value="<?= $edit_data ? $edit_data['nama'] : ''; ?>"><br><br>

    <label>Harga:</label>
    <input type="text" name="harga" required value="<?= $edit_data ? $edit_data['harga'] : ''; ?>"><br><br>

    <label>Foto:</label>
    <input type="file" name="foto"><br><br>

    <button type="submit" name="<?= $edit_data ? 'update' : 'add'; ?>">
        <?= $edit_data ? 'Update' : 'Tambah'; ?>
    </button>
</form>