<!-- /#wrapper -->
<div id="wrapper">
  <div id="content-wrapper">
    <div class="container-fluid">
      <!-- DataTables Example -->
      <div class="card mb-3">
        <div class="card-header">
          <div class="row">
            <div class="col-8">
              <i class="fas fa-table"></i>
              Listado de Docentes | Materia
            </div>
            <div class="col-4" style="text-align: right;">
              <strong class="mb-3">Asignar Área</strong>
              <button class="btn" data-id-turno="{{$turno->id}}" data-id-clave="{{$claves[0]->id}}" data-peso-turno="{{$peso_turno}}" data-toggle="modal" data-target="#areasModal" onclick="$('#areasModal').modal();" title="Asignar Área a Turno">
                <span class="icon-add text-primary">
                </span>
              </button>
            </div>
          </div>
        </div>
        
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Modalidad</th>
                  <th>Cantidad de preguntas</th>
                  <th>Peso</th>
                  <th>Opciones</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>#</th>
                  <th>Modalidad</th>
                  <th>Cantidad de preguntas</th>
                  <th>Peso</th>
                  <th>Opciones</th>
                </tr>
              </tfoot>
              <tbody>
                @if(count($claves[0]->clave_areas) > 0 )
                @foreach($claves[0]->clave_areas as $clave_area)
                <tr>
                  <input type="hidden" value="{{ $clave_area->id}}" id="id_clave_area_edit">
                  <td>{{ $loop->iteration }}</td>
                  <td>
                    @if($clave_area->aleatorio)
                      <i class="icon-dice" title="Aleatorio">&nbsp;&nbsp;</i> 
                    @else
                      <i class="icon-hand-paper-o" title="Manual">&nbsp;&nbsp;</i> 
                    @endif
                    {{ $clave_area->area->tipo_item->nombre_tipo_item }}
                  </td>
                  <!--El atributo cantidad_preguntas es un campo calculado en el modelo Clave_Area apartado de accessors-->
                  @if($clave_area->cantidad_preguntas!=0)
                    <td id="id_cantidad" class="text-center">{{ $clave_area->cantidad_preguntas }}</td>
                  @else
                    <td id="id_cantidad" class="text-center">-</td>
                  @endif
                  <td id="id_peso">{{ $clave_area->peso }}</td>
                  <td>
                    <button class="icon-delete btn btn-sm btn-danger" href="#" title="Eliminar Área" data-eliminar-ca="{{ $clave_area->id }}"></button>
                    <button class="icon-edit btn btn-sm btn-primary" href="#" title="Editar Área" data-editar-ca="{{ $clave_area->id }}" data-aleatorio="{{ $clave_area->aleatorio }}"></button>
                    @if($clave_area->aleatorio)
                      <a href="{{ URL::signedRoute('preguntas_por_area', ['id' => $clave_area->id]) }}" class="icon-list btn btn-sm btn-success" title="Ver preguntas de esta área"></a>
                    @else
                      @if($clave_area->area->tipo_item_id==3)
                        <button class="icon-information-solid btn btn-sm btn-secondary" href="#" title="Ver preguntas agregadas" data-preguntas-emp="{{ $clave_area->id }}"></button>
                        <button class="icon-add-solid btn btn-sm btn-info" title="Agregar preguntas" data-id-clave-area-emp="{{ $clave_area->id }}"></button>
                      @else
                        <button class="icon-information-solid btn btn-sm btn-secondary" href="#" title="Ver preguntas agregadas" data-preguntas="{{ $clave_area->id }}"></button>
                        <button class="icon-add-solid btn btn-sm btn-info" title="Agregar preguntas" data-id-clave-area="{{ $clave_area->id }}"></button>
                      @endif
                    @endif
                  </td>
                </tr>
                 @endforeach
                 @else
                  <tr>
                    <td colspan="5">No se encuentran resultados disponibles</td>
                </tr>
                 @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- /.container-fluid -->
  </div>
  <!-- /.content-wrapper -->
</div>

@include('turno.areasclave')

<!-- Modal agregar preguntas-->
<div class="modal fade" id="asignarPreguntasClaveArea" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Asiganar preguntas a la clave</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('agregar_clave_area') }}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="clave_area" value="" id="id_clave_area_add">
        <input type="hidden" name="modalidad" value="" id="id_clave_area_add_emp">
        <div class="modal-body" id="asignar-preguntas">
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

  <!-- Modal listar preguntas-->
<div class="modal fade" id="listarPreguntasClaveArea" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="listarModalCenterTitle">Preguntas asignadas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body" id="listar-preguntas">
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-info" data-dismiss="modal">Aceptar</button>
        </div>
    </div>
  </div>
</div>

<!-- Modal editar Asignación de área a clave-->
<div class="modal fade" id="editarClaveArea" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editarModalCenterTitle">Editar asignación de área a clave</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('editar_clave_area')}}" method="POST">
        {{ csrf_field() }}
        <div class="modal-body" id="editar-preguntas">
          <input type="hidden" value="" id="id_ca" name="id_clave_area">
          <div class="form-group">
            <label for="cantidad_preguntas_id" id="msj_cant_preg">Cantidad de preguntas</label>
            <input type="number"  min="1" class="form-control" id="cantidad_preguntas_id" name="numero_preguntas">
          </div>
          <div class="form-group">
            <label for="peso_ca_id">Peso del área</label>
            <input type="number" min="0" max="100" class="form-control" id="peso_ca_id" name="peso">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal elimanr Asignación de área a clave-->
<div class="modal fade" id="eliminarClaveArea" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eliminarModalCenterTitle">Eliminar área</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body" id="elimanr-preguntas">
          <h3><strong>¿Desea eliminar esta área de la clave?</strong></h3>
        </div>
        <div class="modal-footer">
          <form action="{{ route('eliminar_clave_area')}}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" value="" id="id_ca_eliminar" name="id_clave_area">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger">Eliminar</button>
          </form>
        </div>
    </div>
  </div>
</div>