<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistributorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('distributor')->check()) {
            return redirect()->route('home');
        }
        
        $distributor = Auth::guard('distributor')->user();
        
        // Verificar se o distribuidor está ativo
        if (!$distributor || $distributor->status != 1) {
            Auth::guard('distributor')->logout();
            return redirect()->route('home')->withErrors(['Distribuidor inativo ou não encontrado.']);
        }
        
        return $next($request);
    }
}