<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Evaluacion;
use App\CicloMateria;
use App\CargaAcademica;
use App\Turno;
use Carbon\Carbon;

class EvaluacionController extends Controller
{
    //muestra el detalle de la evaluacion

    public function show($id){
    	$evaluacion = Evaluacion::findOrFail($id);
    	return view('evaluacion.detalleEvaluacion')->with(compact('evaluacion'));

    }

    //crear Evaluacion
    public function getCreate($id){
    	return view('evaluacion.createEvaluacion')->with(compact('id'));

    }

    public function postCreate($id,Request $request){
        //dd($request->all());
        $rules =[
            
            'title' => ['required', 'string','min:5','max:191'],
            'description' => ['required'],
            'duration' => ['required'],
            'intentos' => ['required'],
            'paginacion' => ['required'],
        ];
        /* Mensaje de Reglas de Validación */
        $messages = [
            
            'title.required' => 'Debe de ingresar un título para la evaluación',
            'title.min' => 'El título debe contener como mínimo 5 caracteres',
            'description.required' => 'Debe de ingresar una descripción para la evaluacion',
            'duration.required' => 'Debe de indicar la duración del examen',
            'intentos.required' => 'Debe de indicar el numero de intentos de la evaluacion',
            'paginacion.required' => 'Debe de indicar la paginación de la evaluación',

        ];
        
        $this->validate($request,$rules,$messages);
        $evaluacion = new Evaluacion();
        $evaluacion->nombre_evaluacion= $request->input('title');
        $evaluacion->id_carga=$id;
        $evaluacion->duracion=$request->input('duration');
        $evaluacion->intentos=$request->input('intentos');
        $evaluacion->descripcion_evaluacion=$request->input('description');
        $evaluacion->preguntas_a_mostrar=$request->input('paginacion');
        $evaluacion->revision=0;

        if(isset($request->all()['revision']))
            $evaluacion->revision = 1;
        $evaluacion->save();
        //return back()->with('notification','Se registró exitosamente');
        return redirect()->action('EvaluacionController@listado', ['id' => $id]);


    }

    //en update se recibe como parametro el id de la evaluación a editar
    public function getUpdate($id_eva){
        $evaluacion = Evaluacion::find($id_eva);
        return view('evaluacion.updateEvaluacion')->with(compact('evaluacion'));

    }

    //en update se recibe como parametro el id de la evaluación a editar
    public function postUpdate($id_eva,Request $request){
        //dd($request->all());
        $rules =[
            
            'title' => ['required', 'string','min:5','max:191'],
            'description' => ['required'],
            'duration' => ['required'],
            'intentos' => ['required'],
            'paginacion' => ['required'],
        ];
        /* Mensaje de Reglas de Validación */
        $messages = [
            
            'title.required' => 'Debe de ingresar un título para la evaluación',
            'title.min' => 'El título debe contener como mínimo 5 caracteres',
            'description.required' => 'Debe de ingresar una descripción para la evaluacion',
            'duration.required' => 'Debe de indicar la duración del examen',
            'intentos.required' => 'Debe de indicar el numero de intentos de la evaluacion',
            'paginacion.required' => 'Debe de indicar la paginación de la evaluación',

        ];
        
        $this->validate($request,$rules,$messages);
        $evaluacion = Evaluacion::find($id_eva);;
        $evaluacion->nombre_evaluacion= $request->input('title');
        $evaluacion->duracion=$request->input('duration');
        $evaluacion->intentos=$request->input('intentos');
        $evaluacion->descripcion_evaluacion=$request->input('description');
        $evaluacion->preguntas_a_mostrar=$request->input('paginacion');
        $evaluacion->revision=0;

        if(isset($request->all()['revision']))
            $evaluacion->revision = 1;
        $evaluacion->save();
        //return back()->with('notification','Se registró exitosamente');
        return redirect()->action('EvaluacionController@listado', ['id' => $evaluacion->id_carga]);


    }


    /*listado de evaluaciones por docente
    el id que recibe es la carga academica si es docente (role=1)
    el id que recibe es materia_ciclo si es admin (role=0)*/

    public function listado($id){
        $id_carga = $id;
        if(auth()->user()->role==0){
            $cargas=  CargaAcademica::where('id_mat_ci',$id)->get();
            $evaluaciones = array();
            foreach ($cargas as $carga) {
                 $evas= Evaluacion::where('id_carga',$carga->id_carg_aca  )->get();
                 foreach ($evas as $eva) {
                    array_push($evaluaciones, $eva);
                 }
            }
        }
        else{
            $evaluaciones = Evaluacion::where('id_carga',$id)->where('habilitado',1)->get();
        }
    	return view('evaluacion.listadoEvaluacion')->with(compact('evaluaciones','id_carga'));

    }

    /*listado de evaluaciones por docente
    el id que recibe es la carga academica si es docente (role=1)
    el id que recibe es materia_ciclo si es admin (role=0)*/

    public function reciclaje($id){
        $id_carga = $id;
        $evaluaciones = Evaluacion::where('id_carga',$id)->where('habilitado',0)->get();
        return view('evaluacion.recycleEvaluacion')->with(compact('evaluaciones','id_carga'));

    }

    //Deshabilita evaluaciones, con excepción de aquellas que cuentan con turnos que están en periodo de evaluacion
    public function deshabilitarEvaluacion(Request $request){
        //dd($request->all());
        $id_evaluacion = $request->input('id_evaluacion');
        if($id_evaluacion){
            $si_deshabilita =true;
            $notification = 'exito';
            $mensaje = 'La evaluación ha sido deshabilitada exitosamente'; 
            $evaluacion = Evaluacion::find($id_evaluacion); 
            if($evaluacion->turnos){
                $fecha_actual = Carbon::now('America/Denver')->format('Y-m-d H:i:s');
                foreach ($evaluacion->turnos as $turno) {
                    if($fecha_actual > $turno->fecha_inicio_turno && $fecha_actual < $turno->fecha_final_turno){
                        $notification = 'error';
                        $mensaje = 'La evaluacion no puede ser deshabilitada ya que posee uno o varios turnos en periodo de evaluación';
                        $si_deshabilita =false;
                    }
                }
            }
            if($si_deshabilita){
                $evaluacion->habilitado = 0;
                $evaluacion->save();
            }
        }else{
            $notification = 'error';
            $mensaje = 'La evaluacion no pudo ser deshabilitada, intente de nuevo';
        }

        return back()->with($notification, $mensaje);
    }

    //habilita evaluaciones
    public function habilitar(Request $request){
        //dd($request->all());
        $id_evaluacion = $request->input('id_evaluacion');
        if($id_evaluacion){
            $notification = 'exito';
            $mensaje = 'La evaluación ha sido habilitada exitosamente'; 
            $evaluacion = Evaluacion::find($id_evaluacion); 
            $evaluacion->habilitado = 1;
            $evaluacion->save();
        }else{
            $notification = 'error';
            $mensaje = 'La evaluacion no pudo ser habilitada, intente de nuevo';
        }

        return back()->with($notification, $mensaje);
    }

}
