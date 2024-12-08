<?php

namespace Tests\Unit;

use App\Models\Facility;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FacilityTest extends TestCase
{
    public function testCreateFacilityWithAllFields()
    {
        $payload = [
            'name' => 'Test Facility',
            'user_id' => 1,
            'number' => '12345',
            'used' => 'Yes',
            'size' => '100m2',
            'unity' => 'Unit A',
            'report_last_update' => '2023-12-01',
            'consultant_name' => 'Consultant Test',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'region' => 'Test Region',
            'country' => 'Test Country',
            'zip_code' => '12345',
            'year_installed' => '2000',
            'replacement_cost' => '10000',
            'description' => 'Test Description',
            'images' => [
                UploadedFile::fake()->image('test1.jpg'),
                UploadedFile::fake()->image('test2.jpg'),
            ],
        ];

        $response = app('App\Services\Facility\FacilityService')->create(new \Illuminate\Http\Request($payload));
        $this->assertTrue($response['status']);
        $this->assertDatabaseHas('facilities', ['name' => 'Test Facility']);
        $this->assertDatabaseHas('facility_images', ['filename' => 'test1.jpg']);
    }

    public function testCreateFacilityWithPartialFields()
    {
        $payload = [
            'name' => 'Partial Facility',
            'user_id' => 1,
            'number' => '67890',
        ];

        $response = app('App\Services\Facility\FacilityService')->create(new \Illuminate\Http\Request($payload));
        $this->assertTrue($response['status']);
        $this->assertDatabaseHas('facilities', ['name' => 'Partial Facility']);
    }

    public function testUpdateFacilityWithSomeFields()
    {
        $facility = Facility::factory()->create([
            'name' => 'Old Name',
            'address' => 'Old Address',
        ]);

        $payload = [
            'name' => 'Updated Name',
            'address' => 'Updated Address',
        ];

        $response = app('App\Services\Facility\FacilityService')->update(new \Illuminate\Http\Request($payload), $facility->id);
        $this->assertTrue($response['status']);
        $this->assertDatabaseHas('facilities', ['name' => 'Updated Name']);
        // $this->assertDatabaseMissing('facilities', ['name' => 'Old Name']);
    }

    public function testDeleteFacility()
    {
        $facility = Facility::factory()->create([
            'name' => 'Facility to Delete',
        ]);

        $response = app('App\Services\Facility\FacilityService')->delete($facility->id);
        $this->assertTrue($response['status']);
        $this->assertEquals($response['data'], 'Facility to Delete');
    }
}
