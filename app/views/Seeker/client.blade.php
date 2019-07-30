<li class="list-group-item">
	<div class="row" style="margin:0">
		<b>{{ $label }}</b>
	    <span class="pull-right">
		    <span class="badge" style="background: #3476AF;">{{ $type }}</span>
		  	<button type="button" class="btn btn-default btn-xs btn-delete-client">
		    	<i class="far fa-trash-alt"></i>
		  	</button>
		</span>
	</div>
	<input type="hidden" name="clientes[]" value="{{ $value }}" >
	<input type="hidden" name="tipos_cliente[]" value="{{ $id_tipo_cliente }}">
</li>

