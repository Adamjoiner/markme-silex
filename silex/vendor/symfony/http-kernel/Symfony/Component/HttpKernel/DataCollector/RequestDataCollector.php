<?php










namespace Symfony\Component\HttpKernel\DataCollector;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;






class RequestDataCollector extends DataCollector
{



public function collect(Request $request, Response $response, \Exception $exception = null)
{
$responseHeaders = $response->headers->all();
$cookies = array();
foreach ($response->headers->getCookies() as $cookie) {
$cookies[] = $this->getCookieHeader($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
}
if (count($cookies) > 0) {
$responseHeaders['Set-Cookie'] = $cookies;
}

$attributes = array();
foreach ($request->attributes->all() as $key => $value) {
if (is_object($value)) {
$attributes[$key] = sprintf('Object(%s)', get_class($value));
if (is_callable(array($value, '__toString'))) {
$attributes[$key] .= sprintf(' = %s', (string) $value);
}
} else {
$attributes[$key] = $value;
}
}

$content = null;
try {
$content = $request->getContent();
} catch (\LogicException $e) {

 $content = false;
}

$this->data = array(
'format' => $request->getRequestFormat(),
'content' => $content,
'content_type' => $response->headers->get('Content-Type') ? $response->headers->get('Content-Type') : 'text/html',
'status_code' => $response->getStatusCode(),
'request_query' => $request->query->all(),
'request_request' => $request->request->all(),
'request_headers' => $request->headers->all(),
'request_server' => $request->server->all(),
'request_cookies' => $request->cookies->all(),
'request_attributes' => $attributes,
'response_headers' => $responseHeaders,
'session_attributes' => $request->hasSession() ? $request->getSession()->all() : array(),
'flashes' => $request->hasSession() ? $request->getSession()->getFlashBag()->peekAll() : array(),
'path_info' => $request->getPathInfo(),
);
}

public function getPathInfo()
{
return $this->data['path_info'];
}

public function getRequestRequest()
{
return new ParameterBag($this->data['request_request']);
}

public function getRequestQuery()
{
return new ParameterBag($this->data['request_query']);
}

public function getRequestHeaders()
{
return new HeaderBag($this->data['request_headers']);
}

public function getRequestServer()
{
return new ParameterBag($this->data['request_server']);
}

public function getRequestCookies()
{
return new ParameterBag($this->data['request_cookies']);
}

public function getRequestAttributes()
{
return new ParameterBag($this->data['request_attributes']);
}

public function getResponseHeaders()
{
return new ResponseHeaderBag($this->data['response_headers']);
}

public function getSessionAttributes()
{
return $this->data['session_attributes'];
}

public function getFlashes()
{
return $this->data['flashes'];
}

public function getContent()
{
return $this->data['content'];
}

public function getContentType()
{
return $this->data['content_type'];
}

public function getStatusCode()
{
return $this->data['status_code'];
}

public function getFormat()
{
return $this->data['format'];
}




public function getName()
{
return 'request';
}

private function getCookieHeader($name, $value, $expires, $path, $domain, $secure, $httponly)
{
$cookie = sprintf('%s=%s', $name, urlencode($value));

if (0 !== $expires) {
if (is_numeric($expires)) {
$expires = (int) $expires;
} elseif ($expires instanceof \DateTime) {
$expires = $expires->getTimestamp();
} else {
$expires = strtotime($expires);
if (false === $expires || -1 == $expires) {
throw new \InvalidArgumentException(sprintf('The "expires" cookie parameter is not valid.', $expires));
}
}

$cookie .= '; expires='.substr(\DateTime::createFromFormat('U', $expires, new \DateTimeZone('UTC'))->format('D, d-M-Y H:i:s T'), 0, -5);
}

if ($domain) {
$cookie .= '; domain='.$domain;
}

$cookie .= '; path='.$path;

if ($secure) {
$cookie .= '; secure';
}

if ($httponly) {
$cookie .= '; httponly';
}

return $cookie;
}
}
