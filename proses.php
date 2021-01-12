<?php
$host = "localhost";
$user = "root";
$pass = "";
$data = "cobacoba";

$conn = mysqli_connect($host, $user, $pass, $data);

$act = isset($_GET['act']) ? $_GET['act'] : '';

switch($act) {

    case 'list': 

        $sql = "select * from kontak order by id desc";
        $query = mysqli_query($conn, $sql);
        $data = [];
        while($row = mysqli_fetch_array($query)) {
            array_push($data, $row);
        }

        die(json_encode(['data' => $data]));

        break;

    case 'simpan':

        $nama = $_POST['nama'];

        $sql = "insert into kontak(nama) values('$nama');";
        $simpan = mysqli_query($conn, $sql);

        if($simpan) {
            die(json_encode([
                "status" => "sukses",
                "pesan" => "User ini berhasil menambah data!"
            ]));
        } else {
            die(json_encode([
                "status" => "gagal"
            ]));
        }

        break;

    case 'ubah':

        $kode = intval($_POST['kode']);

        $sql = "select * from kontak where id = '$kode' limit 1;";
        $query = mysqli_query($conn, $sql);
        $data = mysqli_fetch_assoc($query);
        die(json_encode(['data' => $data]));

        break;

    case 'update':

        $id = intval($_POST['id']);
        $nama = $_POST['nama'];

        $sql = "update kontak set nama = '$nama' where id = '$id'";
        $simpan = mysqli_query($conn, $sql);

        if($simpan) {
            die(json_encode([
                "status" => "sukses",
                "pesan" => "User ini berhasil mengubah data!"
            ]));
        } else {
            die(json_encode([
                "status" => "gagal"
            ]));
        }

        break;

    case 'hapus':

        $kode = intval($_POST['kode']);

        $sql = "delete from kontak where id = '$kode';";
        $query = mysqli_query($conn, $sql);

        if($query) {
            die(json_encode([
                "status" => "sukses",
                "pesan" => "User ini berhasil menghapus data!"
            ]));
        } else {
            die(json_encode([
                "status" => "gagal"
            ]));
        }
        
        break;

    default: 
        die('Sorry');
        break;

}