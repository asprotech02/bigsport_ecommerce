<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAdminFromFrontend
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'sales', 'manager'])) {
            // Redirect admin, sales, and manager roles to the admin dashboard when they access the frontend
            if (!$request->is('admin*') && 
                !$request->is('api*') && 
                !$request->is('logout') && 
                !$request->is('biteship*') && 
                !$request->is('midtrans*')) {
                
                return redirect()->route('admin.dashboard');
            }
        }

        return $next($request);
    }
}
