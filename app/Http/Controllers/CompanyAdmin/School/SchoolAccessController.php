<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SchoolAccessController extends Controller
{
    private const ROLES = [
        'companyadmin' => 'Administrateur principal',
        'school_admin' => 'Administrateur scolaire',
        'school_principal' => 'Proviseur',
        'school_director' => 'Directeur',
        'school_teacher' => 'Enseignant',
        'school_bursar' => 'Intendant',
        'school_supervisor' => 'Surveillant général',
        'school_sector_supervisor' => 'Surveillant de secteur',
        'school_parent' => 'Parent',
        'school_librarian' => 'Bibliothécaire',
    ];

    private function companyId(Request $request): int
    {
        $actor = $request->user();
        abort_unless($actor && $actor->company_id, 403);
        abort_unless(in_array($actor->role, ['companyadmin', 'school_admin'], true), 403);
        return (int) $actor->company_id;
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId($request);
        $users = User::query()
            ->where('company_id', $companyId)
            ->whereIn('role', array_keys(self::ROLES))
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->query('q'));
                $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%"));
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $logs = DB::table('school_access_logs')
            ->where('company_id', $companyId)
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        return view('company.school.access.index', [
            'users' => $users,
            'logs' => $logs,
            'roles' => self::ROLES,
        ]);
    }

    public function sendResetLink(Request $request, User $user)
    {
        $companyId = $this->companyId($request);
        abort_unless((int) $user->company_id === $companyId, 404);
        abort_unless(array_key_exists($user->role, self::ROLES), 404);
        abort_if($user->role === 'companyadmin', 403, 'Le compte principal est protégé.');
        abort_unless($user->is_active, 422, 'Activez ce compte avant de lui envoyer un lien.');

        $status = Password::broker()->sendResetLink(['email' => $user->email]);

        DB::table('school_access_logs')->insert([
            'company_id' => $companyId,
            'actor_user_id' => $request->user()->id,
            'target_user_id' => $user->id,
            'action' => 'password_reset_link',
            'result' => $status === Password::RESET_LINK_SENT ? 'sent' : 'failed',
            'created_at' => now(),
        ]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Lien de réinitialisation envoyé à ' . $user->email . '.')
            : back()->withErrors(['access' => 'Envoi impossible : ' . __($status)]);
    }

    private function exportQuery(Request $request)
    {
        $companyId = $this->companyId($request);
        return User::query()->where('company_id', $companyId)
            ->whereIn('role', array_keys(self::ROLES))
            ->orderBy('name');
    }

    private function exportRows(Request $request): array
    {
        return $this->exportQuery($request)->get(['name', 'email', 'role', 'is_active'])
            ->map(fn ($u) => [
                $u->name, $u->email, self::ROLES[$u->role] ?? $u->role,
                $u->is_active ? 'Actif' : 'Inactif',
            ])->all();
    }

    public function exportExcel(Request $request)
    {
        $this->companyId($request);
        abort_unless(class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class), 503,
            'Installer phpoffice/phpspreadsheet pour l’export XLSX.');

        $sheetBook = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $sheetBook->getActiveSheet();
        $sheet->setTitle('Acces scolaires');
        $sheet->fromArray(['Nom', 'Email', 'Role', 'Statut'], null, 'A1');
        foreach ($this->exportRows($request) as $index => $row) {
            foreach ($row as $column => $value) {
                // Explicit STRING prevents spreadsheet formulas supplied in user data.
                $sheet->setCellValueExplicit(
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column + 1) . ($index + 2),
                    (string) $value,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );
            }
        }
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        foreach (['A' => 30, 'B' => 38, 'C' => 30, 'D' => 15] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        $tmp = tempnam(sys_get_temp_dir(), 'nexora_xlsx_');
        try {
            (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($sheetBook))->save($tmp);
            $bytes = file_get_contents($tmp);
        } finally {
            @unlink($tmp);
            $sheetBook->disconnectWorksheets();
        }
        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="acces-scolaires.xlsx"',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $this->companyId($request);
        abort_unless(class_exists(\Barryvdh\DomPDF\Facade\Pdf::class), 503,
            'Installer barryvdh/laravel-dompdf pour l’export PDF.');
        return \Barryvdh\DomPDF\Facade\Pdf::loadView('company.school.access.export-pdf', [
            'rows' => $this->exportRows($request),
        ])->setPaper('a4', 'landscape')->download('acces-scolaires.pdf');
    }

    public function sendSms(Request $request, User $user)
    {
        $companyId = $this->companyId($request);
        abort_unless((int) $user->company_id === $companyId, 404);
        abort_unless(array_key_exists($user->role, self::ROLES), 404);
        abort_if($user->role === 'companyadmin', 403, 'Compte principal protégé.');
        abort_unless($user->is_active, 422, 'Compte inactif.');
        $data = $request->validate(['phone' => ['required', 'regex:/^\+[1-9][0-9]{7,14}$/']]);

        $endpoint = config('services.school_sms.endpoint');
        $token = config('services.school_sms.token');
        abort_unless($endpoint && $token, 503, 'Passerelle SMS non configurée.');
        abort_unless(str_starts_with($endpoint, 'https://'), 503, 'La passerelle SMS doit utiliser HTTPS.');
        abort_unless(Route::has('password.reset'), 503, 'Route password.reset introuvable.');

        // New token is created for this request, never stored in logs or exported.
        $resetToken = Password::broker()->createToken($user);
        $url = route('password.reset', ['token' => $resetToken, 'email' => $user->email]);
        $message = 'NEXORA : pour définir votre mot de passe, ouvrez ce lien personnel : ' . $url;
        $sent = false;
        try {
            $response = Http::withToken($token)->acceptJson()->timeout(12)
                ->post($endpoint, ['to' => $data['phone'], 'message' => $message]);
            $sent = $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Passerelle SMS scolaire indisponible', ['exception' => get_class($e)]);
        }
        DB::table('school_access_logs')->insert([
            'company_id' => $companyId, 'actor_user_id' => $request->user()->id,
            'target_user_id' => $user->id, 'action' => 'password_reset_sms',
            'result' => $sent ? 'sent' : 'failed', 'created_at' => now(),
        ]);
        return $sent
            ? back()->with('success', 'Demande envoyée à la passerelle SMS.')
            : back()->withErrors(['access' => 'La passerelle SMS n’a pas confirmé l’envoi.']);
    }

}
