<?php

namespace Tests\Unit\Action;

use App\Actions\CreateAward;
use App\Actions\Init;
use App\Exceptions\Model\ResourceErrorException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateAwardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        (new Init(['type' => 'test']))->run();
    }

    public function testCreateAwardSuccess()
    {
        // act
        (new CreateAward())->run([
            'awardName' => '螺旋獎',
            'money' => 10000,
            'number' => 3,
        ]);

        // assert
        $this->assertDatabaseHas(
            'awards',
            [
                'name' => '螺旋獎',
                'amount_of_money' => 10000,
                'number' => 3,
            ]
        );
    }

    public function testDrawMultipleCandidatesFailed()
    {
        // expect exception
        $this->expectExceptionMessage('該獎項已存在！');
        $this->expectException(ResourceErrorException::class);

        // act
        (new CreateAward())->run([
            'awardName' => '一獎',
            'money' => 10000,
            'number' => 3,
        ]);
    }
}
