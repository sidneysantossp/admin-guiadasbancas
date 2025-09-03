<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Foundation\Application;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:distributor');
    }

    // Página de configurações do distribuidor
    public function index(): View|Application|RedirectResponse
    {
        // Por ora, redirecionamos para a página de perfil que já existe,
        // até que haja uma view dedicada de configurações do distribuidor.
        return redirect()->route('distributor.profile.view');
    }
}