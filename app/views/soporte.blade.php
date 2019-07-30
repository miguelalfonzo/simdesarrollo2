<html>
	@if( isset( $exception ) )
		<ol>
			<li>User: {{Auth::user()->id.' '.Auth::user()->username}}</li>
			<li>Error: {{$exception->getMessage()}}</li>
			<li>Code: {{$exception->getCode()}}
			<li>File: {{$exception->getFile()}}</li>
			<li>Line: {{$exception->getLine()}}</li>
			<li>Description: {{$exception->getTraceAsString()}}</li>
		</ol>
	@elseif( isset( $description) )
		<ol>
			<li>User: {{Auth::user()->id.' '.Auth::user()->username}}</li>
			<li>Function: {{$function}}</li>
			<li>Line: {{$line}}</li>
			<li>File: {{$file}}</li>
			<li>Description: {{$description}}</li>
		</ol>
	@elseif( isset( $subject ) )
		<h1>
			{{$subject}}
		</h1>
	@endif
</html>