<?php

/*
	Simple YouTube GData Feed Parser
	Created by JmZ
	Sampai jumpa :p
	
	PHP >= 5.1
*/

class YouTubeVideo {
	public $id;
	public $title;
	public $published;
	public $category;
	public $description;
	public $link;
	public $author;
	
	public function values(Array $attributes)
	{
		foreach($attributes as $attr => $val)
		{
			$this->$attr = $val;
		}
		return $this;
	}
}

class YouTube implements Iterator {
	public $xml;
	public $keywords;
	public $maxResults = 30;
	protected $position = 0;
	
	public function __construct($keywords)
	{
		$this->keywords = urlencode(trim(preg_replace('#\W+#', ' ', $keywords), '/'));
		$this->xml = simplexml_load_file('http://gdata.youtube.com/feeds/api/videos?q=' . $this->keywords . '&max-results=' . $this->maxResults);
	}
	
	public function current()
	{
		$entry = $this->xml->entry[$this->position];
		$instance = new YouTubeVideo;
		return $instance->values(array(
			'id' => substr(strrchr((string) $entry->id, '/'), 1),
			'title' => (string) $entry->title,
			'published' => (string) $entry->published,
			'category' => (string) $entry->category[1]['label'],
			'description' => (string) $entry->content,
			'link' => (string) $entry->link[0]['href'],
			'author' => (string) $entry->author->name,
		));
	}
	
	public function key()
	{
		return $this->position;
	}
	
	public function next()
	{
		$this->position++;
	}
	
	public function rewind()
	{
		$this->position = 0;
	}
	
	public function valid()
	{
		return isset($this->xml->entry[$this->position]);
	}
}