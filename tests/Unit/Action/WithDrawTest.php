<?php

namespace Tests\Unit\Action;

use App\Actions\Init;
use App\Actions\Withdraw;
use App\Exceptions\Model\ResourceErrorException;
use App\Model\Candidate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithDrawTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        (new Init(['type' => 'test']))->run();
    }

    public function testWithdrawFailedBecauseTheCandidateIsNotAWinner()
    {
        // expect exception
        $this->expectExceptionMessage('沒中獎的人不能放棄得獎！');
        $this->expectException(ResourceErrorException::class);

        // act
        (new Withdraw())->run([
            'staffCode' => '0209',
        ]);
    }

    public function testWithdrawSuccess()
    {
        // arrange 偽裝抽獎行為：抽到首獎（首獎只有一名）
        $candidate = Candidate::find(1);
        $candidate->award_id = 1;
        $candidate->save();
        $candidate->award->decrement('number');

        // act
        (new Withdraw())->run([
            'staffCode' => $candidate->staff_code,
        ]);

        // assert
        $this->assertDatabaseHas(
            'candidates',
            [
                'id' => 1,
                'award_id' => null,
            ]
        );
        $this->assertDatabaseHas(
            'awards',
            [
                'name' => '一獎',
                'number' => 1,
            ]
        );
    }
}
