<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     *  Handle an incoming request.
     * this is just a middleware to handle CORS requests from external applications, it just permits only authorized domains
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     **/
    public function handle($request, Closure $next)
    {
        $domains = [
            'http://127.0.0.1:3000',
            'http://localhost:3000/',
            'http://localhost:3000',
            'http://localhost:8000/',
//            'http://178.128.179.130/',
//            'http://178.128.179.130',
//            'https://178.128.179.130/',
//            'https://178.128.179.130',
            'https://rklinic-test.rkanjel.com/',
            'https://rklinic-test.rkanjel.com',
            'https://seena-app.com',
            'https://seena-app.com/',
            'http://seena-app.com',
            'http://seena-app.com/',
            'https://rklinic.com',
            'https://rklinic.com/',
            'http://rklinic.com',
            'http://rklinic.com/',
            'https://www.seena-app.com/',
            'https://www.seena-app.com',
        ];

        if (isset($request->server()['HTTP_ORIGIN'])) {
            $origin = $request->server()['HTTP_ORIGIN'];
            if (in_array($origin, $domains)) {
                return $next($request)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization, Client-Security-Token, Accept-Encoding, Lang,lang')
                    ->header('Allow', 'GET, POST, PUT, DELETE, OPTIONS')
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            }
        }
        return $next($request);
    }
}
