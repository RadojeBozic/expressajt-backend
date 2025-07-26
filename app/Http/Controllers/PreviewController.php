<?php
namespace App\Http\Controllers;

use App\Models\FreeSiteRequest;
use Illuminate\View\View;

class PreviewController extends Controller
{
    public function show(string $slug): View
    {
        $data = FreeSiteRequest::where('slug', $slug)->firstOrFail();

        return view('preview', ['data' => $data]);
    }
}
