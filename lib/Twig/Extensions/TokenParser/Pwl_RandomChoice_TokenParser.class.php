<?php
/**
 * This is a Twig TokenParser that will parse custom tags, count the blocks and then fabricate 
 * an if/elseif block checking the block sequence number against a random number between 1 - the 
 * total number of blocks. The block that matches the random will be returned.
 *
 * @author Trevor Wencl <twencl@gmail.com>
 * @example 
 *				{% random_select %}
 *					will  we display this one??
 *				{% or_select %}
 *					or will we display this OTHER one?
 *				{% end_random_select %}
 */
class Pwl_RandomSelect_TokenParser extends Twig_TokenParser
{
	private $select_tag = 'random_select';
	private $or_tag = 'or_select';
	private $end_tag = 'end_random_select';

	/**
 	* Parses a token and returns a node.
 	*
 	* @param Twig_Token $token A Twig_Token instance
 	*
 	* @return Twig_NodeInterface A Twig_NodeInterface instance
 	*/
	public function parse(Twig_Token $token)
	{

		// count the blocks to create the random range
		$end = false;
		$count = 0;
   	
		$stream = clone $this->parser->getStream();
		while (!$end) {
			$value = $stream->next()->getValue();
			switch ($value) {
				case $this->or_tag:
					break;
				case $this->end_tag:
					$end = true;
					break;

				default:
					if(!empty($value)) {
						$count++;
					}
					break;
			}
		}   

		$rand = rand(1, $count);
		$lineno = $token->getLine();
		$count = 1;
		
		// fabricate an expression
		$left = new Twig_Node_Expression_Constant($count, $lineno);
		$right = new Twig_Node_Expression_Constant($rand, $lineno);
		$expr = new Twig_Node_Expression_Binary_Equal($left, $right, $lineno);

		$this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
		$body = $this->parser->subparse(array($this, 'decideIfFork'));
		$tests = array($expr, $body);
		$else = null; // will always be null
		$end = false;
		
		while (!$end) {
			switch ($this->parser->getStream()->next()->getValue()) {
				case $this->or_tag:
					
					// fabricate an expression
					$left = new Twig_Node_Expression_Constant(++$count, $lineno);
					$right = new Twig_Node_Expression_Constant($rand, $lineno);
					$expr = new Twig_Node_Expression_Binary_Equal($left, $right, $lineno);
					
					$this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
					$body = $this->parser->subparse(array($this, 'decideIfFork'));
					$tests[] = $expr;
					$tests[] = $body;
					break;

				case $this->end_tag:
					$end = true;
					break;

				default:
					throw new Twig_Error_Syntax(sprintf('Unexpected end of template. Twig was looking for the following tags "'.$this->or_tag.'", or "'.$this->end_tag.'" to close the "'.$this->select_tag.'" block started at line %d)', $lineno), -1);
			}
		}

		$this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);

		return new Twig_Node_If(new Twig_Node($tests), $else, $lineno, $this->getTag());
	}

	public function decideIfFork(Twig_Token $token)
	{
		return $token->test(array($this->or_tag, $this->end_tag));
	}

	public function decideIfEnd(Twig_Token $token)
	{
		return $token->test(array($this->end_tag));
	}

	/**
 	* Gets the tag name associated with this token parser.
 	*
 	* @param string The tag name
 	*/
	public function getTag()
	{
		return $this->select_tag;
	}
}