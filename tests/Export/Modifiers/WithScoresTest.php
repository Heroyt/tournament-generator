<?php

declare(strict_types=1);

namespace Export\Modifiers;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Category;
use TournamentGenerator\Export\Modifiers\WithScoresModifier;
use TournamentGenerator\Group;
use TournamentGenerator\Tournament;

/**
 * @internal
 *
 * @coversNothing
 */
class WithScoresTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[DataProvider('invalidData')]
    public function testInvalidObjectArgument(array $data) : void {
        $this->expectException(InvalidArgumentException::class);
        WithScoresModifier::process($data);
    }

    public static function invalidData() : array {
        return [
            [
                ['something' => 123], // Without object
            ],
            [
                ['object' => new Tournament('Name')], // Invalid object
            ],
            [
                [ // Objects without object
                    (object) [
                        'data' => 123,
                    ],
                    (object) [
                        'data' => 567,
                    ],
                ],
            ],
            [
                [ // Objects with invalid objects
                    (object) [
                        'object' => new Group('Group'),
                        'data'   => 123,
                    ],
                    (object) [
                        'object' => new Category('Group'),
                        'data'   => 567,
                    ],
                ],
            ],
        ];
    }
}
