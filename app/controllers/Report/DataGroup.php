<?php

namespace Report;

use \Report\TbQuery;
use \Report\TbReporte;
use \Report\UserReport;
use \Validator;
use \DateTime;
use \Log;

class DataGroup
{
    // PROCESS MAIN DATA GROUP
    public static function process($parameters)
    {

        $result = array(
            'status' => 'OK'
        );
        try{
            //$data, $rows, $columns, $values, $keyColumns
            self::showRam("DataGroup process ini");
            $rules  = array(
                'body'       => 'required|array', 
                'rows'       => 'required|array', 
                'columns' => 'array', 
                'values'     => 'required|array', 
                'keyColumns' => 'array'
            );
            $validator = Validator::make($parameters, $rules);
            if ($validator->fails()){
                $error            = $validator->messages();
                $result['status'] = 'ERROR';
                $result['data']   = $error;
                
            }else{
                $data = $parameters['body'];
                $filter = self::sortByFields($parameters['rows']);
                $data   = self::array_orderby($data, $filter);
                foreach ($parameters['rows'] as $key => $value) {
                    $data = self::recursiveGroup($data, $parameters['rows'], $value);
                }
                $data = self::operation($data, $parameters['values'], $parameters['rows'], $parameters['columns'], $parameters['keyColumns']);
                $data = self::parseData($data, $parameters['rows'], $parameters['columns']);
                $temp_total  = array();
                foreach ($parameters['values'] as $key => $valueElementTemp) {
                    $total        = 0;
                    $total_value  = 0;
                    $values_temp  = explode(":",$valueElementTemp);

                    $operator     = $values_temp[0];
                    $valueElement = $values_temp[1];
                    foreach ($data as $key => $value) {
                        foreach ($parameters['columns'] as $keyColumns => $valueColumns) {
                            if(is_array($value[$valueColumns])){
                                if (!isset($temp_total[$valueColumns][$valueElement])) {
                                    $temp_total[$valueColumns][$valueElement] = $value[$valueColumns][$valueElement];
                                } else {
                                    $temp_total[$valueColumns][$valueElement] += $value[$valueColumns][$valueElement];
                                }
                            }else{
                                if (!isset($temp_total[$valueColumns])) {
                                    $temp_total[$valueColumns] = $value[$valueColumns];
                                } else {
                                    $temp_total[$valueColumns] += $value[$valueColumns];
                                }
                            }
                            $total = $value[$valueColumns];
                        }
                        $total_value += $value[$valueElement];
                    }
                    $fields_size = count($parameters['rows']) - 1;
                    for ($i = 0; $i <= $fields_size; $i++) {
                        if ($i == $fields_size)
                            $temp_total[$parameters['rows'][$i]] = "Total";
                        else
                            $temp_total[$parameters['rows'][$i]] = "";
                    }
                    $temp_total[$valueElement] = $total_value;
                }
                $result['total'] = $temp_total;
                self::showRam("DataGroup process ini");  
                $result['data'] = $data;
            }
        }
        catch (Exception $e) {
            $result["status"]  = 'ERROR';
            $result["message"] = REPORT_MESSAGE_EXCEPTION;
        }
        finally
        {
            return $result;
        }    
    }

    public static function arrayCastRecursive($array)
    {
        set_time_limit(REPORT_TIME_LIMIT);
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = self::arrayCastRecursive($value);
                }
                if ($value instanceof stdClass) {
                    $array[$key] = self::arrayCastRecursive((array) $value);
                }
            }
        }
        if ($array instanceof stdClass) {
            
            return arrayCastRecursive((array) $array);
        }
        
        return $array;
    }
    
    public static function sortByFields($array)
    {
        $filter = array();
        foreach ($array as $key => $value) {
            array_push($filter, $value);
            array_push($filter, SORT_ASC);
        }
        return $filter;
    }

    public static function array_orderby()
    {
        // try{
        $args = func_get_args();
        $data = array_shift($args);
        $args = $args[0];
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row){
                    $tmp[$key] = $row[$field];
                }
                $args[$n] = $tmp;
            }
        }
        $args[] =& $data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
        // }catch(Exception $e){
        // }
    }
    
    public static function recursiveGroup($valueData, $field, $label)
    {
        $result;
        self::showRam("DataGroup recursiveGroup ini");  
        $elem = count($field) >= 1 ? array_shift($field) : $field;
        
        $igual = $elem == $label;
        
        if ($igual) {
            
            $result_temp = array();
            foreach ($valueData as $key => $value) {
                $result_temp[$value[$label]][] = $value;
            }
            $result = $result_temp;
        } else {
            
            foreach ($valueData as $key => $value) {
                $result_temp     = self::recursiveGroup($value, $field, $label);
                $valueData[$key] = $result_temp;
            }
            $result = $valueData;
        }
        self::showRam("DataGroup recursiveGroup ini");  
        return $result;
    }
    
    public static function operation($data, $values, $rows, $columns, $keyColumns)
    {
        set_time_limit(REPORT_TIME_LIMIT);
        return self::operationForEach($data, $values, count($rows), $rows, $columns, $keyColumns);
    }
    
    private static function operationForEach($data, $values, $count, $rows, $columns, $keyColumns)
    {
        set_time_limit(REPORT_TIME_LIMIT);
        $result = array();
        try{
            $count--;
            if($count >= 1) {
                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        $recursiveResult = self::operationForEach($value, $values, $count, $rows, $columns, $keyColumns);
                        $result[$key]    = $recursiveResult;
                    }
                }
            } else {
                foreach ($data as $keyKeyValue => $valueValueValue) {
                    $temp_result = array();
                    $xlas = false;
                    foreach ($valueValueValue as $keyKeyKeyValue => $valueValueValueValue) {

                        $keyKeyValue = $keyKeyValue == '' ? NULL : $keyKeyValue;

                        if (!isset($temp_result[$keyKeyValue])) {
                            $temp = array();
                            foreach ($rows as $keyField => $valueField) {
                                // if(!empty(trim($valueValueValueValue[$valueField], " ")))
                                    $temp[$valueField] = trim($valueValueValueValue[$valueField], " ");
                            }
                            if(count($temp)==0)
                                $xlas = true;
                            foreach ($keyColumns as $key => $value) {
                                $temp[$value] = $valueValueValueValue[$value];
                            }

                            foreach ($values as $key => $valueElement) {
                                $value_temp = explode(":",$valueElement);
                                $operator = $value_temp[0];
                                $values_element = $value_temp[1];

                                foreach ($columns as $key => $value) {
                                    if(!isset($temp[$value]))
                                        $temp[$value] = array();
                                    if ($value == $valueValueValueValue[$keyColumns[0]]) {
                                        if ($operator == 'SUM') {
                                            $temp[$valueValueValueValue[$keyColumns[0]]][$values_element] = (int) ($valueValueValueValue[$values_element] === null ? 0 : $valueValueValueValue[$values_element]);
                                        }elseif ($operator == 'COUNT') {
                                            $temp[$valueValueValueValue[$keyColumns[0]]][$values_element] = array();
                                            $temp[$valueValueValueValue[$keyColumns[0]]][$values_element][] = $valueValueValueValue[$values_element];
                                        }
                                    } else {
                                        if ($operator == 'SUM') {
                                            $temp[$value][$values_element] = 0;
                                        }elseif ($operator == 'COUNT') {
                                            $temp[$value][$values_element] = array();
                                        }
                                    }
                                }
                                if ($operator == 'SUM') {
                                    $temp[$values_element] = $valueValueValueValue[$values_element];
                                } elseif ($operator == 'COUNT') {
                                    $temp[$values_element][] = $valueValueValueValue[$values_element];
                                }
                            }
                            if($xlas == false){
                                $temp_result[$keyKeyValue] = $temp;
                            }
                        } else {
                            foreach ($rows as $keyField => $valueField) {
                                $temp_result[$keyKeyValue][$valueField] = $valueValueValueValue[$valueField];
                            }

                            foreach ($values as $key => $valueElement) {

                                $value_temp     = explode(":",$valueElement);
                                $operator       = $value_temp[0];
                                $values_element = $value_temp[1];
                                foreach ($columns as $key => $value) {
                                    if ($valueValueValueValue[$keyColumns[0]] == $value) {
                                        if ($operator == 'SUM') {
                                            $temp_result[$keyKeyValue][$value][$values_element] += $valueValueValueValue[$values_element];
                                        } elseif ($operator == 'COUNT') {
                                            $temp_result[$keyKeyValue][$value][$values_element][] = $valueValueValueValue[$values_element];
                                        }
                                    }
                                }
                                if ($operator == 'SUM')
                                    $temp_result[$keyKeyValue][$values_element] += $valueValueValueValue[$values_element];
                                elseif ($operator == 'COUNT'){
                                    $temp_result[$keyKeyValue][$values_element][] = $valueValueValueValue[$values_element];
                                }
                            }
                            
                        }
                        
                    }
                    if ($xlas != true){
                        foreach ($values as $key => $valueElement) {
                            $value_temp     = explode(":",$valueElement);
                            $operator       = $value_temp[0];
                            $values_element = $value_temp[1];

                            if ($operator == 'COUNT'){
                                if(is_array($temp_result[$keyKeyValue][$values_element]))
                                {
                                    $temp_array_unique = array_unique($temp_result[$keyKeyValue][$values_element]);
                                    sort($temp_array_unique, SORT_NATURAL | SORT_FLAG_CASE);
                                    $temp_result[$keyKeyValue][$values_element] = count($temp_array_unique);
                                }
                                foreach ($columns as $colkey => $colvalue) {
                                    if(is_array($temp_result[$keyKeyValue][$colvalue][$values_element])){
                                        $temp_array_unique = array_unique($temp_result[$keyKeyValue][$colvalue][$values_element]);
                                        sort($temp_array_unique, SORT_NATURAL | SORT_FLAG_CASE);
                                        $temp_result[$keyKeyValue][$colvalue][$values_element] = count($temp_array_unique);
                                    }
                                }
                            }
                        }
                        $result[$keyKeyValue] = $temp_result[$keyKeyValue];
                    }
                }
                
            }
        }catch(Exception $e){
        }finally{
            return $result;
        }
    }
    
    
    private static function parseDataForEach($data, $fields, $valuesList)
    {
        $result  = array();
        $element = array_shift($fields);
        if ($element) {
            if (count($fields) >= 1) {
                foreach ($data as $key => $value) {
                    $result = array_merge($result, self::parseDataForEach($value, $fields, $valuesList));
                }
            } else {
                foreach ($data as $key => $value) {
                    $result[] = $value;
                }
            }
        }
        return $result;
    }
    
    
    public static function parseData($data, $fields, $valuesList)
    {
        $result = array();
        $result = self::parseDataForEach($data, $fields, $valuesList);
        return $result;
    }
    
    
    
    public static function replaceSlaceDate($date)
    {
        return strpos($date, "/") == -1 ? $date : str_replace("/", "-", $date);
    }
    
    public static function cantidadMesesFechas($fromDate, $toDate)
    {
        $fechainicial = new DateTime(self::replaceSlaceDate($fromDate) . ' - 1 day');
        $fechafinal   = new DateTime(self::replaceSlaceDate($toDate));
        $diferencia   = $fechainicial->diff($fechafinal);
        
        $meses = ($diferencia->y * 12) + $diferencia->m + $diferencia->d / 30;
        return $meses;        
    }

    public static function showRam($identifier){
        $total_memoria  = ini_get('memory_limit');
        $memory_usage   = round((memory_get_usage()/1024)/1024);
        $peak_usage     = round((memory_get_peak_usage()/1024)/1024);
    }
}