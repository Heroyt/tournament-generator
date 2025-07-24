<?php

declare(strict_types=1);

namespace TournamentGenerator\Containers;

use Exception;
use InvalidArgumentException;
use Override;

/**
 * Class GameContainer.
 *
 * Special container for games.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
class GameContainer extends BaseContainer
{
    /** @var GameContainer[] Direct child containers */
    protected array $children = [];

    /** @var int First auto increment value for recalculating the ids */
    protected int $firstIncrement = 1;

    /** @var int Autoincrement for contained games */
    protected int $autoIncrement = 1;

    /** @var null|GameContainer Parent container reference */
    protected ?BaseContainer $parent = null;

    /**
     * Increments the auto-incremented id.
     *
     * @post  Propagates to all children but the sender
     * @post  Propagates to the parent if not the sender
     *
     * @since 0.5
     */
    public function incrementId(?GameContainer $gameContainer = null) : void {
        // Increment
        ++$this->autoIncrement;
        // Propagate to parent
        if ($this->parent instanceof BaseContainer && $this->parent !== $gameContainer) {
            $this->parent->incrementId($this);
        }
        // Propagate to children
        foreach ($this->children as $child) {
            if ($child !== $gameContainer) {
                $child->incrementId($this);
            }
        }
    }

    /**
     * @since 0.5
     */
    public function getAutoIncrement() : int {
        return $this->autoIncrement;
    }

    /**
     * Sets the autoincrement number for games.
     *
     * @post  The value is propagated to child containers
     * @post  The firstIncrement value is set too
     *
     * @since 0.5
     */
    public function setAutoIncrement(int $autoIncrement) : GameContainer {
        $this->autoIncrement = $autoIncrement;
        $this->firstIncrement = $autoIncrement;
        foreach ($this->children as $child) {
            $child->setAutoIncrement($autoIncrement);
        }

        return $this;
    }

    /**
     * Resets the autoincrement number for games to the FirstIncrement.
     *
     * @post  Propagates to all children but the sender
     * @post  Propagates to the parent if not the sender
     *
     * @since 0.5
     */
    public function resetAutoIncrement(?GameContainer $gameContainer = null) : GameContainer {
        $this->autoIncrement = $this->firstIncrement;
        // Propagate to parent
        if ($this->parent instanceof BaseContainer && $this->parent !== $gameContainer) {
            $this->parent->resetAutoIncrement($this);
        }
        // Propagate to children
        foreach ($this->children as $child) {
            if ($child !== $gameContainer) {
                $child->resetAutoIncrement($this);
            }
        }

        return $this;
    }

    /**
     * @since 0.5
     */
    public function getFirstIncrement() : int {
        return $this->firstIncrement;
    }

    /**
     * Adds a child container.
     *
     * @param GameContainer[] $containers
     *
     * @return $this
     *
     * @post Parent container is set for the added children
     * @post Autoincrement value is propagated to added child containers
     *
     * @throws Exception
     */
    #[Override]
    public function addChild(BaseContainer ...$containers) : BaseContainer {
        foreach ($containers as $container) {
            if (!$container instanceof self) {
                throw new InvalidArgumentException('GameContainer must contain only other GameContainers.');
            }
            $container->setAutoIncrement($this->autoIncrement);
        }
        parent::addChild(...$containers);

        return $this;
    }
}
