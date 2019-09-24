<?php

namespace App\Http\Controllers;

use DB;

class MateriaController extends Controller
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

    public function listar()
    {
        $id = auth()->user()->id;

        switch (auth()->user()->role) {
            case 0:
                $materias = array();

                /*Se recupera los ciclos ordenados de mayor a menor con respecto al id, asumiendo que el ultimo registro en la tabla ciclo es el ciclo que se encuentra activo*/
                $ciclos = DB::table('ciclo')->orderBy('id_ciclo', 'desc')->get();

                foreach ($ciclos as $ciclo) {
                    /*Se crea un array asociativo donde se guardaran las materias por ciclo Ejemplo materias[1][] esto significa que del ciclo con id 1 obtienen todas las materias*/
                    $materias[$ciclo->id_ciclo] = DB::table('cat_mat_materia')
                        ->join('materia_ciclo', 'cat_mat_materia.id_cat_mat', '=', 'materia_ciclo.id_cat_mat')
                        ->join('ciclo', 'ciclo.id_ciclo', '=', 'materia_ciclo.id_ciclo')
                        ->where('ciclo.id_ciclo', '=', $ciclo->id_ciclo)
                        ->select('cat_mat_materia.*', 'materia_ciclo.*', 'ciclo.*')
                        ->get();
                }
                /*Se ordena inversamente el arreglo bidimensional tomando como parametro la llave, que son los id de los ciclos*/

                krsort($materias);

                return view("materia.listadoMateria", compact("materias", "ciclos"));
                break;

            case 1:
                $materias = array();
                /*Se recupera los ciclos ordenados de mayor a menor con respecto al id, asumiendo que el ultimo registro en la tabla ciclo es el ciclo que se encuentra activo*/
                $ciclos = DB::table('ciclo')->orderBy('id_ciclo', 'desc')->get();
                foreach ($ciclos as $ciclo) {
                    $materias[$ciclo->id_ciclo] = DB::table('cat_mat_materia')
                        ->join('materia_ciclo', 'cat_mat_materia.id_cat_mat', '=', 'materia_ciclo.id_cat_mat')
                        ->join('carga_academica', 'carga_academica.id_mat_ci', '=', 'materia_ciclo.id_mat_ci')

                        ->join('pdg_dcn_docente', function ($join) {
                            //Consulta Avanzada donde se determina de que docente se trata
                            $idUser = auth()->user()->id;
                            $join->on('pdg_dcn_docente.id_pdg_dcn', '=', 'carga_academica.id_pdg_dcn')
                                ->where('pdg_dcn_docente.user_id', '=', $idUser);
                        })
                        ->join('ciclo', 'ciclo.id_ciclo', '=', 'materia_ciclo.id_ciclo')
                        ->where('ciclo.id_ciclo', '=', $ciclo->id_ciclo)
                        ->select('cat_mat_materia.*', 'materia_ciclo.*','carga_academica.id_carg_aca')->get();
                }
                return view("materia.listadoMateria", compact("materias", "ciclos"));
                break;
            case 2:
                $ciclo    = DB::table("ciclo")->where("estado", "=", 1)->get();
                $materias = $this->materiasEstudiante($id);
                return view("materia.listadoMateria", compact("materias", "ciclo"));
                break;
        }
    }

    /**
     * Funcion para obtener las materias segun estudiante y el ciclo activo
     * @param int $id_user ID del usuario del estudiante
     * @author Ricardo Estupinian
     */
    private function materiasEstudiante($id_user){
        $ciclo    = DB::table("ciclo")->where("estado", "=", 1)->get();
        $materias = DB::table('cat_mat_materia')
            ->join('materia_ciclo', 'cat_mat_materia.id_cat_mat', '=', 'materia_ciclo.id_cat_mat')
            ->join('carga_academica', 'carga_academica.id_mat_ci', '=', 'materia_ciclo.id_mat_ci')
            ->join('detalle_insc_est', 'detalle_insc_est.id_carg_aca', '=', 'carga_academica.id_carg_aca')
            ->join('estudiante', 'estudiante.id_est', '=', 'detalle_insc_est.id_est')
            ->where('estudiante.user_id', '=',$id_user)
            ->join('ciclo', 'ciclo.id_ciclo', '=', 'materia_ciclo.id_ciclo')
            ->where('ciclo.estado', '=', 1)
            ->select('cat_mat_materia.*', 'materia_ciclo.id_mat_ci','carga_academica.id_carg_aca')->get();
        return $materias;
    }

    /**
     * Funcion para web service de materias que cursa un determinado estudiante
     * @param int $id_user ID del usuario del estudiante
     * @author Ricardo Estupinian
     */
    public function getMateriasEstudiante($id_user){
        $materias=$this->materiasEstudiante($id_user);
        response()->Json($materias);
    }
}
