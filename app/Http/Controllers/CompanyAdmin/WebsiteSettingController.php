<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WebsiteSettingController extends Controller
{
    private function companyId(): int
    {
        $id = auth()->user()?->company_id;
        abort_unless($id, 403);
        return (int) $id;
    }

    public function edit(): View
    {
        $settings = WebsiteSetting::firstOrCreate(['company_id' => $this->companyId()]);
        return view('company.website.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $companyId = $this->companyId();
        $data = $request->validate([
            'primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'secondary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:2000'],
            'footer_text' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'hero' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_hero' => ['nullable', 'boolean'],
        ]);

        $settings = WebsiteSetting::firstOrCreate(['company_id' => $companyId]);
        $updates = collect($data)->only(['primary_color','secondary_color','hero_title','hero_subtitle','footer_text'])->all();
        $newPaths = [];
        $oldPaths = [];

        try {
            foreach (['logo' => 'logo_path', 'hero' => 'hero_path'] as $field => $column) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store("website/{$companyId}/branding", 'public');
                    if (!$path) { throw new \RuntimeException('Échec du stockage de l’image.'); }
                    $newPaths[] = $path;
                    if ($settings->$column) { $oldPaths[] = $settings->$column; }
                    $updates[$column] = $path;
                } elseif ($request->boolean('remove_'.$field)) {
                    if ($settings->$column) { $oldPaths[] = $settings->$column; }
                    $updates[$column] = null;
                }
            }
            $settings->update($updates);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($newPaths);
            throw $e;
        }
        if ($oldPaths) { Storage::disk('public')->delete($oldPaths); }
        return back()->with('success', 'Identité visuelle enregistrée.');
    }
}
