<tr type="{{ $type }}">
    @foreach( $records as $record )
        @if( $record->cell_type == 0 )
            <td class="text-center" disabled></td>
        @elseif( $record->cell_type == 1 )
            <td class="text-center" column="{{ $record->key }}">
                <select class="form-control input-sm" style="width:100%">
                    @foreach( $record->data as $row )
                        <option value="{{ $row->id }}">{{ $row->descripcion }}</option>
                    @endforeach
                </select>
            </td>
        @elseif( $record->cell_type == 2 )
            <td class="text-center" column="{{ $record->key }}">
                <input type="text" class="form-control input-sm {{{ $record->class or '' }}}" {{{ $record->max_length or '' }}} style="width:100%">
            </td>
        @endif
    @endforeach
    <td style="text-align:center">
        <button type="button" class="btn btn-success btn-xs maintenance-save">
            <span class="glyphicon glyphicon-floppy-disk"></span>
        </button>
        <button type="button" class="btn btn-warning btn-xs maintenance-remove">
            <span class="glyphicon glyphicon-remove"></span>
        </button>
    </td>
</tr>