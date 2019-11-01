<?php
include 'Database.php';
class GenetikaPenjadwalan{
    public $ukuranPopulasi, $jmlHari, $crossoverRate, $mutationRate, $maksimalGen, $penalti, $g = 0;
    public function __construct($ukuranPopulasi = "ukuranPopulasi", $jmlHari = "jmlHari", $mutationRate = "mutationRate", $maksimalGen = "maksimalGen"){
        $this->ukuranPopulasi = $ukuranPopulasi;
        $this->jmlHari = $jmlHari;
        $this->mutationRate = $mutationRate;
        $this->maksimalGen = $maksimalGen;
    }

    public function setUkuranPopulasi($ukuranPopulasi){
        $this->ukuranPopulasi = $ukuranPopulasi;
    }
    public function getUkuranPopulasi(){
        return $this->ukuranPopulasi;
    }
    public function setJmlHari($jmlHari){
        $this->jmlHari = $jmlHari;
    }
    public function getJmlHari(){
        return $this->jmlHari;
    }
    public function setMutationRate($mutationRate){
        $this->mutationRate = $mutationRate;
    }

    public function getMutationRate(){
        return $this->mutationRate;
    }
    public function setMaksimalGen($maksimalGen){
        $this->maksimalGen = $maksimalGen;
    }
    public function getMaksimalGen(){
        return $this->maksimalGen;
    }

    public function hitungPopulasi(){
        $x = $this->ukuranPopulasi;
        return $x*2;
    }

    public function database(){
        $db = new Database();
        $data = $db->tampil_data();
        
        $data_anggota = [];
        while($row = mysqli_fetch_assoc($data)){
            $data_anggota[] = $row;
        }
        return $data_anggota;
    }

    public function inisialisasi_awal(){
        $data_anggota = $this->database();

        //anggota polisi
        $gen = [];
        for ($i=0; $i < count($data_anggota); $i++) { 
            $gen[$i] = $data_anggota[$i]['nama_anggota'];
        }
        //anggota polisi wanita
        $gender = [];
        for ($i=0; $i < count($data_anggota); $i++) { 
            $gender[$i] = $data_anggota[$i]['gender'];
        }
        $wanita = [];
        while ($array_name = current($gender)) {
            if ($array_name == 'P') {
                $wanita[] = key($gender);
            }
            next($gender);
        }

        $kromosom = $this->populasi($gen);
        
        //proses GA
        $fk = $this->evaluasi($kromosom, $wanita);

        //Simpan bibit ke file json
        $json = json_encode($fk);
        file_put_contents("data.json", $json);
        
        // $_SESSION['fitness_kromosom'] = $fk;
        $_SESSION['ukuran_populasi'] = $this->getUkuranPopulasi();
        $_SESSION['jml_hari'] = $this->getJmlHari();

        return $fk;
    }
    public function prosesPenjadwalanGA(){
        $data_anggota = $this->database();
        $gen = [];
        for ($i=0; $i < count($data_anggota); $i++) { 
            $gen[$i] = $data_anggota[$i]['nama_anggota'];
        }
        //anggota polisi wanita
        $gender = [];
        for ($i=0; $i < count($data_anggota); $i++) { 
            $gender[$i] = $data_anggota[$i]['gender'];
        }
        $wanita = [];
        while ($array_name = current($gender)) {
            if ($array_name == 'P') {
                $wanita[] = key($gender);
            }
            next($gender);
        }

        //print_r($this->getUkuranPopulasi());
        if($this->getUkuranPopulasi() != -1){
            $fitness_kromosom = $this->inisialisasi_awal();
        }else{
            $this->setUkuranPopulasi($_SESSION['ukuran_populasi']);
            $source = 'data.json';
            $content = file_get_contents($source);
            $fitness_kromosom = json_decode($content, true);
        }

        $fitness_terbaik = 0;
        $kromosom_terbaik = [];
        $g = 0;
        $start_time = microtime(true);
        while($fitness_terbaik < (24*$this->jmlHari) && $g < $this->maksimalGen){
            $hasil_seleksi = $this->seleksi($fitness_kromosom);
            $hasil_crossover = $this->crossover($hasil_seleksi);
            $hasil_mutasi = $this->mutation($hasil_crossover);
            $hasil_akhir = $this->evaluasi_akhir($hasil_mutasi, $wanita);
            usort($hasil_akhir, function($a, $b) {
                return $b['fitness'] <=> $a['fitness'];
            });
            $kromosom_tertinggi = $hasil_akhir[0];
            if(empty($kromosom_terbaik)){
                $kromosom_terbaik = $kromosom_tertinggi;
            }else if($kromosom_tertinggi['fitness'] > $kromosom_terbaik['fitness']){
                $kromosom_terbaik = $kromosom_tertinggi;
            }else{
                $kromosom_terbaik = $kromosom_terbaik;
            }
            $fitness_terbaik = $kromosom_terbaik['fitness'];
             $g = $g+1;
             
        }
        $end_time = microtime(true);

        // Calculate script execution time 
        $execution_time = ($end_time - $start_time);
        $seconds = $execution_time;
        $start_seconds = round($seconds);
        if($start_seconds <60)
        {
            $hours = "";
            $minutes ="";
            $seconds = $start_seconds."s";
        }elseif($start_seconds>60 && $start_seconds<3600){
       
            $minutes = floor($start_seconds/60);
            $seconds = $start_seconds - $minutes*60;
        
            $minutes = "$minutes"."m";
            $seconds = "$seconds"."s";
            $hours = "";
        }else{
            $hours = floor($start_seconds/3600);
            $minutes = floor (($start_seconds - $hours*3600)/60);
            $seconds = ($start_seconds-($hours*3600))- $minutes*60;
         
            $minutes = "$minutes"."m";
            $seconds = "$seconds"."s";
            $hours = "$hours"."h";
        }

        for ($i=0; $i < count($kromosom_terbaik['kromosom']); $i++) {
            $nama_anggota['jmlHari'] = $this->jmlHari;
            $nama_anggota['penalti'] = $kromosom_terbaik['penalti'];
            $nama_anggota['fitness'] = $kromosom_terbaik['fitness'];
            $nama_anggota['iterasi'] = $g;
            $nama_anggota['exec'] = [$hours, $minutes, $seconds];
            $nama_anggota['kromosom'][$i]['key'] = $kromosom_terbaik['kromosom'][$i];
            $nama_anggota['kromosom'][$i]['nama'] = $gen[$kromosom_terbaik['kromosom'][$i]];
        }

        $nama_anggota['akurasi'] = 0;
        $hasil_cek = $this->cek_benturan($nama_anggota, $wanita);
        for ($i=0; $i < count($kromosom_terbaik['kromosom']); $i++) { 
            $nama_anggota['kromosom'][$i]['bg'] = $hasil_cek[$i];
        }

         $nama_anggota['akurasi'] = round(($nama_anggota['fitness']/(24*$this->jmlHari))*100,2);
        return $nama_anggota;
    }

    public function populasi($gen){
        $keygen = array_keys($gen);
        $kromosom = [[]];

        $jmlGen = (24*$this->jmlHari)*$this->ukuranPopulasi;
        $g1 = floor($jmlGen / count($keygen));
        $g2 = $jmlGen % count($keygen);
        $randomArray = [[]];
        $randomArray2 = [];

        for ($i=0; $i < $g1 ; $i++) {
            $randomArray[$i] = [];
            while (count($randomArray[$i]) < count($keygen)) {
                $randomKey = mt_rand(0, count($keygen)-1);
                $randomArray[$i][$randomKey] = $keygen[$randomKey];
            }
        }
        $random1 = $this->array_flatten($randomArray);

        while (count($randomArray2) < $g2) {
            $randomKey = mt_rand(0, count($keygen)-1);
            $randomArray2[$randomKey] = $keygen[$randomKey];
        }
        $random2 = $randomArray2;

        $rand_gen = array_merge($random1, $random2);
        
        for ($i=0; $i < $this->ukuranPopulasi; $i++) {
                $kromosom[$i]= array_slice($rand_gen, $i*(24*$this->jmlHari), 24*$this->jmlHari);
        }

        return $kromosom;
    }

    public function evaluasi($kromosom, $wanita){
        //evaluasi hitung nilai fitness
        $kromosom_pecah = [];
        $total_fitness = [];
        for ($i=0; $i < $this->ukuranPopulasi; $i++) {
            $penalti = 0;
                for ($k=0; $k < $this->jmlHari ; $k++) { 
                    $kromosom_perhari[$i][$k] = array_slice($kromosom[$i], $k*24, 24);

                    //perhari
                    $kromosom_perhari_s[$i][$k] = $kromosom_perhari[$i][$k];
                    sort($kromosom_perhari_s[$i][$k]);

                    $benturan=[[]];
                    for ($j=0; $j < count($kromosom_perhari_s[$i][$k]); $j++) { 
                        if($j > 0){
                            if($kromosom_perhari_s[$i][$k][$j] == $kromosom_perhari_s[$i][$k][$j-1]){
                                $benturan[$i][$k][$j] = 1;
                            }else{
                                $benturan[$i][$k][$j] = 0;
                            }
                        }
                    }

                    $p = array_sum($benturan[$i][$k]);
                    $penalti+=$p*2;

                    for ($l=0; $l < 3; $l++) {
                        $kromosom_pershift[$i][$k][$l] = array_slice($kromosom_perhari[$i][$k], $l*8, 8);

                        sort($kromosom_pershift[$i][$k][$l]);
                        // print_r($kromosom_pecah[$i][$j]);
                        for ($x=0; $x < count($kromosom_pershift[$i][$k][$l]); $x++) { 
                            if($x > 0){
                                if($kromosom_pershift[$i][$k][$l][$x] == $kromosom_pershift[$i][$k][$l][$x-1]){
                                    //$benturan[$i][$j][$x] = $kromosom_pecah[$i][$j][$x];
                                    $penalti+=1*2;
                                }
                            }
                        }
                        //Cek shift wanita di malam hari
                        if($l == 2){
                            for($m = 0; $m < count($kromosom_pershift[$i][$k][$l]); $m++){
                                if (in_array($kromosom_pershift[$i][$k][$l][$m], $wanita)) {
                                    $penalti += 1;
                                }
                            }
                        }

                    }
                    //cek shift malam, langsung masuk pagi
                    if($k > 0){
                        $diff = array_diff($kromosom_pershift[$i][$k][0],$kromosom_pershift[$i][$k-1][2]);
                        if(count($diff) != count($kromosom_pershift[$i][$k][2])){
                            $penalti+=1;
                        }
                    }
                }
            
            $total_fitness[$i] = (24*$this->jmlHari) - $penalti;
        }

        $fitness_kromosom = [[]];
        for ($i=0; $i < $this->ukuranPopulasi; $i++) {
            $fitness_kromosom[$i]['penalti'] = $penalti;
            $fitness_kromosom[$i]['fitness'] = $total_fitness[$i];
            $fitness_kromosom[$i]['kromosom'] = $kromosom[$i];
        }
        
        return $fitness_kromosom;
    }

    public function seleksi($fitness_kromosom){
        $seleksi=[[]];
        $key_seleksi=[[]];
        $hasil_seleksi=[];
        for ($i=0; $i < $this->ukuranPopulasi; $i++) {
            for ($j=0; $j < 3; $j++) { 
                $key_seleksi[$i][$j] = array_rand($fitness_kromosom, 1);
                $seleksi[$i][$j] = $fitness_kromosom[$key_seleksi[$i][$j]];
            }

            usort($seleksi[$i], function($a, $b) {
                return $b['fitness'] <=> $a['fitness'];
            });

            $hasil_seleksi[$i] = $seleksi[$i][0];    
        }

        return $hasil_seleksi;
    }

    public function crossover($hasil_seleksi){
        //sorting dr yg terbesar
        usort($hasil_seleksi, function($a, $b) {
            return $b['fitness'] <=> $a['fitness'];
        });
        //memilih induk
        $kromosom_induk = [];
        $kromosom_induk[0] = $hasil_seleksi[0];
        $kromosom_induk[1] = $hasil_seleksi[1];
        if($kromosom_induk[1] == $kromosom_induk[0]){
            $kromosom_induk[1] = $hasil_seleksi[2];
        }

        $batas_gen = count($kromosom_induk[0]['kromosom']);
        $key_potong = array_rand($kromosom_induk[0]['kromosom'], 1);
        $key_induk = [];
        for ($i=$key_potong; $i < $batas_gen; $i++) { 
            $key_induk[$i] = $i;
        }

        $key = array_keys($kromosom_induk[0]['kromosom']);

        for($i=0;$i<count($kromosom_induk[0]['kromosom']);$i++) {
            if(in_array($key[$i], $key_induk)) {
                $temp = $kromosom_induk[0]['kromosom'][$i];
                $kromosom_induk[0]['kromosom'][$i] = $kromosom_induk[1]['kromosom'][$i];
                $kromosom_induk[1]['kromosom'][$i] = $temp;
            }
        }

        array_splice($hasil_seleksi, -2, 2, $kromosom_induk);

        return ($hasil_seleksi);
    }

    public function random_0_1(){
        return (float)mt_rand() / (float)getrandmax();
    }

    public function mutation($hasil_crossover){

        $hasil_mutasi = [[]];
        for($i=0; $i < $this->ukuranPopulasi; $i++) {
            $r = $this->random_0_1();

            if($r<$this->mutationRate){
                for ($j=0; $j < $this->jmlHari*3 ; $j++) { 
                    $kromosom_pecah_mutasi[$i][$j] = array_slice($hasil_crossover[$i]['kromosom'], $j*8, 8);
                    $array = $kromosom_pecah_mutasi[$i][$j];
                    $a = array_rand($kromosom_pecah_mutasi[$i][$j]);
                    $b = array_rand($hasil_crossover[$i]['kromosom']);
                    $this->moveElement($array, $a, $b);
                    $kromosom_pecah_mutasi[$i][$j] = $array;
                }
                $array1 = $kromosom_pecah_mutasi[$i];
                
                $hasil_mutasi[$i] = $this->array_flatten($array1);
            }else{
                $hasil_mutasi[$i] = $hasil_crossover[$i]['kromosom'];
            }
        }

        return $hasil_mutasi;
    }

    public function moveElement(&$array, $a, $b) {
        $out = array_splice($array, $a, 1);
        array_splice($array, $b, 0, $out);
    }

    public function array_flatten($array1) { 
        if (!is_array($array1)) { 
          return FALSE; 
        } 
        $result = array(); 
        foreach ($array1 as $key => $value) { 
          if (is_array($value)) { 
            $result = array_merge($result, $this->array_flatten($value)); 
          } 
          else { 
            $result[$key] = $value; 
          } 
        } 
        return $result; 
    }

    public function evaluasi_akhir($hasil_mutasi, $wanita){
        //evaluasi hitung nilai fitness
        $kromosom_pecah = [];
        $total_fitness = [];
        for ($i=0; $i < $this->ukuranPopulasi; $i++) {
            
            $penalti_akhir = 0;
                for ($k=0; $k < $this->jmlHari ; $k++) { 
                    $kromosom_perhari[$i][$k] = array_slice($hasil_mutasi[$i], $k*24, 24);
                    //perhari
                    $kromosom_perhari_s[$i][$k] = $kromosom_perhari[$i][$k];
                    sort($kromosom_perhari_s[$i][$k]);
                    $benturan=[[]];
                    for ($j=0; $j < count($kromosom_perhari_s[$i][$k]); $j++) { 
                        if($j > 0){
                            if($kromosom_perhari_s[$i][$k][$j] == $kromosom_perhari_s[$i][$k][$j-1]){
                                //$benturan[$i][$k][$j] = $kromosom_perhari_s[$i][$k][$j];
                                $benturan[$i][$k][$j] = 1;
                            }else{
                                $benturan[$i][$k][$j] = 0;
                            }
                        }
                    }

                    $p = array_sum($benturan[$i][$k]);
                    $penalti_akhir+=$p*2;

                    for ($l=0; $l < 3; $l++) {
                        $kromosom_pershift[$i][$k][$l] = array_slice($kromosom_perhari[$i][$k], $l*8, 8);

                        sort($kromosom_pershift[$i][$k][$l]);
                        for ($x=0; $x < count($kromosom_pershift[$i][$k][$l]); $x++) { 
                            if($x > 0){
                                if($kromosom_pershift[$i][$k][$l][$x] == $kromosom_pershift[$i][$k][$l][$x-1]){
                                    $penalti_akhir+=1*2;
                                }
                            }
                        }
                        if($l == 2){
                            for($m = 0; $m < count($kromosom_pershift[$i][$k][$l]); $m++){
                                if (in_array($kromosom_pershift[$i][$k][$l][$m], $wanita)) {
                                    $penalti_akhir += 1;
                                }
                            }
                        }
                    }

                    //cek shift malam, langsung masuk pagi
                    if($k > 0){
                        $diff = array_diff($kromosom_pershift[$i][$k][0],$kromosom_pershift[$i][$k-1][2]);
                        if(count($diff) != count($kromosom_pershift[$i][$k][2])){
                            $penalti_akhir+=1;
                        }
                    }
                }
            
            $total_fitness_akhir[$i] = (24*$this->jmlHari) - $penalti_akhir;
        }

        $hasil_akhir = [[]];
        for ($i=0; $i < $this->ukuranPopulasi; $i++) {
            $hasil_akhir[$i]['penalti'] = $penalti_akhir;
            $hasil_akhir[$i]['fitness'] = $total_fitness_akhir[$i];
            $hasil_akhir[$i]['kromosom'] = $hasil_mutasi[$i];
        }
        
        return $hasil_akhir;
    }

    public function cek_benturan($nama_anggota ,$wanita){
        for ($i=0; $i < $this->getJmlHari()*24 ; $i++) {
            $krm[$i] = $nama_anggota['kromosom'][$i]['key'];
        }
        $same = [];
        for ($j=0; $j < $this->getJmlHari(); $j++) {
            $kromosom_perhari[$j] = array_slice($krm, $j*24, 24);
            $kromosom_perhari_[$j] = array_slice($krm, $j*24, 24);

            sort($kromosom_perhari[$j]);

            //perhari
            for ($k=0; $k < count($kromosom_perhari[$j]); $k++) { 
                if($k > 0){
                    if($kromosom_perhari[$j][$k] == $kromosom_perhari[$j][$k-1]){
                        $benturan[$j][$k] = $kromosom_perhari[$j][$k];
                    }else{
                        $benturan[$j][$k] = -1;
                    }
                }
            }
            $benturan_pershift = [[]];
            $benturan_malamPagi = [[-1]];
            for ($l=0; $l < 3; $l++) {
                $kromosom_pershift[$j][$l] = array_slice($kromosom_perhari_[$j], $l*8, 8);
                                
                if($j > 0 && $l == 2){
                    $same[$j][$l] = array_intersect($kromosom_pershift[$j][0],$kromosom_pershift[$j-1][2]);
                    $same_[$j][$l] = array_values($same[$j][$l]);
                    if(!empty($same_[$j][$l])){
                        $benturan_malamPagi[$j] = $same_[$j][$l];
                    }else{
                        $benturan_malamPagi[$j][0] = -1;
                    }
                }
                
                sort($kromosom_pershift[$j][$l]);
                for ($m=0; $m < count($kromosom_pershift[$j][$l]); $m++) { 
                    if ($m > 0) {
                        if ($kromosom_pershift[$j][$l][$m] == $kromosom_pershift[$j][$l][$m-1]) {
                            $benturan_pershift[$j][$l][$m-1] = $kromosom_pershift[$j][$l][$m];
                        }else{
                            $benturan_pershift[$j][$l][$m-1] = -1;
                        }
                    }
                }
            }
            
            $benturan[$j] = array_unique($benturan[$j]);

            for ($k=0; $k < count($kromosom_perhari_[$j]); $k++) {
                if ($k < 8 && in_array($kromosom_perhari_[$j][$k], $benturan_pershift[$j][0])) {
                    $bg[$j][$k] = "red";
                }elseif($k > 8 && $k < 16 && in_array($kromosom_perhari_[$j][$k], $benturan_pershift[$j][1])){
                    $bg[$j][$k] = "red";
                }elseif($k > 16 && $k < 24 && in_array($kromosom_perhari_[$j][$k], $benturan_pershift[$j][2])){
                    $bg[$j][$k] = "red";
                }elseif( in_array($kromosom_perhari_[$j][$k], $benturan_malamPagi[$j])){
                    $bg[$j][$k] = "blue";
                }elseif(in_array($kromosom_perhari_[$j][$k], $benturan[$j])) {
                    $bg[$j][$k] = "yellow";
                } elseif ($k > 15 && in_array($kromosom_perhari_[$j][$k], $wanita)) {
                    $bg[$j][$k] = "green";
                } else {
                    $bg[$j][$k] = "white";
                }
            }
            
        }

        $hasil_cek = $this->array_flatten($bg);
        return $hasil_cek;
    }    
}