<?php 
// $koneksi = mysqli_connect("localhost","root","","polsek_tengah");
 
// // Check connection
// if (mysqli_connect_errno()){
// 	echo "Koneksi database gagal : " . mysqli_connect_error();
// }
class Database{
	private $host = "localhost";
	private $username = "root";
	private $pass = "";
	private $db = "polsek_tengah";

	public function __construct()
	{
		$this->dbs = mysqli_connect($this->host, $this->username, $this->pass, $this->db);
	}

	public function tampil_data()
	{
		$data = mysqli_query($this->dbs, "select nama_anggota, gender from anggota_polisi");
		return $data;
	}
} 

 
?>