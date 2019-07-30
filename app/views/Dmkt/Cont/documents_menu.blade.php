@extends('template.main')
@section('solicitude')
<div class="page-header">
  <h3>Busqueda de Documentos Registrados</h3>
</div>
<div class="panel-body" id="documents">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="form-group col-xs-8 col-sm-4 col-md-2 col-lg-2">
            <div>
                <select id="idProof" class="form-control">
                    @foreach( $proofTypes as $proofType )
                        <option value="{{$proofType['ID']}}">{{$proofType['DESCRIPCION']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        
        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <div>
                <input id="doc-search-key" class="form-control" type="text" style="background-color:#FFF" placeholder="Serie, Número, RUC o Razón Social">
            </div>
        </div>

        <div class="form-group col-xs-4 col-sm-2 col-md-1 col-lg-1">
            <div>
                <a id="search-documents" class="btn btn-sm btn-primary ladda-button" data-style="zoom-in">
                    <i class="glyphicon glyphicon-search"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@include('template.Modals.documents')
@stop