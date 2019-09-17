@extends("../layouts.plantilla")
@section("head")
@php
    {{
        $valores=$paginacion->items();
    }}
@endphp
@endsection

@section("body")
@section("ol_breadcrumb")
<a href="#">
    Evaluacion \ Parcial I
</a>
@endsection
@section("main")
<!--Card que contiene a cada pregunta-->
<!--Se agrego la etiqueta form para persistencia-->
<form id="quiz_form">
    <div class="card">
    <div class="card-body">
        <!--Aqui iran las preguntas-->
        @for($i=0;$i<count($valores);$i++)
        @if($valores[$i]['tipo_item']!=3)
        <div class="card bg-light mt-2 mb-2">
            <div class="card-header" name='{{ $valores[$i]['pregunta']->id }}'>
                <strong><h4>{{ $i+1}}.&nbsp&nbsp{{ $valores[$i]['pregunta']->pregunta }}</h4></strong> 
            </div>
        <!--Respuesta Corta-->
        @if($valores[$i]['tipo_item']==4)
            <input aria-describedby="emailHelp" class="form-control" id="exampleInputEmail1" placeholder="Ingrese respuesta" type="text">
                </input>
            </div>
        @else
        <div class="card-body">
            <!--Preguntas de opcion multiple-->
            @for($j=0;$j<count($valores[$i]['opciones']);$j++)
                <div class="custom-control custom-radio">
                    <input class="custom-control-input" id="{{ $valores[$i]['opciones'][$j]->id}}" name="pregunta_{{ $valores[$i]['pregunta']->id }}" type="radio" value="opcion_{{ $valores[$i]['opciones'][$j]->id }}" @if($valores[$i]['opciones'][$j]->seleccionada) checked @endif>
                        <label class="custom-control-label" for="{{ $valores[$i]['opciones'][$j]->id}}">
                             <h5>{{ $valores[$i]['opciones'][$j]->opcion }}</h5>
                        </label>
                    </input>
                </div>
            @endfor
        </div>
        @endif
        </div>
        @endif

        <!--Preguntas de Emparejamiento-->
        @if($valores[$i]['tipo_item']==3)
        <div class="card bg-light mt-2">
            <div class="card-header">
                {{ $valores[$i]['descripcion_gpo'] }}
            </div>
            <div class="card-body">
                <table class="table">
                    <tbody>
                        @for($r=0;$r<count($valores[$i]['preguntas']);$r++)
                        <tr>
                        <td> 
                            {{ $valores[$i]['preguntas'][$r]->pregunta }}
                        </td>
                        <td>
                            <select class="custom-select col-12" id="{{ $valores[$i]['preguntas'][$r]->id }}">
                                <option selected="" value="-1">
                                    Seleccione
                                </option>
                                @php
                                    {{
                                        /*Funcionalidad que mezcla las opciones de un select,
                                        para que no se muestren de manera ordenada segun respuesta
                                        de cada pregunta

                                        FUNCIONALIDAD CON DUDA
                                        
                                        */
                                        $nums=range(0,count($valores[$i]['preguntas'])-1);
                                        shuffle($nums);
                                    }}
                                @endphp
                                @for($m=0;$m<count($valores[$i]['preguntas']);$m++)
                                <option value="{{ $valores[$i]['preguntas'][$nums[$m]]->opciones[0]->id }}">
                                    {{ $valores[$i]['preguntas'][$nums[$m]]->opciones[0]->opcion }}
                                </option>
                                @endfor
                            </select>
                        </tr>
                        @endfor
                        <tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        @endfor

    </div>
    <div class="card-footer">
        <!--Botones de control para paginacion-->
        <div class="offset-10">{{ $paginacion->links('vendor.pagination.simple-bootstrap-4') }}</div>
    </div>
</div>
</form>
@endsection
@endsection

@section("js")
<script type="text/javascript">
    
    $("#previous_btn").click(function(){
        var data = $("#quiz_form").serialize();
        respuestas_val = data.replace(/&/g,"-");
        $.get('http://localhost:8000/persistencia', {respuestas:respuestas_val}, function(response){
            console.log("success");
        });
    });
    
    $("#next_btn").click(function(){
        var data = $("#quiz_form").serialize();
        respuestas_val = data.replace(/&/g,"-");
        $.get('http://localhost:8000/persistencia', {respuestas:respuestas_val}, function(response){
            console.log("success");
        });
    });
    
</script>
@endsection
