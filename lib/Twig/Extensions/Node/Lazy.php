/**
* Represents a trans node.
*
* @package twig
* @author Fabien Potencier <fabien.potencier@symfony-project.com>
*/
class Twig_Extensions_Node_Lazy extends Twig_Node
{
	public function __construct(Twig_NodeInterface $body, $lineno, $tag)
	{
		parent::__construct(array('body' => $body), array(), $lineno, $tag);
	}
	
	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		$compiler->addDebugInfo($this)
					->write('$env_strict = $this->env->isStrictVariables();')->raw("\n")
					->write('$this->env->disableStrictVariables();')->raw("\n")
					->subcompile($this->getNode('body'))
					->write('if ($env_strict) $this->env->enableStrictVariables();')->raw("\n");
	}
}
