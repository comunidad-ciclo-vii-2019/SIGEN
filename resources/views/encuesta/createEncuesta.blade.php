@extends("../layouts.plantilla")
@section("css")

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" />

  <style media="screen">
    #preview{
      width: 420px;
      margin: 0 auto;
    }

    img{
      max-width: 250px;
      height: auto;
    }
  </style>

@endsection


@section("body")

@section("ol_breadcrumb")
    <li class="breadcrumb-item"><a href="{{ URL::signedRoute('listado_encuesta') }}">Encuesta</a></li>
    <li class="breadcrumb-item">Nueva Encuesta</li>
@endsection
@section("main")
  <div class="row">
  <div class="col-md-2"></div>

  <div class="col-md-8">
  <div class="card">
  <div class="card-header bg-default">Nueva Encuesta</div>
  <div class="card-body">


    @if (session('notification'))
      <div class="alert alert-success">
        <ul>
          <h4 class="text-center">{{session('notification')}}</h4>
        </ul>
      </div>
    @endif
    
    @if (count($errors) > 0)
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{$error}}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if (session('danger'))
      <div class="alert alert-danger">
        <ul>
          <li>
            {{session('danger')}}
          </li>
        
        </ul>
      </div>
    @endif
    
    
      <form action="" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
          <div class="form-group">
             <label for="title"><b title="Campo Obligatorio">*</b>&nbsp;Título</label>
             <input type="text" name="title" class="form-control" value="{{ old('title') }}">
          </div>
          <div class="form-group">
             <label for="description"><b title="Campo Obligatorio">*</b>&nbsp;Descripción</label>
             <textarea name="description" class="form-control">{{ old('description') }}</textarea>
          </div>
          
            <div class="row">

              <div class="col-md-6">
                <div class="form-group">
                  <label for="exampleInputEmail1"><b title="Campo Obligatorio">*</b>&nbsp;Fecha y Hora de inicio:</label>
                    <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                        <input id="datetimepicker1input" type="text" name="fecha_inicio" class="form-control datetimepicker-input" data-target="#datetimepicker1" placeholder="dd/mm/yyyy hh:mm A" value="{{ old('fecha_inicio') }}"/>
                        <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="exampleInputEmail1"><b title="Campo Obligatorio">*</b>&nbsp;Fecha y Hora de fin:</label>
                  <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                        <input id="datetimepicker2input" type="text" name="fecha_final" class="form-control datetimepicker-input col-md-12" data-target="#datetimepicker2" placeholder="dd/mm/yyyy hh:mm A" value="{{ old('fecha_final') }}"/>
                        <div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
              </div>
              
              
            </div>
            <!--
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for=""></label>
                  <div class="form-check">
                    <input type="checkbox" name="visible" class="form-check-input" >
                    <label class="form-check-label" for="exampleCheck1">Visible</label>
                    <small class="form-text text-muted text-justify">La encuesta será visible para los usuarios. No se podrá acceder mientras no se encuentre en periodo de disponibilidad</small>
                  </div>
                </div>
              </div>
            </div>
          -->
          <div class="form-group">
             <input name="img_encuesta" type="file" accept="image/*" id="img_encuesta" onchange="validarFile(this);">
          </div>
         

          <div class="form-group">
             <button class="btn btn-primary mb-3">Crear</button>
             <a href="{{ URL::signedRoute('listado_encuesta')}}"
              class="btn btn-secondary mb-3"> Cancelar</a>
          </div>

           <hr>
          <div id="preview"></div>
         
      </form>
  </div>
</div>
</div>
</div>


  
@endsection
@endsection

@section("js")
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/js/tempusdominus-bootstrap-4.min.js"></script>  
    <script type="text/javascript" src="{{ asset('js/encuesta/fecha.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/encuesta/image.js') }}"></script>
@endsection



