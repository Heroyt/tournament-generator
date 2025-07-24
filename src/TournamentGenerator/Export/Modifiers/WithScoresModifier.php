<?php

declare(strict_types=1);

namespace TournamentGenerator\Export\Modifiers;

use Exception;
use InvalidArgumentException;
use TournamentGenerator\Team;

class WithScoresModifier implements Modifier
{
    /**
     * @throws Exception
     */
    public static function process(array &$data) : array {
        // Check for "single" export
        if (isset($data['object'])) {
            if (!$data['object'] instanceof Team) {
                throw new InvalidArgumentException('WithScores modifier needs a Team object.');
            }

            return self::processArray($data);
        }

        foreach ($data as $object) {
            if (!isset($object->object) || !$object->object instanceof Team) {
                throw new InvalidArgumentException('WithScores modifier needs a Team object.');
            }
            self::processObject($object);
        }

        return $data;
    }

    /**
     * Modify a "Single" data.
     *
     * @throws Exception
     */
    protected static function processArray(array &$data) : array {
        /** @var Team $team */
        $team = $data['object'];
        $data['scores'] = array_map(
            static function (array $group) : array {
                unset($group['group']); // Get rid of the Group object reference

                return $group;
            },
            $team->getGroupResults()
        );

        return $data;
    }

    /**
     * Modify data.
     *
     * @throws Exception
     */
    protected static function processObject(object $object) : object {
        /** @var Team $team */
        $team = $object->object;
        $object->scores = array_map(
            static function (array $group) : array {
                unset($group['group']); // Get rid of the Group object reference

                return $group;
            },
            $team->getGroupResults()
        );

        return $object;
    }
}
