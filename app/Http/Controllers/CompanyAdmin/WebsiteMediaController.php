<?php
namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteMedia;
use App\Models\WebsitePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebsiteMediaController extends Controller
{
    private function companyId(): int
    {
        $id = auth()->user()?->company_id;
        abort_unless($id, 403, 'Aucune entreprise associée à cet utilisateur.');
        return (int) $id;
    }

    public function index(Request $request)
    {
        $media = WebsiteMedia::query()
            ->where('company_id', $this->companyId())
            ->latest()
            ->paginate(18);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $media->getCollection()->map(fn (WebsiteMedia $item) => $this->payload($item))->values(),
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
                'next_page_url' => $media->nextPageUrl(),
            ]);
        }

        return view('company.website.media.index', compact('media'));
    }

    public function store(Request $request)
    {
        $companyId = $this->companyId();
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);
        $file = $request->file('image');
        $path = $file->store('website/' . $companyId, 'public');
        if (!$path) {
            return $this->failure($request, "Impossible d'enregistrer l'image.");
        }
        try {
            $media = WebsiteMedia::create([
                'company_id' => $companyId,
                'original_name' => Str::limit($file->getClientOriginalName(), 100, ''),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);
            throw $e;
        }
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Image ajoutée.', 'data' => $this->payload($media)], 201);
        }
        return back()->with('success', 'Image ajoutée à la médiathèque avec succès.');
    }

    public function destroy(Request $request, int $media)
    {
        $companyId = $this->companyId();
        $item = WebsiteMedia::query()->where('company_id', $companyId)->findOrFail($media);
        $path = $item->path;
        $url = $item->url;
        // Chercher aussi dans builder_content, pas seulement dans content.
        // LIKE est conservateur : une correspondance empêche la suppression.
        $used = WebsitePage::query()->where('company_id', $companyId)
            ->where(function ($query) use ($path, $url) {
                foreach ([$path, $url, '/storage/' . $path] as $needle) {
                    $needle = addcslashes($needle, '%_\\');
                    $query->orWhere('content', 'like', '%' . $needle . '%')
                          ->orWhere('builder_content', 'like', '%' . $needle . '%');
                }
            })->exists();
        if ($used) {
            return $this->failure($request, 'Cette image est utilisée dans une page. Retirez-la avant suppression.');
        }
        // On conserve l'enregistrement si la suppression du fichier échoue.
        if (Storage::disk('public')->exists($path) && !Storage::disk('public')->delete($path)) {
            return $this->failure($request, 'Impossible de supprimer le fichier.');
        }
        $item->delete();
        return $request->expectsJson()
            ? response()->json(['message' => 'Image supprimée.'])
            : back()->with('success', 'Image supprimée avec succès.');
    }

    private function payload(WebsiteMedia $item): array
    {
        return [
            'id' => $item->id,
            'original_name' => $item->original_name,
            'url' => $item->url,
            'mime_type' => $item->mime_type,
            'size' => $item->size,
        ];
    }

    private function failure(Request $request, string $message)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $message], 422)
            : back()->withErrors(['image' => $message]);
    }
}
