<?php
include 'config.php';

// Tambah customer
if (isset($_POST['add'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $insert = mysqli_query($conn, "INSERT INTO customer (nama, email, password) VALUES ('$nama', '$email', '$password')");
    echo $insert ? "Customer berhasil ditambahkan." : "Error: " . mysqli_error($conn);
}

// Edit customer
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $query = "UPDATE customer SET nama='$nama', email='$email'";
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query .= ", password='$password'";
    }
    $query .= " WHERE customer_id=$id";

    $update = mysqli_query($conn, $query);
    echo $update ? "Customer berhasil diperbarui." : "Error: " . mysqli_error($conn);
}

// Delete customer
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $delete = mysqli_query($conn, "DELETE FROM customer WHERE customer_id=$id");
    echo $delete ? "Customer berhasil dihapus." : "Error: " . mysqli_error($conn);
}

// Ambil semua data
$customers = mysqli_query($conn, "SELECT * FROM customer ORDER BY created_at DESC");

// Ambil data untuk form edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_result = mysqli_query($conn, "SELECT * FROM customer WHERE customer_id=$edit_id");
    $edit_data = mysqli_fetch_assoc($edit_result);
}
?>

<link rel="stylesheet" href="css/styled.css">
<h2>Data Customer</h2>
<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Created At</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($customers)) { ?>
    <tr>
        <td><?= $row['customer_id']; ?></td>
        <td><?= htmlspecialchars($row['nama']); ?></td>
        <td><?= htmlspecialchars($row['email']); ?></td>
        <td><?= $row['created_at']; ?></td>
        <td>
            <a href="?edit=<?= $row['customer_id']; ?>">Edit</a> |
            <a href="?delete=<?= $row['customer_id']; ?>" onclick="return confirm('Hapus customer ini?')">Hapus</a>
        </td>
    </tr>
    <?php } ?>
</table>

<hr>

<h2><?= $edit_data ? 'Edit' : 'Tambah'; ?> Customer</h2>
<form method="post">
    <?php if ($edit_data) { ?>
    <input type="hidden" name="id" value="<?= $edit_data['customer_id']; ?>">
    <?php } ?>

    <label>Nama:</label>
    <input type="text" name="nama" required value="<?= $edit_data ? $edit_data['nama'] : ''; ?>"><br><br>

    <label>Email:</label>
    <input type="email" name="email" required value="<?= $edit_data ? $edit_data['email'] : ''; ?>"><br><br>

    <label>Password <?= $edit_data ? '(Biarkan kosong jika tidak ingin diubah)' : ''; ?>:</label>
    <input type="password" name="password"><br><br>

    <button type="submit" name="<?= $edit_data ? 'update' : 'add'; ?>">
        <?= $edit_data ? 'Update' : 'Tambah'; ?>
    </button>
</form>