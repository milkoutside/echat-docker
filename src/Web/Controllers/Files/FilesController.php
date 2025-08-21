<?php

namespace src\Web\Controllers\Files;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    public function __construct(

    )
    {
    }

    public function createFileForClient(Request $request)
    {
        // Получение данных из запроса
        $clientId = $request->input('client_id'); // Предполагается, что ID клиента будет передан
        $file = $request->file('file'); // Загружаемый файл

        // Проверка наличия файла
        if (!$file) {
            return response()->json(['error' => 'Файл не найден'], 400);
        }

        // Установим папку для клиента в public
        $folderName = "client_{$clientId}";

        // Проверяем, существует ли папка, и создаем её, если нужно
        if (!Storage::disk('public')->exists($folderName)) {
            Storage::disk('public')->makeDirectory($folderName);
        }

        // Сохраняем файл в папку public/client_{$clientId}
        $path = $file->storeAs($folderName, $file->getClientOriginalName(), 'public');

        // Возвращаем успешный ответ с публичной ссылкой
        return response()->json([
            'message' => 'Файл успешно загружен',
            'url' => Storage::url($path), // Public URL для файла
            'path' => $path, // Относительный путь к файлу
        ]);
    }



}
