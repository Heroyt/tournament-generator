<?php


namespace TournamentGenerator\Import;

use TournamentGenerator\Constants;
use TournamentGenerator\Preset\DoubleElimination;
use TournamentGenerator\Preset\R2G;
use TournamentGenerator\Preset\SingleElimination;

/**
 * Validator for import data
 *
 * Validates if the input data is a valid import object.
 *
 * @package TournamentGenerator\Import
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
class ImportValidator
{

	/**
	 * @var array Expected import data structure description
	 */
	public const STRUCTURE = [
		'tournament'   => [
			'type'       => 'object',
			'parameters' => [
				'type'       => [
					'default' => 'general',
					'type'    => 'string',
					'values'  => ['general', SingleElimination::class, DoubleElimination::class, R2G::class],
				],
				'name'       => [
					'default' => '',
					'type'    => 'string',
				],
				'skip'       => [
					'default' => false,
					'type'    => 'bool',
				],
				'timing'     => [
					'default'    => null,
					'type'       => 'object',
					'parameters' => [
						'play'         => [
							'default' => 0,
							'type'    => 'int',
						],
						'gameWait'     => [
							'default' => 0,
							'type'    => 'int',
						],
						'categoryWait' => [
							'default' => 0,
							'type'    => 'int',
						],
						'roundWait'    => [
							'default' => 0,
							'type'    => 'int',
						],
						'expectedTime' => [
							'default' => 0,
							'type'    => 'int',
						],
					]
				],
				'categories' => [
					'default'   => [],
					'type'      => 'array',
					'subtype'   => 'id',
					'reference' => 'categories',
				],
				'rounds'     => [
					'default'   => [],
					'type'      => 'array',
					'subtype'   => 'id',
					'reference' => 'rounds',
				],
				'groups'     => [
					'default'   => [],
					'type'      => 'array',
					'subtype'   => 'id',
					'reference' => 'groups',
				],
				'teams'      => [
					'default'   => [],
					'type'      => 'array',
					'subtype'   => 'id',
					'reference' => 'teams',
				],
				'games'      => [
					'default'   => [],
					'type'      => 'array',
					'subtype'   => 'int',
					'reference' => 'games',
				],
			],
		],
		'categories'   => [
			'type'       => 'array',
			'subtype'    => 'object',
			'parameters' => [
				'id'     => [
					'default' => null,
					'type'    => 'id',
				],
				'name'   => [
					'default' => '',
					'type'    => 'string',
				],
				'skip'   => [
					'default' => false,
					'type'    => 'bool',
				],
				'rounds' => [
					'default'   => [],
					'type'      => 'array',
					'subtype'   => 'id',
					'reference' => 'rounds',
				],
				'groups' => [
					'default'   => [],
					'type'      => 'array',
					'subtype'   => 'id',
					'reference' => 'groups',
				],
				'teams'  => [
					'default'   => [],
					'type'      => 'array',
					'subtype'   => 'id',
					'reference' => 'teams',
				],
				'games'  => [
					'default'   => [],
					'type'      => 'array',
					'subtype'   => 'int',
					'reference' => 'games',
				],
			],
		],
		'rounds'       => [
			'type'       => 'array',
			'subtype'    => 'object',
			'parameters' => [
				'id'     => [
					'default' => null,
					'type'    => 'id',
				],
				'name'   => [
					'default' => '',
					'type'    => 'string',
				],
				'skip'   => [
					'default' => false,
					'type'    => 'bool',
				],
				'played' => [
					'default' => false,
					'type'    => 'bool',
				],
				'groups' => [
					'default'   => [],
					'type'      => 'array',
					'subtype'   => 'id',
					'reference' => 'groups',
				],
				'teams'  => [
					'default'   => [],
					'type'      => 'array',
					'subtype'   => 'id',
					'reference' => 'teams',
				],
				'games'  => [
					'default'   => [],
					'type'      => 'array',
					'subtype'   => 'int',
					'reference' => 'games',
				],
			],
		],
		'groups'       => [
			'type'       => 'array',
			'subtype'    => 'object',
			'parameters' => [
				'id'      => [
					'default' => null,
					'type'    => 'id',
				],
				'name'    => [
					'default' => '',
					'type'    => 'string',
				],
				'type'    => [
					'default' => Constants::ROUND_ROBIN,
					'type'    => 'string',
					'values'  => Constants::GroupTypes,
				],
				'skip'    => [
					'default' => false,
					'type'    => 'bool',
				],
				'points'  => [
					'default'    => null,
					'type'       => 'object',
					'parameters' => [
						'win'         => [
							'default' => 3,
							'type'    => 'int',
						],
						'loss'        => [
							'default' => 0,
							'type'    => 'int',
						],
						'draw'        => [
							'default' => 1,
							'type'    => 'int',
						],
						'second'      => [
							'default' => 2,
							'type'    => 'int',
						],
						'third'       => [
							'default' => 3,
							'type'    => 'int',
						],
						'progression' => [
							'default' => 50,
							'type'    => 'int',
						],
					]
				],
				'played'  => [
					'default' => false,
					'type'    => 'bool',
				],
				'inGame'  => [
					'default' => 2,
					'type'    => 'int',
					'values'  => [2, 3, 4],
				],
				'maxSize' => [
					'default' => 4,
					'type'    => 'int',
				],
				'teams'   => [
					'default'   => [],
					'type'      => 'array',
					'subtype'   => 'id',
					'reference' => 'teams',
				],
				'games'   => [
					'default'   => [],
					'type'      => 'array',
					'subtype'   => 'int',
					'reference' => 'games',
				],
			],
		],
		'progressions' => [
			'type'       => 'array',
			'subtype'    => 'object',
			'parameters' => [
				'from'       => [
					'type'      => 'id',
					'reference' => 'groups',
				],
				'to'         => [
					'type'      => 'id',
					'reference' => 'groups',
				],
				'offset'     => [
					'type'    => 'int',
					'default' => 0,
				],
				'length'     => [
					'type'    => 'int',
					'default' => null,
				],
				'filters'    => [
					'type'       => 'array',
					'subtype'    => 'object',
					'default'    => [],
					'parameters' => [
						'what'   => [
							'type'    => 'string',
							'default' => 'points',
							'values'  => ['points', 'score', 'wins', 'draws', 'losses', 'second', 'third', 'team', 'not-progressed', 'progressed'],
						],
						'how'    => [
							'type'    => 'string',
							'default' => '>',
							'values'  => ['>', '<', '>=', '<=', '=', '!='],
						],
						'val'    => [
							'default' => 0,
						],
						'groups' => [
                            'type' => 'array',
                            'subtype' => 'id',
                            'reference' => 'groups',
                        ],
                    ],
                ],
                'progressed' => [
                    'type' => 'bool',
                    'default' => false,
                ],
                'points' => [
                    'type' => ['int', 'null'],
                    'default' => null,
                ],
            ],
		],
		'teams'        => [
			'type'       => 'array',
			'subtype'    => 'object',
			'parameters' => [
				'id'     => [
					'type'    => 'id',
					'default' => null,
				],
				'name'   => [
					'type'    => 'string',
					'default' => '',
				],
				'scores' => [
					'type'         => 'array',
					'subtype'      => 'object',
					'keyReference' => 'groups',
					'parameters'   => [
						'points' => [
							'type'    => 'int',
							'default' => 0,
						],
						'score'  => [
							'type'    => 'int',
							'default' => 0,
						],
						'wins'   => [
							'type'    => 'int',
							'default' => 0,
						],
						'draws'  => [
							'type'    => 'int',
							'default' => 0,
						],
						'losses' => [
							'type'    => 'int',
							'default' => 0,
						],
						'second' => [
							'type'    => 'int',
							'default' => 0,
						],
						'third'  => [
							'type'    => 'int',
							'default' => 0,
						],
					],
				],
			],
		],
		'games'        => [
			'type'       => 'array',
			'subtype'    => 'object',
			'parameters' => [
				'id'     => [
					'type'    => 'int',
					'default' => null,
				],
				'teams'  => [
					'type'      => 'array',
					'subtype'   => 'id',
					'reference' => 'teams',
				],
				'scores' => [
					'type'         => 'array',
					'subtype'      => 'object',
					'keyReference' => 'teams',
					'parameters'   => [
						'score'  => [
							'type' => 'int',
						],
						'points' => [
							'type' => 'int',
						],
						'type'   => [
							'type'   => 'string',
							'values' => ['win', 'loss', 'draw', 'second', 'third'],
						],
					],
				],
			],
		],
	];

	protected static array $data;

	/**
	 * Validates if the data is correct
	 *
	 * Checks the data
	 *
	 * @param array $data         Data to check - can be modified (type casted from array to object)
	 * @param bool  $throwOnError If true, throw a InvalidImportDataException
	 *
	 * @return bool
	 * @throws InvalidImportDataException
	 */
	public static function validate(array &$data, bool $throwOnError = false) : bool {

		// Check for empty
		if (empty($data)) {
			if ($throwOnError) {
				throw new InvalidImportDataException('Import data is empty.');
			}
			return false;
		}

		self::$data = $data;

		try {
			foreach ($data as $key => &$value) {
				if (!isset(self::STRUCTURE[$key])) {
					throw new InvalidImportDataException('Unknown data key: '.$key);
				}
				self::validateParams($value, [$key], self::STRUCTURE[$key]);
			}
			self::validateGroupParents($data);
		} catch (InvalidImportDataException $e) {
			if ($throwOnError) {
				throw $e;
			}
			return false;
		}

		return true;
	}

	/**
	 * @param       $data
	 * @param array $keys
	 * @param array $setting
	 *
	 * @throws InvalidImportDataException
	 */
	public static function validateParams(&$data, array $keys, array $setting) : void {
		$primitive = false;
		// Check type
		if (isset($setting['type'])) {
			switch ($setting['type']) {
				case 'array':
					if (!is_array($data)) {
						throw new InvalidImportDataException('Invalid data type for: '.implode('->', $keys).'. Expected array.');
					}
					// Validate subtypes
					if (isset($setting['subtype'])) {
						foreach ($data as $key => $var) {
							self::validateType($var, array_merge($keys, [$key]), $setting['subtype']);
						}
					}
					if (isset($setting['keyReference'])) {
						foreach ($data as $key => $val) {
							self::validateReference($key, $setting['keyReference']);
						}
					}
					if (isset($setting['reference'])) {
						foreach ($data as $val) {
							self::validateReference($val, $setting['reference']);
						}
					}
					break;
				case 'object':
					if (!self::isObject($data)) {
						throw new InvalidImportDataException('Invalid data type for: '.implode('->', $keys).'. Expected object.');
					}
					break;
				default:
					$primitive = true;
					if (!array_key_exists('default', $setting) || !is_null($data)) {
						if (!is_array($setting['type'])) {
							$setting['type'] = [$setting['type']];
						}
						self::validateType($data, $keys, ...$setting['type']);
					}
					break;
			}
		}

		if (!$primitive) {
			if (isset($setting['parameters'])) {
				if ($setting['type'] === 'object') {
					foreach (((array) $data) as $key => $value) {
						self::validateParams($value, array_merge($keys, [$key]), $setting['parameters'][$key]);
					}
				}
				else {
					foreach (((array) $data) as $object) {
						foreach (((array) $object) as $key => $value) {
							self::validateParams($value, array_merge($keys, [$key]), $setting['parameters'][$key]);
						}
					}
				}
			}
			return;
		}
		if (isset($setting['values']) && !in_array($data, $setting['values'], true)) {
			throw new InvalidImportDataException('Invalid value for: '.implode('->', $keys).'. Expected values: '.implode(', ', $setting['values']).'.');
		}
		if (isset($setting['reference'])) {
			self::validateReference($data, $setting['reference']);
		}
	}

	/**
	 * Check type of a variable
	 *
	 * @param        $var
	 * @param array  $keys
	 * @param string ...$types Expected type
	 *
	 * @throws InvalidImportDataException
	 */
	public static function validateType($var, array $keys, string ...$types) : void {
		foreach ($types as $type) {
			switch ($type) {
				case 'array':
					if (is_array($var)) {
						return;
					}
					break;
				case 'object':
					if (self::isObject($var)) {
						return;
					}
					break;
				case 'int':
					if (is_int($var)) {
						return;
					}
					break;
				case 'string':
					if (is_string($var)) {
						return;
					}
					break;
                case 'id':
                    if (is_int($var) || is_string($var)) {
                        return;
                    }
                    break;
                case 'bool':
                    if (is_bool($var)) {
                        return;
                    }
                    break;
                case 'null':
                    if (is_null($var)) {
                        return;
                    }
                    break;
            }
		}
		throw new InvalidImportDataException('Invalid data type for: '.implode('->', $keys).'. Expected '.implode('|', $types).'.');
	}

	/**
	 * Checks if a variable is object or associative array
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public static function isObject($data) : bool {
		return is_object($data) || (is_array($data) && !empty($data) && array_keys($data) !== range(0, count($data) - 1));
	}

	/**
	 * Check if object of id exists in export data
	 *
	 * @param        $id
	 * @param string $key
	 *
	 * @throws InvalidImportDataException
	 */
	public static function validateReference($id, string $key) : void {
		if (isset(self::$data[$key])) {
			$ids = array_map(static function($object) {
				return ((object) $object)->id;
			}, self::$data[$key]);
			if (in_array($id, $ids, false)) {
				return;
			}
			throw new InvalidImportDataException('Invalid reference of '.$key.' on id: '.$id);
		}
	}

	/**
	 * Validate that the import does not contain groups without a parent round
	 *
	 * @param array $data
	 *
	 * @return void
	 * @throws InvalidImportDataException
	 */
	public static function validateGroupParents(array $data) : void {
		if (empty($data['groups'])) {
			return;
		}
		$groups = [];
		foreach ($data['groups'] as $group) {
			$groups[] = is_array($group) ? $group['id'] : $group->id;
		}

		$rounds = $data['rounds'] ?? [];
		foreach ($rounds as $round) {
			foreach (is_array($round) ? $round['groups'] : $round->groups as $groupId) {
				unset($groups[array_search($groupId, $groups, true)]);
			}
		}

		if (!empty($groups)) {
			throw new InvalidImportDataException('Some groups are missing a parent round: '.implode(', ', $groups));
		}
	}

}