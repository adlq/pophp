<?php 

class POEntry 
{
	private $comments;
	private $context;
	private $source;
	private $target;
	private $isWrapped;
	
	public function __construct($source, $target, $context = "", $comments = array(), $isWrapped = false)
	{
		$this->comments = $comments;
		$this->context = $context;
		$this->source = $source;
		$this->target = $target;
		$this->isWrapped = $isWrapped;
	}	

	public function isWrapped()
	{
		return $this->isWrapped;
	}
	
	public function isTranslated()
	{
		return ($this->getTarget() !== '');
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
	
	public function __toString()
	{
		return $this->getSource() . " => " . $this->getTarget();
	}
	
	public function display() 
	{
		// Display comments first
		$comments = $this->getComments();            
		foreach ($comments as $type => $comment) 
		{
			// Display comments
			switch ($type)
			{
				case "translator":
					foreach ($comment as $translatorComment)
					{
						echo "# $translatorComment\n";
					}
					break;
				case "extracted":
					foreach ($comment as $extracted)
					{
						echo "#. $extracted\n";
					}
					break;
				case "reference":
					foreach ($comment as $ref)
					{
						echo "#: " . str_replace('/', '\\', $ref) . "\n";
					}
					break;
				case "flag":
					echo "#, ";
					foreach ($comments['flag'] as $id => $flag)
					{
						if ($id === 0)
						{
							echo "$flag"; 
						} 
						else 
						{
							echo ", $flag";
						}
					}
					echo "\n";
					break;
				default:
					foreach ($comment as $old)
					{
						echo "#| $old\n";
					}
					break;
			}
		}

		// Context
		$context = $this->getContext();
		if ($context != "") 
			echo "msgctxt \"" . $context . "\"\n";

		// msgid
		$source = $this->getSource();
		echo 'msgid ';
		if ($this->isWrapped())
		{
			echo "\"\"\n";
		} 
		$this->displayWithLineBreak($source);

		// msgstr 
		$target = $this->getTarget();
		echo 'msgstr ';
		if ($this->isTranslated())
		{
			if ($this->isWrapped()) 
			{
				echo "\"\"\n";
			}
			$this->displayWithLineBreak($target);
		} 
		else 
		{
			echo "\"\"\n";
		}
		echo "\n";
	}
	
	
    private function displayWithLineBreak($str)
    {
		// Only perform this if the string is not empty
		if ($str !== '')
		{
			// Offset to be used with strpos
			$offset = 0;
			
			// Find first occurence of line break in the string
			$break = strpos($str, '\n', $offset) !== false;
			
			// If there is no line break, simply print out the string
			if ($break == false)
			{
				echo "\"$str\"\n";
			}
			else 
			{
				// Otherwise, we break lines till there are no more to break
				while ($break !== false)
				{
					$break = strpos($str, '\n', $offset);
					if ($break !== false)
					{
						echo "\"" . substr($str, $offset, $break - $offset) . '\n' . "\"\n";
						$padding = strlen('\n');
						$offset = $break + $padding > strlen($str) ? strlen($target) - 1 : $break + $padding; 
					}
				}
				if ($offset !== strlen($str))
					echo "\"" . substr($str, $offset) . "\"\n";
			}
		}
    } 
}

?>