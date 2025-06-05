<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewApplicationNotification;
use App\Notifications\ApplicationStatusChangedNotification;

class JobApplicationController extends Controller
{
    /**
     * Submeter uma candidatura a uma oferta de emprego.
     */
    public function store(Request $request, Job $job)
    {
        $user = Auth::user();

        // Verificar se o utilizador é um ex-aluno
        if (!$user->isStudent()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        // Verificar se a oferta está ativa e não expirada
        if (!$job->is_active || $job->expiration_date < now()) {
            return response()->json(['message' => 'Esta oferta não está disponível para candidaturas.'], 422);
        }

        // Verificar se o utilizador já se candidatou a esta oferta
        if (JobApplication::where('user_id', $user->id)->where('job_id', $job->id)->exists()) {
            return response()->json(['message' => 'Já se candidatou a esta oferta.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string',
            'cv' => 'required_without:use_profile_cv|file|mimes:pdf,doc,docx|max:2048',
            'use_profile_cv' => 'required_without:cv|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Processar CV
        $cvPath = null;
        if ($request->boolean('use_profile_cv')) {
            if (!$user->cv) {
                return response()->json(['message' => 'Não tem CV no seu perfil.'], 422);
            }
            $cvPath = $user->cv;
        } elseif ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs', 'private');
        }

        // Criar candidatura
        $application = JobApplication::create([
            'user_id' => $user->id,
            'job_id' => $job->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'course_completion_year' => $user->course_completion_year,
            'cv' => $cvPath,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // Notificar empresa sobre nova candidatura
        $job->company->notify(new NewApplicationNotification($application));

        return response()->json([
            'message' => 'Candidatura submetida com sucesso.',
            'application' => $application
        ], 201);
    }

    /**
     * Listar candidaturas do ex-aluno autenticado.
     */
    public function userApplications(Request $request)
    {
        $user = Auth::user();

        // Verificar se o utilizador é um ex-aluno
        if (!$user->isStudent()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $query = JobApplication::where('user_id', $user->id)
            ->with(['job:id,title,company_id,location,contract_type', 'job.company:id,company_name,company_logo']);

        // Filtrar por estado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Ordenar resultados
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        // Paginação
        $perPage = $request->get('per_page', 10);
        $applications = $query->paginate($perPage);

        return response()->json($applications);
    }

    /**
     * Listar candidaturas às ofertas da empresa autenticada.
     */
    public function companyApplications(Request $request)
    {
        $user = Auth::user();

        // Verificar se o utilizador é uma empresa
        if (!$user->isCompany()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $query = JobApplication::whereHas('job', function ($query) use ($user) {
            $query->where('company_id', $user->id);
        })->with(['job:id,title,location,contract_type']);

        // Filtrar por oferta
        if ($request->has('job_id')) {
            $query->where('job_id', $request->job_id);
        }

        // Filtrar por estado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Ordenar resultados
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        // Paginação
        $perPage = $request->get('per_page', 10);
        $applications = $query->paginate($perPage);

        return response()->json($applications);
    }

    /**
     * Atualizar estado de uma candidatura.
     */
    public function updateStatus(Request $request, JobApplication $application)
    {
        $user = Auth::user();

        // Verificar se o utilizador é a empresa proprietária da oferta
        $job = Job::findOrFail($application->job_id);
        if ($user->id !== $job->company_id) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Atualizar estado
        $oldStatus = $application->status;
        $application->status = $request->status;
        $application->processed_at = now();
        $application->save();

        // Notificar ex-aluno sobre mudança de estado
        if ($oldStatus !== $request->status) {
            $applicant = $application->user;
            $applicant->notify(new ApplicationStatusChangedNotification($application));
        }

        return response()->json([
            'message' => 'Estado da candidatura atualizado com sucesso.',
            'application' => $application
        ]);
    }

    /**
     * Remover uma candidatura.
     */
    public function destroy(JobApplication $application)
    {
        $user = Auth::user();

        // Verificar se o utilizador é o proprietário da candidatura
        if ($user->id !== $application->user_id) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $application->delete();

        return response()->json([
            'message' => 'Candidatura removida com sucesso.'
        ]);
    }
}
