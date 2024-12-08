<?php

namespace Tests\Unit;

use App\Models\Action;
use App\Models\Component;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    use RefreshDatabase;

    public function testSearchActions()
    {
        $component = Component::factory()->create();
        $actions = Action::factory()->count(5)->create(['component_id' => $component->id]);

        $response = app('App\Services\Action\ActionService')->search(new \Illuminate\Http\Request([
            'component_id' => $component->id,
            'take' => 5,
        ]));

        $this->assertEquals(5, $response->count());
        $this->assertEquals($actions->pluck('id')->sort()->values(), $response->pluck('id')->sort()->values());
    }

    public function testGetActionById()
    {
        $action = Action::factory()->create();
        $response = app('App\Services\Action\ActionService')->getById($action->id);

        $this->assertNotNull($response);
        $this->assertEquals($action->id, $response->id);
    }

    public function testCreateActionWithImage()
    {
        $component = Component::factory()->create();

        Storage::fake('public');
        $image = UploadedFile::fake()->image('test.jpg');

        $payload = [
            'component_id' => $component->id,
            'name' => 'Test Action',
            'image' => $image,
        ];

        $response = app('App\Services\Action\ActionService')->create(new \Illuminate\Http\Request($payload));

        $this->assertTrue($response['status']);

        $createdAction = Action::where('name', 'Test Action')->first();
        $this->assertNotNull($createdAction);
        $this->assertEquals($component->id, $createdAction->component_id);

        $storedImagePath = 'storage/images/' . $image->hashName();
        $this->assertTrue(Storage::disk('public')->exists(str_replace('storage/', '', $storedImagePath)));

        $this->assertEquals($storedImagePath, $createdAction->image);
    }

    public function testUpdateActionWithImage()
    {
        $component = Component::factory()->create();
        $action = Action::factory()->create(['component_id' => $component->id]);

        Storage::fake('public');
        $newImage = UploadedFile::fake()->image('new_test.jpg');

        $payload = [
            'name' => 'Updated Action',
            'image' => $newImage,
        ];

        $response = app('App\Services\Action\ActionService')->update(new \Illuminate\Http\Request($payload), $action->id);

        $this->assertTrue($response['status']);

        $updatedAction = Action::find($action->id);
        $this->assertEquals('Updated Action', $updatedAction->name);

        $storedImagePath = 'storage/images/' . $newImage->hashName();
        $this->assertTrue(Storage::disk('public')->exists(str_replace('storage/', '', $storedImagePath)));
        $this->assertEquals($storedImagePath, $updatedAction->image);
    }

    public function testDeleteAction()
    {
        $action = Action::factory()->create();

        $response = app('App\Services\Action\ActionService')->delete($action->id);

        $this->assertTrue($response['status']);

        $deletedAction = Action::find($action->id);
        $this->assertNull($deletedAction);
    }
}
