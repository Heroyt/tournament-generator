<?php

declare(strict_types=1);

namespace TournamentGenerator\Export;

use JsonException;
use JsonSerializable;
use TournamentGenerator\Export\Modifiers\Modifier;
use TournamentGenerator\HierarchyBase;
use TournamentGenerator\Interfaces\WithId;

/**
 * Base class for exporters.
 *
 * Exporters operate on some HierarchyBase class. They extract data and/or settings from these classes in a form of PHP array.
 * Exporters also allow of adding modifiers to the exported query - adding more data. These modifiers are added via specific methods (usually starting with "with" keyword).
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.5
 */
abstract class ExporterBase implements ExporterInterface, JsonSerializable
{
    /** @var Modifier[] Modifiers to apply to exported data */
    protected array $modifiers = [];

    public function __construct(
        /** @var HierarchyBase Hierarchy object to export */
        protected WithId $object
    ) {}

    /**
     * Return result as json.
     *
     * @throws JsonException
     *
     * @see ExporterBase::jsonSerialize()
     * @see ExporterBase::get()
     */
    public function getJson() : string {
        return json_encode($this->get(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Finish the export query -> get the result.
     *
     * @return array The query result
     */
    public function get() : array {
        $data = $this->getBasic();
        $this->applyModifiers($data);

        return array_map(
            static function (object $object) : object {
                unset($object->object);

                return $object;
            },
            $data
        );
    }

    /**
     * Apply set modifiers to data array.
     */
    protected function applyModifiers(array &$data) : void {
        foreach ($this->modifiers as $modifier) {
            $modifier::process($data);
        }
    }

    /**
     * Serialize exported data as JSON.
     *
     * @see json_encode()
     */
    public function jsonSerialize() : array {
        return $this->get();
    }
}
