<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; // Importa el facade de Auth



use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validar los datos de entrada: solo que sean requeridos
            $validated = $request->validate([
                'name' => 'required|string',
                'lastname' => 'required|string',
                'email' => 'required|string|email',
                'phone' => 'required|numeric',
                'password' => 'required|string',
            ]);

            // Verificar si el correo ya existe
            if (User::where('email', $validated['email'])->exists()) {
                return response()->json(['error' => 'El correo electrónico ya está registrado.'], 400);
            }

            // Crear el nuevo usuario
            $user = new User();
            $user->name = $validated['name'];
            $user->lastname = $validated['lastname'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'];
            $user->password = bcrypt($validated['password']);  // Encriptar la contraseña
            $user->save();

            return response()->json(['message' => 'Usuario creado con éxito!'], 201);
        } catch (\Exception $e) {
            // Capturar cualquier error no relacionado con la validación
            return response()->json(['error' => 'Error al registrar el usuario: ' . $e->getMessage()], 500);
        }
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            return response()->json(['error' => 'El correo electrónico no está registrado.'], 404);
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            return response()->json([
                'message' => 'Login exitoso',
                'user' => $user
            ], 200);
        } else {
            return response()->json(['error' => 'Credenciales incorrectas.'], 401);
        }
    }
}

