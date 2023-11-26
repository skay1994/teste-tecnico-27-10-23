<?php

namespace App\Http\Controllers;

use Cache;
use Http;
use Illuminate\Http\Request;

class ViaCepController extends Controller
{
    public function __invoke(Request $request)
    {
        $cep = $request->input('cep');
        $cep = preg_replace('/\D/', '', $cep);
        $key = sprintf("cep-%s", $cep);

        if(!$cep) {
            return response()->json([
                'message' => 'Cep inválido'
            ], 400);
        }

        if(Cache::has($key)) {
            return response()->json(Cache::get($key));
        }

        try {
            $body = Http::get("https://viacep.com.br/ws/$cep/json/")->json();

            if(!$body || isset($body['erro'])) {
                return response()->json([
                    'message' => 'Cep inválido'
                ], 400);
            }

            Cache::put($key, $body);

            return response()->json($body);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Cep inválido'
            ], 400);
        }
    }
}
