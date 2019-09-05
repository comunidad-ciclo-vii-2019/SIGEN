<!-- Modal para la visualización de las Áreas predefinidas en la Materia-->

	<div class="modal" id="areasModal">
		<div class="modal-dialog" role="document" style="max-width: 60% !important;">
			
			<div class="modal-content">				
				
				<div class="modal-header border-bottom-0">

					<h5 class="modal-title">Asignar Áreas a Turno</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>

				</div>

				<div class="modal-body p-0">

					<div class="form-group" style="display:none;">
						<label class="col-form-label" for="turno_id">Turno ID:</label>
						<input type="text" class="form-control" name="turno_id" placeholder="ID de Pregunta" id="turno_id">
						<label class="col-form-label" for="clave_id">Clave ID:</label>
						<input type="text" class="form-control" name="clave_id" placeholder="ID de Pregunta" id="clave_id">
					</div>

					<table class="table table-hover border-top-0">

						<thead class="text-left ">
							<tr class="d-flex border-top-0">
								<th class="col-1 border-bottom-0">N°</th>
								<th class="col-5 border-bottom-0">Área</th>
								<th class="col-3 border-bottom-0">Tipo</th>
								<th class="col-3 border-bottom-0">Acción</th>
							</tr>
						</thead>

						<tbody>

							@forelse ($areas as $area)
								<tr class="d-flex">
									<th class="col-sm-1 text-center">
										{{ $loop->iteration }}
									</th>

									<td class="col-sm-5">
										{{ $area->titulo }}
									</td>
									
									@switch( $area->tipo_item_id )
										@case(1)
											<td class="col-sm-3">
												Opción Múltiple
											</td>
										@break
										@case(2)
											<td class="col-sm-3">
												Verdadero/Falso
											</td>
										@break
										@case(3)
											<td class="col-sm-3">
												Emparejamiento
											</td>
										@break
										@case(4)
											<td class="col-sm-3">
												Respuesta Corta
											</td>
										@break
									@endswitch
									@if(in_array($area->id, $id_areas))
										<td class="col-sm-3 text-center">
											<button type="button" class="btn btn-secondary" disabled="" >Asignado</button>
										</td>
									@else
										<td class="col-sm-3 text-center">
											<button type="button" class="btn btn-info" data-id-turno="{{$turno->id}}" data-id-clave="{{$claves[0]->id}}" data-id-area="{{$area->id}}" data-toggle="modal" data-target="#asignarModal" data-dismiss="modal" onclick="$('#asignarModal').modal();">&nbsp;Asignar&nbsp;</button>
										</td>
									@endif
								</tr>
							@empty
								<tr><td><h3>No se han definido áreas para la materia.</h3></td></tr>
							@endforelse

							<tr class="d-flex" id="trBtn">
								<td class="col-sm-1"></td>
								<td class="col-sm-5"></td>
								<td class="col-sm-3"></td>
								<td class="col-sm-3 text-center">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">&nbsp;&nbsp;Cerrar&nbsp;&nbsp;</button>
								</td>
							</tr>

						</tbody>

					</table>

				</div>

			</div>

		</div>
	</div>

<!-- Fin de Modal para Visualización de Áreas -->

<!-- Modal para la asignación de Áreas predefinidas al Turno-->

	<div class="modal" id="asignarModal">
		<div class="modal-dialog" role="document" style="max-width: 50% !important;">
			
			<div class="modal-content">
				
				<div class="modal-header">

					<h5 class="modal-title">Configuración de Área</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>

				</div>

				<div class="modal-body">

					<div class="form-group">

						<form action="{{ route('asignar-area-clave',$turno->id)}}" method="POST">

							<div class="form-group" style="display: none;">
								<label class="col-form-label" for="turno_id">Turno ID:</label>
								<input type="text" class="form-control" name="turno_id" placeholder="ID de Pregunta" id="turno_id">
								<label class="col-form-label" for="clave_id">Clave ID:</label>
								<input type="text" class="form-control" name="clave_id" placeholder="ID de Pregunta" id="clave_id">
								<label class="col-form-label" for="area_id">Area ID:</label>
								<input type="text" class="form-control" name="area_id" placeholder="ID de Pregunta" id="area_id">
							</div>

							<div class="form-group">
								<label class="col-form-label" for="peso">Peso de cada Pregunta del Área:</label>
								<input type="text" class="form-control" name="peso" placeholder="Inserte Peso" id="peso">
							</div>

							<fieldset class="form-group">
								<p>Modo de asignación de Preguntas al Área:</p>
								<div class="form-check">
									<label class="form-check-label">
										<input type="radio" class="form-check-input" name="aleatorio" id="aleatorio-no" value="0" checked="" onchange="aleatorioNo()">
										Manual
									</label>
								</div>
								<div class="form-check">
									<label class="form-check-label">
										<input type="radio" class="form-check-input" name="aleatorio" id="aleatorio-si" value="1" onchange="aleatorioSi()">
										Aleatorio
									</label>
								</div>
							</fieldset>

							<div class="form-group" id="divAleatorias">
								<label class="col-form-label" for="cantidad">Cantidad de Preguntas a escoger aleatoriamente:</label>
								<input type="text" class="form-control" name="cantidad" placeholder="Inserte cantidad de Preguntas" id="cantidad">
							</div>

							{{ csrf_field() }}

							<button type="submit" class="btn btn-primary">Asignar Área</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

						</form>

					</div>

				</div>

			</div>
		</div>
	</div>

<!-- Fin de Modal para la asignación de Áreas predefinidas al Turno-->