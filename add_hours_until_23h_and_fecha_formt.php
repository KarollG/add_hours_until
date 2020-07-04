<?php

  function get_holidays($fecha){
    global $DB;
    $query = ("SELECT * FROM name_table where date = '$fecha'");
    $iterator = $DB->request($query);
    return count($iterator);
  }

  $date_case_insert= date('2020-06-24');
  $hora_ini_caso = date('H:i:s', strtotime('10:36:00'));
  $horas_a_sumar_case = date('H:i:s', strtotime('04:00:00'));

  // $date_case_insert= date('2020-07-18');
  // $date_case_insert= date('2020-08-06');
  // $hora_ini_caso = date('H:i:s', strtotime('15:36:00'));
  // $horas_a_sumar_case = date('H:i:s', strtotime('18:00:00'));


  $today = date("Y-m-d H:i:s");
  $week_day= date("w", strtotime($today));
  $time = date("H:i:s", strtotime($today));


    $ini_labor_ma = date('H:i:s', strtotime('06:00:00'));
    $fin_labor_ma = date('H:i:s', strtotime('12:00:00'));
    $ini_labor_tar = date('H:i:s', strtotime('14:00:00'));
    $fin_labor_tar = date('H:i:s', strtotime('18:00:00'));

    function is_habil_time($time){
      global $ini_labor_ma, $fin_labor_ma, $ini_labor_tar, $fin_labor_tar;
      $jornada= ($time>=$ini_labor_ma && $time<$fin_labor_ma || $time>=$ini_labor_tar && $time<$fin_labor_tar)? true : false;
          return $jornada;
    }

    function is_habil_time_ma($time){
      global $ini_labor_ma, $fin_labor_ma;
      $jornada_ma = ($time>=$ini_labor_ma && $time<$fin_labor_ma )? true : false;
      return $jornada_ma;
    }

    function is_habil_time_ta($time){
      global $ini_labor_tar, $fin_labor_tar;
      $jornada_tar =($time>=$ini_labor_tar &&$time<$fin_labor_tar)? true : false;
      return $jornada_tar;
    }

    $ini_labor_ma_s = date('H:i:s', strtotime('08:00:00'));
    $fin_labor_ma_s = date('H:i:s', strtotime('12:00:00'));
    function is_habil_time_s($time){
      global $ini_labor_ma_s, $fin_labor_ma_s;
      $jornada_s=($time>=$ini_labor_ma_s && $time<$fin_labor_ma_s)? true : false;
          return $jornada_s;
    }

    function rest_hour($hora_inicial, $hora_a_restar){
      $resultado2=date("H:i:s",strtotime("00:00") +strtotime($hora_inicial) - strtotime($hora_a_restar) );
      return $resultado2;
    }

    function sum_hour($hora_inicial, $hora_a_sumar){
      $resultado3=date("H:i:s",strtotime("14:00") +strtotime($hora_inicial) + strtotime($hora_a_sumar) );
      return $resultado3;
     }

    $date_labor_case = date('Y-m-d', strtotime($date_case_insert));
    $week_day_date_labor_case = date("w", strtotime($date_labor_case));

    function convert_to_habil_day(){
      global $date_labor_case , $week_day_date_labor_case, $date_case_insert;
      if(get_holidays($date_labor_case) || $week_day_date_labor_case==0){
        while(get_holidays($date_labor_case) || $week_day_date_labor_case==0){
          $date_labor_case = date("Y-m-d",strtotime($date_labor_case."+ 1 days"));
        }
      }
    }
    convert_to_habil_day();

    $is_sabado = ($week_day_date_labor_case == 6) ? true : false;
    $is_l_v = ($week_day_date_labor_case>0 && $week_day_date_labor_case<6) ? true : false;

    function convert_to_habil_hour(){
      global $week_day_date_labor_case,$hora_ini_caso, $date_labor_case, $ini_labor_ma, $fin_labor_ma , $ini_labor_tar, $fin_labor_tar, $ini_labor_ma_s, $fin_labor_ma_s;
      if(!is_habil_time($hora_ini_caso) && !is_habil_time_s($hora_ini_caso) ){
        if($week_day_date_labor_case==6){
          if($hora_ini_caso<$ini_labor_ma_s){
            $hora_ini_caso= date('H:i:s', strtotime('08:00:00'));
            return $hora_ini_caso;
          }else if($hora_ini_caso>=$fin_labor_ma_s){
            $hora_ini_caso= date('H:i:s', strtotime('06:00:00'));
            $date_labor_case = date("Y-m-d",strtotime($date_labor_case."+ 1 days"));
            convert_to_habil_day();
            return array($hora_ini_caso, $date_labor_case);
          }
        } elseif($week_day_date_labor_case>0 && $week_day_date_labor_case<6){
          if($hora_ini_caso<$ini_labor_ma){
            $hora_ini_caso= date('H:i:s', strtotime('06:00:00'));
            return $hora_ini_caso;
          }
          else if($hora_ini_caso>=$fin_labor_ma && $hora_ini_caso<$ini_labor_tar ){
            $hora_ini_caso= date('H:i:s', strtotime('14:00:00'));
            return $hora_ini_caso;
          }else if ($hora_ini_caso>=$fin_labor_tar){
            $hora_ini_caso= date('H:i:s', strtotime('06:00:00'));
            $date_labor_case = date("Y-m-d",strtotime($date_labor_case."+ 1 days"));
            convert_to_habil_day();
            return array($hora_ini_caso, $date_labor_case);
          }
        }else{
          convert_to_habil_day();
          echo 'se ejecuto el  convert to habil day';
          return $date_labor_case;
        }
      }
    }
    convert_to_habil_hour();

    function cal_hor_labor_disp_rest(){

      $horas_falt_rest=1;
      global $hora_ini_caso, $fin_labor_ma_s, $horas_a_sumar_case, $date_case_insert, $date_labor_case;
      global $fin_labor_ma, $fin_labor_tar, $horas_falt_suma, $rest, $rest_int,$horas_falt_suma_int, $week_day_date_labor_case;
      while($horas_falt_rest>0){
        // echo "<br/>\n";
        // echo ('dia '.$week_day_date_labor_case. ' hora inicio case '. date('H:i:s', strtotime($hora_ini_caso)).' dia inicio caso'. $date_case_insert . 'dia final caso' . $date_case_insert . 'hora a sumar' . $horas_a_sumar_case .' ');
        // echo "<br/>\n";
        if($week_day_date_labor_case==6){
          $rest= rest_hour($fin_labor_ma_s, $hora_ini_caso) ;
          $rest_int= (int) $rest;
          $horas_falt_suma= rest_hour($horas_a_sumar_case ,$rest) ;
          $horas_a_sumar_case_int= (int)  $horas_a_sumar_case;
          $horas_falt_suma_int= $horas_a_sumar_case_int - $rest_int;
            if($horas_falt_suma_int<=0){
              $hora_final= sum_hour($fin_labor_ma, $horas_falt_suma);
              // $mensaje= 'dia  sabado';
              // return array ($mensaje, date('H:i:s', strtotime($rest)) ,  date('H:i:s', strtotime($horas_falt_suma))  ,date('H:i:s', strtotime($hora_final)),date("Y-m-d",strtotime($date_labor_case ))) ;
              return array ('hora final',date('H:i:s', strtotime($hora_final)), 'dia final',date("Y-m-d",  strtotime($date_labor_case ))) ;
            } else {
              $hora_ini_caso= date('H:i:s', strtotime('06:00:00'));
              $date_labor_case = date("Y-m-d",strtotime($date_labor_case."+ 2 days"));
              convert_to_habil_day();
              $horas_a_sumar_case= rest_hour($horas_a_sumar_case, $rest ) ;
              $week_day_date_labor_case = date("w", strtotime($date_labor_case));
            }
          } else if( $week_day_date_labor_case>0 && $week_day_date_labor_case<6){
            if(is_habil_time_ma($hora_ini_caso)){
              $rest= rest_hour( $fin_labor_ma, $hora_ini_caso);
              $rest_int= (int) $rest;
              $horas_falt_suma= rest_hour( $horas_a_sumar_case, $rest);
              $horas_a_sumar_case_int= (int)  $horas_a_sumar_case;
              $horas_falt_suma_int= $horas_a_sumar_case_int - $rest_int;
              if ($horas_falt_suma_int<=0){
                  $hora_final= sum_hour($fin_labor_ma, $horas_falt_suma);
                  // $mensaje= 'dia de semana mañana';
                  // return array ($week_day_date_labor_case, $mensaje, date('H:i:s', strtotime($rest)) ,$horas_a_sumar_case_int, $rest_int ,$horas_falt_suma_int,    date('H:i:s', strtotime($horas_falt_suma))  ,date('H:i:s', strtotime($hora_ini_caso)),date('H:i:s', strtotime($hora_final)),date("Y-m-d",strtotime($date_labor_case ))) ;
                  return array ('hora final',date('H:i:s', strtotime($hora_final)), 'dia final',date("Y-m-d",  strtotime($date_labor_case ))) ;
              } else if($horas_falt_suma_int>0){
                  $hora_a_sumar= date('H:i:s', strtotime('02:00:00'));
                  $hora_ini_caso= sum_hour($fin_labor_ma, $hora_a_sumar);
                  $horas_a_sumar_case= rest_hour($horas_a_sumar_case, $rest ) ;
              }
            } else if(is_habil_time_ta($hora_ini_caso)){
              $rest= rest_hour( $fin_labor_tar, $hora_ini_caso);
              $rest_int= (int) $rest;
              $horas_falt_suma= rest_hour( $horas_a_sumar_case, $rest);
              $horas_a_sumar_case_int= (int)  $horas_a_sumar_case;
              $horas_falt_suma_int=  $horas_a_sumar_case_int -$rest_int ;
              if($horas_falt_suma_int<=0){
                $hora_final= sum_hour($fin_labor_tar, $horas_falt_suma);
                // $mensaje= 'dia de semana tarde';
                //return array ($horas_a_sumar_case,$horas_falt_suma, 'horas falta a sumar int',$horas_falt_suma_int,'mens',$mensaje, 'rest', date('H:i:s', strtotime($rest))  ,  'horas falta sumar',  date('H:i:s', strtotime($horas_falt_suma)) , 'hora inicio case', date('H:i:s', strtotime($hora_ini_caso)), 'hora final',date('H:i:s', strtotime($hora_final)), 'dia final',date("Y-m-d",  strtotime($date_labor_case ))) ;
                return array ('hora final',date('H:i:s', strtotime($hora_final)), 'dia final',date("Y-m-d",  strtotime($date_labor_case ))) ;
              } else if($horas_falt_suma_int>0){
                  $date_labor_case = date("Y-m-d",strtotime($date_labor_case."+ 1 days"));
                  $horas_a_sumar_case= rest_hour($horas_a_sumar_case, $rest ) ;
                  convert_to_habil_day();
                  $week_day_date_labor_case = date("w", strtotime($date_labor_case));
                  if($week_day_date_labor_case==6){
                    $hora_ini_caso= date('H:i:s', strtotime('08:00:00'));
                  }else{
                    $hora_ini_caso= date('H:i:s', strtotime('06:00:00'));
                  }
              }
            }
          }
      }
    }

    //  echo cal_hor_labor_disp_rest();
     echo "<br/>\n";
     echo "<br/>\n";
     print_r(cal_hor_labor_disp_rest());

  ?>