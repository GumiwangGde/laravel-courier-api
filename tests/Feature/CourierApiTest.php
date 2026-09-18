<?php

use App\Models\Courier;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

test('it can list couriers with pagination and default sorting by name ascending', function () {
    Courier::factory()->create(['name' => 'Charlie Pratama']);
    Courier::factory()->create(['name' => 'Alice Septiani']);
    Courier::factory()->create(['name' => 'Bob Santoso']);

    $response = getJson('/api/couriers');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'phone', 'email', 'level', 'is_active', 'registered_at'],
            ],
            'links',
            'meta' => ['current_page', 'per_page', 'total'],
        ]);

    $names = collect($response->json('data'))->pluck('name')->toArray();
    expect($names)->toBe(['Alice Septiani', 'Bob Santoso', 'Charlie Pratama']);
});

test('it can override default sorting to sort by registered_at date', function () {
    Courier::factory()->create(['name' => 'Kurir Lama', 'registered_at' => Carbon::now()->subDays(10)]);
    Courier::factory()->create(['name' => 'Kurir Baru', 'registered_at' => Carbon::now()->subDay()]);
    Courier::factory()->create(['name' => 'Kurir Sedang', 'registered_at' => Carbon::now()->subDays(5)]);

    $response = getJson('/api/couriers?sort_by=registered_at&order=desc');

    $response->assertOk();
    $names = collect($response->json('data'))->pluck('name')->toArray();
    expect($names)->toBe(['Kurir Baru', 'Kurir Sedang', 'Kurir Lama']);
});

test('it matches multi-keyword search such as budi agung to Budiono Hadi Agung', function () {
    $targetCourier = Courier::factory()->create(['name' => 'Budiono Hadi Agung']);
    $otherCourier = Courier::factory()->create(['name' => 'Siti Rahmawati']);

    $response = getJson('/api/couriers?search=budi+agung');

    $response->assertOk();
    $names = collect($response->json('data'))->pluck('name');

    expect($names)->toContain($targetCourier->name)
        ->and($names)->not->toContain($otherCourier->name);
});

test('it can filter couriers by comma-separated levels like 2,3', function () {
    Courier::factory()->create(['name' => 'Kurir Level 1', 'level' => 1]);
    Courier::factory()->create(['name' => 'Kurir Level 2', 'level' => 2]);
    Courier::factory()->create(['name' => 'Kurir Level 3', 'level' => 3]);
    Courier::factory()->create(['name' => 'Kurir Level 4', 'level' => 4]);

    $response = getJson('/api/couriers?level=2,3');

    $response->assertOk();
    $levels = collect($response->json('data'))->pluck('level')->unique()->values()->toArray();
    sort($levels);

    expect($levels)->toBe([2, 3]);
});

test('it returns all data for a single courier on show endpoint', function () {
    $courier = Courier::factory()->create([
        'name' => 'Ahmad Dahlan',
        'level' => 3,
        'phone' => '081234567890',
        'email' => 'ahmad@example.com',
    ]);

    $response = getJson('/api/couriers/'.$courier->id);

    $response->assertOk()
        ->assertJson([
            'data' => [
                'id' => $courier->id,
                'name' => 'Ahmad Dahlan',
                'level' => 3,
                'phone' => '081234567890',
                'email' => 'ahmad@example.com',
                'is_active' => true,
            ],
        ]);
});

test('it returns 404 when showing non-existent courier', function () {
    $response = getJson('/api/couriers/99999');

    $response->assertNotFound()
        ->assertJson([
            'message' => 'Courier not found.',
        ]);
});

test('it validates and stores a courier in database', function () {
    $payload = [
        'name' => 'Rian Hidayat',
        'phone' => '089876543210',
        'email' => 'rian@example.com',
        'level' => 4,
        'is_active' => true,
        'registered_at' => now()->format('Y-m-d H:i:s'),
    ];

    $response = postJson('/api/couriers', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Rian Hidayat')
        ->assertJsonPath('data.level', 4);

    assertDatabaseHas('couriers', [
        'phone' => '089876543210',
        'name' => 'Rian Hidayat',
    ]);
});

test('it fails validation when storing courier with level outside 1-5', function () {
    $payload = [
        'name' => 'Kurir Invalid',
        'phone' => '081111111111',
        'level' => 6,
    ];

    $response = postJson('/api/couriers', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['level']);
});

test('it validates and updates an existing courier in database', function () {
    $courier = Courier::factory()->create([
        'name' => 'Nama Awal',
        'level' => 1,
    ]);

    $response = putJson('/api/couriers/'.$courier->id, [
        'name' => 'Nama Baru Diupdate',
        'level' => 5,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Nama Baru Diupdate')
        ->assertJsonPath('data.level', 5);

    assertDatabaseHas('couriers', [
        'id' => $courier->id,
        'name' => 'Nama Baru Diupdate',
        'level' => 5,
    ]);
});

test('it deletes a courier and confirms removal from database', function () {
    $courier = Courier::factory()->create();

    $response = deleteJson('/api/couriers/'.$courier->id);

    $response->assertOk()
        ->assertJson(['message' => 'Courier deleted successfully.']);

    assertDatabaseMissing('couriers', [
        'id' => $courier->id,
    ]);
});
