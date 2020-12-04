<?php

namespace Tests\Unit\Action;

use App\Actions\GetAllWinners;
use App\Actions\Init;
use App\Exceptions\Model\ResourceErrorException;
use App\Model\Award;
use App\Model\Candidate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class GetAllWinnersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        (new Init(['type' => 'test']))->run();
    }

    public function testGetAllWinnersSuccess()
    {
        // arrange
        $this->arrangeFakeWinners();

        // act
        $actual = (new GetAllWinners())->run();

        // assert
        $this->assertInstanceOf(Collection::class, $actual);
        $this->assertInstanceOf(Award::class, $actual->first());
        $this->assertLessThanOrEqual(4, $actual->count());
    }

    public function testGetAllWinnersFailedBecauseNoWinners()
    {
        // expect exception
        $this->expectExceptionMessage('目前沒有得獎者！');
        $this->expectException(ResourceErrorException::class);

        // act
        (new GetAllWinners())->run();
    }

    /**
     * 創造四位得獎者（得獎獎項有可能相同）
     */
    private function arrangeFakeWinners()
    {
        $candidates = Candidate::find([1, 2, 3, 4]);
        $candidates->each(function ($candidate) {
            $candidate->award_id = rand(1, 4);
            $candidate->save();
            $candidate->award->decrement('number');
        });
    }
}
