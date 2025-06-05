<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AreaOfInterest;
use App\Models\RegistrationWindow;
use App\Notifications\StudentWelcomeNotification;
use App\Notifications\CompanyWelcomeNotification;
use App\Notifications\PendingRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Login de utilizador.
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => 'Dados inválidos', 'errors' => $validator->errors()], 422);
            }

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Credenciais inválidas'], 401);
            }

            $user = User::where('email', $request->email)->first();

            // Verificar estado do registo
            if ($user->registration_status === 'pending') {
                Auth::logout();
                return response()->json(['message' => 'O seu registo está pendente de aprovação.'], 403);
            }

            if ($user->registration_status === 'rejected') {
                Auth::logout();
                return response()->json(['message' => 'O seu registo foi rejeitado. Por favor, contacte a administração.'], 403);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro no login: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            return response()->json(['message' => 'Erro ao processar login: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Registo de ex-aluno.
     */
    public function register(Request $request)
    {
        try {
            Log::info('Iniciando registo de ex-aluno', ['request' => $request->all()]);
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'required|string|max:20',
                'course_completion_year' => 'required|integer|min:1990|max:' . date('Y'),
                'areas_of_interest' => 'required|array|min:1',
                'areas_of_interest.*' => 'exists:area_of_interests,id',
                'registration_password' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                Log::warning('Validação falhou no registo de ex-aluno', ['errors' => $validator->errors()]);
                return response()->json(['message' => 'Dados inválidos', 'errors' => $validator->errors()], 422);
            }

            // Verificar janela de registo
            $window = RegistrationWindow::where('is_active', true)
                ->where('start_time', '<=', now())
                ->where('end_time', '>=', now())
                ->first();

            // Verificar password da janela de registo
            if ($window && $window->password) {
                if (!$request->registration_password || $request->registration_password !== $window->password) {
                    Log::warning('Password de registo inválida', ['window_id' => $window->id]);
                    return response()->json(['message' => 'Password de registo inválida.'], 422);
                }
            }

            // Determinar estado do registo
            $registrationStatus = 'pending';
            if ($window) {
                $registrationStatus = 'approved';
                
                // Verificar limite de registos
                if ($window->max_registrations && $window->current_registrations >= $window->max_registrations) {
                    $registrationStatus = 'pending';
                } else if ($window->max_registrations) {
                    $window->incrementRegistrations();
                }
            }

            // Criar utilizador
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'course_completion_year' => $request->course_completion_year,
                'role' => 'student',
                'registration_status' => $registrationStatus,
            ]);

            // Associar áreas de interesse
            if ($request->areas_of_interest) {
                $user->areasOfInterest()->attach($request->areas_of_interest);
            }

            // Enviar notificações
            if ($registrationStatus === 'approved') {
                $user->notify(new StudentWelcomeNotification());
                
                return response()->json([
                    'message' => 'Registo efetuado com sucesso.',
                    'user' => $user,
                ], 201);
            } else {
                // Notificar superadmins sobre novo registo pendente
                $superadmins = User::where('role', 'superadmin')->get();
                foreach ($superadmins as $admin) {
                    $admin->notify(new PendingRegistrationNotification($user));
                }
                
                return response()->json([
                    'message' => 'Registo submetido com sucesso e aguarda aprovação.',
                    'status' => 'pending',
                    'user' => $user,
                ], 201);
            }
        } catch (\Exception $e) {
            Log::error('Erro no registo de ex-aluno: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json(['message' => 'Erro ao processar registo: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Registo de empresa.
     */
    public function registerCompany(Request $request)
    {
        try {
            Log::info('Iniciando registo de empresa', ['request' => $request->all()]);
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'required|string|max:20',
                'company_name' => 'required|string|max:255',
                'company_city' => 'required|string|max:255',
                'company_website' => 'nullable|url|max:255',
                'company_description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                Log::warning('Validação falhou no registo de empresa', ['errors' => $validator->errors()]);
                return response()->json(['message' => 'Dados inválidos', 'errors' => $validator->errors()], 422);
            }

            // Criar utilizador empresa
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'company_name' => $request->company_name,
                'company_city' => $request->company_city,
                'company_website' => $request->company_website,
                'company_description' => $request->company_description,
                'role' => 'admin',
                'registration_status' => 'approved',
            ]);

            // Enviar notificação de boas-vindas
            $user->notify(new CompanyWelcomeNotification());

            return response()->json([
                'message' => 'Registo de empresa efetuado com sucesso.',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erro no registo de empresa: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json(['message' => 'Erro ao processar registo de empresa: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Logout de utilizador.
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logout efetuado com sucesso.']);
        } catch (\Exception $e) {
            Log::error('Erro no logout: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $request->user() ? $request->user()->id : null
            ]);
            return response()->json(['message' => 'Erro ao processar logout: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obter dados do utilizador autenticado.
     */
    public function user(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->role === 'student') {
                $user->load('areasOfInterest');
            }
            
            return response()->json($user);
        } catch (\Exception $e) {
            Log::error('Erro ao obter dados do utilizador: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $request->user() ? $request->user()->id : null
            ]);
            return response()->json(['message' => 'Erro ao obter dados do utilizador: ' . $e->getMessage()], 500);
        }
    }
}
