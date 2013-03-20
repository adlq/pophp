<?php 

class POEntry 
{
	private $comments;
	private $context;
	private $source;
	private $target;
	
	public function __construct($source, $target, $context = "", $comments = array())
	{
		$this->comments = $comments;
		$this->context = $context;
		$this->source = $source;
		$this->target = $target;
	}	

    public function getComments()
    {
        return $this->comments;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getTarget()
    {
        return $this->target;
    }
}

?>