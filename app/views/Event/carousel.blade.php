<div id="album" class="carousel slide" data-ride="carousel" data-interval="5000">
    <!-- Indicators -->
    <ol class="carousel-indicators">
  	@foreach($photos as $key => $photo)
	    @if($key == 0)
	       <li data-target="#album" data-slide-to="{{$key}}" class="active"></li>
	    @else
	       <li data-target="#album" data-slide-to="{{$key}}"></li>
	    @endif
    @endforeach
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox">
  	@foreach($photos as $key => $photo)
	    @if($key == 0)
	       <div class="item active">
	    @else
	       <div class="item">
	    @endif
			<img class="img-responsive" style="width:100%;" src="{{asset($photo->directory.$photo->id.'.'.$photo->extension)}}">
			<div class="carousel-caption">
				{{$photo->event->name}} - {{$photo->event->description}}
			</div>
    	</div>
    @endforeach
    </div>
    <!-- Controls -->
    <a class="left carousel-control" href="#album" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#album" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>