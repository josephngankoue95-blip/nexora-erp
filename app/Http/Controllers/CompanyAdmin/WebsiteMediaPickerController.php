<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class WebsiteMediaPickerController extends Controller
{
    public function index(): JsonResponse
    {
        $companyId = auth()->user()?->company_id;
        abort_unless($companyId, 403);

        $items = WebsiteMedia::query()
            ->where('company_id', $companyId)
            ->latest()
            ->limit(60)
            ->get(['id', 'original_name', 'path']);

        return response()->json([
            'images' => $items->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->original_name,
                'url' => url(Storage::disk('public')->url($item->path)),
            ])->values(),
        ]);
    }
}
