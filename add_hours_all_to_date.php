<?php
    // Current time and day
    $today = date("Y-m-d H:i:s");
    // $date_case_insert = date("Y-m-d", strtotime($today));
    $date = explode(' ',$today);
    $date_case_insert= $date[0];
    $hour_start_case = $date[1];

    //Any date and time
    // $date_case_insert= date('2020-06-27');
    // $hour_start_case = '10:36:00';

    //Hours to add
    $hours_to_add_case =(int) '3';

    $date_labor_case = date('Y-m-d', strtotime($date_case_insert));
    $week_day_date_labor_case = date("w", strtotime($date_labor_case));

    function get_holidays($fecha){
        global $DB;
        $query = ("SELECT * FROM table_name where date = '$fecha'");
        $iterator = $DB->request($query);
        return count($iterator);
    }

    function convert_num_to_hours($num){
        $cero= 0;
        if ($num>=0 && $num<=9){
            $num= $cero.$num.':'.$cero.$cero.':'.$cero.$cero;
            return $num;
        }else{
            $num= $num.':'.$cero.$cero.':'.$cero.$cero;
            return $num;
        }
    }

    $start_work_morning = '06:00:00';
    $end_work_morning = '12:00:00';
    $start_work_afternoon = '14:00:00';
    $end_work_afternoon = '18:00:00';

    function is_habil_time($time){
        global $start_work_morning, $end_work_morning, $start_work_afternoon, $end_work_afternoon;
        $jornada= (($time>=$start_work_morning && $time<$end_work_morning) || ($time>=$start_work_afternoon && $time<$end_work_afternoon))? true : false;
            return $jornada;
      }

    function is_habil_time_morning($time){
        global $start_work_morning, $end_work_morning;
        $jornada_ma = ($time>=$start_work_morning && $time<$end_work_morning )? true : false;
        return $jornada_ma;
      }

    function is_habil_time_afternoon($time){
        global $start_work_afternoon, $end_work_afternoon;
        $jornada_tar =($time>=$start_work_afternoon &&$time<$end_work_afternoon)? true : false;
        return $jornada_tar;
    }

    $start_work_saturday = '08:00:00';
    $end_work_saturday = '12:00:00';

    function is_habil_time_saturday($time){
        global $ini_labor_ma_s2, $end_work_saturday;
        $jornada_s=($time>=$ini_labor_ma_s2 && $time<$end_work_saturday)? true : false;
            return $jornada_s;
    }

    function add_hours($hora1,$hora2){
        $hora1=explode(":",$hora1);
        $hora2=explode(":",$hora2);
        $temp=0;
        $segundos=(int)$hora1[2]+(int)$hora2[2];
        while($segundos>=60){
            $segundos=$segundos-60;
            $temp++;
        }
        $minutos=(int)$hora1[1]+(int)$hora2[1]+$temp;
        $temp=0;
        while($minutos>=60){
            $minutos=$minutos-60;
            $temp++;
        }
        $horas=(int)$hora1[0]+(int)$hora2[0]+$temp;
        if($horas<10)
            $horas= '0'.$horas;
        if($minutos<10)
            $minutos= '0'.$minutos;
        if($segundos<10)
            $segundos= '0'.$segundos;
        $sum_hrs = $horas.':'.$minutos.':'.$segundos;
        return ($sum_hrs);
    }

    function subtract_hours($hora1,$hora2){
        $temp1 = explode(":",$hora1);
        $temp_h1 = (int)$temp1[0];
        $temp_m1 = (int)$temp1[1];
        $temp_s1 = (int)$temp1[2];
        $temp2 = explode(":",$hora2);
        $temp_h2 = (int)$temp2[0];
        $temp_m2 = (int)$temp2[1];
        $temp_s2 = (int)$temp2[2];
        if( $temp_h1 < $temp_h2 ){
            $temp  = $hora1;
            $hora1 = $hora2;
            $hora2 = $temp;
        }elseif( $temp_h1 == $temp_h2 && $temp_m1 < $temp_m2){
            $temp  = $hora1;
            $hora1 = $hora2;
            $hora2 = $temp;
        } elseif( $temp_h1 == $temp_h2 && $temp_m1 == $temp_m2 && $temp_s1 < $temp_s2){
            $temp  = $hora1;
            $hora1 = $hora2;
            $hora2 = $temp;
        }
        $hora1=explode(":",$hora1);
        $hora2=explode(":",$hora2);
        $temp_horas = 0;
        $temp_minutos = 0;
        $segundos=0;
        if( (int)$hora1[2] < (int)$hora2[2] ){
            $temp_minutos = -1;
            $segundos = ( (int)$hora1[2] + 60 ) - (int)$hora2[2];
        }else
            $segundos = (int)$hora1[2] - (int)$hora2[2];
        $minutos=0;
        if( (int)$hora1[1] < (int)$hora2[1] ){
            $temp_horas = -1;
            $minutos = ( (int)$hora1[1] + 60 ) - (int)$hora2[1] + $temp_minutos;
        }else
            $minutos =  (int)$hora1[1] - (int)$hora2[1] + $temp_minutos;
        $horas = (int)$hora1[0]  - (int)$hora2[0] + $temp_horas;
        if($horas<10)
            $horas= '0'.$horas;
        if($minutos<10)
            $minutos= '0'.$minutos;
        if($segundos<10)
            $segundos= '0'.$segundos;
        $rst_hrs = $horas.':'.$minutos.':'.$segundos;
        return ($rst_hrs);
     }

    function convert_to_habil_day(){
        global $date_labor_case,$hour_start_case ;
        $week_day_date_labor_case = date("w", strtotime($date_labor_case));
        while(get_holidays($date_labor_case) xor ($week_day_date_labor_case == 0)){
            if(get_holidays($date_labor_case) xor ($week_day_date_labor_case == 0) ){
                $date_labor_case = date("Y-m-d",strtotime($date_labor_case."+ 1 days"));
                $week_day_date_labor_case = date("w", strtotime($date_labor_case));
                if($week_day_date_labor_case == 0){
                    $date_labor_case = date("Y-m-d",strtotime($date_labor_case."+ 1 days"));
                    $hour_start_case= '06:00:00';
                    return array($hour_start_case, $date_labor_case);
                }else if(get_holidays($date_labor_case) ){
                    $date_labor_case = date("Y-m-d",strtotime($date_labor_case."+ 1 days"));
                    $week_day_date_labor_case = date("w", strtotime($date_labor_case));
                    $hour_start_case= '06:00:00';
                    return array($hour_start_case, $date_labor_case);
                }else if($week_day_date_labor_case==6){
                    $hour_start_case= '08:00:00';
                    return array($hour_start_case, $date_labor_case);
                }else{
                    $hour_start_case= '06:00:00';
                    return array($hour_start_case, $date_labor_case);
                }
            }
        }
    }
    convert_to_habil_day();

    function convert_to_habil_hour(){
        global $week_day_date_labor_case,$hour_start_case, $date_labor_case, $start_work_morning, $end_work_morning , $start_work_afternoon, $end_work_afternoon, $ini_labor_ma_s2, $end_work_saturday, $hour_start_case;
        $week_day_date_labor_case = date("w", strtotime($date_labor_case));
        if(!is_habil_time($hour_start_case) || !is_habil_time_saturday($hour_start_case) ){
          if($week_day_date_labor_case==6){
            if(!is_habil_time_saturday($hour_start_case)){
                if($hour_start_case<$ini_labor_ma_s2){
                    $hour_start_case= '08:00:00';
                    return $hour_start_case;
                }else if($hour_start_case>=$end_work_saturday){
                    $hour_start_case= '06:00:00';
                    $date_labor_case = date("Y-m-d",strtotime($date_labor_case."+ 2 days"));
                    convert_to_habil_day();
                    $week_day_date_labor_case = date("w", strtotime($date_labor_case));
                    return array($hour_start_case, $date_labor_case);
                }
            }
          } elseif($week_day_date_labor_case>0 && $week_day_date_labor_case<6){
            if(!is_habil_time_morning($hour_start_case)&& !is_habil_time_afternoon($hour_start_case)){
                if($hour_start_case<$start_work_morning){
                     $hour_start_case= '06:00:00';
                     return $hour_start_case;
                }
                else if($hour_start_case>=$end_work_morning && $hour_start_case<$start_work_afternoon ){
                    $hour_start_case= '14:00:00';
                    return $hour_start_case;
                }else if ($hour_start_case>=$end_work_afternoon){
                    $date_labor_case = date("Y-m-d",strtotime($date_labor_case."+ 1 days"));
                    convert_to_habil_day();
                    $week_day_date_labor_case = date("w", strtotime($date_labor_case));
                    if($week_day_date_labor_case==6){
                        $hour_start_case= '08:00:00';
                    }else{
                        $hour_start_case= '06:00:00';
                    }
                }
            }
          }
        }
    }
     convert_to_habil_hour();

    function calc_hour_work_and_date_solution(){
      global  $date_labor_case,$hour_start_case, $week_day_date_labor_case,  $hours_to_add_case, $hours_left_to_add ;
      global $end_work_morning, $end_work_afternoon, $end_work_saturday;
      $hours_to_add_case= convert_num_to_hours($hours_to_add_case);
      $week_day_date_labor_case = date("w", strtotime($date_labor_case));
      $horas_falt_rest=1;
      while($horas_falt_rest>0){
        if($week_day_date_labor_case==6){
          $residuary = subtract_hours($end_work_saturday, $hour_start_case);
          $residuary_int= (int) $residuary;
          $hours_left_to_add= subtract_hours($hours_to_add_case ,$residuary) ;
          $hours_to_add_case_int= (int)  $hours_to_add_case;
          $hours_left_to_add_int= $hours_to_add_case_int - $residuary_int;
            if($hours_left_to_add_int<=0){
              $final_hour= add_hours($hour_start_case, $hours_to_add_case);
              if($final_hour>$end_work_saturday){
                $hours_to_add_case= subtract_hours($end_work_saturday, $final_hour) ;
                $hour_start_case= '06:00:00';
                $date_labor_case = date("Y-m-d",strtotime($date_labor_case."+ 2 days"));
                convert_to_habil_day();
                $week_day_date_labor_case = date("w", strtotime($date_labor_case));
              }else{
                return array ( $final_hour,date("Y-m-d",strtotime($date_labor_case ))) ;
              }
            } else  {
              $hour_start_case= '06:00:00';
              $date_labor_case = date("Y-m-d",strtotime($date_labor_case."+ 2 days"));
              convert_to_habil_day();
              $hours_to_add_case= subtract_hours($hours_to_add_case, $residuary ) ;
              $week_day_date_labor_case = date("w", strtotime($date_labor_case));
            }
          } else if( $week_day_date_labor_case>0 && $week_day_date_labor_case<6){
            if(is_habil_time_morning($hour_start_case)){
              $residuary = subtract_hours($end_work_morning, $hour_start_case);
              $residuary_int= (int) $residuary;
              $hours_left_to_add= subtract_hours($hours_to_add_case ,$residuary) ;
              $hours_to_add_case_int= (int)  $hours_to_add_case;
              $hours_left_to_add_int= $hours_to_add_case_int - $residuary_int;
              if ($hours_left_to_add_int<=0){
                  $final_hour= add_hours($hour_start_case, $hours_to_add_case);
                  if($final_hour>$end_work_morning){
                    $hours_to_add_case= subtract_hours($end_work_morning, $final_hour ) ;
                    $hour_start_case= '14:00:00';
                  }else{
                    return array ($final_hour,date("Y-m-d",strtotime($date_labor_case ))) ;
                  }
                } else if($hours_left_to_add_int>0){
                  $hour_start_case= '14:00:00';
                  $hours_to_add_case= subtract_hours($hours_to_add_case, $residuary ) ;
              }
            } else if(is_habil_time_afternoon($hour_start_case)){
              $residuary = subtract_hours($end_work_afternoon, $hour_start_case);
              $residuary_int= (int) $residuary;
              $hours_left_to_add= subtract_hours($hours_to_add_case ,$residuary) ;
              $hours_to_add_case_int= (int)  $hours_to_add_case;
              $hours_left_to_add_int= $hours_to_add_case_int - $residuary_int;
                if($hours_left_to_add_int<=0){
                  $final_hour= add_hours($hour_start_case, $hours_to_add_case);
                  if($final_hour>$end_work_afternoon){
                    $hours_to_add_case= subtract_hours($end_work_afternoon, $final_hour ) ;
                    $date_labor_case = date("Y-m-d",strtotime($date_labor_case."+ 1 days"));
                    convert_to_habil_day();
                    $week_day_date_labor_case = date("w", strtotime($date_labor_case));
                    if($week_day_date_labor_case==6){
                        $hour_start_case= '08:00:00';
                    }else{
                        $hour_start_case= '06:00:00';
                    }
                  }else{
                    return array ($final_hour, date("Y-m-d",  strtotime($date_labor_case ))) ;
                  }
              } else if($hours_left_to_add_int>0){
                  $date_labor_case = date("Y-m-d",strtotime($date_labor_case."+ 1 days"));
                  $hours_to_add_case= subtract_hours($hours_to_add_case, $residuary ) ;
                  convert_to_habil_day();
                  $week_day_date_labor_case = date("w", strtotime($date_labor_case));
                  if($week_day_date_labor_case==6){
                    $hour_start_case= '08:00:00';
                  }else{
                    $hour_start_case= '06:00:00';
                  }
              }
            }
          }
      }
    }
    $resultad0= calc_hour_work_and_date_solution();
    echo "<br/>\n";
    echo 'Solution Time: '.  $resultad0[0];
    echo "<br/>\n";
    echo 'Solution Date: '.  $resultad0[1];
  ?>