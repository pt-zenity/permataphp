<?php
defined('main') or die('Restricted access');

class Iso8583 {
  
  private $DATA_ELEMENT  = array (
    1  => array('b', 64, 0, 0,1),
    2  => array('n', 19, 1, 0,2),
    3  => array('n', 6, 0, 0,3),
    4  => array('n', 12, 0, 0,4),
    5  => array('n', 12, 0, 0,5),
    6  => array('n', 12, 0, 0,6),
    7  => array('n', 10, 0, 0,7),
    8  => array('n', 8, 0, 0,8),
    9  => array('n', 8, 0, 0,9),
    10  => array('n', 8, 0, 0,10),
    11  => array('n', 6, 0, 0,11),
    12  => array('n', 6, 0, 0,12),
    13  => array('n', 4, 0, 0,13),
    14  => array('n', 4, 0, 0,14),
    15  => array('n', 4, 0, 0,15),
    16  => array('n', 4, 0, 0,16),
    17  => array('n', 4, 0, 0,17),
    18  => array('n', 4, 0, 0,18),
    19  => array('n', 3, 0, 0,19),
    20  => array('n', 3, 0, 0,20),
    21  => array('n', 3, 0, 0,21),
    22  => array('n', 3, 0, 0,22),
    23  => array('n', 3, 0, 0,23),
    24  => array('n', 3, 0, 0,24),
    25  => array('n', 2, 0, 0,25),
    26  => array('n', 2, 0, 0,26),
    27  => array('n', 1, 0, 0,27),
    28  => array('n', 8, 0, 0,28),
    29  => array('n', 8, 0, 0,29),
    30  => array('n', 8, 0, 0,30),
    31  => array('n', 8, 0, 0,31),
    32  => array('n', 11, 1, 0,32),
    33  => array('n', 11, 1, 0,33),
    34  => array('n', 28, 1, 0,34),
    35  => array('z', 37, 1, 0,35),
    36  => array('n', 104, 1, 0,36),
    37  => array('an', 12, 0, 0,37),
    38  => array('an', 6, 0, 0,38),
    39  => array('an', 2, 0, 0,39),
    40  => array('an', 3, 0, 0,40),
    41  => array('ans', 8, 0, 0,41),
    42  => array('ans', 15, 0, 0,42),
    43  => array('ans', 40, 0, 43,43),
    44  => array('ans', 25, 1, 0,44),
    45  => array('an',  76, 1, 0,45),
    46  => array('an', 999, 1, 0,46),
    47  => array('an', 999, 1, 0,47),
    48  => array('ans', 999, 1, 0,48),
    49  => array('an', 3, 0, 0,49),
    50  => array('an', 3, 0, 0,50),
    51  => array('an', 3, 0, 0,51),
    52  => array('an', 64, 0, 0,52),
    53  => array('n', 16, 0, 0,53),
    54  => array('an', 120, 1, 0,54),
    55  => array('ans', 999, 1, 0,55),
    56  => array('ans', 999, 1, 0,56),
    57  => array('ans', 999, 1, 0,57),
    58  => array('ans', 999, 1, 0,58),
    59  => array('ans', 999, 1, 0,59),
    60  => array('ans', 999, 1, 0,60),
    61  => array('ans', 999, 1, 0,61),
    62  => array('ans', 999, 1, 0,62),
    63  => array('ans', 999, 1, 0,63),
    64  => array('b', 16, 0, 0,64),
    65  => array('b', 1, 0, 0,65),
    66  => array('n', 1, 0, 0,66),
    67  => array('n', 2, 0, 0,67),
    68  => array('n', 3, 0, 0,68),
    69  => array('n', 3, 0, 0,69),
    70  => array('n', 3, 0, 0,70),
    71  => array('n', 4, 0, 0,71),
    72  => array('n', 4, 0, 0,72),
    73  => array('n', 6, 0, 0,73),
    74  => array('n', 10, 0, 0,74),
    75  => array('n', 10, 0, 0,75),
    76  => array('n', 10, 0, 0,76),
    77  => array('n', 10, 0, 0,77),
    78  => array('n', 10, 0, 0,78),
    79  => array('n', 10, 0, 0,79),
    80  => array('n', 10, 0, 0,80),
    81  => array('n', 10, 0, 0,81),
    82  => array('n', 12, 0, 0,82),
    83  => array('n', 12, 0, 0,83),
    84  => array('n', 12, 0, 0,84),
    85  => array('n', 12, 0, 0,85),
    86  => array('n', 16, 0, 0,86),
    87  => array('n', 16, 0, 0,87),
    88  => array('n', 16, 0, 0,88),
    89  => array('n', 16, 0, 0,89),
    90  => array('n', 42, 0, 0,90),
    91  => array('an', 1, 0, 0,91),
    92  => array('an', 2, 0, 0,92),
    93  => array('an', 5, 0, 0,93),
    94  => array('an', 7, 0, 0,94),
    95  => array('an', 42, 0, 0,95),
    96  => array('b', 64, 0, 0,96),
    97  => array('n', 16, 0, 0,97),
    98  => array('ans', 25, 0, 0,98),
    99  => array('n', 11, 1, 0,99),
    100  => array('n', 11, 1, 0,100),
    101  => array('ans', 17, 1, 0,101),
    102  => array('ans', 28, 1, 0,102),
    103  => array('ans', 28, 1, 0,103),
    104  => array('ans', 100, 1, 0,104),
    105  => array('ans', 999, 1, 0,105),
    106  => array('ans', 999, 1, 0,106),
    107  => array('ans', 999, 1, 0,107),
    108  => array('ans', 999, 1, 0,108),
    109  => array('ans', 999, 1, 0,109),
    110  => array('ans', 999, 1, 0,110),
    111  => array('ans', 999, 1, 0,111),
    112  => array('ans', 999, 1, 0,112),
    113  => array('ans', 999, 1, 0,113),
    114  => array('ans', 999, 1, 0,114),
    115  => array('ans', 999, 1, 0,115),
    116  => array('ans', 999, 1, 0,116),
    117  => array('ans', 999, 1, 0,117),
    118  => array('ans', 999, 1, 0,118),
    119  => array('ans', 999, 1, 0,119),
    120  => array('ans', 999, 1, 0,120),
    121  => array('ans', 999, 1, 0,121),
    122  => array('ans', 999, 1, 0,122),
    123  => array('ans', 999, 1, 0,123),
    124  => array('ans', 999, 1, 0,124),
    125  => array('ans', 999, 1, 0,125),
    126  => array('ans', 999, 1, 0,126),
    127  => array('ans', 999, 1, 0,127),
    128  => array('b', 64, 0, 0,128)
  ); 
      
  private $_data  = array(); 
  private $_bitmap  = '';
  private $_mti  = '';
  private $_iso  = '';
  private $_valid  = array();
  
  public function initData(){
      $this->_data = array() ; 
  }
  
  //return data element in correct format
  private function _packElement($data_element, $data) {
    $result  = "";
    //numeric value

    //echo($data_element[4] . '<br> ');
    if ($data_element[0]=='n' && is_numeric($data) && strlen($data)<=$data_element[1]) {
      $data  = str_replace(".", "", $data);

      //fix length
      if ($data_element[2]==0) {
        $result  = sprintf("%0". $data_element[1] ."s", $data);
      }else{
        //dinamic length        
        if (strlen($data) <= $data_element[1]) {                
          $result  = sprintf("%0". strlen($data_element[1])."d", strlen($data)). $data;
        }
      }
    }
     //37  => array('an', 12, 0, 0),
    //alpha value
    if (($data_element[0]=='a' && ctype_alpha($data) && strlen($data)<=$data_element[1]) ||
      ($data_element[0]=='an' && ctype_alnum($data) && strlen($data)<=$data_element[1]) ||
      ($data_element[0]=='z' && strlen($data)<=$data_element[1]) ||
      ($data_element[0]=='ans' && strlen($data)<=$data_element[1])) {

      //fix length
      if ($data_element[2]== 0) {
        $result  = sprintf("% ". $data_element[1] ."s", $data);
        if($data_element[4] == "37" || $data_element[4] == "41" || $data_element[4] == "42"|| $data_element[4] == "43"){
          $result  = sprintf("%-". $data_element[1] ."s", $data);
        }
      }else{ 
        //dinamic length        
        if (strlen($data) <= $data_element[1]) {                
          $result  = sprintf("%0". strlen($data_element[1])."s", strlen($data)). $data;
        }
      }
    }

    //bit value
    if ($data_element[0]=='b' && strlen($data)<=$data_element[1]) {
      //fix length
      if ($data_element[2]==0) {
        $tmp  = sprintf("%0". $data_element[1] ."d", $data);

        while ($tmp!='') {
          $result  .= base_convert(substr($tmp, 0, 4), 2, 16);
          $tmp  = substr($tmp, 4, strlen($tmp)-4);
        }
      }
    }

    return $result;
  }

  public function generateEmtpyIso($nLength, $nData){
    return sprintf("%0". $nLength . "d",$nData);
  }
  
  public function ClearANSNotFix($nLength, $s){
    return sprintf("%0". $nLength . "d",$nData);
  }
  //calculate bitmap from data element    
  private function _calculateBitmap() {  
    $tmp  = sprintf("%064d", 0);    
    $tmp2  = sprintf("%064d", 0);    
    foreach ($this->_data as $key=>$val) {
      if ($key<65) {
        $tmp[$key-1]  = 1;
      }
      else {
        $tmp[0]  = 1;
        $tmp2[$key-65]  = 1;
      }
    }

    $result  = "";
    if ($tmp[0]==1) {
      while ($tmp2!='') {
        $result  .= base_convert(substr($tmp2, 0, 4), 2, 16);
        $tmp2  = substr($tmp2, 4, strlen($tmp2)-4);
      }
    }
    $main  = "";
    while ($tmp!='') {
      $main  .= base_convert(substr($tmp, 0, 4), 2, 16);
      $tmp  = substr($tmp, 4, strlen($tmp)-4);
    }
    $this->_bitmap  = strtoupper($main. $result);
    //ShowLog("aku hanya " . $this->_bitmap) ;
    return $this->_bitmap;
  }

  //parse iso string and retrieve mti 
  public function _parseMTI() {
    $this->addMTI(substr($this->_iso, 0, 4));
    if (strlen($this->_mti)==4 && $this->_mti[1]!=0) {
      $this->_valid['mti'] = true;
    }
  }

  //clear all data
  public function _clear() {
    $this->_mti  = '';
    $this->_bitmap  = '';
    $this->_data  = '';
    $this->_iso  = '';
  }

  //parse iso string and retrieve bitmap    
  public function _parseBitmap() {
    $this->_valid['bitmap']  = false; 
    //$this->_iso = "0210B01800000A111008000000000600000001100000000000000011231308103b31107693870025032020318400 0320203184000322018-08-14 11:33:22|1110015074010000000000000000000000000000000096e79218965eb72c92a549dd5a3301120240002890141032111185107201211100150740100" ;
    $inp  = substr($this->_iso, 4, 32);
    //print $inp ;
    if (strlen($inp)>=16) {
      $primary  = '';
      $secondary  = '';
      for ($i=0; $i<16; $i++) {
        $primary  .= sprintf("%04d", base_convert($inp[$i], 16, 2));
      }
      if ($primary[0]==1 && strlen($inp)>=32) {
        for ($i=16; $i<32; $i++) {
          $secondary  .= sprintf("%04d", base_convert($inp[$i], 16, 2));
        }
        $this->_valid['bitmap'] = true;
      }
      if ($secondary=='') $this->_valid['bitmap']  = true;
    }
    //save to data element with ? character
    $tmp = $primary . $secondary ;
    //print $tmp ;
    $vaTest = array() ;
    for ($i=0; $i<strlen($tmp); $i++) {
      if ($tmp[$i]==1) {
        $this->_data[$i+1]  = '?';          
        $vaTest[$i+1] = '?' ; 
      }
    }
    $this->_data = $vaTest ;
    $this->_bitmap  = $tmp;
    //print_r($vaTest) ; 
    //print_r($this->_data) ; 
    return $tmp;
  }

  //parse iso string and retrieve data element
  public function _parseData() {     
    //Pembedaan Header MTI,Bite dan Isi Data
    
    if ($this->_data[1] == '?') { 
    //if (!empty($this->_data[1])) {
      $inp  = substr($this->_iso, 4+32, strlen($this->_iso)-4-32); 
    } else {
      $inp  = substr($this->_iso, 4+16, strlen($this->_iso)-4-16);
    }
    if (is_array($this->_data)) {
      $this->_valid['data']  = true;
      foreach ($this->_data as $key=>$val) {
        $this->_valid['de'][$key]  = false;

        if ($this->DATA_ELEMENT[$key][0]!='b') {            
          if ($this->DATA_ELEMENT[$key][2]==0) {
            $tmp  = substr($inp, 0, $this->DATA_ELEMENT[$key][1]);
            if (strlen($tmp)==$this->DATA_ELEMENT[$key][1]) {
              if ($this->DATA_ELEMENT[$key][0]=='n') {
                $this->_data[$key]  = substr($inp, 0, $this->DATA_ELEMENT[$key][1]);
              } else {
                $this->_data[$key]  = ltrim(substr($inp, 0, $this->DATA_ELEMENT[$key][1]));
              }
              $this->_valid['de'][$key]  = true;
              $inp  = substr($inp, $this->DATA_ELEMENT[$key][1], strlen($inp)-$this->DATA_ELEMENT[$key][1]);
            }
          } else {
            $len  = strlen($this->DATA_ELEMENT[$key][1]);
            if($this->DATA_ELEMENT[$key][4] == "125"){
              $len  = "3";
            }
            $tmp  = substr($inp, 0, $len);
            if($this->DATA_ELEMENT[$key][4] == "100" || $this->DATA_ELEMENT[$key][4] == "125" || $this->DATA_ELEMENT[$key][4] == "103"){
              //echo($this->DATA_ELEMENT[$key][0] . ' tmp ' . $tmp . ' =  '  . strlen($this->DATA_ELEMENT[$key][1])  . ' atau ' . $len . ' isinya : '  . $this->DATA_ELEMENT[$key][1] . ' ' .$this->DATA_ELEMENT[$key][2]. ' ' .$this->DATA_ELEMENT[$key][4]. ' ' .$this->DATA_ELEMENT[$key][4] . '<br>' );
              // echo(strlen($tmp) . ' == ' . $len . '<br>'. '<br>');
            }

            if (strlen($tmp)==$len ) {
              $num  = (integer) $tmp;
              if($this->DATA_ELEMENT[$key][4] == "125"){
                $len  = strlen($this->DATA_ELEMENT[$key][1]);
              }
              $inp  = substr($inp, $len, strlen($inp)-$len);

              $tmp2  = substr($inp, 0, $num);
              if($this->DATA_ELEMENT[$key][4] == "100" || $this->DATA_ELEMENT[$key][4] == "125" || $this->DATA_ELEMENT[$key][4] == "103"){
                // echo($inp  . ' ' . $tmp2 . '=====>  '.  $num); 
                // echo('<br>'); 
                // echo('<br>'); 
              }
              if (strlen($tmp2)==$num) {
                if ($this->DATA_ELEMENT[$key][0]=='n') {
                  $this->_data[$key]  = (double) $tmp2;
                }
                else {
                  $this->_data[$key]  = ltrim($tmp2);
                }

                $inp  = substr($inp, $num, strlen($inp)-$num);
                $this->_valid['de'][$key]  = true;

                if($this->DATA_ELEMENT[$key][4] == "100" || $this->DATA_ELEMENT[$key][4] == "125" || $this->DATA_ELEMENT[$key][4] == "103"){

                   // echo($this->_data[$key] . '=====>  '  .(double) $tmp2 . ' ' .$this->DATA_ELEMENT[$key][4]); 
                  //  echo('<br>'); 
                  //  echo('<br>'); 
                  //  echo('<br>'); 

                  //  echo($inp  . ' = ' .  ' substr( ' . $inp . ' , ' .  $num . ' ,  ' . ' strlen( ' . $inp . ' )- ' . $num . ' ) '); 
                  //  echo($this->_valid['de'][$key]); 
                  //   echo('<br>'); 
                 //   echo($key); 
                  //  echo('<br>'); echo('<br>'); echo('<br>'); 
                }
              }
            }
          }
        } else {
          if ($key>1) {
            //fix length
            if ($this->DATA_ELEMENT[$key][2]==0) {
              $start  = false;
              for ($i=0; $i<$this->DATA_ELEMENT[$key][1]/4; $i++) {                        
                $bit  = base_convert($inp[$i], 16, 2);

                if ($bit!=0) $start  = true;
                if ($start) $this->_data[$key]  .= $bit;
              }
              $this->_data[$key]  = $bit;
            }
          } else {
            $tmp  = substr($this->_iso, 4+16, 16);
            if (strlen($tmp)==16) {
                $this->_data[$key]  = substr($this->_iso, 4+16, 16);
                $this->_valid['de'][$key]  = true;
            }
          }
        }
        if (!$this->_valid['de'][$key]) $this->_valid['data']  = false;
      }
    }

    return $this->_data;
  }

  //method: add data element
  public function addData($bit, $data) {
    if ($bit>2 && $bit<129) {
      $this->_data[$bit]  = $this->_packElement($this->DATA_ELEMENT[$bit], $data);
            
      ksort($this->_data);
      //Print_r($this->_data);
      $this->_calculateBitmap();
    }
  }

  //method: add MTI
  public function addMTI($mti) {
    if (strlen($mti)==4 && ctype_digit($mti)) {
      $this->_mti  = $mti;
    }
  }   

  //method: retrieve data element
  public function getData() {
    return $this->_data;
  }

  //method: retrieve bitmap
  public function getBitmap() {
    return $this->_bitmap;
  }

  //method: retrieve mti
  public function getMTI() {
    return $this->_mti;
  }

  //method: retrieve iso with all complete data
  public function getISO() {
    unset($this->_data[1])  ;
    $this->_iso  = $this->_mti. $this->_bitmap. implode($this->_data);
    
    //ShowLog("entak endeng dsss " . );
    return $this->_iso;
  }

  //method: add ISO string
  public function addISO($iso) {
    $this->_clear();
    if ($iso!='') {
      $this->_iso  = $iso;      
      $this->_parseMTI();      
      $this->_parseBitmap();
      $this->_parseData();            
    }
  }

  //method: return true if iso string is a valid 8583 format or false if not
  public function validateISO() {
    return $this->_valid['mti'] && $this->_valid['bitmap'] && $this->_valid['data'];
  }

  //method: remove existing data element
  public function removeData($bit) {
    if ($bit>1 && $bit<129) {
      unset($this->_data[$bit]);
      ksort($this->_data);            
      $this->_calculateBitmap();
    }
  }
  
  public function ParseISO($cISO){
    $this->addISO($cISO);
    $cMTI       = $this->getMTI() ; 
    $va         = $this->getData() ;
    $cMD5       = md5($cISO) ;
    $vaData     = array("ISO_MD5"=>$cMD5,"ISO"=>$cISO,"MTI"=>$cMTI) ;
    
    foreach($va as $key=>$value){
      $key = "DE" . str_pad($key,3,"0",STR_PAD_LEFT) ;
      $vaData [$key] = $value ;
    }
    return $vaData;
  } 
    
}


?>