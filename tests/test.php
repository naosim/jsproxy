<?php
require_once("./src/infra.php");

// declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ServiceTest extends TestCase
{
    // public function testCanBeCreatedFromValidEmailAddress(): void
    // {
    //   $this->assertInstanceOf(
    //     Email::class,
    //     Email::fromString('user@example.com')
    //   );
    // }

    // public function testCannotBeCreatedFromInvalidEmailAddress(): void
    // {
    //     $this->expectException(InvalidArgumentException::class);

    //     Email::fromString('invalid');
    // }

    public function testCreateUrl():void {
      $act = createUrl('hoge.com/foo/bar.json');
        $this->assertEquals(
            'https://hoge.com/foo/bar.json',
            $act->getValue(),
            'create url with https'
        );
    }


}