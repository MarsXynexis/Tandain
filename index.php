<?php
$conn = mysqli_connect("localhost", "root", "", "crud_berita");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());


}
$query = "SELECT * FROM berita";
$result = mysqli_query($conn, $query);
$beritas = mysqli_fetch_all($result, MYSQLI_ASSOC);
var_dump($beritas);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Terbau</title>
</head>

<body>
    <div>
        <p>Portal Berita</p>
        <a href="#">Home</a>
        <a href="input.php">Input Berita</a>
    </div>
    <div>
        <h1>Berita Terbau</h1>
        <p>Selamat datang di situs berita terbau!</p>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Judul</th>
                    <th>Isi</th>
                    <th>Penerbit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td><img src="src/1.png" alt=""></td>
                    <td>Fanboy</td>
                    <td>Isiiiiiiiiiiiiiiiiii</td>
                    <td>yuwomp</td>
                    <td><a href="#">Edit</a> | <a href="#">Hapus</a></td>
                </tr>
                <?php 
                foreach ($beritas as $berita) {
                    echo "<tr>";
                    echo "<td>" . $berita['ID'] . "</td>";
                    echo '<td><img src="src/' . $berita['PATH_IMG'] . '" alt=""></td>';
                    echo "<td>" . $berita['JUDUL'] . "</td>";
                    echo "<td>" . $berita['ISI'] . "</td>";
                    echo "<td>" . $berita['PENERBIT'] . "</td>";
                    echo "<td><a href='edit.php?id=" . $berita['ID'] . "'>Edit</a> | <a href='hapus.php?id=" . $berita['ID'] . "'>Hapus</a></td>";
                    echo "</tr>";
                }
                ?>
                <!-- Data berita akan ditampilkan di sini -->
            </tbody>0
        </table>
    </div>
</body>

</html>