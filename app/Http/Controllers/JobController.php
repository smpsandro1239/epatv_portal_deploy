<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    /**
     * Listar todas as ofertas de emprego ativas.
     */
    public function index(Request $request)
    {
        $query = Job::with(['company:id,company_name,company_logo,company_city', 'category:id,name'])
            ->where('is_active', true)
            ->where('expiration_date', '>=', now());

        // Filtrar por categoria
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrar por localização
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filtrar por tipo de contrato
        if ($request->has('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        // Ordenar resultados
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        // Paginação
        $perPage = $request->get('per_page', 10);
        $jobs = $query->paginate($perPage);

        return response()->json($jobs);
    }

    /**
     * Mostrar uma oferta de emprego específica.
     */
    public function show(Job $job)
    {
        // Incrementar contador de visualizações
        $job->views_count += 1;
        $job->save();

        $job->load(['company:id,company_name,company_logo,company_city,company_website,company_description', 'category:id,name']);

        return response()->json($job);
    }

    /**
     * Criar uma nova oferta de emprego.
     */
    public function store(Request $request)
    {
        // Verificar se o utilizador é uma empresa
        if (!Auth::user()->isCompany()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'contract_type' => 'required|in:full-time,part-time,internship,temporary,freelance',
            'expiration_date' => 'required|date|after:today',
            'category_id' => 'required|exists:area_of_interests,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $job = Job::create([
            'company_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'salary' => $request->salary,
            'contract_type' => $request->contract_type,
            'expiration_date' => $request->expiration_date,
            'category_id' => $request->category_id,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Oferta de emprego criada com sucesso.',
            'job' => $job
        ], 201);
    }

    /**
     * Atualizar uma oferta de emprego.
     */
    public function update(Request $request, Job $job)
    {
        // Verificar se o utilizador é o proprietário da oferta
        if (Auth::id() !== $job->company_id) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'contract_type' => 'required|in:full-time,part-time,internship,temporary,freelance',
            'expiration_date' => 'required|date|after:today',
            'category_id' => 'required|exists:area_of_interests,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $job->update($request->all());

        return response()->json([
            'message' => 'Oferta de emprego atualizada com sucesso.',
            'job' => $job
        ]);
    }

    /**
     * Remover uma oferta de emprego.
     */
    public function destroy(Job $job)
    {
        // Verificar se o utilizador é o proprietário da oferta
        if (Auth::id() !== $job->company_id) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $job->delete();

        return response()->json([
            'message' => 'Oferta de emprego removida com sucesso.'
        ]);
    }

    /**
     * Listar ofertas de emprego da empresa autenticada.
     */
    public function companyJobs(Request $request)
    {
        $query = Job::where('company_id', Auth::id())
            ->with('category:id,name');

        // Filtrar por estado
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Ordenar resultados
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        // Paginação
        $perPage = $request->get('per_page', 10);
        $jobs = $query->paginate($perPage);

        return response()->json($jobs);
    }

    /**
     * Guardar uma oferta de emprego (ex-aluno).
     */
    public function saveJob(Job $job)
    {
        $user = Auth::user();

        // Verificar se o utilizador é um ex-aluno
        if (!$user->isStudent()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        // Verificar se a oferta já está guardada
        if ($user->savedJobs()->where('job_id', $job->id)->exists()) {
            return response()->json([
                'message' => 'Esta oferta já está guardada.'
            ], 422);
        }

        $user->savedJobs()->attach($job->id);

        return response()->json([
            'message' => 'Oferta guardada com sucesso.'
        ]);
    }

    /**
     * Remover uma oferta guardada (ex-aluno).
     */
    public function unsaveJob(Job $job)
    {
        $user = Auth::user();

        // Verificar se o utilizador é um ex-aluno
        if (!$user->isStudent()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $user->savedJobs()->detach($job->id);

        return response()->json([
            'message' => 'Oferta removida dos favoritos com sucesso.'
        ]);
    }

    /**
     * Listar ofertas guardadas pelo ex-aluno autenticado.
     */
    public function savedJobs(Request $request)
    {
        $user = Auth::user();

        // Verificar se o utilizador é um ex-aluno
        if (!$user->isStudent()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $query = $user->savedJobs()
            ->with(['company:id,company_name,company_logo,company_city', 'category:id,name'])
            ->where('is_active', true)
            ->where('expiration_date', '>=', now());

        // Ordenar resultados
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        // Paginação
        $perPage = $request->get('per_page', 10);
        $jobs = $query->paginate($perPage);

        return response()->json($jobs);
    }
}
