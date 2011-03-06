<?php


class Chof_Controller_Helper_Params extends Zend_Controller_Action_Helper_Abstract
{
  /**
   * @var array of parameters detected in content body
   */
  protected $bodyParams = array();

  /**
   * Detection of content type; retrieve parameters from body if any
   *
   * @return void
   */
  public function init()
  #*****************************************************************************
  {
    $request     = $this->getRequest();
    $contentType = $request->getHeader('Content-Type');
    $rawBody     = $request->getRawBody();
    
    if (!$rawBody) 
    {
        return;
    }
    else if (strstr($contentType, 'application/json'))
    {
      $this->setBodyParams(Zend_Json::decode($rawBody));
    }
    else if (strstr($contentType, 'application/xml'))
    {
      $config = new Zend_Config_Xml($rawBody);
      $this->setBodyParams($config->toArray());
    }
    else
    {
      if ($request->isPut()) 
      {
        parse_str($rawBody, $params);
        $this->setBodyParams($params);
      }
    }
    
  }

  /**
   * Set the http request body parameters
   *
   * @param  array $params
   * @return Scrummer_Controller_Action
   */
  public function setBodyParams(array $params)
  #*****************************************************************************
  {
      $this->bodyParams = $params;
      return $this;
  }

  /**
   * Retrieve the body parameters
   *
   * @return array of the parameters in the body of the http request
   */
  public function getBodyParams()
  #*****************************************************************************
  {
    return $this->bodyParams;
  }

  /**
   * Get a body parameter
   *
   * @param  string $name the name of the parameter
   * @return mixed
   */
  public function getBodyParam($name)
  #*****************************************************************************
  {
    if ($this->hasBodyParam($name)) 
    {
      return $this->bodyParams[$name];
    }
    else
    {
      return null;
    }
  }

  /**
   * Checks wether a given parameter has been set or not
   *
   * @param  string $name the requested parameter
   * @return bool true if the parameter is present
   */
  public function hasBodyParam($name)
  #*****************************************************************************
  {
    return (isset($this->bodyParams[$name])); 
  }

  /**
   * Returns true if the request contained any body parameters
   *
   * @return bool
   */
  public function hasBodyParams()
  #*****************************************************************************
  {
    return (count($this->bodyParams)>0);
  }  
  
  /**
   * Get either the body parameters of any have been set or any post parameters
   * as an associative array
   *
   * @return array
   */
  public function getSubmitParams()
  #*****************************************************************************
  {
    return ($this->hasBodyParams()) 
      ? $this->getBodyParams() : $this->getRequest()->getPost();
  }

  public function direct()
  #*****************************************************************************
  {
    return $this->getSubmitParams();
  }
}
?>