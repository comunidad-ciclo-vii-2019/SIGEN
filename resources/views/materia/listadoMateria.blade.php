@extends("../layouts.plantilla")
@section("head")
@endsection

@section("body")
@section("ol_breadcrumb")
    <li class="breadcrumb-item">Materia</li>
@endsection
@section("main")
@if(count($materias)>0)
<table class="table table-striped">
  <thead>
    <tr>	
      <th scope="col">#</th>
      <th scope="col">Codigo Materia</th>
      <th scope="col">Nombre Materia</th>
      <th scope="col">Electiva</th>
      @if(auth()->user()->is_teacher)
      <th scope="col">Acciones</th>
      @endif
    </tr>
  </thead>
  <tbody>

  	@foreach($materias as $materia)
    <tr>
      <th scope="row">{{ $loop->iteration }}</th>
      <td>{{ $materia->codigo_mat }}</td>
      <td>{{ $materia->nombre_mar }}</td>
      @if($materia->es_electiva==0)
      <td>NO</td>
      @else
      <td>SI</td>
      @endif
      @if(auth()->user()->is_teacher)
      <td><a href="{{ route('listado_estudiante',$materia->id_mat_ci) }}"> Listado Alumnos  </a></td>
      @endif
    </tr>
    @endforeach
    
  </tbody>
</table>
@else
<h2 class="h1">No hay datos.</h2>
@endif
@endsection
@endsection


@section("footer")
@endsection