<?php

namespace Tests\Unit\Action;

use App\Actions\GetCandidates;
use App\Actions\Init;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetCandidatesTest extends TestCase
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
        $actual = GetCandidates::run();

        // assert
        $this->assertDatabaseHas(
            'candidates',
            [
                'id' => 1,
            ]
        );
        $this->assertDatabaseCount('candidates', $actual->count());
    }
}
