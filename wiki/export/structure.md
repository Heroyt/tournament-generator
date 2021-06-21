## Export structure

Description of all data that could be exported using **Exporter** classes.

### Setup

```
{
    "tournament": {},
    "categories": [],
    "rounds": [],
    "groups": [],
    "progressions": [],
    "teams": [],
    "games": [],
}
```

### Team

```
{
  "id": int|string,
  "name": string,
  "scores": {
		groupId(int|string): {
			"points": int,
			"score": int,
			"wins": int,
			"draws": int,
			"losses": int,
			"second": int,
			"third": int
		}
	}
}
```

### Game

```
{
  "id": int,
  "teams": [
    id(string|int)
  ],
  "scores": {
		teamId(string|int): {
			"score": int,
			"points": int,
			"type": "win"|"loss"|"draw"|"second"|"third"
		}
	}
}
```

### Tournament

```
{
  "name": string,
  "type": "general"|"\TournamentGenerator\Preset\SingleElimination"|"\TournamentGenerator\Preset\DoubleElimination"|"\TournamentGenerator\Preset\R2G",
  "skip": bool,
  "timing": {
    "play": int,
    "gameWait": int,
    "categoryWait": int,
    "roundWait": int,
    "expectedTime": int
  },
  "categories": [
    id(string|int)
  ],
  "rounds": [
    id(string|int)
  ],
  "groups": [
    id(string|int)
  ],
  "teams": [
    id(string|int)
  ],
  "games": [
    id(int)
  ]
}
```

### Category

```
{
  "id": string|int,
  "name": string,
  "skip": bool,
  "rounds": [
    id(string|int)
  ],
  "groups": [
    id(string|int)
  ],
  "teams": [
    id(string|int)
  ],
  "games": [
    id(int)
  ]
}
```

### Round

```
{
  "id": string|int,
  "name": string,
  "skip": bool,
  "played": bool,
  "groups": [
    id(string|int)
  ],
  "teams": [
    id(string|int)
  ],
  "games": [
    id(int)
  ]
}
```

### Group

```
{
  "id": string|int,
  "name": string,
  "type": Constants::ROUND_ROBIN|Constants::ROUND_TWO|Constants::ROUND_SPLIT,
  "skip": bool,
  "played": bool,
  "inGame": 2|3|4,
  "points": {
    "win": int,
    "draw": int,
    "loss": int,
    "second": int,
    "third": int,
    "progression": int,
  },
  "teams": [
    id(string|int)
  ],
  "games": [
    id(int)
  ]
}
```

### Progression

```
{
  "from": groupId(string|int),
  "to": groupId(string|int),
  "offset": int,
  "length": int,
  "progressed": bool,
  "filters": [
    {
        "what": 'points'|'score'|'wins'|'draws'|'losses'|'second'|'third'|'team'|'not-progressed'|'progressed',
        "how": '>'|'<'|'>='|'<='|'='|'!=',
        "val": mixed,
        "groups": [
            groupId(string|int)
        ] 
    }
  ]
}
```