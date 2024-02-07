<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Classes\Transactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PDOException;
use RuntimeException;
use Throwable;


class UserController extends Controller
{
    use Transactions;

    public function registrarUsuario(Request $request){

        $ValidarDatosUsuario = $request-> validate([
            'apodo' =>   'required|min:3|max:20',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ]);

        try {
            $this->bloquearTablaRead('users');
            $this->iniciarTransaccion();

            $User = new User();

            $User -> apodo  = $request->post('apodo');
            $User -> email  = $request->post('email');
            $User -> password  = Hash::make($request->post('password'));

            $User->createToken('authToken')->accessToken;

            $User->save();

            $this->commitearYDesbloquearTablas();

            return $User;
        }
        catch (QueryException $th) {
            $this->rollbackearTablas();
            return response(['message' => 'Something Went Wrong'],404);
        }
        catch (PDOException $th) {
            return response(['message' => 'Something Went Wrong'],500);
        }
        catch (RuntimeException $th) {
            $this->rollbackearTablas();
            return response(['message' => 'Not Found'],404);
        }
        catch (Throwable $th) {
            $this->rollbackearTablas();
            return response(['message' => $th],404);
        }

    }

    public function validarTokenUsuario(Request $request){
        try {
            return auth('api')->user();
        }
        catch (QueryException $th) {
            return response(['message' => 'Something Went Wrong'],404);
        }
        catch (PDOException $th) {
            return response(['message' => 'Something Went Wrong'],500);
        }
        catch (RuntimeException $th) {
            return response(['message' => 'Not Found'],404);
        }
        catch (Throwable $th) {
            return response(['message' => 'Something Went Wrong'],404);
        }
    }

    public function cerrarSesionUsuario(Request $request){
        try {
            $User = $request->user()->token()->revoke();
            return $User;
        }
        catch (QueryException $th) {
            return response(['message' => 'Something Went Wrong'],404);
        }
        catch (PDOException $th) {
            return response(['message' => 'Something Went Wrong'],500);
        }
        catch (RuntimeException $th) {
            return response(['message' => 'Not Found'],404);
        }
        catch (Throwable $th) {
            return response(['message' => 'Something Went Wrong'],404);
        }
    }

    public function crearPartido(Request $request){

        $ValidarDatosUsuario = $request-> validate([
            'nombre' =>   'required|min:3|max:20',
            'precio' => 'required|integer',
            'cantidad' => 'required|integer|min:10|max:22',
            'fecha' => 'required|date_format:"Y-m-d H:i:s"|after_or_equal:now',
            'lugar' => 'required|string|max:255',
            'nota' => 'string|max:255',
        ]);

        try {
            $this->bloquearTablaRead('partidos');
            $this->iniciarTransaccion();

            $Partido = new Partido;

            $Partido->nombre = $request->post('nombre');
            $Partido->precio = $request->post('precio');
            $Partido->cantidad = $request->post('cantidad');
            $Partido->fecha = $request->post('fecha');
            $Partido->lugar = $request->post('lugar');
            $Partido->nota = $request->post('nota');
            $Partido->id_usuario = $request->header('id_usuario');

            $Partido->save();

            $this->commitearYDesbloquearTablas();

            return $Partido;
        }
        catch (QueryException $th) {
            $this->rollbackearTablas();
            return response(['message' => 'Something Went Wrong'],404);
        }
        catch (PDOException $th) {
            return response(['message' => 'Something Went Wrong'],500);
        }
        catch (RuntimeException $th) {
            $this->rollbackearTablas();
            return response(['message' => 'Not Found'],404);
        }
        catch (Throwable $th) {
            $this->rollbackearTablas();
            return response(['message' => 'Something Went Wrong'],404);
        }
    }

    public function listarPartidosCreados(Request $request){
        try {
            $Partidos = DB::select("SELECT p.*, COUNT(j.id) as asistentes FROM partidos p LEFT JOIN jugadores j ON p.id = j.id_partido WHERE
                                p.id_usuario = :id_usuario GROUP BY p.id", ['id_usuario' => $request->header('id_usuario')]);

            return $Partidos;
        }
        catch (QueryException $th) {
            return response(['message' => 'Something Went Wrong'],404);
        }
        catch (PDOException $th) {
            return response(['message' => 'Something Went Wrong'],500);
        }
        catch (RuntimeException $th) {
            return response(['message' => 'Not Found'],404);
        }
        catch (Throwable $th) {
            return response(['message' => 'Something Went Wrong'],404);
        }
    }
}
