/**
 * Lazy Twig extension.
 * Temporarily disables strict variable checking.
 *
 * @package twig
 * @author Christophe SAUVEUR <christophe@xhaleera.com>
 */
class Twig_Extensions_Extension_Lazy extends Twig_Extension
{
		/**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers() {
			return array(new Twig_Extensions_TokenParser_Lazy());
		}
		
		/**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName() {
			return 'Lazy';
		}
}
