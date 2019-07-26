## Introduction

**[Tournament](/classes/tournament.md)** class is a main class, you will be working with most of the time. It's used to generate brackets, create new games and store all information about the tournament.

---

### Creating a new tournament class

```php
include_once 'vendor/autoload.php';

$tournament = new TournamentGenerator\Tournament('Tournament name');
```

### Setting up a basic tournament

See [Examples](/examples/basic.md)

---

## Properties

| Scope | Name | Type | Default | Description |
| :---: | :--: | :--: | :-----: | :---------: |
| public | `$name` | string | `''` | Name of the tournament |
| private | `$categories` | array | `[]` | List of tournament categories |
| private | `$rounds` | array | `[]` | List of tournament rounds |
| private | `$teams` | array | `[]` | List of teams |
| private | `$expectedPlay` | int | `0` | Time to play one game in minutes |
| private | `$expectedGameWait` | int | `0` | Pause between 2 games in minutes |
| private | `$expectedRoundWait` | int | `0` | Pause between 2 rounds in minutes |
| private | `$allowSkip` | bool | `false` | If `true`, generator will skip unplayed games without throwing exception |

## Methods

### `TournamentGenerator\Tournament __construct(string $name)`

Creates a new **[Tournament](/classes/tournament.md)** class

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$name` | string | `''` | Name of the tournament |

##### Return value
```php
new TournamentGenerator\Tournament();
```

#### Code

??? Expand
    ```php
    function __construct(string $name = ''){
    	$this->name = $name;
    }
    ```

---

### `public string __toString()`

Returns tournament name

##### Return value
```php
string $this->name;
```
#### Code

??? Expand
    ```php
    public function __toString() {
  		return $this->name;
  	}
    ```

---

### ` public TournamentGenerator\Tournament setPlay(int $play)`

Sets time to play one game in minutes.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$play` | int |  | Time of one game in minutes |

##### Return value
```php
TournamentGenerator\Tournament $this
```
#### Code

??? Expand
    ```php
    public function setPlay(int $play) {
  		$this->expectedPlay = $play;
  		return $this;
  	}
    ```

---

### ` public int getPlay()`

Gets time to play one game in minutes.

##### Return value
```php
int $this->expectedPlay;
```
#### Code

??? Expand
    ```php
    public function getPlay() {
  		return $this->expectedPlay;
  	}
    ```

---

### ` public TournamentGenerator\Tournament setGameWait(int $wait)`

Sets time to wait between games in minutes.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$wait` | int |  | Pause between two games in minutes |

##### Return value
```php
TournamentGenerator\Tournament $this
```
#### Code

??? Expand
    ```php
    public function setGameWait(int $wait) {
  		$this->expectedGameWait = $wait;
  		return $this;
  	}
    ```

---

### ` public int getGameWait()`

Sets time to play one game in minutes.

##### Return value
```php
int $this->expectedGameWait;
```
#### Code

??? Expand
    ```php
    public function getGameWait() {
  		return $this->expectedGameWait;
  	}
    ```

---

### ` public TournamentGenerator\Tournament setRoundWait(int $wait)`

Sets time to wait between rounds in minutes.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$wait` | int |  | Pause between rounds in minutes |

##### Return value
```php
TournamentGenerator\Tournament $this
```
#### Code

??? Expand
    ```php
    public function setRoundWait(int $wait) {
  		$this->expectedRoundWait = $wait;
  		return $this;
  	}
    ```

---

### ` public int getRoundWait()`

Gets time between rounds in minutes.

##### Return value
```php
int $this->expectedRoundWait
```
#### Code

??? Expand
    ```php
    public function getRoundWait() {
  		return $this->expectedRoundWait;
  	}
    ```

---

### ` public TournamentGenerator\Tournament setCategoryWait(int $wait)`

Sets time to wait between categories in minutes.

##### Parameters
| Name | Type | Default | Description |
| :--: | :--: | :-----: | :---------: |
| `$wait` | int |  | Pause between categories in minutes |

##### Return value
```php
TournamentGenerator\Tournament $this
```
#### Code

??? Expand
    ```php
    public function setCategoryWait(int $wait) {
  		$this->expectedCategoryWait = $wait;
  		return $this;
  	}
    ```

---

### ` public int getCategoryWait()`

Gets time between categories in minutes.

##### Return value
```php
int $this->expectedCategoryWait
```
#### Code

??? Expand
    ```php
    public function getCategoryWait() {
  		return $this->expectedCategoryWait;
  	}
    ```

---

### ` public int getTournamentTime()`

Gets expected time to play the whole tournament.

##### Return value
```php
int
```
#### Code

??? Expand
    ```php
    public function getTournamentTime(){
  		$games = count($this->getGames());
  		return $games*$this->expectedPlay+$games*$this->expectedGameWait+count($this->getRounds())*$this->expectedRoundWait+count($this->getCategories())*$this->expectedCategoryWait;
  	}
    ```

---
