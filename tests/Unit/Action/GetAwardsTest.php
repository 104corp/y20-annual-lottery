<?php

namespace Tests\Unit\Action;

use App\Actions\GetAwards;
use App\Actions\Init;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAwardsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Init::run(['type' => 'test']);
    }

    public function testInsertDataSuccess()
    {
        // act
        $actual = GetAwards::run();

        // assert
        $this->assertDatabaseHas(
            'awards',
            [
                'id' => 1,
            ]
        );
        $this->assertDatabaseCount('awards', $actual->count());
    }
}
