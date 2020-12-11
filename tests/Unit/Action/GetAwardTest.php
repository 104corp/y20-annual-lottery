<?php

namespace Tests\Unit\Action;

use App\Actions\GetAward;
use App\Actions\Init;
use App\Exceptions\Model\ResourceErrorException;
use App\Model\Award;
use App\Model\Candidate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Tests\TestCase;

class GetAwardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        (new Init(['type' => 'test']))->run();
    }

    public function testGetCertainWinnerSuccess()
    {
        // arrange
        $candidate = Candidate::find(1);
        $candidate->award_id = 1;
        $candidate->save();
        $candidate->award->decrement('number');

        // act
        $actual = (new GetAward(['awardName' => '一獎']))->run();

        // assert
        $this->assertInstanceOf(Award::class, $actual);
        $this->assertEquals('一獎', $actual->name);
    }

    public function testGetAwardFailedBecauseThereIsNoCorrespondingAward()
    {
        // expect exception
        $this->expectExceptionMessage('找不到對應的獎項！');
        $this->expectException(ResourceNotFoundException::class);

        // act
        (new GetAward(['awardName' => 'Obama']))->run();
    }

    public function testGetAwardFailedBecauseThereIsNoWinnerForThisAward()
    {
        // expect exception
        $this->expectExceptionMessage('目前此獎項尚未有中獎者！');
        $this->expectException(ResourceErrorException::class);

        // act
        (new GetAward(['awardName' => '一獎']))->run();
    }
}
