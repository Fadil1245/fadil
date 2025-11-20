<?php
session_start();

//membuat koneksi ke database
$conn = mysqli_connect("localhost","root","","stockbarang");


// Menambah barang baru
if(isset($_POST['addnewbarang'])){
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];
    

      // soal gambar
    $allowed_extension = array('png','jpg');
    $nama = $_FILES['file']['name']; // mengambil nama gambar
    $dot = explode('.',$nama);
    $ekstensi = strtolower(end($dot)); // mengambil ekstensi gambar
    $ukuran = $_FILES['file']['size']; // mengambil size filenya
    $file_tmp = $_FILES['file']['tmp_name']; // ngambil lokasi file

    // penamaan file -> enksripsi
    $image = md5(uniqid($nama,true) . time()).'.'.$ekstensi; // menggabungkan nama file yang di deskripsi dengan ekstensinya
    
    // Validasi udah ada atau belum
    $cek = mysqli_query($conn, "select * from stock where namabarang='$namabarang'");
    $hitung = mysqli_num_rows($cek);

    if($hitung<1){
        // jika belum

        // proses upload gambar
        if(in_array($ekstensi, $allowed_extension) === true){
            // validasi ukiuran file
            if($ukuran < 15000000){
                move_uploaded_file($file_tmp, 'images/'.$image);

            $addtotabbel = mysqli_query($conn,"insert into stock (namabarang, deskripsi, stock, image ) values('$namabarang','$deskripsi','$stock','$image')");
            if($addtotabbel){
                header('location:index.php');
            } else {
                echo 'gagal';
                header('location:index.php');
            }

            } else {
                // kalau filenya lebih dari 15mb
            echo '
            <script>
            alert("Ukuran terlalu besar");
            window.location.href="index.php";
            </script>
            ';
            }
        } else {
            // kalau file tidak png / jpg
            echo '
            <script>
            alert("File harus png/jpg");
            window.location.href="index.php";
            </script>
            ';
        }


    } else {
        // jika sudah ada
        echo '
        <script>
        alert("nama barang sudah terdaftar");
        window.location.href="index.php";
        </script>
        ';
    }
};

//Menambah barang masuk
if(isset($_POST['barangmasuk'])){
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];
    $admin = $_SESSION['email'];

    $cekstocksekarang = mysqli_query($conn,"select * from stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang+$qty;

    $addtomasuk = mysqli_query($conn,"insert into masuk(idbarang, keterangan, qty, admin) values('$barangnya','$penerima','$qty','$admin')");
    $updatestockmasuk = mysqli_query($conn,"update stock set stock='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
    if($addtomasuk&&$updatestockmasuk){
        header('location:masuk.php');
    } else {
        echo 'Gagal';
        header('location:masuk.php');
    }
}

//Menambah barang keluar
if(isset($_POST['addbarangkeluar'])){
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn,"select * from stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    
    if($stocksekarang >= $qty){
        // kalau barangnya cukup
    $tambahkanstocksekarangdenganquantity = $stocksekarang-$qty;

        $addtomasuk = mysqli_query($conn,"insert into keluar(idbarang, penerima, qty) values('$barangnya','$penerima','$qty')");
        $updatestockmasuk = mysqli_query($conn,"update stock set stock='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
        if($addtomasuk&&$updatestockmasuk){
            header('location:keluar.php');
        } else {
            echo 'Gagal';
            header('location:keluar.php');
        }
    } else {
        // kalau barangnya gak cukup
        echo '
        <script>
        alert("Stock saat ini tidak mencukupi");
        window.location.href="keluar.php"
        </script>
        ';
    }
}


// update info barang
if(isset($_POST['updatebarang'])){
    $idb = $_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
      // soal gambar
    $allowed_extension = array('png','jpg');
    $nama = $_FILES['file']['name']; // mengambil nama gambar
    $dot = explode('.',$nama);
    $ekstensi = strtolower(end($dot)); // mengambil ekstensi gambar
    $ukuran = $_FILES['file']['size']; // mengambil size filenya
    $file_tmp = $_FILES['file']['tmp_name']; // ngambil lokasi file

    // penamaan file -> enksripsi
    $image = md5(uniqid($nama,true) . time()).'.'.$ekstensi; // menggabungkan nama file yang di deskripsi dengan ekstensinya

if($ukuran==0){
    // jika tidak ingin upload
        $update = mysqli_query($conn,"update stock set namabarang='$namabarang',deskripsi='$deskripsi' where idbarang ='$idb'");
    if($update){
        header('location:index.php');
    } else {
        echo 'Gagal';
        header('location:index.php');
    }
} else {
    // jika ingin
        move_uploaded_file($file_tmp, 'images/'.$image);
        $update = mysqli_query($conn,"update stock set namabarang='$namabarang',deskripsi='$deskripsi', image='$image' where idbarang ='$idb'");
    if($update){
        header('location:index.php');
    } else {
        echo 'Gagal';
        header('location:index.php');
    }
}

}

// Menghapus barang dari stock
if(isset($_POST['hapusbarang'])){
    $idb = $_POST['idb'];

    $gambar = mysqli_query($conn,"select * from stock where idbarang='$idb'");
    $get = mysqli_fetch_array($gambar);
    $img = 'images/'.$get['image'];
    unlink($img);
       
    
    $hapus = mysqli_query($conn,"delete from stock where idbarang='$idb'");
    if($hapus){
        header('location:index.php');
    } else {
        echo 'Gagal';
        header('location:index.php');
    }
}

// mengubah data barang masuk
if(isset($_POST['updatebarangmasuk'])){
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];
    $deskripsi = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];
    
    $qtyskrg = mysqli_query($conn, "select * from masuk where idmasuk='$idm'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty>$qtyskrg){
        $selisih = $qty-$qtyskrg;
        $kurangin = $stockskrg + $selisih;
        $kurangistocknya = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
        $updatenya = mysqli_query($conn,"update masuk set qty='$qty', keterangan='$deskripsi' where idmasuk='$idm'");
        if($kurangistocknya&&$updatenya){
            header('location.masuk.php');
        } else {
            echo 'Gagal';
            header('location:masuk.php');
        }
    } else {
        $selisih = $qtyskrg-$qty;
        $kurangin = $stockskrg - $selisih;
        $kurangistocknya = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
        $updatenya = mysqli_query($conn,"update masuk set qty='$qty', keterangan='$deskripsi' where idmasuk='$idm'");
        if($kurangistocknya&&$updatenya){
            header('location.masuk.php');
        } else {
            echo 'Gagal';
            header('location:masuk.php');
        }
    }

}   

// Menghapus barang masuk
if(isset($_POST['hapusbarangmasuk'])) {
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idm = $_POST['idm'];
    
    $getdatastock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock-$qty;

    $update = mysqli_query($conn,"update stock set stock='$selisih' where idbarang='$idb'");
    $hapusdata = mysqli_query($conn,"delete from masuk where idmasuk='$idm'");

    if($update&&$hapusdata){
        header('location:keluar.php');
    } else {
        header ('location:keluar.php');
    }
}



// mengubah data barang keluar
if(isset($_POST['updatebarangkeluar'])){
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty']; //Qty baru inputan user

    // mengambil stock barang saat ini
    $lihatstock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];
    
    // qty barang keluar saat ini
    $qtyskrg = mysqli_query($conn, "select * from keluar where idkeluar='$idk'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty>$qtyskrg){
        $selisih = $qty-$qtyskrg;
        $kurangin = $stockskrg - $selisih;

        if($selisih <= $stockskrg){
        $kurangistocknya = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
        $updatenya = mysqli_query($conn,"update keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
            if($kurangistocknya&&$updatenya){
                header('location:keluar.php');
            } else {
                echo 'Gagal';
                header('location:keluar.php');
            }
        } else {
            echo '
            <script>alert("stock tidak mencukupi");
            window.location.href="keluar.php";
            </script>
            ';
        }

    } else {
        $selisih = $qtyskrg-$qty;
        $kurangin = $stockskrg + $selisih;
        $kurangistocknya = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
        $updatenya = mysqli_query($conn,"update keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
        if($kurangistocknya&&$updatenya){
            header('location:keluar.php');
        } else {
            echo 'Gagal';
            header('location:keluar.php');
        }
    }

}   

// Menghapus barang keluar
if(isset($_POST['hapusbarangkeluar'])) {
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idk = $_POST['idk'];
    
    $getdatastock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock+$qty;

    $update = mysqli_query($conn,"update stock set stock='$selisih' where idbarang='$idb'");
    $hapusdata = mysqli_query($conn,"delete from keluar where idkeluar='$idk'");

    if($update&&$hapusdata){
        header('location:keluar.php');
    } else {
        header ('location:keluar.php');
    }
}


//Menambah admin baru
if(isset($_POST['addadmin'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $queryinsert = mysqli_query($conn,"insert into login (email, password) values('$email','$password')");

    if($queryinsert){
        // if berhasil
        header('location:admin.php');
    } else {
        // kalau gagal insert ke db
        header('location:admin.php');
    }
}

// edit data admin
if(isset($_POST['updateadmin'])){
    $emailbaru = $_POST['emailadmin'];
    $passwordbaru = $_POST['passwordbaru'];
    $idnya = $_POST['id'];

    $queryupdate = mysqli_query($conn,"update login set email='$emailbaru', password='$passwordbaru' where iduser='$idnya'");
    
    if($queryupdate){
        header('location:admin.php');
    } else {
        header('location:admin.php');
    }
}

// hapus admin
if(isset($_POST['hapusadmin'])){
    $id = $_POST['id'];

    $querydelete = mysqli_query($conn,"delete from login where iduser='$id'");

    if($querydelete){
        header('location:admin.php');
    } else {
        header('location:admin.php');
    }
}


// meminjam barang
if(isset($_POST['pinjam'])){
    $idbarang = $_POST['barangnya'];
    $qty = $_POST['qty'];
    $penerima = $_POST['penerima'];

    // ambil stock sekarang
    $stok_saat_ini = mysqli_query($conn,"SELECT * FROM stock WHERE idbarang='$idbarang'");
    $stok_nya = mysqli_fetch_array($stok_saat_ini);
    $stok = $stok_nya['stock'];

    // kurangi stok
    $new_stock = $stok - $qty;

    // insert peminjaman
    $insertpinjam = mysqli_query($conn, "INSERT INTO peminjaman (idbarang, qty, peminjam) VALUES ('$idbarang', '$qty', '$penerima')");

    // update stok
    $kurangistok = mysqli_query($conn,"UPDATE stock SET stock='$new_stock' WHERE idbarang='$idbarang'");
    
    if($insertpinjam && $kurangistok){
        echo '<script>alert("Berhasil"); window.location.href="keluar.php";</script>';
    } else {
        echo '<script>alert("Tidak Berhasil"); window.location.href="keluar.php";</script>';
    }
}

// menyelesaikan pinjam
if(isset($_POST['barangkembali'])){
    $idpinjam = $_POST['idpinjam'];
    $idbarang = $_POST['idbarang'];

    // ubah status jadi kembali
    $update_status = mysqli_query($conn, "UPDATE peminjaman SET status='kembali' WHERE idpeminjaman='$idpinjam'");

    // ambil stok saat ini
    $stok_saat_ini = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idbarang'");
    $stok_nya = mysqli_fetch_array($stok_saat_ini);
    $stok = $stok_nya['stock'];

    // ambil qty yang dipinjam
    $stok_saat_ini1 = mysqli_query($conn, "SELECT * FROM peminjaman WHERE idpeminjaman='$idpinjam'");
    $stok_nya1 = mysqli_fetch_array($stok_saat_ini1);
    $qty_dipinjam = $stok_nya1['qty'];

    // tambahkan kembali stok
    $new_stock = $stok + $qty_dipinjam;

    // update stok di tabel stock
    $kembalikan_stock = mysqli_query($conn, "UPDATE stock SET stock='$new_stock' WHERE idbarang='$idbarang'");

    if($update_status && $kembalikan_stock){
        echo '<script>alert("Berhasil"); window.location.href="keluar.php";</script>';
    } else {
        echo '<script>alert("Tidak Berhasil"); window.location.href="keluar.php";</script>';
    }
}




?>