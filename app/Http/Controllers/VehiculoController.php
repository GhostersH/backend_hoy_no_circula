<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VehiculoController extends Controller
{
    // Opción 1: Registrar vehículo
    public function registrar(Request $request)
    {
        $request->validate([
            'placa' => 'required|unique:vehiculos',
            'color' => 'required',
            'modelo' => 'required|integer|min:1900|max:' . Carbon::now()->year,
            'chasis' => 'required',
        ]);

        $vehiculo = Vehiculo::create($request->all());

        return response()->json([
            'message' => 'Vehículo registrado con éxito',
            'vehiculo' => $vehiculo
        ]);
    }

    // Opción 2: Consultar si el vehículo puede circular
    public function puedeCircular(Request $request)
    {
        // Validar la placa y la fecha antes de cualquier proceso
        $request->validate([
            'placa' => 'required',
            'fecha' => 'required|date|after_or_equal:' . Carbon::now()->subMinutes(1),
        ]);

        // Buscar el vehículo por su placa
        $vehiculo = Vehiculo::where('placa', $request->placa)->first();

        // Si no se encuentra el vehículo, devolver un mensaje de error
        if (!$vehiculo) {
            return response()->json([
                'error' => 'Esa placa no existe en nuestros registros.',
            ], 404); // Código 404 para "No encontrado"
        }

        // Verificar la hora actual del servidor
        $horaActualServidor = Carbon::now();
        $fechaIngresada = Carbon::parse($request->fecha);
        $diaSemana = $fechaIngresada->dayOfWeek;
        $ultimaCifraPlaca = substr($vehiculo->placa, -1);

        // Horarios de restricción
        $horaInicioManana = $fechaIngresada->copy()->setTime(6, 0);
        $horaFinManana = $fechaIngresada->copy()->setTime(9, 30);
        $horaInicioTarde = $fechaIngresada->copy()->setTime(16, 0);
        $horaFinTarde = $fechaIngresada->copy()->setTime(20, 00);

        $puedeCircular = true;

        // Lógica de restricción basada en el día y la placa
        if (($diaSemana == 1 && in_array($ultimaCifraPlaca, [1, 2])) ||
            ($diaSemana == 2 && in_array($ultimaCifraPlaca, [3, 4])) ||
            ($diaSemana == 3 && in_array($ultimaCifraPlaca, [5, 6])) ||
            ($diaSemana == 4 && in_array($ultimaCifraPlaca, [7, 8])) ||
            ($diaSemana == 5 && in_array($ultimaCifraPlaca, [9, 0]))) {

            if (($fechaIngresada->between($horaInicioManana, $horaFinManana)) ||
                ($fechaIngresada->between($horaInicioTarde, $horaFinTarde))) {
                $puedeCircular = false;
            }
        }

        return response()->json([
            'vehiculo' => $vehiculo,
            'puede_circular' => $puedeCircular ? 'Sí' : 'No',
            'mensaje' => $puedeCircular ? 'El vehículo puede circular.' : 'El vehículo NO puede circular dentro del horario de restricción.',
            'hora_actual_servidor' => $horaActualServidor->toDateTimeString(),
            'fecha_ingresada' => $fechaIngresada->toDateTimeString(),
        ]);
    }

}
