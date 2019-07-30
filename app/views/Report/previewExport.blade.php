<html>{{''; function bigintval($value) {
	  $value = trim($value);
	  if (ctype_digit($value)) {
	    return $value;
	  }
	  $value = preg_replace("/[^0-9](.*)$/", '', $value);
	  if (ctype_digit($value)) {
	    return $value;
	  }
	  return sqrt(-1);
	}

	$rowNum = count($rows) }}

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
.table-striped > tbody > tr:nth-of-type(odd) {
  background-color: #f9f9f9;
}
.table > thead > tr > th {
  vertical-align: bottom;
  border-bottom: 2px solid #ddd;
}
</style>
<table>
	<tbody>
		<tr>
			<td colspan="2">
			{{ $title }}
			</td>
		</tr>
	</tbody>
</table>
<table id="dt_report" class="table table-striped" cellspacing="0">
	<thead>
		@if(count($theadList) > 0)
			<tr>
				@foreach($theadList as $thead)
					<th>{{$thead}}</th>
				@endforeach
			</tr>
		@endif

	</thead>
	<tbody>
		{{ ''; $totalArray = array() }}
			@foreach($valores as $valoresKey => $valoresValue)
				{{ ''; $totalArray[$valoresKey] = array() }}
			@endforeach
		@if(count($filter) > 0)
			@foreach($filter as $filtro)
				<tr>
					@foreach($tbodyList[$filtro] as $key => $element)				
							{{ ''; $posTotalArray = array_search($tbodyList[$filtro][$rowNum], $valores) }}
						
							{{''; $posTotalArray = !is_null($posTotalArray) ? $posTotalArray : 0 }}

							@if(!isset($totalArray[$posTotalArray][$key]))
								{{ ''; $totalArray[$posTotalArray][$key] = 0 }}
							@endif

							{{ ''; $number = bigintval($element) }}

							@if($rowNum <= $key)
								@if(!is_nan($number))
										@if(count($valores) >= 2)
											@if(is_null($totalArray[$posTotalArray][$key-1]))
												{{ ''; $totalArray[$posTotalArray][$key-2] = 'Total' }}
												{{ ''; $totalArray[$posTotalArray][$key-1] = $tbodyList[$filtro][$key-1] }}
											@endif
										@else
											@if(is_null($totalArray[$posTotalArray][$key-1]))
												{{ ''; $totalArray[$posTotalArray][$key-1] = 'Total' }}
											@endif
										@endif
									{{ ''; $totalArray[$posTotalArray][$key] = $totalArray[$posTotalArray][$key] + $number }}
								@else
									{{ ''; $totalArray[$posTotalArray][$key] = null }}
								@endif
							@else
								{{ ''; $totalArray[$posTotalArray][$key] = null }}
							@endif
						<td>{{$element}}</td>
					@endforeach
				</tr>
			@endforeach
		@else
			@foreach($tbodyList[0] as $i => $headColumn)
				{{ ''; $number = bigintval($headColumn) }}
				@if(!is_nan($number))
					@if($rowNum <= $key)
					@foreach($valores as $v => $data)
						@if(count($valores) >= 2)
							@if(is_null($totalArray[$v][$i-1]))
								{{ ''; $totalArray[$v][$i-2] = 'Total' }}
								{{ ''; $totalArray[$v][$i-1] = $tbodyList[0][$i-1] }}
							@endif
						@else
							@if(is_null($totalArray[$v][$i-1]))
								{{ ''; $totalArray[$v][$i-1] = 'Total' }}
							@endif
						@endif
						{{ ''; $totalArray[$v][$i] = 0 }}
					@endforeach
					@endif
				@else
					@foreach($valores as $v => $data)
						{{ ''; $totalArray[$v][$i] = null }}
					@endforeach
				@endif
			@endforeach
		@endif	
	</tbody>
	<tfoot>
		@if(count($totalArray) > 0)
			@foreach($totalArray as $tfoot)
				@if(count($tfoot) > 0)
				<tr>
					@foreach($tfoot as $element)
						<td>{{$element}}</td>
					@endforeach
				</tr>
				@endif
			@endforeach
		@endif	
	</tfoot>
</table>
</html>