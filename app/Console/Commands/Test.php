<?php

namespace App\Console\Commands;

use App\Events\InboxMessageEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use src\Domain\Entities\Messages\Messages;
use src\Infrastructure\External\CRM\ZohoCRM\ZohoCRMApi;
use src\Infrastructure\External\Echat\EchatApi;
use src\Infrastructure\Helpers\Logger\Logger;
use src\Infrastructure\Repositories\ClientsRepository\ClientsRepository;

class Test extends Command
{
    use Logger;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        dd(Broadcast(new InboxMessageEvent(new Messages('3809999999','tes','','','124','success','','2','incoming',false))));

        dd(  $phoneResults = ZohoCRMApi::getInstance()
            ->api()
            ->records()
            ->searchRecords('Leads', 'phone', '380508139234'));
        $clientRepository = new ClientsRepository();
        $phoneNumber = '097697953783';
        $telegramNickname = 'sj_saha';
        $dbClient = $clientRepository->getClientByPhoneOrUsername('','sj_saha');
        if(empty($dbClient)){
            $modulesToSearch = ['Contacts', 'Leads'];

// Упрощаем логику проверки
            $findClient = false;

            foreach ($modulesToSearch as $module) {
                // Поиск по телефону
                $phoneResults = ZohoCRMApi::getInstance()
                    ->api()
                    ->records()
                    ->searchRecords($module, 'phone', $phoneNumber);

                // Поиск по Telegram никнейму (унифицированный формат запроса)
                $usernameResults = ZohoCRMApi::getInstance()
                    ->api()
                    ->records()
                    ->searchRecords(
                        $module,
                        'criteria',
                        "Telegram_nickname:equals:$telegramNickname"
                    );

                // Проверяем наличие результатов в любом из поисков
                if (!empty($phoneResults[0]) || !empty($usernameResults[0])) {
                    $findClient = true;
                    break; // Прерываем цикл при нахождении совпадения
                }
            }
        }
        if (!$findClient){
            ZohoCRMApi::getInstance()->api()->records()->createRecord('Leads', [
                "data"=>[
                    [
                        "Last_Name" => !empty($phoneNumber) ? $phoneNumber : $telegramNickname,
                        "Lead_Source" => "Telegram",
                        "Source_details" => "Джерело комент",
                        "Phone" => $phoneNumber,
                        "Telegram_nickname" => $telegramNickname,
                    ]
                ],
                "trigger" => [
                    "workflow",
                    "blueprint",
                    "approval"
                ]
            ]);
        }
        dd($clientRepository->getClientByPhoneOrUsername('','sj_saha'));
        dd(ZohoCRMApi::getInstance()->api()->records()->searchRecords('Contacts','phone','097697953783'));
        $channel = 'telegram';
        $perPage = 10;
        $page = 1;
        $latestMessageSubquery = DB::table('messages')
            ->select('client_id', DB::raw('MAX(send_time) as latest_send_time'))
            ->when($channel !== 'all', function ($query) use ($channel) {
                $query->where('channel', $channel); // Фильтруем по каналу, если не 'all'
            })
            ->groupBy('client_id');

        // Подзапрос для подсчета непрочитанных сообщений
        $unreadMessagesSubquery = DB::table('messages')
            ->select('client_id', DB::raw('COUNT(*) as unread_messages_count'))
            ->where('message_read', false)
            ->when($channel !== 'all', function ($query) use ($channel) {
                $query->where('channel', $channel); // Фильтруем по каналу, если не 'all'
            })
            ->groupBy('client_id');

        // Основной запрос
        $clients = DB::table('clients')
            ->whereExists(function ($query) use ($channel) {
                $query->select(DB::raw(1))
                    ->from('messages')
                    ->whereColumn('messages.client_id', 'clients.id')
                    ->when($channel !== 'all', function ($query) use ($channel) {
                        $query->where('messages.channel', $channel); // Фильтруем по каналу, если не 'all'
                    });
            })
            ->leftJoinSub($latestMessageSubquery, 'latest_messages', function ($join) {
                $join->on('clients.id', '=', 'latest_messages.client_id');
            })
            ->leftJoinSub($unreadMessagesSubquery, 'unread_messages', function ($join) {
                $join->on('clients.id', '=', 'unread_messages.client_id');
            })
            ->leftJoin('messages', function ($join) use ($channel) {
                $join->on('messages.client_id', '=', 'clients.id')
                    ->when($channel !== 'all', function ($query) use ($channel) {
                        $query->where('messages.channel', $channel); // Фильтруем по каналу, если не 'all'
                    })
                    ->whereColumn('messages.send_time', '=', 'latest_messages.latest_send_time');
            })
            ->select(
                'clients.*',
                'messages.text as last_message_text',
                'messages.fileUrl as last_message_fileUrl',
                'messages.channel as last_message_channel', // Добавляем источник последнего сообщения
                'messages.send_time as last_message_time',
                DB::raw('COALESCE(unread_messages.unread_messages_count, 0) as unread_messages_count')
            )
            ->orderByDesc('unread_messages_count')
            ->paginate($perPage, ['*'], 'page', $page);

        dd($clients);
        EchatApi::getInstance()->api()->telegram()->messages()->sendMessage(['test']);
    }
}
