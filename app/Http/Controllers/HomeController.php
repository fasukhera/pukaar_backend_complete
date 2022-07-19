<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function trackingtest()
    {

        //$this->enableView(false);
        //$this->getLayout()->setLayout("blank");
        //$db = Pms_Application::getDb('mysql');
        $response = array();
        //        $data = $_REQUEST['postData'];
        //        $data = json_decode(base64_decode($data));
        $time_start = microtime(true);
        $data['rawData'] = '';

        try {

            function gethex2bin($hex)
            {
                $table = array('0' => '0000', '1' => '0001', '2' => '0010', '3' => '0011',
                    '4' => '0100', '5' => '0101', '6' => '0110', '7' => '0111',
                    '8' => '1000', '9' => '1001', 'a' => '1010',
                    'b' => '1011', 'c' => '1100', 'd' => '1101', 'e' => '1110',
                    'f' => '1111');
                $bin = '';

                for ($i = 0; $i < strlen($hex); $i++) {
                    $bin .= $table[strtolower(substr($hex, $i, 1))];
                }

                return $bin;
            }

            function bin2ASCII($input)
            {
                $output = '';
                for ($i = 0; $i < strlen($input); $i += 8) {
                    $output .= chr(intval(substr($input, $i, 8), 2));
                }
                return $output;
            }

            // timestamp : 2064
//            $data['rawData'] = "29 29 80 00 28 2a ba 9e 0c 64 07 27 05 26 25 00 00 00 00 00 00 00 00 00 00 00 00 7b 02 3f d2 7f ff 1a 00 00 00 00 00 00 02 00 00 e4 0d";

            $data['rawData'] = "29 29 80 00 28 2a c8 9d 1b 19 03 01 06 59 15 02 62 18 32 05 00 12 34 00 28 00 46 fb 00 0e 97 7f ff 19 00 00 00 00 00 00 00 00 00 61 0d";
            //$data['rawData'] = "29 29 80 00 28 2a cf 83 53 19 01 24 13 18 20 02 13 36 64 03 91 21 67 00 00 03 49 fb 00 19 10 7f 7f 1c 00 00 00 04 00 00 45 00 00 f8 0d";
            //$data['rawData'] = "29 29 80 00 28 2a cf 83 53 18 12 04 16 18 54 02 44 06 54 04 64 46 92 00 15 02 56 fb 00 04 05 7f ff 11 00 00 00 00 00 00 00 00 00 43 0d";
            //$data['rawData'] = "29 29 80 00 28 2a cf 83 53 18 12 26 08 18 41 02 44 58 93 04 64 21 91 00 63 02 45 fb 00 11 a4 7f ff 12 00 00 00 0b 00 00 6a 00 00 24 0d";
            //$data['rawData'] = "29 29 a3 00 38 2f 99 9f 34 19 02 08 11 21 57 02 13 18 51 03 91 25 26 00 00 01 68 fb 00 00 02 ff ff 15 00 00 00 00 00 00 00 00 00 00 10 24 01 00 10 02 00 80 0e 01 64 0d 02 01 65 19 0d";
            //$data['rawData'] = "29 29 b1 00 07 29 80 8a 04 0c 9d 0d";
            //$data['rawData'] = "29 29 B3 00 07 0A 9F 95 38 0C 80 0D";
            //$data['rawData'] = "29 29 85 00 28 29 80 8B 5A 19 01 07 08 39 23 02 61 69 42 05 01 27 08 00 00 02 14 FB 00 14 C3 7B FF 1B 00 00 00 00 00 00 00 00 39 27 0D";
            //  $data['rawData'] = "29 29 a3 00 5c 20 80 8a 01 21 07 08 09 22 33 02 24 28 91 11 34 86 73 00 00 00 00 7b 00 00 00 7f ff 12 10 63 00 00 00 00 00 00 00 00 34 10 02 00 81 2b 09 00 24 90 00 0e e3 00 01 cc 41 0a 89 86 04 93 19 21 80 70 69 88 43 15 02 11 31 24 65 64 04 0b 22 15 7c 11 31 24 66 64 04 0b 18 15 7c b1 0d";

            //Data used on 10th July 2021
            //  $data['rawData'] = "29 29 a3 00 5c 20 80 8a 01 21 07 07 09 24 05 02 24 28 91 11 34 86 73 00 00 00 00 7b 00 00 00 7f ff 10 10 63 00 00 00 00 01 00 00 00 34 10 02 00 82 2b 09 00 24 90 00 0e e4 00 01 cc 41 0a 89 86 04 93 19 21 80 70 69 88 43 15 02 11 31 24 66 64 04 0b 4a 15 18 11 31 24 65 64 04 0b 36 15 18 cf 0d ";
            //  $data['rawData'] = "29 29 a3 00 5c 20 80 8a 01 21 07 10 10 24 05 02 24 28 91 11 34 86 73 00 00 00 00 7b 00 00 00 7f ff 10 10 63 00 00 00 00 01 00 00 00 34 10 02 00 82 2b 09 00 24 90 00 0e e4 00 01 cc 41 0a 89 86 04 93 19 21 80 70 69 88 43 15 02 11 31 24 66 64 04 0b 4a 15 18 11 31 24 65 64 04 0b 36 15 18 cf 0d";


            $data_gv = $data['rawData']; //converted from object to key
//            echo "<b>The rawdata from device:</b> " . $data_gv;
            date_default_timezone_set('Asia/Riyadh');
            //date_default_timezone_set('GMT');


            $split_data = explode("29 29 ", $data_gv); //fetching data after specific key 29 29 in this case
//            $split_data =  array_filter($split_data); by me

            foreach ($split_data as $key => $val) {
//                echo '<br>' . $val;
                if (strlen($val) > 0) {
                    $byteCountExplode = explode(" ", $val); // breaking into array
                    $bytes = count($byteCountExplode) + 1; //counting array elements and incrementing by 1

                    $pos_data = substr($val, 0, 2); //fetching IMEI condition
                    $pos_data1 = strtoupper($pos_data); //converting into Upper case

                    //converting and merging imei
                    if (($pos_data1 == 80) || ($pos_data1 == '8E') || ($pos_data1 == 82) || ($pos_data1 == 'B1') ||
                        ($pos_data1 == 85) || ($pos_data1 == 84) || ($pos_data1 == 'A3')) {

                        $imei = substr($val, 9, 11);

                        $imei1 = hexdec(substr($imei, 0, 2));
                        if (strlen($imei1) == 1) {
                            $imei1 = '0' . $imei1;
                        }

                        $imei2 = substr($imei, 3, 2);
                        $imei2 = hexdec($imei2) - hexdec(80);
                        if (strlen($imei2) == 1) {
                            $imei2 = '0' . $imei2;
                        }

                        $imei3 = substr($imei, 6, 2);
                        $imei3 = hexdec($imei3) - hexdec(80);
                        if (strlen($imei3) == 1) {
                            $imei3 = '0' . $imei3;
                        }

                        $imei4 = hexdec(substr($imei, 9, 2));
                        if (strlen($imei4) == 1) {
                            $imei4 = '0' . $imei4;
                        }
                        $imei = $imei1 . $imei2 . $imei3 . $imei4;

                        echo " <br> <b>imei:</b> " . $imei;
                        $power = NULL;
                        $temp1 = NULL;
                        $rfid = NULL;
                        $commandType = NULL;
                        $fuelper1 = NULL;
                        $fuelVolume1 = NULL;
                        $fuelper2 = NULL;
                        $gps = NULL;
                        $loadVoltage = NULL;
                        $iccid = NULL;
                        $deviceWorkingStatus = NULL;
                        $loadSensorDisconnectAlarm = NULL;
                        $eventAlarm = NULL;
                        $shakeAlarm = NULL;
                        $fuelrefill = 0;

                        echo '<br>';
//                        print_r($byteCountExplode);
//                        die;

                        if (($pos_data1 == 84)) {
                            $currentdate = gmdate("Y-m-d H:i:s", time());
                            $stringLength = strlen($val);
                            $ackString = substr($val, 21, $stringLength - 27);
                            $hex2binaryAck = gethex2bin($ackString);
                            $replyString = bin2ASCII($hex2binaryAck);

                            $sql_d = "select d.Id, d.vehicleId from fm_devicecmd d
							JOIN fm_vehicle v ON v.vehicleId = d.vehicleId
							where d.IMEI='" . $imei . "' and d.successStatus='3' and d.ackYesno = 'yes' and d.reply IS NULL order by d.updateDate desc limit 1";
                            //echo $sql_d;
                            $devicedata = $db->fetchRow($sql_d);
                            if ($devicedata) {
                                echo "SUCCESS";
                                $devicecmd_update = $db->update("fm_devicecmd", array("reply" => $replyString), "Id='" . $devicedata['Id'] . "'");
                                echo "Updated Commands";
                                // add reply decode code here

                                if (strpos($replyString, 'google') !== false) {
                                    $replySplit = explode(" ", $replyString);

                                    $speed = substr($replySplit[0], 6, strpos($replySplit[0], 'km/h') - 6);
                                    $direction = substr($replySplit[1], 6, strlen($replySplit[1]) - 6);

                                    $url = explode('q=', $replySplit[2]);
                                    $latLong = explode(',', $url[1]);
                                    $latitude = $latLong[0];
                                    $longitude = $latLong[1];

                                    $date = $replySplit[3];
                                    $time = $replySplit[4];
                                    $timestamp = $date . ' ' . $time;

                                } else {
                                    echo 'no google k/w';
                                    exit;
                                }
                            } else {
                                echo 'no devicedata';
                                exit;
                            }


                        }

                        if (($pos_data1 == 85)) {
                            $currentdate = gmdate("Y-m-d H:i:s", time());
                            //$inputString = "29 29 ".$val;
                            echo "Acknowledge String found : IMEI:" . $imei . " SERVER GMT:" . $currentdate;
                            $sql_d = "select d.Id, d.vehicleId, d.eventType from fm_devicecmd d
							JOIN fm_vehicle v ON v.vehicleId = d.vehicleId
							where d.IMEI='" . $imei . "' and d.successStatus='1' and TIME_TO_SEC(TIMEDIFF('" . $currentdate . "', d.sendDate))<=60 order by d.sendDate desc limit 1";
                            $devicedata = $db->fetchRow($sql_d);

                            if ($devicedata) {
                                echo "SUCCESS";
                                $devicecmd_update = $db->update("fm_devicecmd", array("successStatus" => '3', "ackYesno" => 'yes', 'updateDate' => $currentdate), "Id='" . $devicedata['Id'] . "'");
                                if ($devicedata['eventType'] == 253) {//0=>lock and 1=>unlock
                                    $veh_update = $db->update("fm_vehicle", array("lockFlag" => 0), "vehicleId='" . $devicedata['vehicleId'] . "'");
                                } elseif ($devicedata['eventType'] == 254) {
                                    $veh_update = $db->update("fm_vehicle", array("lockFlag" => 1), "vehicleId='" . $devicedata['vehicleId'] . "'");
                                }

                            }//if($devicedata)
                            echo "Updated Commands";

                            exit;
                        }//if(($pos_data == 85))
                        if (($pos_data1 == 80) || ($pos_data1 == '82') || ($pos_data1 == '8E') || ($pos_data1 == 'A3')) {
                            $date = substr($val, 21, 8);
                            $year = '20' . substr($date, 0, 2);
                            $month = substr($date, 3, 2);
                            $day = substr($date, 6, 2);

                            $tm = substr($val, 30, 8);
                            $hour = substr($tm, 0, 2);
                            $min = substr($tm, 3, 2);
                            $sec = substr($tm, 6, 2);

                            $time = $hour . ":" . $min . ":" . $sec;

                            //$timestamp=date("Y-m-d H:i:s",mktime($hour,$min,$sec,$month,$day,$year)); // not work for future timestamp in local

                            $dateTimeObj = new DateTime();
                            $dateTimeObj->setTime($hour, $min, $sec);
                            $dateTimeObj->setDate($year, $month, $day);
                            $timestamp = $dateTimeObj->format('Y-m-d H:i:s');

                            $latitude = substr($val, 39, 11);
                            $lat = str_replace(' ', '', $latitude);
                            $lat = $lat / 1000;
                            $lat1 = substr($lat, 0, 2);
                            $lat2 = substr($lat, 2, 6);
                            $latitude = $lat1 + ($lat2 / 60);


                            $longitude = substr($val, 51, 11);
                            $lon = str_replace(' ', '', $longitude);
                            $lon = $lon / 1000;
                            $lon1 = substr($lon, 0, 2);
                            $lon2 = substr($lon, 2, 6);
                            $longitude = $lon1 + ($lon2 / 60);

                            $speed = intval(str_replace(' ', '', substr($val, 63, 5)));

                            $direction = intval(str_replace(' ', '', substr($val, 69, 5)));
                        }//if(($pos_data == 80) || ($pos_data == '82') ||($pos_data == '8E') || ($pos_data == 'a3') || ($pos_data == 'A3')){
                        $door = NULL;
                        $seatbelt = NULL;
                        $ac = NULL;
                        $panic = NULL;
                        //////////////////////////////////////// common for 80 & 82 & 8E & a3/A3 upto here

                        //$st = decbin(hexdec(substr($val,75,2)));

                        if (($pos_data1 == 80) || ($pos_data1 == '8E') || ($pos_data1 == 'A3')) {

                            $st = gethex2bin(substr($val, 75, 2));

                            $gps_digit = substr($st, 0, 1); // D7 bit
                            if ($gps_digit == 0) {
                                $gps = 0;
                            } else if ($gps_digit == 1) {
                                $gps = 1;
                            }

                            $power_digit = substr($st, 4, 1); // D3 bit
                            if ($power_digit == 0) {
                                $power = 1;//power disconnected
                            } else if ($power_digit == 1) {
                                $power = 0;//power connected
                            }

                            $st_last_digits = substr($st, 6, 2);

                            $unit = array('00' => 'm',
                                '01' => 'm',
                                '10' => 'km',
                                '11' => 'km');

                            $mileage_unit = $unit[$st_last_digits];
//                            dd(substr($val, 78, 8));
                            //conversion not working
                            $mileage = hexdec(substr($val, 78, 8));
                            //avoiding conversion
                            $mileage = 147410;
                            $status = substr($val, 87, 11);
                            $st1 = gethex2bin(substr($status, 0, 2));
                            if (substr($st1, 0, 1) == 0) {
                                $acc = 1;
                            } else {
                                $acc = 0;
                            }

                            $st2 = gethex2bin(substr($status, 3, 2));


                            if (substr($st2, 0, 1) == 0) {
                                $SOS = 1;
                            }

// Code Added to consider DEF2 for Digital Inputs

                            if (substr($st1, 2, 1) == 0) {
                                $DEF2 = 1;
                            } else {
                                $DEF2 = 0;
                            }

                            $sql_v = "select digital_Input1 from fm_vehicle where full_imei = '" . $imei . "' limit 1";

                            $digInputData = $db->fetchRow($sql_v);

                            if ($digInputData['digital_Input1'] != NULL) {
                                if ($digInputData['digital_Input1'] == 'DOOR') {
                                    $door = $DEF2;
                                }
                                if ($digInputData['digital_Input1'] == 'SEATBELT') {
                                    $seatbelt = $DEF2;
                                }
                                if ($digInputData['digital_Input1'] == 'TRAILER') {

                                    if (substr($st1, 2, 1) == 0) {
                                        $DEF2 = 0;
                                    } else {
                                        $DEF2 = 1;
                                    }

                                    $ac = $DEF2;

                                }
                                if ($digInputData['digital_Input1'] == 'PANIC') {
                                    $panic = $DEF2;
                                }

                            }


                            //$st3 = decbin(hexdec(substr($status,6,2)));
                            //$st4 = decbin(hexdec(substr($status,9,2)));


                        } //if(($pos_data == 80) || ($pos_data == '8E')){
                        if ($pos_data1 == 'A3') {
                            // get periferal data
                            $pfDataLength = hexdec(substr($val, 123, 5));  // total periferal data length including length byte
                            $pfCount = ($pfDataLength * 2) + ($pfDataLength - 1);

                            $pfDataBytes = substr($val, 123, $pfCount); // total periferal data bytes
                            $pfUpperCase = strtoupper($pfDataBytes);

                            // Temp Calculation
                            $pos = strpos($pfUpperCase, '0D');

                            if ($pos != NULL) {
                                $tempByteLength = hexdec(substr($pfDataBytes, $pos + 3, 2)); // byte after 'OD' : it defines the temp byte length
                                $tempLength = ($tempByteLength * 2) + ($tempByteLength - 1);
                                $tempBytes = substr($pfDataBytes, $pos + 6, $tempLength);


                                $tempBinary = gethex2bin($tempBytes); // convert to binary
                                if (substr($tempBinary, 0, 1) == '0') {
                                    $tempSign = NULL;
                                    $tempVal = hexdec($tempBytes) * 0.1;
                                } else if (substr($tempBinary, 0, 1) == '1') {
                                    $tempSign = '-';
                                    $excludeSignBit = ltrim($tempBinary, '1');
                                    $tempVal = bindec($excludeSignBit) * 0.1;
                                }

                                $temp1 = $tempSign . $tempVal;
                            }

                            //$tempValBits = substr($tempBinary,1,15);   // get 0-14 bits
                            // RFID Calculation
                            $pos16 = strpos($pfUpperCase, '16');
                            if ($pos16 != NULL) {
                                $rfidByteLength = hexdec(substr($pfDataBytes, $pos16 + 3, 2));     // byte after '16' : it defines the RFID byte length
                                $rfidLength = ($rfidByteLength * 2) + ($rfidByteLength - 1);
                                $rfidBytes = substr($pfDataBytes, $pos16 + 6, $rfidLength);

                                $rfidValBytes = substr($rfidBytes, 3, 29);
                                // converting rfid bytes to ascii
                                $rfidValBin = gethex2bin($rfidValBytes);
                                $rfidVal = bin2ASCII($rfidValBin);

                                $rfidSignByte = substr($rfidBytes, 0, 2);
                                $signBinary = gethex2bin($rfidSignByte);// convert to binary

                                if (substr($signBinary, 0, 1) == '0') {
                                    $rfidSign = NULL;
                                } else if (substr($signBinary, 0, 1) == '1') {
                                    $rfidSign = '-';
                                }

                                $rfid = $rfidSign . $rfidVal;
                            }//if($pos16 != NULL)

                            // calculate fuels percentage
                            $pos0E = strpos($pfUpperCase, '0E');
                            if ($pos0E != NULL) {
                                $fuelByteLength = hexdec(substr($pfDataBytes, $pos0E + 3, 2));     // byte after '16' : it defines the RFID byte length
                                if ($fuelByteLength == 1) {
                                    $fuelLength = ($fuelByteLength * 2) + ($fuelByteLength - 1);
                                    $fuelBytes = substr($pfDataBytes, $pos0E + 6, $fuelLength);
                                    $fuelper1 = hexdec($fuelBytes);
                                    $fuelrefill = 1;
                                }
                            }//if($pos0E != NULL)
                            // echo "<br>fuelByteLength->".$fuelByteLength;
                            // echo "<br>fuelLength->".$fuelLength;
                            // echo "<br>fuelBytes->".$fuelBytes;
                            // echo "<br>fuelper1->".$fuelper1; exit;
                            $pos2F = strpos($pfUpperCase, '2F');
                            if ($pos2F != NULL) {
                                $voltageByteLength = hexdec(substr($pfDataBytes, $pos2F + 3, 2));     // byte after '2F' : it defines the load voltage byte length
                                $voltageLength = ($voltageByteLength * 2) + ($voltageByteLength - 1);
                                $voltageBytes = substr($pfDataBytes, $pos2F + 6, $voltageLength);
                                $loadVoltage = hexdec($voltageBytes) / 100;

                            }//if($pos2F != NULL)

                            //ICCID
                            $pos41 = strpos($pfUpperCase, '41');
                            if ($pos41 != NULL) {
                                $iccidByteLength = hexdec(substr($pfDataBytes, $pos41 + 3, 2));     // byte after '2F' : it defines the load voltage byte length
                                $iccidLength = ($iccidByteLength * 2) + ($iccidByteLength - 1);
                                $iccidBytes = substr($pfDataBytes, $pos41 + 6, $iccidLength - 1);
                                $iccid = str_replace(' ', '', $iccidBytes);
                            }//if($pos2F != NULL)

                            //Added New
                            $pos43 = strpos($pfUpperCase, '43');

                            if ($pos43 != NULL) {
                                $TByteLength = hexdec(substr($pfDataBytes, $pos43 + 3, 2));     // byte after '43' : it defines the temp byte length

                                $TLength = ($TByteLength * 2) + ($TByteLength - 1);

                                $TBytes = substr($pfDataBytes, $pos43 + 6, $TLength);            // Get the substring of sensor data
                                // $loadT = hexdec($TBytes)/100;
                                $te = explode(' ', $TBytes);
                                // $noOfSensors =  hexdec(substr($pfDataBytes,$pos43+6,2));	// No of sensors connected Hex to dec
                                // $sensorsData = substr($pfDataBytes,$pos43+9,30);	       // Get sensor 1 string
                                $noOfSensors = hexdec($te[0]);
                                unset($te[0]);
                                // print_r(array_chunk($te,10));exit;
                                $DivideArray = array_chunk($te, 10);
                                $concatTemp = $concatHumidity = '';
                                foreach ($DivideArray as $k => $v) {

                                    $sensorId = $v[0] . $v[1] . $v[2] . $v[3];
                                    $battery = hexdec($v[4]);
                                    $TempNHumidityLength = hexdec($v[5]);
                                    $temperature = hexdec($v[6] . $v[7]) / 100;

                                    $tempBinary = gethex2bin($v[6] . $v[7]); // convert to binary

                                    if (substr($tempBinary, 1, 1) == '0') {
                                        $tempSign = NULL;
                                        $tempVal = hexdec($v[6] . $v[7]) * 0.01;
                                    } else if (substr($tempBinary, 1, 1) == '1') {
                                        $tempSign = '-';
                                        $excludeSignBit = ltrim($tempBinary, '1');
                                        $tempVal = bindec($excludeSignBit) * 0.01;
                                    }

                                    $temp = $tempSign . $tempVal;
                                    $concatTemp .= $sensorId . ':' . $temp . ", ";
                                    $concatTempNew .= "BLE Temp" . $sensorId . ':' . $temp . ", ";

                                    $humidity = hexdec($v[8] . $v[9]) / 100;
                                    $concatHumidity .= $sensorId . ':' . $humidity . ", ";
                                    $concatHumidityNew .= "BLE Humidity" . $sensorId . ':' . $humidity . ", ";
                                    // echo '<br>sensorId->'.$sensorId;
                                    // echo '<br>battery->'.$battery;
                                    // echo '<br>TempNHumidityLength->'.$TempNHumidityLength;
                                    // echo '<br>temperature->'.$temperature;
                                    // echo '<br>temperatureNew->'.$temp1;
                                    // echo '<br>humidity->'.$humidity;
                                }
                                $finalconcatTemp = rtrim($concatTemp, ", ");
                                $finalconcatHumidity = rtrim($concatHumidity, ", ");
                                $finalconcatTempNew = rtrim($concatTempNew, ", ");
                                $finalconcatHumidityNew = rtrim($concatHumidityNew, ", ");
                            }

                        }//if(($pos_data == 'a3') || ($pos_data == 'A3')){


                        echo "In PHP Input Recived:" . $imei;
                        $in_data = array();
                        $fromLastData = 0;

                        echo "<br/>";

                        if (strlen($imei) > 0) {
                            $data3 = array('imei' => $imei);

                            $mt90frm = new App_forms_mt90_Mt90($data3);
                            $vehicleData = $mt90frm->addVehicle();

                            $vehicleId = $vehicleData['vehicleId'];

                            $lst = new App_models_mt90_Mt90new();
                            $lastdata = $lst->getLastdata($vehicleId);

                            if ($fuelByteLength == 1) { //Added on 10-07-2021
                                if ($fuelper1 == 0) {
                                    $fuelVolume1 = 0;
                                } elseif ($pos_data1 == 'A3' && $fuelper1 && $vehicleData['tankVolume']) {
                                    $fuelVolume1 = ($fuelper1 * $vehicleData['tankVolume']) / 100;
                                }
                            }


                            if ($pos_data1 == 'B1') {
                                if ($lastdata) {
                                    $timestamp = date("Y-m-d H:i:s", strtotime("-3 hours"));
                                    $lastdata_update = $db->update("fm_vehiclelastdata", array("packetType" => 'B1', "b1Timestamp" => $timestamp, "updateDate" => date('Y-m-d H:i:s')), "vehicleId='" . $vehicleId . "'");
                                    echo "Result Success! Updated B1 Packet!";
                                }
                                exit;
                            }


                            $lastAcc = 0;
                            $isLastData = 0;
                            $lastSpeed = NULL;
                            $lastseatbelt = NULL;
                            $lastDistance = NULL;
                            $lastloadsensordisconnect = NULL;
                            $lastevent = NULL;
                            $lastpower = NULL;
                            $loadSensorDisconnect = $loadSensorDisconnectAlarm;
                            if ($lastdata) {
                                $isLastData = 1;
                                $lastpower = $lastdata['power']; // setting power from fm_vehiclelastdata
                                $lastAcc = $lastdata['acc'];
                                $lastSpeed = $lastdata['speed'];
                                $lastseatbelt = $lastdata['seatbelt'];
                                $lastDistance = $lastdata['distance'];
                                $lastloadsensordisconnect = $lastdata['loadSensorDisconnect'];
                                $lastevent = $lastdata['event'];
                            }

                            if (($pos_data1 == 82)) {
                                if ($lastdata) {
                                    $loadVoltage = $lastdata['loadVoltage'];
                                    $fuelper1 = $lastdata['fuelper1'];
                                    $fuelVolume1 = $lastdata['fuelVolume1'];
                                }

                                $gpsByte = gethex2bin(substr($val, 75, 2));

                                $gps_digit = substr($gpsByte, 0, 1);
                                if ($gps_digit == 0) {
                                    $gps = 0;
                                } else if ($gps_digit == 1) {
                                    $gps = 1;
                                }

                                $alarm_params = substr($val, 93, 14);
                                $status = gethex2bin(substr($alarm_params, 3, 2));
                                if (substr($status, 7, 1) == 0) {
                                    $acc = 1;
                                } else {
                                    $acc = 0;
                                }
                                if ($lastdata && $lastdata['distance']) {
                                    $mileage = $lastdata['distance'];
                                } else {
                                    $mileage = 0;
                                }
                                if ($lastdata) {
                                    $door = $lastdata['door'];
                                    $seatbelt = $lastdata['seatbelt'];
                                    $ac = $lastdata['ac'];
                                    $panic = $lastdata['panic'];
                                }

                                //Alarm data
                                $alarm_params = substr($val, 84, 23);

                                $alarm_byte1 = gethex2bin(substr($alarm_params, 0, 2));
                                $byte1D0 = substr($alarm_byte1, 7, 1);
                                if ($byte1D0 == 1) {
                                    $illegalIgnitionAlarm = 1;
                                } else {
                                    $illegalIgnitionAlarm = 0;
                                }
                                $byte1D1 = substr($alarm_byte1, 6, 1);
                                if ($byte1D1 == 1) {
                                    $DEF1Alarm = 1;
                                } else {
                                    $DEF1Alarm = 0;
                                }
                                $byte1D2 = substr($alarm_byte1, 5, 1);
                                if ($byte1D2 == 1) {
                                    $DEF2Alarm = 1;
                                } else {
                                    $DEF2Alarm = 0;
                                }
                                $byte1D3 = substr($alarm_byte1, 4, 1);
                                if ($byte1D3 == 1) {
                                    $DEF5Alarm = 1;
                                } else {
                                    $DEF5Alarm = 0;
                                }
                                $byte1D4 = substr($alarm_byte1, 3, 1);
                                if ($byte1D4 == 1) {
                                    $lowPowerAlarm = 1;
                                } else {
                                    $lowPowerAlarm = 0;
                                }
                                $byte1D5 = substr($alarm_byte1, 2, 1);
                                if ($byte1D5 == 1) {
                                    $offsetRouterAlarm = 1;
                                } else {
                                    $offsetRouterAlarm = 0;
                                }
                                $byte1D6 = substr($alarm_byte1, 1, 1);
                                if ($byte1D6 == 1) {
                                    $outAreaAlarm = 1;
                                } else {
                                    $outAreaAlarm = 0;
                                }
                                $byte1D7 = substr($alarm_byte1, 0, 1);
                                if ($byte1D7 == 1) {
                                    $inAreaAlarm = 1;
                                } else {
                                    $inAreaAlarm = 0;
                                }

                                /////////////
                                $alarm_byte2 = gethex2bin(substr($alarm_params, 3, 2));

                                $byte2D0 = substr($alarm_byte2, 7, 1);
                                if ($byte2D0 == 1) {
                                    $sosAlarm = 1;
                                } else {
                                    $sosAlarm = 0;
                                }
                                $byte2D1 = substr($alarm_byte2, 6, 1);
                                if ($byte2D1 == 1) {
                                    $overSpeedAlarm = 1;
                                } else {
                                    $overSpeedAlarm = 0;
                                }
                                $byte2D2 = substr($alarm_byte2, 5, 1);
                                if ($byte2D2 == 1) {
                                    $stopOverTimeAlarm = 1;
                                } else {
                                    $stopOverTimeAlarm = 0;
                                }
                                $byte2D3 = substr($alarm_byte2, 4, 1);

                                if ($byte2D3 == 1) {
                                    $powerCutAlarm = 1;
                                } else {
                                    $powerCutAlarm = 0;
                                }

                                $byte2D4 = substr($alarm_byte2, 3, 1);
                                if ($byte2D4 == 1) {
                                    $DEF4Alarm = 1;
                                } else {
                                    $DEF4Alarm = 0;
                                }
                                $byte2D5 = substr($alarm_byte2, 2, 1);
                                if ($byte2D5 == 1) {
                                    $gSensorAlarm = 1;
                                } else {
                                    $gSensorAlarm = 0;
                                }
                                $byte2D6 = substr($alarm_byte2, 1, 1);
                                if ($byte2D6 == 1) {
                                    $towAwayAlarm = 1;
                                } else {
                                    $towAwayAlarm = 0;
                                }
                                $byte2D7 = substr($alarm_byte2, 0, 1);
                                if ($byte2D7 == 1) {
                                    $doorOpenAlarm = 1;
                                } else {
                                    $doorOpenAlarm = 0;
                                }
                                ////////////////
                                $alarm_byte3 = gethex2bin(substr($alarm_params, 6, 2));
                                $byte3D0 = substr($alarm_byte3, 7, 1);
                                if ($byte3D0 == 1) {
                                    $fuelSensorDisconnect = 1;
                                } else {
                                    $fuelSensorDisconnect = 0;
                                }
                                $byte3D1 = substr($alarm_byte3, 6, 1);
                                if ($byte3D1 == 1) {
                                    $gsmJammingAlarm = 1;
                                } else {
                                    $gsmJammingAlarm = 0;
                                }
                                $byte3D2 = substr($alarm_byte3, 5, 1);
                                if ($byte3D2 == 1) {
                                    $temperatureAbnormal = 1;
                                } else {
                                    $temperatureAbnormal = 0;
                                }
                                $byte3D3 = substr($alarm_byte3, 4, 1);
                                if ($byte3D3 == 1) {
                                    $harshTurning = 1;
                                } else {
                                    $harshTurning = 0;
                                }
                                $byte3D4 = substr($alarm_byte3, 3, 1);
                                if ($byte3D4 == 1) {
                                    $tamperBoxAlarm = 1;
                                } else {
                                    $tamperBoxAlarm = 0;
                                }
                                $byte3D5 = substr($alarm_byte3, 2, 1);
                                if ($byte3D5 == 1) {
                                    $parkingStatus = 1;
                                } else {
                                    $parkingStatus = 0;
                                }
                                $byte3D6 = substr($alarm_byte3, 1, 1);
                                if ($byte3D6 == 1) {
                                    $fatigueAlarm = 1;
                                } else {
                                    $fatigueAlarm = 0;
                                }
                                $byte3D7 = substr($alarm_byte3, 0, 1);
                                if ($byte3D7 == 1) {
                                    $idleAlarm = 1;
                                } else {
                                    $idleAlarm = 0;
                                }
                                /////////////////////
                                $alarm_byte4 = gethex2bin(substr($alarm_params, 9, 2));

                                ////////////////////
                                $alarm_byte5 = gethex2bin(substr($alarm_params, 12, 2));
                                $byte5D0 = substr($alarm_byte5, 7, 1);
                                if ($byte5D0 == 1) {
                                    $lockStatus = 1;
                                } else {
                                    $lockStatus = 0;
                                }
                                $byte5D1 = substr($alarm_byte5, 6, 1);
                                if ($byte5D1 == 1) {
                                    $chargeFullAlarm = 1;
                                } else {
                                    $chargeFullAlarm = 0;
                                }
                                $byte5D2 = substr($alarm_byte5, 5, 1);

                                $byte5D3 = substr($alarm_byte5, 4, 1);
                                if ($byte5D3 == 1) {
                                    $lockWireCutAlarm = 1;
                                } else {
                                    $lockWireCutAlarm = 0;
                                }
                                $byte5D4 = substr($alarm_byte5, 3, 1);
                                if ($byte5D4 == 1) {
                                    $protectionAlarm = 1;
                                } else {
                                    $protectionAlarm = 0;
                                }
                                $byte5D5 = substr($alarm_byte5, 2, 1);

                                if ($byte5D5 == 1) {
                                    $blootooth = 1;
                                } else {
                                    $blootooth = 0;
                                }
                                $byte5D6 = substr($alarm_byte5, 1, 1);

                                if ($temperatureAbnormal == 1) {
                                    if ($byte5D6 == 1) {
                                        $tempHighAlarm = 1;
                                        $tempLowAlarm = 0;
                                    } else {
                                        $tempLowAlarm = 1;
                                        $tempHighAlarm = 0;
                                    }
                                } else {
                                    $tempHighAlarm = 0;
                                    $tempLowAlarm = 0;
                                }

                                $byte5D7 = substr($alarm_byte5, 0, 1);
                                if ($byte5D7 == 1) {
                                    $chargingAlarm = 1;
                                } else {
                                    $chargingAlarm = 0;
                                }
                                /////////////////////
                                $alarm_byte6 = gethex2bin(substr($alarm_params, 15, 2));

                                //////////////
                                $alarm_byte7 = gethex2bin(substr($alarm_params, 18, 2));
                                $byte7D0 = substr($alarm_byte7, 7, 1);
                                if ($byte7D0 == 1) {
                                    $lowFuelAlarm = 1;
                                } else {
                                    $lowFuelAlarm = 0;
                                }
                                $byte7D1 = substr($alarm_byte7, 6, 1);
                                if ($byte7D1 == 1) {
                                    $fuelStolenAlarm = 1;
                                } else {
                                    $fuelStolenAlarm = 0;
                                }
                                $byte7D2 = substr($alarm_byte7, 5, 1);
                                if ($byte7D2 == 1) {
                                    $tempSensorAbnormal = 1;
                                } else {
                                    $tempSensorAbnormal = 0;
                                }
                                $byte7D3 = substr($alarm_byte7, 4, 1);
                                if ($byte7D3 == 1) {
                                    $shakeAlarm = 1;
                                } else {
                                    $shakeAlarm = 0;
                                }
                                $byte7D4 = substr($alarm_byte7, 3, 1);
                                if ($byte7D4 == 1) {
                                    $harshDeceleration = 1;
                                    $deviceWorkingStatus = 1; // device not working
                                } else {
                                    $harshDeceleration = 0;
                                }
                                $byte7D5 = substr($alarm_byte7, 2, 1);
                                if ($byte7D5 == 1) {
                                    $harshAcceleration = 1;
                                } else {
                                    $harshAcceleration = 0;
                                }
                                $byte7D6 = substr($alarm_byte7, 1, 1);
                                if ($byte7D6 == 1) {
                                    $turnover = 1;
                                } else {
                                    $turnover = 0;
                                }
                                $byte7D7 = substr($alarm_byte7, 0, 1);
                                if ($byte7D7 == 1) {
                                    $crashAlarm = 1;
                                } else {
                                    $crashAlarm = 0;
                                }
                                ////////////////////
                                $alarm_byte8 = gethex2bin(substr($alarm_params, 21, 2));
                                $byte8D0 = substr($alarm_byte8, 7, 1);

                                $byte8D1 = substr($alarm_byte8, 6, 1);

                                $byte8D2 = substr($alarm_byte8, 5, 1);

                                $byte8D3 = substr($alarm_byte8, 4, 1);

                                $byte8D4 = substr($alarm_byte8, 3, 1);
                                if ($byte8D4 == 1 && $acc == 1) {
                                    $loadSensorDisconnectAlarm = 1;
                                } else {
                                    $loadSensorDisconnectAlarm = 0;
                                }

                                $byte8D5 = substr($alarm_byte8, 2, 1);
                                if ($byte8D5 == 1) {
                                    $lowSpeedAlarm = 1;
                                } else {
                                    $lowSpeedAlarm = 0;
                                }
                                $byte8D6 = substr($alarm_byte8, 1, 1);

                                $byte8D7 = substr($alarm_byte8, 0, 1);

                                //Alarm data ends
                                if ($powerCutAlarm == 1) {
                                    $power = $powerCutAlarm;
                                } else {
                                    $power = $lastpower;
                                }
                                $loadSensorDisconnect = $loadSensorDisconnectAlarm;

                                $alarmEvents = array(
                                    'Harsh Acceleration' => $harshAcceleration,
                                    'Harsh Deceleration' => $harshDeceleration,
                                    'Load Sensor Disconnect Alarm' => $loadSensorDisconnectAlarm,
                                    'Power cut Alarm' => $powerCutAlarm,
                                    'GSM Jamming Alarm' => $gsmJammingAlarm
                                );
                                // get keys of $alarmEvents with value 1
                                $alarmKeyArr = array_keys(array_intersect($alarmEvents, array(1)));
                                $eventAlarm = implode(',', $alarmKeyArr);
                            }//if(($pos_data == 82)){

                            if ($pos_data1 != 82) {
                                $loadSensorDisconnect = $lastloadsensordisconnect;
                                $eventAlarm = $lastevent;
                            }

                            if (($pos_data1 == 84)) {
                                $mileage = $lastDistance;
                                $acc = $lastAcc;
                                $power = NULL;
                                $commandType = 'Health Check';
                            }


                            if ($vehicleData['companyId'] > 0) {
                                $lst = new App_models_mt90_Mt90new();
                                $offsetMinutes = $lst->getOffsetMinutes($vehicleData['companyId']);
                            }

                            if ($offsetMinutes == NULL) {
                                $offsetMinutes = 180;
                            }
                            $gmtTimediff = $offsetMinutes;

                            if ($lastdata && (($acc == 0 && $lastdata['acc'] == 0) || $speed == 0) && strlen($lastdata['location']) > 0) {
                                $location = $lastdata['location'];
                                $geofenceId = $lastdata['geofenceId'];
                                $fenceId[] = array($lastdata['geofenceId']);
                            } else {
                                $locData = array(
                                    'lat' => $latitude,
                                    'lon' => $longitude,
                                    'inAcc' => $acc,
                                    'lastAcc' => $lastdata['acc'],
                                    'companyId' => $vehicleData['companyId'],
                                    'speed' => $speed
                                );

                                if (strlen($latitude) > 0 && strlen($longitude) > 0) {

                                    $mt = new App_models_mt90_Mt90new();
                                    $locationData = $mt->getlocationSaudi($locData);

                                    $location = $locationData['location'];
                                    $geofenceId = $locationData['geofenceId'];
                                    $fenceId = $locationData['fenceId'];
                                    $definedSpeed = $locationData['definedSpeed'];
                                    $overspeed = $locationData['overspeed'];
                                }

                            }

                            /*if(strlen($latitude)>0 && strlen($longitude)>0){
                                $lst = new App_models_mt90_Mt90new();
                                $location = $lst->getlocationSaudi($latitude,$longitude);
                            } */

                            $lst = new App_models_mt90_Mt90new();
                            $driverData = $lst->getDriverDetails($vehicleId, $rfid);
                            // check arabic letters in drivername
                            if ($driverData && preg_match('/\p{Arabic}/u', $driverData['driverName'])) {
                                $driverData['driverName'] = NULL;
                            }

                            if ($temp1 > 2000) {
                                $temp1 = 0;
                            }
                            // static data code starts

                            /* if($vehicleId==83){
                                   $fuelper1 = 18;
                               }elseif($vehicleId==82){
                                   $fuelper1 = 19;
                               }elseif($vehicleId==84){
                                   $fuelper1 = 20;
                               }
                               if($vehicleId==83 || $vehicleId==84 || $vehicleId==82){
                                       $fuelVolume1 = ($fuelper1*$vehicleData['tankVolume'])/100;
                               }*/
                            // static data ends


                            $in_data = array(
                                "vehicleId" => $vehicleId,
                                "latitude" => $latitude,
                                "longitude" => $longitude,
                                "distance" => $mileage,
                                "direction" => $direction,
                                "timestamp" => $timestamp,
                                "speed" => round($speed, 2),
                                "acc" => $acc,
                                "location" => $location,
                                "geofenceId" => $geofenceId,
                                "power" => $power,
                                "lastacc" => $lastAcc,
                                "lastspeed" => $lastSpeed,
                                "panic" => $panic,
                                "door" => $door,
                                "ac" => $ac,
                                "seatbelt" => $seatbelt,
                                "lastseatbelt" => $lastseatbelt,
                                "temp1" => $temp1,
                                "rfid" => $rfid,
                                "fuelper1" => $fuelper1,
                                "fuelper2" => $fuelper2,
                                "fuelVolume1" => $fuelVolume1,
                                "loadVoltage" => $loadVoltage,
                                "driverName" => $driverData['driverName'],
                                "driverId" => $driverData['driverId'],
                                "commandType" => $commandType,
                                "overSpeed" => $overspeed,
                                "definedSpeed" => $definedSpeed,
                                "GPS" => $gps,
                                "packetType" => $pos_data1,
                                "loadSensorDisconnectAlarm" => $loadSensorDisconnectAlarm,
                                "loadSensorDisconnect" => $loadSensorDisconnect,
                                "eventAlarm" => $eventAlarm,
                                "shakeAlarm" => $shakeAlarm,
                                "iccid" => $iccid,
                                //"GPS"=>$GPS,
                                //"input1"=>isset($analog_input1)?$analog_input1:NULL,
                                //"input2"=>isset($analog_input2)?$analog _input2:NULL,
                                "updateDate" => date('Y-m-d H:i:s'),
                                "rawData" => $val,
                                "fromDevice" => 'ATelematics',
                                "gmtTimediff" => $offsetMinutes == NULL ? 180 : $offsetMinutes,
                                "status" => $finalconcatTemp,
                                "message" => $finalconcatHumidity,
                                "bleTemperature" => $finalconcatTempNew,
                                "bleHumidity" => $finalconcatHumidityNew,

                            );
                            $compTime = date('Y-m-d H:i:s', strtotime($in_data['timestamp']) + ($in_data['gmtTimediff'] * 60));//(-5*3600));//-6


                            //-----------------------------Idle Time of Vehicle-------------------------------------
                            $in_data['idleStatus'] = 0;
                            $idleTime = 3;

                            if (strlen($rfid) > 0) {
                                $updated = $db->update("fm_vehicle", array("ibuttonid" => $rfid), "vehicleId=" . $vehicleId);
                            }

                            if ($in_data['acc'] == 1 && intval($in_data['speed']) == 0 && $lastdata['idleTime'] == NULL) {
                                $idleTime = $compTime;
                                $in_data['idleStatus'] = 1;
                            } elseif ($in_data['acc'] == 0 || intval($in_data['speed']) > 0) {
                                $idleTime = NULL;
                                $in_data['idleStatus'] = 0;
                            } elseif ($lastdata['idleTime'] != NULL) {
                                $idleTime = 3;
                                $in_data['idleStatus'] = 1;
                            }

                            // print_r($in_data);exit;
                            $lst = new App_models_mt90_Mt90new();
                            $fuelSensorData = $lst->getFuelSensorData($data_gv, $vehicleData);


                            foreach ($fuelSensorData as $sdata) {
                                if ($sdata['number'] == 1) {
                                    //$in_data['fuelper1']=$sdata['fuelperfromdevice'];
                                    //$in_data['fuelVolume1']=$sdata['totalvolume'];//*0.001
                                }
                                if ($sdata['number'] == 2) {
                                    $in_data['fuelper2'] = $sdata['fuelperfromdevice'];
                                    $in_data['fuelVolume2'] = $sdata['totalvolume'];
                                }
                            }//foreach($fuelSensors as $sdata){

                            if ($idleTime != 3) {
                                $in_data['idleTime'] = $idleTime;
                            }

                            $in_data['isLastData'] = $isLastData;
                            $in_data['bytes'] = $bytes;
                            $in_data['country'] = 'saudi';
                            $in_data['lasttimestamp'] = $lastdata['timestamp'];


                            if ($fuelper1 > 0) {

                                /*$sql_t1="select * from fm_fuelcalibration where status=1";
                                $resT=$db->fetchRow($sql_t1);*/

                                $sql_t1 = "select fuelCalibrationId from fm_vehicle where vehicleId='" . $vehicleId . "'";
                                $resT = $db->fetchRow($sql_t1);

                                if ($resT['fuelCalibrationId'] > 0) {
                                    $sql2 = "select * from fm_additional_fuelcalibreation where fuelCalibrationId = " . $resT['fuelCalibrationId'] . " order by voltage";
                                    $dRes = $db->fetchAll($sql2);


                                    foreach ($dRes as $key => $val) {
                                        //if($devdata['fuelHeight1'] > )
                                        if ($key == 0) {
                                            if ($fuelper1 < $val['voltage']) {
                                                $fuel = $val['fuel'];
                                                $vlt = $val['voltage'];
                                            }
                                        } else {
                                            if ($fuelper1 >= $voltage && $fuelper1 <= $val['voltage']) {
                                                $fuel = $val['fuel'];
                                                $vlt = $val['voltage'];
                                            }
                                        }


                                        $voltage = $val['voltage'];
                                    }

                                    //echo $fuel;
                                    $finalFuel = (($fuelper1 * $fuel) / $vlt);
                                    $in_data['fuelVolume1'] = $finalFuel;
                                } else {
                                    if ($vehicleData['tankVolume'] > 0) {
                                        $in_data['fuelVolume1'] = ($fuelper1 * $vehicleData['tankVolume']) / 100;
                                    }
                                }


                            }
                            $mt90frm = new App_forms_mt90_Mt90($in_data);
                            $mt90Id = $mt90frm->add();

                            if (($pos_data1 == 84)) {

                                if ($mt90Id) {
                                    echo "Result Success!";
                                }
                                exit;
                            }


                            if (($pos_data1 == 82)) {
                                $alarm_data = array(
                                    "mt90Id" => $mt90Id,
                                    "vehicleId" => $vehicleId,
                                    "latitude" => $latitude,
                                    "longitude" => $longitude,
                                    "distance" => $mileage,
                                    "timestamp" => $timestamp,
                                    "speed" => round($speed, 2),
                                    "acc" => $acc,
                                    "location" => $location,
                                    "inAreaAlarm" => $inAreaAlarm,
                                    "outAreaAlarm" => $outAreaAlarm,
                                    "offsetRouterAlarm" => $offsetRouterAlarm,
                                    "lowPowerAlarm" => $lowPowerAlarm,
                                    "DEF1Alarm" => $DEF1Alarm,
                                    "DEF2Alarm" => $DEF2Alarm,
                                    "DEF4Alarm" => $DEF4Alarm,
                                    "DEF5Alarm" => $DEF5Alarm,
                                    "illegalIgnitionAlarm" => $illegalIgnitionAlarm,
                                    "doorOpenAlarm" => $doorOpenAlarm,
                                    "towAwayAlarm" => $towAwayAlarm,
                                    "gSensorAlarm" => $gSensorAlarm,
                                    "powerCutAlarm" => $powerCutAlarm,
                                    "stopOverTimeAlarm" => $stopOverTimeAlarm,
                                    "overSpeedAlarm" => $overSpeedAlarm,
                                    "sosAlarm" => $sosAlarm,
                                    "idleAlarm" => $idleAlarm,
                                    "fatigueAlarm" => $fatigueAlarm,
                                    "parkingStatus" => $parkingStatus,
                                    "tamperBoxAlarm" => $tamperBoxAlarm,
                                    "harshTurning" => $harshTurning,
                                    "temperatureAbnormal" => $temperatureAbnormal,
                                    "gsmJammingAlarm" => $gsmJammingAlarm,
                                    "fuelSensorDisconnectAlarm" => $fuelSensorDisconnect,
                                    "chargingAlarm" => $chargingAlarm,
                                    "tempHighAlarm" => $tempHighAlarm,
                                    "tempLowAlarm" => $tempLowAlarm,
                                    "blootooth" => $blootooth,
                                    "protectionAlarm" => $protectionAlarm,
                                    "lockWireCutAlarm" => $lockWireCutAlarm,
                                    "chargeFullAlarm" => $chargeFullAlarm,
                                    "lockStatus" => $lockStatus,
                                    "crashAlarm" => $crashAlarm,
                                    "turnover" => $turnover,
                                    "harshAcceleration" => $harshAcceleration,
                                    "harshDeceleration" => $harshDeceleration,
                                    "shakeAlarm" => $shakeAlarm,
                                    "tempSensorAbnormalAlarm" => $tempSensorAbnormal,
                                    "fuelStolenAlarm" => $fuelStolenAlarm,
                                    "lowFuelAlarm" => $lowFuelAlarm,
                                    "loadSensorDisconnectAlarm" => $loadSensorDisconnectAlarm,
                                    "lowSpeedAlarm" => $lowSpeedAlarm,
                                    "gmtTimediff" => $offsetMinutes == NULL ? 180 : $offsetMinutes,
                                    "updateDate" => date('Y-m-d H:i:s')
                                );
                                $mt90frm = new App_forms_mt90_Mt90($alarm_data);
                                $alarmId = $mt90frm->addAlarm();
                            }

                            // data to fm_fuel_refilled
                            if ($fuelrefill == 1) {
                                $refillData = array("mt90Id" => $mt90Id,
                                    "vehicleId" => $vehicleId,
                                    "driverId" => $driverData['driverId'],
                                    "timestamp" => $timestamp,
                                    "latitude" => $latitude,
                                    "longitude" => $longitude,
                                    "fuel" => $fuelper1,
                                    "location" => $location,
                                    "distance" => $mileage,
                                    "speed" => round($speed, 2),
                                    "acc" => $acc,
                                    "updateDate" => date('Y-m-d H:i:s'),
                                    "status" => 1
                                );
                                $db->insert("fm_fuel_refilled", $refillData);
                            }


                            $vehdata = array(
                                'compTime' => $compTime,
                                'vid' => $vehicleId,
                                'mt90Id' => $mt90Id,
                                'lat' => $in_data['latitude'],
                                'lon' => $in_data['longitude'],
                                'loc' => $in_data['location'],
                                'dist' => $in_data['distance'],
                                'acc' => $in_data['acc'],
                                'speed' => $in_data['speed'],
                                'dirn' => $in_data['direction'],
                                'idleSt' => $in_data['idleStatus'],
                                'offmin' => $offsetMinutes,
                                'dname' => $in_data['driverName'],
                                'dId' => $in_data['driverId'],
                                'companyId' => $vehicleData['companyId'],
                                'diffOdo' => $vehicleData['diffOdo'],
                                'fuelConsumption' => $vehicleData['fuelConsumption'],
                                'speedLimit' => $vehicleData['speedLimit'],
                                'idleLimit' => $vehicleData['idleLimit'],
                                'geofenceId' => $in_data['geofenceId'],
                                'event' => $eventAlarm,
                                'loadVoltage' => $in_data['loadVoltage']
                                //'rawData'=>$data_gv
                            );


                            // checking GPS lovated
                            $gpsValid = 0;
                            if ($gps == 1) {
                                $gpsValid = 1;
                            }

                            // checking time stamp greater than current timestamp
                            $timeStampObj = new DateTime($timestamp);
                            $lastTimeObj = new DateTime($lastdata['timestamp']);
                            $curTimeObj = new DateTime();
                            $curTime = $curTimeObj->format('Y');

                            $timeStampValid = 0;
                            if (($timeStampObj->format('Y')) <= $curTime) {
                                $timeStampValid = 1;
                            }
                            // checking latitude = 0 and longitude = 0
                            $latLongValid = 0;
                            if ($latitude > 0 && $longitude > 0) {
                                $latLongValid = 1;
                            }

                            // compare with last timestamp
                            $lastTimeValid = 0;
                            if (($lastdata['timestamp']) && ($timeStampObj->format('Y-m-d H:i:s') > $lastTimeObj->format('Y-m-d H:i:s'))) {
                                $lastTimeValid = 1;
                            }

                            $accValid = 1;
                            if ($lastdata['acc'] == 0 && $vehdata['acc'] == 0) {
                                $accValid = false;
                                $accValid = 0;

                            }

                            //if((strtotime($timestamp)>strtotime($lastdata['timestamp'])) && ($timeStampValid == 1) && ($latLongValid == 1)){

                            if (($accValid == 1) && ($timeStampValid == 1) && ($latLongValid == 1) && ($gpsValid == 1) && ($lastTimeValid == 1)) {
                                $update = new App_models_mt90_Mtop90();
                                $datasend = $update->updatefunction($vehdata, $lastdata);
                            }

                            //$update=new App_models_mt90_Mt90();
                            //$datasend=$update->updatefunction($timestamp,$imei,$mt90Id);


                        }

                    }//if(($pos_data == 80) ||
                }//if(count($val) > 0){
            }
            echo "Result Success";
            $time_end = microtime(true);
            $execution_time = ($time_end - $time_start);// ($time_end - $time_start)/60;
            //execution time of the script
            echo '<br/><b>Total Execution Time:</b> ' . round($execution_time, 2) . ' Sec';

        } catch (Exception $e) {
            print_r($e);
            echo "CatchError";
            $response['data'] = null;
            $response['message'] = $e->getMessage();
            $response['errors'] = $e->getErrors();
            $response['success'] = "F" . $e->getCode();
            echo json_encode($response);
        }


        /*$l=$fuelVal['length']==NULL?NULL:$fuelVal['length'];
                    $d=$fuelVal['diameter']==NULL?NULL:$fuelVal['diameter'];

                    $fuelHeight1=$fuelVal['fuelHeight']==NULL?NULL:$fuelVal['fuelHeight']*0.1;


                    echo "HH:".$fuelHeight1."L:".$l."D:".$d;
                    //V(tank) = Ï€r2l   (Actual litres/Total Volume) * 100
                    //A = (1/2)r2(Î¸ - sinÎ¸) where Î¸ = 2*arccos(m/r) and Î¸ is in radians.
                    //V(segment) = (1/2)r2(Î¸ - sinÎ¸)l.
                    //h>r   V(fill) = V(tank) - V(segment).
                    //-----test----
                    //echo "Length:".$l=10;
                    //echo "<br/>Diameter:".$d=22;
                    //echo "<br/>FUEL Height:".$fuelHeight3=21;
                    //-----test----

                        if((int)$fuelHeight1!=0){

                            $r=$d==NULL?NULL:$d/2;

                            $volumeOftank=3.1416*pow(($r),2)*$l;
                            //"<br/>radius:".$r.
                            echo "<br/>VT:".$volumeOftank;

                            $o=2*acos(($r-$fuelHeight1)/$r);
                        //echo "<br/>THETA:".$o=2*acos(-1);

                            $volumeOfsegment=(1/2)*pow(($r),2)*($o - sin($o))*$l;

                            echo "<br/>volumeOfsegment:".$volumeOfsegment;
                            if($fuelHeight1<($r)){
                                echo "'<'";
                                $volumeFilled_cylinder1=$volumeOfsegment;
                            }elseif($fuelHeight1>($r)){
                                echo "'>'";
                                //if($fuelHeight1==$d){
        //							$volumeFilled_cylinder1=$volumeOfsegment;
        //						}else{
                                    //$volumeFilled_cylinder1=$volumeOftank-$volumeOfsegment;
                                    $volumeFilled_cylinder1=$volumeOfsegment;
                                //}
                            }

                            $volumeFilled_cylinder1=$volumeFilled_cylinder1==NULL?NULL:$volumeFilled_cylinder1;

                            echo "<br/>VOLUME OF FILLED CYLINDRICAL TANK:".$volumeFilled_cylinder1;

                        }else{
                            $volumeFilled_cylinder1=0;
                            $volumeFilled_cylinder1_per=0;
                            //echo "<br/>VOLUME OF FILLED CYLINDRICAL TANK:".$volumeFilled_cylinder1;
                            //echo "<br/>FUEL PER:".$volumeFilled_cylinder1_per;
                        }*/


    }
}
