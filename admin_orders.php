<?php
include 'config.php';
date_default_timezone_set("Asia/Jakarta");

// Tambah pesanan
if (isset($_POST['add'])) {
    $customer_id = (int)$_POST['customer_id'];
    $product_id = (int)$_POST['product_id'];
    $jumlah = (int)$_POST['jumlah'];

    $q = mysqli_query($conn, "SELECT harga FROM products WHERE id=$product_id");
    $row = mysqli_fetch_assoc($q);
    $total_harga = $row['harga'] * $jumlah;

    $insert = mysqli_query($conn, "INSERT INTO orders (customer_id, product_id, jumlah, total_harga) VALUES ($customer_id, $product_id, $jumlah, $total_harga)");

    if ($insert) echo " Pesanan berhasil ditambahkan.";
    else echo " Error: " . mysqli_error($conn);
}

// Update pesanan
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $customer_id = (int)$_POST['customer_id'];
    $product_id = (int)$_POST['product_id'];
    $jumlah = (int)$_POST['jumlah'];

    $q = mysqli_query($conn, "SELECT harga FROM products WHERE id=$product_id");
    $row = mysqli_fetch_assoc($q);
    $total_harga = $row['harga'] * $jumlah;

    $update = mysqli_query($conn, "UPDATE orders SET customer_id=$customer_id, product_id=$product_id, jumlah=$jumlah, total_harga=$total_harga WHERE id=$id");

    if ($update) echo " Pesanan berhasil diperbarui.";
    else echo " Error: " . mysqli_error($conn);
}

// Delete pesanan
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $delete = mysqli_query($conn, "DELETE FROM orders WHERE id=$id");

    if ($delete) echo " Pesanan berhasil dihapus.";
    else echo " Error: " . mysqli_error($conn);
}

// Ambil data semua pesanan
$orders = mysqli_query($conn, "
    SELECT o.id, c.nama AS customer, p.nama, o.jumlah, o.total_harga, o.tanggal 
    FROM orders o
    JOIN customer c ON o.customer_id = c.customer_id
    JOIN products p ON o.product_id = p.product_id
    ORDER BY o.tanggal DESC
");

// Data untuk form edit jika ada ?edit=
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_result = mysqli_query($conn, "SELECT * FROM orders WHERE id=$edit_id");
    $edit_data = mysqli_fetch_assoc($edit_result);
}
?>

<link rel="stylesheet" href="css/styles.css">
<h2>Data Pesanan</h2>
<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>product</th>
        <th>Jumlah</th>
        <th>Total Harga</th>
        <th>Tanggal</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($orders)) { ?>
    <tr>
        <td><?= $row['id']; ?></td>
        <td><?= htmlspecialchars($row['customer']); ?></td>
        <td><?= htmlspecialchars($row['nama']); ?></td>
        <td><?= $row['jumlah']; ?></td>
        <td>Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
        <td><?= $row['tanggal']; ?></td>
        <td>
            <a href="?edit=<?= $row['id']; ?>">Edit</a> |
            <a href="?delete=<?= $row['id']; ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
        </td>
    </tr>
    <?php } ?>
</table>

<hr>

<h2><?= $edit_data ? 'Edit' : 'Tambah'; ?> Pesanan</h2>
<form method="post">
    <?php if ($edit_data) { ?>
    <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
    <?php } ?>

    <button type="submit" name="<?= $edit_data ? 'update' : 'add'; ?>">
        <?= $edit_data ? 'Update' : 'Tambah'; ?>
    </button>
</form>