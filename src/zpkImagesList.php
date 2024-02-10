<?php

namespace zpksystems\phpzpk;


class zpkImagesList implements \Iterator, \Countable{

	private $images = [];
	private $position = 0;

	public function add( $content )
	{
		$this->images[] = $content;
	}

	public function rewind():void {
		$this->position = 0;
	}

	public function valid():bool {
		return isset($this->images[$this->position]);
	}

	public function current():zpkGeneratedImage {
		return $this->images[$this->position];
	}

	public function next():void {
		++$this->position;
	}

	public function key():int {
		return $this->position;
	}

	public function count():int{
		return count($this->images);
	}

}
