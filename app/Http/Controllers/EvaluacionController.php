<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Evaluacion;
use App\CicloMateria;
use App\CargaAcademica;
use App\Turno;
use App\User;
use App\Clave_Area;
use App\Intento;
use App\Estudiante;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;

class EvaluacionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
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
        //return redirect()->action('EvaluacionController@listado', ['id' => $id]);
        return redirect(URL::signedRoute('listado_evaluacion', ['id' => $id]));


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
        //return redirect()->action('EvaluacionController@listado', ['id' => $evaluacion->id_carga]);
        return redirect(URL::signedRoute('listado_evaluacion', ['id' => $evaluacion->id_carga]));


    }


    /*listado de evaluaciones por docente
    el id que recibe es la carga academica si es docente (role=1)
    el id que recibe es materia_ciclo si es admin (role=0)*/

    public function listado($id){
        $fecha_hora_actual = Carbon::now('America/Denver')->format('Y-m-d H:i:s');
        $id_carga = $id;
        if(auth()->user()->IsAdmin){
            $cargas=  CargaAcademica::where('id_mat_ci',$id)->get();
            $evaluaciones = array();
            foreach ($cargas as $carga) {
                 $evas= Evaluacion::where('id_carga',$carga->id_carg_aca  )->get();
                 foreach ($evas as $eva) {
                    array_push($evaluaciones, $eva);
                 }
            }
        }elseif(auth()->user()->IsTeacher){
            $evaluaciones = Evaluacion::where('id_carga',$id)->where('habilitado',1)->get();
        }elseif(auth()->user()->IsStudent){
            $evaluaciones_all = Evaluacion::where('id_carga',$id)
                                ->where('habilitado',1)
                                ->get();
            $evaluaciones = array();
            //verificacion de que las evaluaciones que se manden a la vista, poseean al menos un turno disponible
            foreach ($evaluaciones_all as $evaluacion) {
                $turnos_activos = false;
                if($evaluacion->turnos){
                    foreach ($evaluacion->turnos as $turno) {
                        if($turno->visibilidad==1 && 
                            $turno->fecha_inicio_turno <= $fecha_hora_actual &&
                            $turno->fecha_final_turno > $fecha_hora_actual){
                            $turnos_activos = true;
                        }
                    }
                    if($turnos_activos==true){
                        $evaluaciones[] = $evaluacion;
                    }
                }
            } 
        }
    	return view('evaluacion.listaEvaluacion')->with(compact('evaluaciones','id_carga'));

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

    //recibimos el id de los turnos que se desean publicar
    public function publicar( Request $request){
        //dd($request->all());
        $turnos = $request->input('turnosnopublicos');
        $notification = "info";
        $message = "";
        if($turnos){
            foreach($turnos as $turno){
                $turno_publico = Turno::find($turno);
                
                $turno_publico->fecha_inicio_turno= DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $turno_publico->fecha_inicio_turno
                )->format('l jS \\of F Y h:i A');
                $turno_publico->fecha_final_turno= DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $turno_publico->fecha_final_turno
                )->format('l jS \\of F Y h:i A');
                
                if($turno_publico->claves){
                    foreach ($turno_publico->claves as $clave) {
                        if(Clave_Area::where('clave_id', $clave->id)->exists()){
                            $areas_de_clave = Clave_Area::where('clave_id', $clave->id)->get();
                            $sumatoria_de_pesos = 0;
                            foreach ($areas_de_clave as $area_de_clave) {
                                $sumatoria_de_pesos += $area_de_clave->peso;
                            }
                            if($sumatoria_de_pesos<100){
                                $message .= "Info: La sumatoria de pesos del turno => <strong> Inicio: </strong>" . $turno_publico->fecha_inicio_turno . " <strong> Final: </strong> " . $turno_publico->fecha_final_turno . " es de ". $sumatoria_de_pesos . ", menor al 100 requerido<br><br>";
                            }elseif($sumatoria_de_pesos>100){
                                $message .= "Info: La sumatoria de pesos del turno => <strong> Inicio: </strong>" . $turno_publico->fecha_inicio_turno . " <strong> Final: </strong> " . $turno_publico->fecha_final_turno . " es de ". $sumatoria_de_pesos . ", mayor al 100 requerido<br><br>";

                            }elseif($sumatoria_de_pesos==100){

                                /*CREACION DE CLAVES
                                *
                                *
                                *
                                *
                                **/

                                $message .= "Info: Publicación exitosa del turno => <strong> Inicio: </strong>" . $turno_publico->fecha_inicio_turno . " <strong> Final: </strong> " . $turno_publico->fecha_final_turno ."<br><br>";

                                $turno_publico->visibilidad = 1;
                                $turno_publico->fecha_inicio_turno= DateTime::createFromFormat(
                                        'l jS \\of F Y h:i A',
                                        $turno_publico->fecha_inicio_turno
                                    )->format('Y-m-d H:i:s');
                                $turno_publico->fecha_final_turno= DateTime::createFromFormat(
                                        'l jS \\of F Y h:i A',
                                        $turno_publico->fecha_final_turno
                                    )->format('Y-m-d H:i:s'); 
                                $turno_publico->save();
                            }
                            

                        }else{
                            $message .= "Info: Debe agregar áreas de preguntas al turno => <strong> Inicio: </strong>" . $turno_publico->fecha_inicio_turno . " <strong> Final: </strong> " . $turno_publico->fecha_final_turno . "<br><br>";
                        }
                    }
                    
                }else{
                    $message .= "Info: no posee clave el turno => <strong>Inicio:</strong>" . $turno_publico->fecha_inicio_turno . " <strong>Final:</strong> " . $turno_publico->fecha_final_turno . "<br><br>";
                }  
            }
            
        }else{
            $notification = "info";
            $message = "Info: no ha seleccionado ningún turno a publicar";
        }
        return back()->with($notification,$message); 
    }

    /**
     * Funcion para validar el acceso a los intentos de evaluaciones.
     * @param 
     * @author Edwin Palacios
     */
    public function acceso(Request $request){
        //declaracion de variables
        $fecha_hora_actual = Carbon::now('America/Denver')->format('Y-m-d H:i:s');
        $id_turno = $request->input('id_turno_acceso');
        $contrasenia = $request->input('contraseña');
        if($contrasenia){
            $estudiante = Estudiante::where('user_id', auth()->user()->id)->first();
            $turno_a_acceder =  Turno::find($id_turno);

            //validacion de fecha
            if(!($fecha_hora_actual >= $turno_a_acceder->fecha_inicio_turno && $turno_a_acceder->fecha_final_turno> $fecha_hora_actual )){
                $notification = "error";
                $message = "Error: El periodo de disponibilidad a finalizado. " . $fecha_hora_actual;
                return back()->with($notification,$message);
            } 
            
            $evaluacion = $turno_a_acceder->evaluacion;
            if($evaluacion->CantIntentos <= 0){
                $notification = "error";
                $message = "Error: Ya ha realizado todos los intentos";
                return back()->with($notification,$message);
            }else{
                //Se valida si la contraseña es valida
                if(Hash::check($contrasenia, $turno_a_acceder->contraseña)){
                    return redirect()->action(
                        'IntentoController@iniciarEvaluacion', 
                        ['id_intento' => $turno_a_acceder->id]
                    );
                }else{
                    $notification = "error";
                    $message = "Error: La contraseña no es valida";
                    return back()->with($notification,$message);
                }
            }
        }else{
            $notification = "error";
            $message = "Error: No ha ingresado la contraseña";
            return back()->with($notification,$message);
        }
    }
    /**
     * Metodo que devuelve las evaluaicones y turnos disponibles (MOVIL)..
     * @author Edwin Palacioes
     * @param id_carga que corresponde al id de la carga academica del estudiante
     * @return Json que contiene las evaluaciones y turnos disponibles.
     */ 
    public function evaluacionTurnosDisponibles($id_carga){
        $fecha_hora_actual = Carbon::now('America/Denver')->addMinutes(10)->format('Y-m-d H:i:s');
        $evaluaciones_all = Evaluacion::where('id_carga',$id_carga)
                                ->where('habilitado',1)
                                ->get();
        $evaluaciones = array();
        $turnos = array();
        //verificacion de que las evaluaciones que se mandan, poseean al menos un turno disponible
        foreach ($evaluaciones_all as $evaluacion) {
            $turnos_activos = false;
            if($evaluacion->turnos){
                foreach ($evaluacion->turnos as $turno) {
                    if($turno->visibilidad==1 && 
                        $turno->fecha_inicio_turno <= $fecha_hora_actual &&
                        $turno->fecha_final_turno > $fecha_hora_actual){
                        $turnos[] = $turno;
                        $turnos_activos = true;
                    }
                }
                if($turnos_activos==true){
                    $evaluaciones[] = $evaluacion;
                }
            }
        } 

        //dd($turnos);
        $data = [
            'evaluaciones'=>$evaluaciones,
            'turnos' => $turnos];
        return response()->json(
            $data,
             200,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], 
            JSON_UNESCAPED_UNICODE);
    }

    /**
     * Metodo que devuelve la consulta del usuario solicitado (MOVIL).
     * @author Edwin Palacioes
     * @param id_carga que corresponde al id de la carga academica del estudiante
     * @return Json que contiene el registro del user.
     */ 
    public function accesoUserMovil($email, $password){
        $user_no_autenticado = User::where('email',$email)->first();
        $user_autenticado = null;
        
        if(Hash::check($password, $user_no_autenticado->password)){
            $user_autenticado = $user_no_autenticado;
        }
        $contrasenia = $user_autenticado->password;
        //dd($user_autenticado);
        $data = ['user'=>$user_autenticado,'pass'=>$contrasenia];
        return $data;
    }

}
