<?php

namespace TournamentGenerator;

/**
 *
 */
class Progression
{

	private $from;
	private $to;
	private $start = 0;
	private $len = null;
	private $filters = [];

	function __construct(Group $from, Group $to, int $start = 0, int $len = null) {
		$this->from = $from;
		$this->to = $to;
		$this->start = $start;
		$this->len = $len;
	}

	function __toString() {
		return 'Team from '.$this->from;
	}

	public function addFilter(...$filters) {
		foreach ($filters as $filter) {
			if (!$filter instanceof TeamFilter) throw new Exception('Trying to add filter which is not an instance of TeamFilter.');
			$this->filters[] = $filter;
		}
		$this->filter[] = $filter;
		return $this;
	}

	public function progress() {
		$teams = $this->from->sortTeams($this->filters);
		if (count($this->filters) === 0 || $this->len !== null || $this->start !== 0) $next = array_splice($teams, $this->start, ($this->len == null ? count($teams) : $this->len));
		else $next = $teams;
		$this->from->addProgressed($next);
		$this->to->addTeam($next);
		return $this;
	}

	public function progressBlank(){
		$teams = $this->from->isPlayed() ? $this->from->sortTeams($this->filters) : $this->from->simulate($this->filters);
		if (count($this->filters) === 0 || $this->len !== null || $this->start !== 0) $next = array_splice($teams, $this->start, ($this->len == null ? count($teams) : $this->len));
		else $next = $teams;
		$this->from->addProgressed($next);
		$i = 1;
		foreach ($next as $team) {
			// echo '<pre>Progressing team from '.$this->from.' to '.$this->to.': '.$team.'</pre>';
			$this->to->addTeam(new BlankTeam($this.' - '.$i, $team));
			$i++;
		}
		return $this;
	}
}


?>
