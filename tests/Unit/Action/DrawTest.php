<?php

namespace Tests\Unit\Action;

use App\Actions\Draw;
use App\Actions\Init;
use App\Exceptions\Model\ResourceErrorException;
use App\Model\Candidate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Tests\TestCase;

class DrawTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        (new Init(['type' => 'test']))->run();
    }

    public function testDrawOneCandidateSuccess()
    {
        // act
        $actual = (new Draw())->run([
            'awardName' => '一獎',
            'candidateNumber' => 1,
        ]);

        // assert
        $this->assertInstanceOf(Collection::class, $actual);
        $this->assertInstanceOf(Candidate::class, $actual->first());
        $this->assertDatabaseHas(
            'candidates',
            [
                'award_id' => 1,
            ]
        );
    }

    public function testDrawMultipleCandidatesSuccess()
    {
        // act
        $actual = (new Draw())->run([
            'awardName' => '三獎',
            'candidateNumber' => 3,
        ]);

        // assert
        $this->assertInstanceOf(Collection::class, $actual);
        $this->assertInstanceOf(Candidate::class, $actual->first());
        $this->assertCount(3, $actual->toArray());
        $this->assertDatabaseHas(
            'candidates',
            [
                'award_id' => 3,
            ]
        );
    }

    public function testDrawMultipleCandidatesFailed()
    {
        // expect exception
        $this->expectExceptionMessage('此獎項剩餘數量不足，抽不出那麼多人！');
        $this->expectException(ResourceErrorException::class);

        // act
        (new Draw())->run([
            'awardName' => '一獎',
            'candidateNumber' => 3,
        ]);
    }

    public function testDrawNotFoundCorrespondingAward()
    {
        // expect exception
        $this->expectExceptionMessage('找不到對應的獎項！');
        $this->expectException(ResourceNotFoundException::class);

        // act
        (new Draw())->run([
            'awardName' => '獎獎獎獎',
            'candidateNumber' => 3,
        ]);
    }

    public function testDrawFailedBecauseEveryCandidateHasAward()
    {
        // expect exception
        $this->expectExceptionMessage('參加者都已有獎項！');
        $this->expectException(ResourceErrorException::class);

        // arrange
        $this->createFakeWinners();

        // act
        (new Draw())->run([
            'awardName' => '一獎',
            'candidateNumber' => 1,
        ]);
    }

    private function createFakeWinners()
    {
        Candidate::notWinners()->update(['award_id' => 2]);
    }
}
