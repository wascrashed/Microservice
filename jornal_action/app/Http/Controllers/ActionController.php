<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActionRequest;
use App\Http\Resources\ActionResource;
use App\Models\Action;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\JsonResponse;

class ActionController extends Controller
{
    /**
     * Получить все записи "Action".
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'actions' => ActionResource::collection(
                Action::query()
                    ->paginate(9)
            )
        ]);
    }
 
    public function store(StoreActionRequest $request)
    {
        dd($request);
        $data = $request->validated();
        $data['date'] = Carbon::createFromTimestamp($data['date'])->toDateTimeString();

        return response()->json([
            'action' => new ActionResource(
                Action::query()
                    ->create([
                        'userId' => auth()->id(),
                        'actionKey' => $data['actionKey'],
                        'date' => $data['date'],
                        'info' => $data['info'],
                        'encrypted_data' => Crypt::encrypt($data),
                    ])
            )
        ]);
    }

    /**
     * Фильтрация записей "Action" по заданным параметрам.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function filter(Request $request): JsonResponse
    {
        $query = Action::query();

        if ($request->filled('encrypted_data')) {

            $query->when(
                $request->filled('dateFrom'),
                fn($query) => $query->where('date', '>=', Carbon::createFromTimestamp($request->input('dateFrom'))->toDateTimeString())
            );
            $query->when(
                $request->filled('dateTo'),
                fn($query) => $query->where('date', '<=', Carbon::createFromTimestamp($request->input('dateTo'))->toDateTimeString())
            );
            $query->when(
                $request->filled('actionKey'),
                fn($query) => $query->where('actionKey', $request->input('actionKey'))
            );
            $query->when(
                $request->filled('info'),
                function ($query) use ($request) {
                    foreach ($request->input('info') as $key => $value) {
                        $query->where('info->' . $key, $value);
                    }
                }
            );
        }

        return response()->json([
            'actions' => ActionResource::collection(
                $query->paginate(9)
            )
        ]);
    }
}
