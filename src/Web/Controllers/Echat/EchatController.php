<?php

namespace src\Web\Controllers\Echat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class EchatController extends Controller
{
    public function __construct(


    )
    {
    }

    public function getSenders()
    {
        $result = DB::table('echat_api_keys')->get();
        return response()->json($result);
    }
    public function createSender(Request $request)
    {
        $id = DB::table('echat_api_keys')->insert([
            'sender' => $request->input('sender'),
            'service' => $request->input('service'),
            'api_key' => $request->input('apikey')
        ]);
        return response()->json(['id' => $id, 'message' => 'Sender added successfully']);
    }

    public function deleteSender(Request $request)
    {
        $deleted = DB::table('echat_api_keys')->where('id', $request->input('id'))->delete();

        if ($deleted) {
            return response()->json(['message' => 'Sender deleted successfully']);
        }
        return response()->json(['message' => 'Sender not found'], 404);
    }
}
