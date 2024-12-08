<?php

namespace Tests\Unit;

use App\Models\Component;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComponentTest extends TestCase
{
    public function testSearchComponents()
    {
        $facility = Facility::factory()->create([
            'name' => 'Old Name',
            'address' => 'Old Address',
        ]);

        $components = Component::factory()->count(5)->create(['facility_id' => $facility->id]);

        $response = app('App\Services\Component\ComponentService')->search(new \Illuminate\Http\Request([
            'facility_id' => $facility->id,
            'take' => 5,
        ]));

        $this->assertEquals(5, $response->count());
        $this->assertEquals($components->pluck('id')->sort()->values(), $response->pluck('id')->sort()->values());
    }

    public function testGetComponentById()
    {
        $component = Component::factory()->create();
        $response = app('App\Services\Component\ComponentService')->getById($component->id);

        $this->assertNotNull($response);
        $this->assertEquals($component->id, $response->id);
    }

    public function testCreateComponentWithImage()
    {
        $facility = Facility::factory()->create([
            'name' => 'Old Name',
            'address' => 'Old Address',
        ]);

        Storage::fake('public');
        $image = UploadedFile::fake()->image('test.jpg');

        $payload = [
            'facility_id' => $facility->id,
            'name' => 'Test Component',
        ];
        
        $request = Request::create('/', 'POST', $payload);
        $request->files->set('image', $image);
        
        $response = app('App\Services\Component\ComponentService')->create($request);
        $this->assertTrue($response['status']);

        $createdComponent = $response['data'];
        $this->assertNotNull($createdComponent);

        $storedImagePath = 'storage/images/' . $image->hashName();
        $this->assertEquals($storedImagePath, $createdComponent->image);
    }

    public function testUpdateComponentWithImage()
    {
        $facility = Facility::factory()->create([
            'name' => 'Old Name',
            'address' => 'Old Address',
        ]);

        $component = Component::factory()->create(['facility_id' => $facility->id]);

        Storage::fake('public');
        $newImage = UploadedFile::fake()->image('new_test.jpg');

        $payload = [
            'name' => 'Updated Component',
            'image' => $newImage,
            'facility_id' => $facility->id,
        ];

        $response = app('App\Services\Component\ComponentService')->update(new \Illuminate\Http\Request($payload), $component->id);

        $this->assertTrue($response['status']);

        $updatedComponent = $response['data'];
        $this->assertEquals('Updated Component', $updatedComponent->name);
    }

    public function testDeleteComponent()
    {
        $facility = Facility::factory()->create([
            'name' => 'Old Name',
            'address' => 'Old Address',
        ]);

        $component = Component::factory()->create(['facility_id' => $facility->id]);

        $response = app('App\Services\Component\ComponentService')->delete($component->id);

        $this->assertTrue($response['status']);

        $deletedComponent = Component::find($component->id);
        $this->assertNull($deletedComponent);
    }
}
