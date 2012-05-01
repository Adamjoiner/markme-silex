<?php










namespace Symfony\Component\HttpFoundation;






class JsonResponse extends Response
{
protected $data;
protected $callback;








public function __construct($data = array(), $status = 200, $headers = array())
{
parent::__construct('', $status, $headers);

$this->setData($data);
}




static public function create($data = array(), $status = 200, $headers = array())
{
return new static($data, $status, $headers);
}








public function setCallback($callback = null)
{
if ($callback) {

 $pattern = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';
if (!preg_match($pattern, $callback)) {
throw new \InvalidArgumentException('The callback name is not valid.');
}
}

$this->callback = $callback;

return $this->update();
}








public function setData($data = array())
{

 if (is_array($data) && 0 === count($data)) {
$data = new \ArrayObject();
}

$this->data = json_encode($data);

return $this->update();
}






protected function update()
{
if ($this->callback) {

 $this->headers->set('Content-Type', 'text/javascript', true);

return $this->setContent(sprintf('%s(%s);', $this->callback, $this->data));
}

$this->headers->set('Content-Type', 'application/json', false);

return $this->setContent($this->data);
}
}
