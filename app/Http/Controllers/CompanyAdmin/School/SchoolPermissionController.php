<?php
namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SchoolPermissionService as Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolPermissionController extends Controller
{
    private function companyId(Request $request): int {
        $actor = $request->user();
        abort_unless($actor && $actor->role === 'companyadmin' && $actor->company_id && $actor->is_active, 403);
        return (int) $actor->company_id;
    }

    private function target(Request $request, User $user): User {
        abort_unless((int)$user->company_id === $this->companyId($request), 404);
        abort_unless(array_key_exists($user->role, Permissions::DEFAULTS), 404);
        return $user;
    }

    public function index(Request $request) {
        $companyId = $this->companyId($request);
        $users = User::where('company_id', $companyId)
            ->whereIn('role', array_keys(Permissions::DEFAULTS))
            ->orderBy('name')->paginate(20);
        return view('company.school.permissions.index', compact('users'));
    }

    public function edit(Request $request, User $user) {
        $this->target($request, $user);
        $overrides = DB::table('school_user_permission_overrides')
            ->where('company_id', $user->company_id)->where('user_id', $user->id)
            ->pluck('allowed','permission')->all();
        return view('company.school.permissions.edit', [
            'schoolUser' => $user,
            'labels' => Permissions::LABELS,
            'defaults' => Permissions::DEFAULTS[$user->role] ?? [],
            'overrides' => $overrides,
        ]);
    }

    public function update(Request $request, User $user) {
        $this->target($request, $user);
        $keys = array_keys(Permissions::LABELS);
        $data = $request->validate([
            'permissions' => ['required','array'],
            'permissions.*' => ['required', Rule::in(['default','allow','deny'])],
        ]);
        abort_unless(count($data['permissions']) === count($keys)
            && !array_diff(array_keys($data['permissions']), $keys)
            && !array_diff($keys, array_keys($data['permissions'])), 422);
        DB::transaction(function () use ($request, $user, $data) {
            DB::table('school_user_permission_overrides')
                ->where('company_id', $user->company_id)->where('user_id', $user->id)->delete();
            foreach ($data['permissions'] as $permission => $choice) {
                if ($permission === 'school.permissions.manage' || $choice === 'default') continue;
                DB::table('school_user_permission_overrides')->insert([
                    'company_id' => $user->company_id,
                    'user_id' => $user->id,
                    'permission' => $permission,
                    'allowed' => $choice === 'allow',
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            DB::table('school_access_logs')->insert([
                'company_id' => $user->company_id,
                'actor_user_id' => $request->user()->id,
                'target_user_id' => $user->id,
                'action' => 'permissions_updated',
                'result' => 'success',
                'created_at' => now(),
            ]);
        });
        return redirect()->route('company.school.permissions.index')->with('success', 'Permissions enregistrées.');
    }
}
