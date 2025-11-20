<?php
require 'function.php';
require 'cek.php';
// Dapetin ID barang yang dipassing di halaman sebelumnya
$idbarang = $_GET['id']; // get id barang
// get informasi barang berdasarkan data base
$get = mysqli_query($conn,"select * from stock where idbarang='$idbarang'");
$fetch = mysqli_fetch_assoc($get);
// set variable
$namabarang = $fetch['namabarang'];
$deskripsi = $fetch['deskripsi'];
$stock = $fetch['stock'];

  //  cel ada gambar atau tidak
 $gambar = $fetch['image']; // ambil gambar
if($gambar==null){
// jika tidak ada gambar
$img = 'No Photo';
} else {
// jika ada gambar
$img = '<img class="card-img-top" src="images/'.$gambar.'" alt="Card image" style="width:100%">';
}

// generate Qr
$urlview = 'http://localhost/stockbarang/view.php?id='.$idbarang;
$qrcode = 'https://api.qrserver.com/v1/create-qr-code/?size=350x350&data='.$urlview.'Example';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <title>Menampilkan Barang</title>
</head>
<body>
    <div class="container mt-4" >
        <p>Detail Barang:</p>
  <div class="card mt-4" style="width:400px">
    <?=$img;?>
    <div class="card-body">
      <h4 class="card-title"><?=$namabarang;?></h4>
      <h4 class="card-text"><?=$deskripsi;?></h4>
      <h4 class="card-text"><?=$stock;?></h4>
      
    </div>
  </div>
  <br>
    </div>
</body>
</html>