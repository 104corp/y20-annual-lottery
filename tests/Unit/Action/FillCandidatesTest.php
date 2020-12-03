<?php

namespace Tests\Unit\Action;

use App\Actions\FillCandidates;
use App\Actions\Init;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FillCandidatesTest extends TestCase
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
        $actual = FillCandidates::run();

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
