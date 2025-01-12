<?php
include "koneksi.php";
$id =isset($_POST['id'])?$_POST['id']:'';
$kelem_t =isset($_POST['kelem_t'])?$_POST['kelem_t']:'';
$suhu_u  =isset($_POST['suhu_u'])?$_POST['suhu_u']:'';
$durasi_penyiraman =isset($_POST['durasi_penyiraman'])?$_POST['durasi_penyiraman']:'';
$reading_time = isset($_POST['reading_time']) ? $_POST['reading_time'] : '';

$query =mysqli_query($mysqli, "INSERT INTO `insert_sensor`(`kelem_t`, `suhu_u`, `durasi_penyiraman`, `reading_time`) VALUES ($kelem_t,$suhu_u,$durasi_penyiraman,$reading_time)");
?>
