<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolTeacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TeacherAccountController extends Controller
{
    private function teacher(int $id): SchoolTeacher
    {
        return SchoolTeacher::query()
            ->where('company_id', auth()->user()->company_id)
            ->findOrFail($id);
    }

    public function edit(int $teacher)
    {
        $teacher = $this->teacher($teacher);
        $account = $teacher->user_id
            ? User::query()->where('company_id', $teacher->company_id)
                ->where('role', 'school_teacher')->find($teacher->user_id)
            : null;

        return view('company.school.teachers.account', compact('teacher', 'account'));
    }

    public function store(Request $request, int $teacher)
    {
        $teacher = $this->teacher($teacher);
        if ($teacher->user_id) {
            throw ValidationException::withMessages(['email' => 'Cet enseignant possède déjà un compte lié.']);
        }
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        DB::transaction(function () use ($teacher, $data) {
            $locked = SchoolTeacher::query()
                ->where('company_id', $teacher->company_id)
                ->lockForUpdate()->findOrFail($teacher->id);
            if ($locked->user_id) {
                throw ValidationException::withMessages(['email' => 'Un compte vient déjà d’être lié à cet enseignant.']);
            }
            $user = User::create([
                'name' => trim($locked->first_name . ' ' . $locked->last_name),
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'school_teacher',
                'company_id' => $locked->company_id,
                'is_active' => $locked->status === 'active',
            ]);
            $locked->user_id = $user->id;
            $locked->save();
        });

        return redirect()->route('company.school.teachers.account.edit', $teacher->id)
            ->with('success', 'Compte enseignant créé et lié.');
    }

    public function toggle(int $teacher)
    {
        $teacher = $this->teacher($teacher);
        abort_unless($teacher->user_id, 404);
        $account = User::query()->where('company_id', $teacher->company_id)
            ->where('role', 'school_teacher')->findOrFail($teacher->user_id);
        if (!$account->is_active && $teacher->status !== 'active') {
            throw ValidationException::withMessages(['account' => 'Activez d’abord la fiche enseignant.']);
        }
        $account->is_active = !$account->is_active;
        $account->save();

        return back()->with('success', 'Statut du compte mis à jour.');
    }
}
