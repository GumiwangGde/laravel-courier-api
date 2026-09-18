<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourierRequest;
use App\Http\Requests\UpdateCourierRequest;
use App\Http\Resources\CourierResource;
use App\Models\Courier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CourierController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Courier::query()
            ->search($request->query('search'))
            ->filterByLevel($request->query('level'));
        $sortBy = $request->query('sort_by', $request->query('sort', 'name'));
        $direction = strtolower($request->query('order', $request->query('direction', 'asc')));
        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        $allowedSorts = ['name', 'registered_at', 'created_at', 'level', 'id'];
        if (in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $direction);
        } else {
            $query->orderBy('name', 'asc');
        }

        $perPage = $request->integer('per_page', 10);
        $couriers = $query->paginate($perPage);

        return CourierResource::collection($couriers);
    }

    public function store(StoreCourierRequest $request): JsonResponse
    {
        $courier = Courier::create($request->validated());

        return (new CourierResource($courier))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Courier $courier): CourierResource
    {
        return new CourierResource($courier);
    }

    public function update(UpdateCourierRequest $request, Courier $courier): CourierResource
    {
        $courier->update($request->validated());

        return new CourierResource($courier);
    }

    public function destroy(Courier $courier): JsonResponse
    {
        $courier->delete();

        return response()->json([
            'message' => 'Courier deleted successfully.',
        ], 200);
    }
}
