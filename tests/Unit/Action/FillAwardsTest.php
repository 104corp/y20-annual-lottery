<?php

namespace Tests\Unit\Action;

use App\Actions\FillAwards;
use App\Actions\Init;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FillAwardsTest extends TestCase
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
        $actual = FillAwards::run();

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
