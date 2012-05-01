<?php










namespace Symfony\Component\Routing;








class Route
{
private $pattern;
private $defaults;
private $requirements;
private $options;
private $compiled;

static private $compilers = array();















public function __construct($pattern, array $defaults = array(), array $requirements = array(), array $options = array())
{
$this->setPattern($pattern);
$this->setDefaults($defaults);
$this->setRequirements($requirements);
$this->setOptions($options);
}

public function __clone()
{
$this->compiled = null;
}






public function getPattern()
{
return $this->pattern;
}










public function setPattern($pattern)
{
$this->pattern = trim($pattern);


 if ('' === $this->pattern || '/' !== $this->pattern[0]) {
$this->pattern = '/'.$this->pattern;
}

$this->compiled = null;

return $this;
}






public function getOptions()
{
return $this->options;
}










public function setOptions(array $options)
{
$this->options = array(
'compiler_class' => 'Symfony\\Component\\Routing\\RouteCompiler',
);

return $this->addOptions($options);
}










public function addOptions(array $options)
{
foreach ($options as $name => $option) {
$this->options[(string) $name] = $option;
}
$this->compiled = null;

return $this;
}













public function setOption($name, $value)
{
$this->options[$name] = $value;
$this->compiled = null;

return $this;
}








public function getOption($name)
{
return isset($this->options[$name]) ? $this->options[$name] : null;
}






public function getDefaults()
{
return $this->defaults;
}










public function setDefaults(array $defaults)
{
$this->defaults = array();

return $this->addDefaults($defaults);
}










public function addDefaults(array $defaults)
{
foreach ($defaults as $name => $default) {
$this->defaults[(string) $name] = $default;
}
$this->compiled = null;

return $this;
}








public function getDefault($name)
{
return isset($this->defaults[$name]) ? $this->defaults[$name] : null;
}








public function hasDefault($name)
{
return array_key_exists($name, $this->defaults);
}











public function setDefault($name, $default)
{
$this->defaults[(string) $name] = $default;
$this->compiled = null;

return $this;
}






public function getRequirements()
{
return $this->requirements;
}










public function setRequirements(array $requirements)
{
$this->requirements = array();

return $this->addRequirements($requirements);
}










public function addRequirements(array $requirements)
{
foreach ($requirements as $key => $regex) {
$this->requirements[$key] = $this->sanitizeRequirement($key, $regex);
}
$this->compiled = null;

return $this;
}








public function getRequirement($key)
{
return isset($this->requirements[$key]) ? $this->requirements[$key] : null;
}











public function setRequirement($key, $regex)
{
$this->requirements[$key] = $this->sanitizeRequirement($key, $regex);
$this->compiled = null;

return $this;
}






public function compile()
{
if (null !== $this->compiled) {
return $this->compiled;
}

$class = $this->getOption('compiler_class');

if (!isset(self::$compilers[$class])) {
self::$compilers[$class] = new $class;
}

return $this->compiled = self::$compilers[$class]->compile($this);
}

private function sanitizeRequirement($key, $regex)
{
if (!is_string($regex)) {
throw new \InvalidArgumentException(sprintf('Routing requirement for "%s" must be a string', $key));
}

if ('' === $regex) {
throw new \InvalidArgumentException(sprintf('Routing requirement for "%s" cannot be empty', $key));
}

if ('^' === $regex[0]) {
$regex = substr($regex, 1);
}

if ('$' === substr($regex, -1)) {
$regex = substr($regex, 0, -1);
}

return $regex;
}
}
