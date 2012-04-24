<?php
/**
 * Twig Extension to add the eval function to evaluate twig code passed into a template
 */
class Twig_Extensions_Extension_Eval extends Twig_Extension
{
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'eval';
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'eval' => new \Twig_Function_Method($this, 'evaluateString', array(
                'needs_environment' => true,
                'needs_context'     => true,
            )),
        );
    }

    /**
     * Loads a string template and returns the rendered version
     *
     * @param  Twig_Environment $env
     * @param  array             $context
     * @param  string            $string  The string template to load
     * @return string
     */
    public function evaluateString(Twig_Environment $env, $context, $string)
    {
        $newEnv = $this->setLoader($env);
        return $newEnv->loadTemplate($string)->render($context);
    }

    /**
     * Clones the current environment and sets the loader to be a string loader
     *
     * @param  Twig_Environment $env
     * @return Twig_Environment
     */
    private function setLoader(Twig_Environment $env)
    {
        $newEnv = clone $env;
        $loader = new Twig_Loader_String();
        $newEnv->setLoader($loader);
        return $newEnv;
    }
}