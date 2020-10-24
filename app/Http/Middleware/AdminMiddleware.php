<?php

namespace App\Http\Middleware;

use Closure;
use App\Repositories\AdminUserRepository;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try{
            $repository = new AdminUserRepository();
            if(!$repository->user(auth()->user())->hasAdminPriviledge()){
                return response()->json(["message" => "Unauthorized"], 403);
            }
            
        }catch(\Exception $e){
            return response()->json(["message" => "Unauthenticated"], 401);
        }

        return $next($request);
    }
}
