<?php

namespace src\Web\Controllers\Inbox;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function __construct()
    {
    }

    public function getLastMessageWithClients(Request $request)
    {
        $channel = $request->input('channel');
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);

        // Подзапрос для получения времени последнего сообщения
        $latestMessageSubquery = DB::table('messages')
            ->select('client_id', DB::raw('MAX(send_time) as latest_send_time'))
            ->when($channel !== 'all', function ($query) use ($channel) {
                $query->where('channel', $channel);
            })
            ->groupBy('client_id');

        // Подзапрос для подсчета непрочитанных сообщений
        $unreadMessagesSubquery = DB::table('messages')
            ->select('client_id', DB::raw('COUNT(*) as unread_messages_count'))
            ->where('message_read', false)
            ->when($channel !== 'all', function ($query) use ($channel) {
                $query->where('channel', $channel);
            })
            ->groupBy('client_id');

        // Основной запрос
        $clients = DB::table('clients')
            ->leftJoinSub($latestMessageSubquery, 'latest_messages', function ($join) {
                $join->on('clients.id', '=', 'latest_messages.client_id');
            })
            ->leftJoinSub($unreadMessagesSubquery, 'unread_messages', function ($join) {
                $join->on('clients.id', '=', 'unread_messages.client_id');
            })
            ->leftJoin('messages', function ($join) use ($channel) {
                $join->on('messages.client_id', '=', 'clients.id')
                    ->when($channel !== 'all', function ($query) use ($channel) {
                        $query->where('messages.channel', $channel);
                    })
                    ->whereColumn('messages.send_time', '=', 'latest_messages.latest_send_time');
            })
            ->when($channel !== 'all', function ($query) {
                $query->whereNotNull('messages.id');
            })
            ->select(
                'clients.*',
                'messages.text as last_message_text',
                'messages.fileUrl as last_message_fileUrl',
                'messages.channel as last_message_channel',
                'messages.send_time as last_message_time',
                'messages.sender as last_sender',
                DB::raw('COALESCE(unread_messages.unread_messages_count, 0) as unread_messages_count'),
                DB::raw('CASE WHEN messages.id IS NOT NULL THEN 1 ELSE 0 END as has_messages')
            )
            ->orderByDesc('has_messages')
            ->orderByDesc('unread_messages_count')
            ->orderBy('clients.id')
            ->paginate($perPage, ['*'], 'page', $page);

        // Преобразуем phones из JSON строки в массив для каждого клиента
        $clients->getCollection()->transform(function ($client) {
            $client->phones = json_decode($client->phones, true) ?? [];
            return $client;
        });

        return response()->json($clients);
    }

    public function findClient(Request $request)
    {
        $channel = 'all';
        $phone = $request->input('phone');
        $username = $request->input('username');

        // Подзапрос для получения времени последнего сообщения
        $latestMessageSubquery = DB::table('messages')
            ->select('client_id', DB::raw('MAX(send_time) as latest_send_time'))
            ->when($channel !== 'all', function ($query) use ($channel) {
                $query->where('channel', $channel);
            })
            ->groupBy('client_id');

        // Подзапрос для подсчета непрочитанных сообщений
        $unreadMessagesSubquery = DB::table('messages')
            ->select('client_id', DB::raw('COUNT(*) as unread_messages_count'))
            ->where('message_read', false)
            ->when($channel !== 'all', function ($query) use ($channel) {
                $query->where('channel', $channel);
            })
            ->groupBy('client_id');

        // Основной запрос
        $query = DB::table('clients')
            ->leftJoinSub($latestMessageSubquery, 'latest_messages', function ($join) {
                $join->on('clients.id', '=', 'latest_messages.client_id');
            })
            ->leftJoinSub($unreadMessagesSubquery, 'unread_messages', function ($join) {
                $join->on('clients.id', '=', 'unread_messages.client_id');
            })
            ->leftJoin('messages', function ($join) use ($channel) {
                $join->on('messages.client_id', '=', 'clients.id')
                    ->when($channel !== 'all', function ($query) use ($channel) {
                        $query->where('messages.channel', $channel);
                    })
                    ->whereColumn('messages.send_time', '=', 'latest_messages.latest_send_time');
            })
            ->when($channel !== 'all', function ($query) {
                $query->whereNotNull('messages.id');
            });

        // Условия поиска
        $query->where(function ($q) use ($username, $phone) {
            if ($username) {
                $q->where('clients.username', 'like', '%' . $username . '%');
            }
            if ($phone) {
                $q->orWhereJsonContains('clients.phones', $phone)
                    ->orWhereJsonContains('clients.phones->main', $phone)
                    ->orWhereJsonContains('clients.phones->viber', $phone)
                    ->orWhereJsonContains('clients.phones->telegram', $phone);
            }
        });

        $client = $query->select(
            'clients.*',
            'messages.text as last_message_text',
            'messages.fileUrl as last_message_fileUrl',
            'messages.channel as last_message_channel',
            'messages.send_time as last_message_time',
            'messages.sender as last_sender',
            DB::raw('COALESCE(unread_messages.unread_messages_count, 0) as unread_messages_count'),
            DB::raw('CASE WHEN messages.id IS NOT NULL THEN 1 ELSE 0 END as has_messages')
        )
            ->orderByDesc('has_messages')
            ->orderByDesc('unread_messages_count')
            ->first();

        if (!$client) {
            return response()->json(['message' => 'Клиент не найден'], 404);
        }

        // Преобразуем phones из JSON строки в массив
        $client->phones = json_decode($client->phones, true) ?? [];

        return response()->json($client);
    }
}
