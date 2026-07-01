<?php

namespace App\Http\Controllers;

use App\Models\Click;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class RedirectController extends Controller
{
    public function __invoke(Request $request, string $code): RedirectResponse
    {
        $link = Link::where('code', $code)->firstOrFail();

        // Фиксируем переход. increment() атомарен на уровне БД.
        Click::create([
            'link_id'    => $link->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer'    => $request->headers->get('referer'),
            'created_at' => now(),
        ]);

        $link->increment('clicks_count');

        return redirect()->away($link->original_url, 302);
    }
}
