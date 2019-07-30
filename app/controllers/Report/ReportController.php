<?php

namespace Report;

use Illuminate\Support\Facades\Response;
use \Excel;
use \View;
use \BaseController;
use \Auth;
use \Report\TbQuery;
use \Report\TbReporte;
use \Report\UserReport;
use \Validator;
use \DateTime;
use \Log;
use \DB;
use \Input;
use \Session;
use \Carbon\Carbon;
use \Exception;

class ReportController extends BaseController
{
    
    public function mainHandler()
    {
        return View::make('Report.main');
    }

    // LIST REPORT USER HANDLER
    public function listReportsUserHandler()
    {
        $result = [ status => ok ]; 
        try 
        {
            $userId       = Auth::user()->id;
            $reportIdList = UserReport::where('id_usuario', $userId)->lists('id_reporte');
            $reportList   = TbReporte::whereIn('id_reporte', $reportIdList)->get();
            if(!$reportList->isEmpty()){
                $result["data"]   = $reportList;
            }
        }
        catch (Exception $e)
        {
            $result = $this->internalException( $e , __FUNCTION__ );
        }
        finally
        {
            return $result;
        }        
    }

    // idkc : CONVERT 20150501 TO 2015/05/01
    private function convertDateHtmlToDateString($dateHtml){
        return substr($dateHtml, 0, 4) . '/' . substr($dateHtml, 4, 2) . '/' . substr($dateHtml, 6, 8);
    }

    // REPORT VIEW HANDLER
    public function reportViewHandler($id_reporte, $fromDate, $toDate)
    {  
        set_time_limit(REPORT_TIME_LIMIT);

        try
        {
            $runTimeIni = $this->getRunTime();
            $configReport = array(
                'reportId' => $id_reporte,
                'fromDate' => $this->convertDateHtmlToDateString($fromDate),
                'toDate'   => $this->convertDateHtmlToDateString($toDate)
            );

            $rules  = array(
                'reportId' => 'required',
                'fromDate' => 'date_format:Y/m/d',
                'toDate'   => 'date_format:Y/m/d'
            );
            // 'toDate'   => 'date_format:Y/m/d|after:fromDate'
            $messages = array(
                'required'    => 'El campo :attribute es requerido.',
                'date_format' => 'El campo :attribute no tiene el formato correcto.',
                'same'        => 'El campo :attribute y :other deben ser iguales.',
                'size'        => 'El campo :attribute debe ser exactamente :size caracteres.',
                'between'     => 'EL campo :attribute debe estar entre :min - :max.',
                'in'          => 'El campo  :attribute debe ser uno de los siguientes tipos: :values'
            );

            $validator = Validator::make($configReport, $rules, $messages);
            if( $validator->fails() )
            {
                return $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );
            }
            else
            {
                $data = $this->reportViewProcess($configReport);
                if( $data[ status ] == ok )
                {
                    if( ! isset( $data[ description ] ) )
                    {
                        $data['reportId'] = $id_reporte;
                        $data['fromDate'] = $fromDate;
                        $data['toDate']   = $toDate;

                        $runTimeFin                   = $this->getRunTime();
                        $data['analytics']['runTime'] = round($runTimeFin - $runTimeIni, 4);
                        $filename                     = Auth::id() . '-' . $data['reportId'] . '-' . $data['fromDate'] . '-' . $data['toDate'];

                        Session::put('reportFileName', $filename);
                        Session::put('reportData', $data);
                        $result = $data;
                    }
                    else
                    {
                        $result = $data;
                    }
                }
                else
                {
                   $result = $this->warningException( REPORT_MESSAGE_EXCEPTION , __FUNCTION__ , __LINE__ , __FILE__ );
                }
            }
        }
        catch( Exception $e )
        {
            $result = $this->internalException( $e , __FUNCTION__ );
        }
        finally
        {
            return $result;
        }
        
    }
    public function reportExcelHandler()
    {
        set_time_limit(REPORT_TIME_LIMIT);
        $result = [ status => ok ];
        try
        {
            $reportFileName = Session::get('reportFileName');
            $reportData     = Session::get('reportData');
            // $inputs         = Input::instance()->getContent();
            // $inputs         = (array) json_decode($inputs);
            $inputs         = Input::all();
            
            if( $inputs['id'] = $reportData['reportId'] && $inputs['fromDate'][0].$inputs['fromDate'][1].$inputs['fromDate'][2] == $reportData['fromDate'] && $inputs['toDate'][0].$inputs['toDate'][1].$inputs['toDate'][2] == $reportData['toDate'] )
            {
                $reportData['filter'] = $inputs['filter'];
                $report               = $this->createReportExcel($reportData, $reportFileName);
                
                if($report[ status ] == ok )
                {
                    list($userId, $reportId, $fromDate, $toDate) = explode("-", $reportFileName);
                    $result['url'] = asset('/reports/export/download/'. $userId .'/'. $reportId .'/'. $fromDate .'/'. $toDate .'/');
                    $result['ext'] = $report['report']['ext'];
                }
                else
                {
                    $result = $this->warningException( REPORT_MESSAGE_EXPORT_GENERATE , __FUNCTION__ , __LINE__ , __FILE__ );
                }
            }
            else
            {
                $result = $this->warningException( REPORT_MESSAGE_EXPORT_GENERATE , __FUNCTION__ , __LINE__ , __FILE__ );
            }
        }
        catch (Exception $e)
        {
            $result = $this->internalException( $e , __FUNCTION__ );
        }
        finally
        {
            return $result;
        }
    }
    
    // REPORT EXCEL HANDLER
    // public function reportExcelHandler2()
    // {
    //     set_time_limit(REPORT_TIME_LIMIT);
    //     try
    //     {
    //         $reportFileName = Session::get('reportFileName');
    //         $reportData     = Session::get('reportData');
    //         $report         = $this->createReportExcel($reportData, $reportFileName);
    //         if($report['status'] == 'OK'){
    //             $file    = public_path() . '/files/'. $reportFileName;
    //             $desc    = TbReporte::select('descripcion')->where('id_reporte', '=', $reportData['reportId'])->first();
    //             $desc    = $desc->descripcion;
    //             $headers = array('Content-Type: application/vnd.ms-excel');
    //             return Response::download($file . '.xls', $desc . '-' . $reportData['fromDate'] . '-' . $reportData['toDate'] . '.xls', $headers);
    //         }else{
    //             return $report['message'];
    //         }
    //     }
    //     catch (Exception $e)
    //     {
    //         return $e;
    //     }
    // }

    // PROCESS REPORT VIEW
    public function reportViewProcess($configReport)
    {   
        $result = [ status => ok ];
        
        $dataReport = $this->getDataReport($configReport);

        if( ! isset( $dataReport[ description ] ) )
        {
            $dataReport  = DataGroup::arrayCastRecursive((array) $dataReport);
            // // idkc : Validacion de inputs
            $rules  = array(
                'reportName' => 'required|string',
                'formula'    => 'required',
                'dataset'    => 'required'
            );

            $validator = Validator::make($dataReport, $rules);

            if( $validator->fails() )
            {
                return $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );
            }
            else if( isset( $dataReport[ 'dataset' ] ) )
            {
                $data = $dataReport['dataset'];

                // idkc : Validacion de Formula
                $rules  = array(
                    'rows'   => 'required|array',
                    'values' => 'required|array'
                );
                $validator = Validator::make($dataReport['formula'], $rules);
                if ($validator->fails())
                {
                    $result = $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );
                }
                else
                {
                    $newData = array(
                        'head' => array()
                    );
                    $columns    = array();
                    if (count($dataReport['formula']['columns']) > 0) 
                    {    
                        $filter       = DataGroup::sortByFields($dataReport['formula']['columns']);
                        // dd(json_encode(array('data'=>$dataReport['dataset']['body'], 'filter'=>$filter)));
                        $dataReport['dataset']['body'] = DataGroup::array_orderby($dataReport['dataset']['body'], $filter);
                        $columns = array_fetch($dataReport['dataset']['body'], $dataReport['formula']['columns'][0]);
                        $columns = array_unique($columns);
                    }

                    // GENERATE List OF VALUES NAME
                    $values_list = array();
                    foreach ($dataReport['formula']['values'] as $key => $value) 
                    {
                        $values_array = explode(":",$value);
                        array_push($values_list, $values_array[1]);
                    }
                    // GENERATE HEADERS WITH ROWS AND COLUMNS
                    $newData['head'][0] = array_merge(array_merge($dataReport['formula']['rows'], $columns), $values_list);
                    sort($columns, SORT_NATURAL | SORT_FLAG_CASE);
                    $resultAddcolumns = $this->addColumns(array(
                        'data'    => $dataReport['dataset']['body'],
                        'columns' => $columns
                    ));
                    $dataReport['dataset']['body'] = $resultAddcolumns[ status ] == ok ? $resultAddcolumns['data'] : null;
                }
                $resultProcess = DataGroup::process(array(
                    'body'       => $dataReport['dataset']['body'], 
                    'rows'       => $dataReport['formula']['rows'], 
                    'columns' => $columns, 
                    'values'     => $dataReport['formula']['values'], 
                    'keyColumns'    => $dataReport['formula']['columns']
                ));
                $newData['body'] = $resultProcess['status'] == 'OK' ? $resultProcess['data'] : null;
                $total           =  $resultProcess['status'] == 'OK' ? $resultProcess['total'] : null;
                // unset($newData['body'][count($newData['body']) - 1]);
                $filter          = DataGroup::sortByFields($dataReport['formula']['rows']);
                $newData['body'] = DataGroup::array_orderby($newData['body'], $filter);
                // array_push($newData['body'], $total);
                $dataReport['analytics']['outputs'] = count($newData['body'])-1;
                // idkc : thead - generacion de cabecera de tabla
                $theadList   = $dataReport['formula']['rows'];
                count($values_list) > 1 ? $theadList[] = 'Valores' : null;
                $theadList   = array_merge($theadList, $columns);
                $theadList[] = 'Total';
                
                
                // idkc : tbody - generacion de datos de tabla
                $tbodyList = $this->convertObjectToArray($newData['body'], array(
                    'columns' => $columns,
                    'rows'    => $dataReport['formula']['rows'],
                    'valores' => $values_list
                ));
                // idkc : tfoot - generacion de pie de tabla
                $tfootList = $this->convertObjectToArray(array($total), array(
                    'columns' => $columns,
                    'rows'    => $dataReport['formula']['rows'],
                    'valores' => $values_list
                ));
                $columnDataTable = $this->convertColumnsToDataTable($theadList);
                $result = array(
                    'title'     => $this->generateTitleReport($dataReport['reportName'], $configReport['fromDate'], $configReport['toDate']),
                    'theadList' => $theadList,
                    'tbodyList' => $tbodyList,
                    'tfootList' => $tfootList,
                    'columns'   => $columnDataTable,
                    'analytics' => $dataReport['analytics'],
                    'valores'   => $values_list,
                    'rows'      => $dataReport['formula']['rows'],
                    status      => ok
                );
            }
        }
        else
        {
            $result = $dataReport;
        }
        return $result;
    }
    public function convertColumnsToDataTable($theadList){
        $result = array();
        foreach ($theadList as $key => $value) {
            $temp = array();
            $temp['title'] = $value;
            $result[] = (object) $temp;
        }
        return $result;
    }

    public function convertObjectToArray($objectList, $parameters){
        $result = null;
        foreach($objectList as $objectPosition => $object){
            foreach($parameters['valores'] as $valoresItem){
                
                if(!empty($object) > 0){
                    $objectArray = array();
                    foreach($parameters['rows'] as $rowsPosition){
                        $objectArray[] = isset($object[$rowsPosition]) ? $object[$rowsPosition] : null;
                    }

                    if(count($parameters['valores'])>1){
                        $objectArray[] = $valoresItem;
                    }
                    foreach($parameters['columns'] as $columnPosition => $columnName)
                    {
                        if(is_array($object[$columnName])){
                            
                            if(!is_array($object[$columnName][$valoresItem]))
                                $objectArray[] = $object[$columnName][$valoresItem];
                            else{
                                //
                            }
                        }
                        else{
                                $objectArray[] = $object[$columnName];
                        }
                    }
                    if(count($parameters['valores'])>=1){
                        $objectArray[] = $object[$valoresItem];
                    }
                    $result[] = $objectArray;
                }
            }
        }
        return $result;
    }

    public function addColumns($parameters){
        $result = [ status => ok ];
        
        $rules  = array(
            'data'    => 'required|array',
            'columns' => 'array'
        );
        $validator = Validator::make($parameters, $rules);
        if( $validator->fails() )
        {
            $result = $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );
        }
        else
        {
            foreach ($parameters['data'] as $key => $data_temp) 
            {
                for ($k = 0; $k < count($parameters['columns']) ; $k++) 
                {
                    $data_temp=array_add($data_temp, $parameters['columns'][$k], null);
                }
                $result['data'][] = $data_temp;
            }
        }
        return $result;
    }

    // CREATE REPORT EXCEL
    public function createReportExcel($data, $file)
    {
        set_time_limit(REPORT_TIME_LIMIT);
        $result = [ status => ok ];
        try 
        {
            $view     = View::make('Report.previewExport', $data)->render();
            $view     = array( 'view' => $view);
            $filename = $file;
            $title    = $data['title'];
            $report = Excel::create($filename, function($excel) use ($view, $filename, $title)
            {
                $excel->setTitle($title);
                $excel->setCreator('Laboratorios Bago | Peru')->setCompany('Laboratorios Bago | Peru');
                $excel->sheet($filename, function($sheet) use ($view)
                {
                    $sheet->setFreeze('B4');
                    $sheet->setAutoSize(true);
                    $sheet->loadView('Report.export', $view);
                });
            })->store('xls', public_path() . REPORT_EXPORT_DIRECTORY, true);
            $result['report'] = $report;
        }
        catch( Exception $e ) 
        {
            $result = $this->internalException( $e , __FUNCTION__ );
        }
        finally
        {
            return $result;
        }
    }

    private function processQuery( $dataArray )
    {
        $result         = [ status => ok ];
        // idkc         : PARAMETER FOR SQL QUERY
        $fromDate       = $dataArray['fromDate'];
        $toDate         = $dataArray['toDate'];
        $zona           = $dataArray['zona'];
        $frecuency      = $dataArray['frecuency'];
        
        if( is_null( $dataArray[ 'query' ] ) )
        {
            $result[ 'data' ] = [];
        }
        else
        {
            $evalQuery = $dataArray[ 'query' ];
            $evalText       = '$query = ' . $evalQuery . ';';
            eval( $evalText );
            $result['data'] = DB::select( $query );
        }
        return $result;
    }

    // GET DATA REPORT
    public function getDataReport( $configReport )
    {
        $result = array();
            
        $rules  = array(
            'reportId' => 'required',
            'fromDate' => 'date_format:Y/m/d',
            'toDate'   => 'date_format:Y/m/d'
        );

        $validator = Validator::make($configReport, $rules);
        
        if ($validator->fails())
        {
            return $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );
        }
        else
        {
            $reportElement = TbReporte::find($configReport['reportId']);
            
            $intervalo     = json_decode($reportElement->formula);
            $info = $this->processQuery( array(
                'query'     => $reportElement->tbQuery->query,
                'fromDate'  => $configReport['fromDate'],
                'toDate'    => $configReport['toDate'],
                'zona'      => $this->getZona(),
                'frecuency' => $intervalo->frecuency
            ));
            $info = $info['data'];

            unset($intervalo);
            if( count( $info ) > 0 ) 
            {
                $result['reportName'] = $reportElement->descripcion;
                $result['formula']    = DataGroup::arrayCastRecursive((array) json_decode($reportElement->formula));

                $result['dataset']    = 
                    array(
                        'head' => $this->setHead(head($info)),
                        'body' => $this->setBody($info)
                    );

                $result[ status ]    = ok;
                $result['analytics'] = array(
                        'inputs' => count($info)
                    );
            }
            else
            {
                $result['reportName']  = $reportElement->descripcion;
                $result['formula']     = DataGroup::arrayCastRecursive( ( array ) json_decode( $reportElement->formula ) );
                $result[ status ]      = ok;
                $result[ description ] = REPORT_DATA_NOT_FOUND;
                $result['analytics']   = array('inputs' => count($info));
            }
            unset($reportElement, $info);
        }
        unset($rules, $validator);    
        return $result;
    }
    
    public function downloadReportExcelHandler($userId, $reportId, $fromDate, $toDate)
    {
        set_time_limit(REPORT_TIME_LIMIT);
        try{
            $reportFileName = $userId . '-' . $reportId . '-' . $fromDate . '-' . $toDate;
            $file           = public_path().REPORT_EXPORT_DIRECTORY.$reportFileName;
            $desc           = TbReporte::select('descripcion')->where('id_reporte', '=', $reportId)->first();
            $desc           = $desc->descripcion;
            $headers        = array('Content-Type: application/vnd.ms-excel');
            return Response::download($file . '.xls', $desc . '-' . $fromDate . '-' . $toDate . '.xls', $headers);
        }catch(Exception $e){
            return $e;
        }
    }
    
    private function setHead($data)
    {
        set_time_limit(REPORT_TIME_LIMIT);
        $head = array();    
        foreach ($data as $key => $value) {
            $key = ucwords($key);
            array_push($head, $key);
        }
        return $head;
    }
    
    private function setBody($data)
    {
        set_time_limit(REPORT_TIME_LIMIT);
        $i    = 0;
        $body = array();
        foreach ($data as $row) {
            foreach ($row as $k => $v) {
                $k            = ucwords($k);
                $body[$i][$k] = $v;
            }
            $i++;
        }
        return $body;
    }

    // CONVERT MONTH FROM NUMBER TO STRING
    private function convertMonthNumberToString($numMonth)
    {
        $months = array(
            1 => "Enero",
            2 => "Febrero",
            3 => "Marzo",
            4 => "Abril",
            5 => "Mayo",
            6 => "Junio",
            7 => "Julio",
            8 => "Agosto",
            9 => "Setiembre",
            10 => "Octubre",
            11 => "Noviembre",
            12 => "Diciembre"
        );
        return $months[(int) $numMonth];
    }

    // GET COLUMN NAME TO SET FREEZE
    private function getColumnNameFreeze($numRows){
        $abc = array(
            0 => "A",
            1 => "B",
            2 => "C",
            3 => "D",
            4 => "E",
            5 => "F",
            6 => "G",
            7 => "H",
            8 => "I",
            9 => "J",
            10 => "K",
            11 => "L",
            12 => "M",
            13 => "N"
        );
        return $numRows <count($abc) ? $abc[$numRows] : "D";
    }

    // GENERATE TITLE OF PROJECT
    public function generateTitleReport($title, $fromDate, $toDate)
    {
        set_time_limit(REPORT_TIME_LIMIT);
        $resultDiff = DataGroup::cantidadMesesFechas($fromDate, $toDate);
        $dateFrom   = new DateTime(DataGroup::replaceSlaceDate($fromDate));
        $dateTo     = new DateTime(DataGroup::replaceSlaceDate($toDate));
        if ($resultDiff < 1) {
            $title .= " - Del " . $dateFrom->format("d") . " al " . $dateTo->format("d") . " de " . $this->convertMonthNumberToString($dateTo->format("m")) . " del " . $dateTo->format("Y");
        }
        if ($resultDiff == 1) {
            $title .= " - " . $this->convertMonthNumberToString($dateTo->format("m")) . " del " . $dateTo->format("Y");
        }
        if ($resultDiff > 1) {
            $title .= " - Del " . $dateFrom->format("d") . " de " . $this->convertMonthNumberToString($dateFrom->format("m")) . " del " . $dateTo->format("Y") . " al " . $dateTo->format("d") . " de " . $this->convertMonthNumberToString($dateTo->format("m")) . " del " . $dateTo->format("Y");
        }
        return $title;
    }

    protected function listDatasetHandler()
    {
        set_time_limit(REPORT_TIME_LIMIT);
        $result = [ status => ok ];
        try 
        {
            $resultTbQuery = TbQuery::select('id', 'name')->get();
            if($resultTbQuery==NULL)
            {
                $result = $this->warningException( REPORT_MESSAGE_DATASET_NOT_FOUND , __FUNCTION__ , __LINE__ , __FILE__ );
            }
            else
            {
                $resultTbQuery->toJson();
                $result[ data ] = json_decode($resultTbQuery->toJson());
            }
        }
        catch( Exception $e )
        {
            $result = $this->internalException( $e , __FUNCTION__ );
        }
        finally
        {
            return $result;
        }
    }

    // LIST COLUMNS DATASET HANDLER
    public function listColumnsDatasetHandler( $queryId )
    {
        set_time_limit(REPORT_TIME_LIMIT);
        $result = [ status => ok ];
        try 
        {
            $data = array('id' => $queryId);
            
            $rules  = array(
                'id' => 'required|integer',
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails() )
            {
                return $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );
            }
            else
            {
                $resultQuery     = TbQuery::find($queryId);
                $nowDate         = Carbon::now(); 
                
                $toDate          = $nowDate->format('Y/m/d');
                $fromDate        = $nowDate->subMonth()->format('Y/m/d');

                $resultQueryExec = $this->processQuery(array(
                    'query'     => $resultQuery->query,
                    'fromDate'  => $fromDate,
                    'toDate'    => $toDate,
                    'zona'      => null,
                    'frecuency' => 'N'
                ));
                
                $resultQueryExec = $resultQueryExec[ 'data' ];
                    // dd($resultQueryExec);
                if($resultQueryExec == NULL)
                {
                    $result = $this->warningException( REPORT_MESSAGE_DATASET_NOT_FOUND_DATA , __FUNCTION__ , __LINE__ , __FILE__ );
                }
                else
                {
                    $result[ data ] = $this->setHead(head($resultQueryExec));
                }
            }
        }
        catch (Exception $e)
        {
            $result = $this->internalException( $e , __FUNCTION__ );
        }
        finally
        {
            return $result;
        }
    }

    // CREATE NEW REPORT HANDLER
    protected function saveReportHandler()
    {
        set_time_limit(REPORT_TIME_LIMIT);
        $result = array( status => ok );
        try 
        {
            // $dataInput    = Input::instance()->getContent();
            // $dataInput = DataGroup::arrayCastRecursive((array) json_decode($dataInput));
            $inputAll = Input::all();
            $dataInput = $inputAll['data'];

            if( count( $dataInput ) > 0 )
            {
                DB::beginTransaction();
                foreach ($dataInput as $key => $value) {
                    // dd($value);
                    $rules  = array(
                        'formula'     => 'required',
                        'descripcion' => 'required|string',
                        'queryId'     => 'required|integer'
                    );

                    $validator = Validator::make($value, $rules);
                    if ($validator->fails())
                    {
                        return $this->warningException( $this->msgValidator( $validator ) , __FUNCTION__ , __LINE__ , __FILE__ );
                    }
                    else
                    {
                        $reporte              = new TbReporte;
                        $reporte->id_reporte  = $reporte->nextId();
                        $reporte->descripcion = $value['descripcion'];
                        $reporte->formula     = $value['formula'];
                        $reporte->query_id    = $value['queryId'];
                        $reporte->save();            
                        $usuarioReporte             = new UserReport;
                        $usuarioReporte->id         = $usuarioReporte->nextId();
                        $usuarioReporte->id_reporte = $reporte->id_reporte;
                        $usuarioReporte->id_usuario = Auth::user()->id;
                        $usuarioReporte->save();
                    }
                }
                DB::commit();
            }
            else
            {
                $reuslt = $this->warningException( 'No se encontro registro para el reporte' , __FUNCTION__ , __LINE__ , __FILE__ );
            }
        }
        catch (Exception $e)
        {
            DB::rollback();
            $result = $this->internalException( $e , __FUNCTION__ );
        }
        finally
        {
            return $result;
        }
        
    }
    function getRunTime()
    {
        list($useg, $seg) = explode(" ", microtime());
        return ((float)$useg + (float)$seg);
    }

    public function getZona(){
        $zona = null;
        $apps =Auth::user()->apps;
        foreach ($apps as $key => $value) {
            if($value->idapp == 3)
                $zona = $value->zona;
        }
        return $zona;
    }
}