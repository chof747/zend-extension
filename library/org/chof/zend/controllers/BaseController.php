<?php 

/**
 * A base controller with extended functionality for action controllers
 * 
 * The BaseController class extends the functions of action controllers by the
 * following features:
 * 
 *  - convinience methods to create form elements
 *  - a foreward queue, to allow predefined sequences of forewards
 *  
 *  <b>Convinience Methods:</b>
 *  
 *  The convinience methods generate form elements and return the respective
 *  Zend_Form_Element instance. For formatting they use predifined static 
 *  decorator strings. And they use a set of css classes to enable the a
 *  custom built format.
 *  
 *  <b>Foreward queue</b>
 *   
 *  The foreward queue allows the setup of a sequence of dispatches to 
 *  controllers which will be called in sequence. To enable the foreward 
 *  queueing the controller
 *  
 *  The foreward queue requests a Chof_Controller_ForewardQueue as datastructure
 *  to store the requests. This datastructure has to be provided and initalized
 *  by the concrete controllers extending this mechanism (e.g. by a session
 *  object) and must be returned in the getQueue() method.
 *  
 *  <b>Note:</b> There is currently no mechanism to invalidate, change or 
 *  prematurely delete a foreward request from the queue, so you cannot remove 
 *  any request. Thus the purpose of the queue is solely to determine where a 
 *  controller should foreward the controll after he is finished with his work 
 *  (i.e. error handling actions within the form of a controller or the 
 *  controller itself can still be invoked by individual _foreward calls)
 *  
 *  When using the foreward queue keep the following points in mind:
 *  
 *   - make the forewards that you push on the queue independent of other actions
 *     queued
 *   - it is not guaranteed that a foreward call gets executed (e.g. user stops
 *     working in the meantime, internet connection get's lost ...) so place only
 *     calls to the queue which are required to put your website in a consistent
 *     state
 *   - handle the use of forewards with care do not use it too extensively
 * 
 * @author chris
 * @package org.chof.controller
 */
abstract class  Chof_Controller_BaseController extends Zend_Controller_Action
{
  private static $TEXT_DECORATOR = array(
    'ViewHelper',
    'Errors',
    array(array('data' => 'HtmlTag'), array('tag' => 'td')),
    array('Label', array('tag' => 'td')),
    array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
  );

  private static $MULTICHECKBOX_DECORATOR = array(
    'ViewHelper',
    'Errors',
    array('HtmlTag', array('tag' => 'div'))
  ); 
  
  private static $TEXTAREA_DECORATOR = array(
    'ViewHelper',
    'Errors',
    array('HtmlTag', array('tag' => 'div'))
  ); 
    
  private static $FILE_DECORATOR = array(
    'Errors',
    array('File'), 
    array(array('data' => 'HtmlTag'), array('tag' => 'td')),
    array('Label', array('tag' => 'td')),
    array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
  );
  
  private static $BUTTON_DECORATOR = array(
    'ViewHelper',
    array(array('data' => 'HtmlTag'), array('tag' => 'td', 'colspan' => '2')),
    array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
  );
      
  
  #****************************************************************************
  # CONVINIENCE METHODS FOR FORM GENERATION
  #****************************************************************************
  
  protected function addTextArea($name, $value, $required = false)
  #****************************************************************************
  {
    $element = new Zend_Form_Element_Textarea($name, array(
      'value' => $value,
      'attribs' => array(
        'class' => 'input',
        'rows' => 10),
      'decorators' => Chof_Controller_BaseController::$TEXTAREA_DECORATOR, 
      'required' => $required));
    
    return $element;
   
  }

  protected function addButton($name, $label)
  #****************************************************************************
  {
    $element = null;
    
    if ($name == 'submit')
      $element = new Zend_Form_Element_Submit($name);
    else
      $element = new Zend_Form_Element_Button($name);
      
    $element->setOptions(array(
      'label' => $label,
      'decorators' => Chof_Controller_BaseController::$BUTTON_DECORATOR));
    
    return $element;
  }
  
  protected function addText($name, $label, $value, $required = false)
  #****************************************************************************
  {
    $element = new Zend_Form_Element_Text($name, array(
      'label' => $label,
      'value' => $value,
      'attribs' => array('class' => 'input'),
      'decorators' => Chof_Controller_BaseController::$TEXT_DECORATOR, 
      'required' => $required));
    
    return $element;
  }
  
  protected function addMultiCheckBox($name, $label, $options, $value, $required = false)
  #****************************************************************************
  {
    $element = new Zend_Form_Element_MultiCheckbox($name, array(
      'label' => $label,
      'decorators' => Chof_Controller_BaseController::$MULTICHECKBOX_DECORATOR,
      'value' => $value,
      'attribs' => array( 'class' => 'input-labeled input', 'label_class' => 'input-label'),
      'required' => $required));
    
    $element->addMultiOptions($options);
    
    return $element;
  }

  protected function addComboBox($name, $label, $value, $options, $required = false)
  #****************************************************************************
  {
    $element = new Zend_Form_Element_Select($name, array(
      'label' => $label,
      'value' => $value,
      'attribs' => array('class' => 'input'),
      'decorators' => Chof_Controller_BaseController::$TEXT_DECORATOR, 
      'required' => $required));
    
    $element->addMultiOptions($options);
    
    return $element;
  }
  
  protected function addFileUpload($label, $name, $destination, $required = false, 
                                   $size = 1024000)
  #****************************************************************************
  {
    $config = Zend_Registry::get('config');
        
    $element = new Zend_Form_Element_File($name);
    $element->setLabel($label)
            ->setDestination($destination)
            ->setAttrib('class', 'input')
            ->addValidator('Count', false, 1)
            ->addValidator('Size', false, 1024000)
            ->setDecorators( Chof_Controller_BaseController::$FILE_DECORATOR);

    return $element;
  }

  #*****************************************************************************
  # FOREWARD QUEUE
  #*****************************************************************************
  
  private $queue = 'not set';
  
  private function queue()
  {
    if ($this->queue == 'not set')
    {
      $queue = $this->getQueue();
      $this->queue = ($queue instanceof Chof_Controller_ForewardQueue) ? $queue : null;
    }
    return $this->queue;
  }
  
  /**
   * Abstract method which must be overwritten by all controller subclasses
   * 
   * Has to return the Chof_Controller_ForewardQueue instance which contains and receives 
   * the stored foreward's
   * 
   * @return Chof_Controller_ForewardQueue
   */
  abstract protected function getQueue();
  
  /**
   * Queues a foreward request 
   * 
   * The signature of the function is the same as the signature of the _foreward function
   * the method puts the request at the end of the queue
   * 
   * if no queue is returned the foreward is immedeately placed to make the usage of queues 
   * transparent
   * 
   * @param string $action
   * @param string $controller
   * @param string $module
   * @param array $params
   * @return integer the position of the foreward in the queue
   */
  protected function queueForeward($action, $controller = null,
                                   $module = null, array $params = null)
  #****************************************************************************
  {
    $queue = $this->queue();
    if ($queue !== null)
    {
      $count = $queue->push($action, $controller, $module, $params);
      return $count;
    }
    else
    {
      $this->_forward($action, $controller, $module, $params);   
      return 0;
    }
  }
  
  /**
   * returns true if a foreward is waiting in the queue
   * @return boolean true if there is at least one foreward in the queue
   */
  protected function hasForewardWaiting()
  #****************************************************************************
  {
    $queue = $this->queue();
    $result = ($queue !== null) ? ($queue->count()>0) : false;
    return $result;
  }
  
  /**
   * returns true if a foreward is waiting in the queue
   * @return boolean true if there is at least one foreward in the queue
   */
  protected function forwardNext()
  #****************************************************************************
  {
    $queue = $this->queue();
    
    if ($queue !== null) 
    {
      $foreward = $queue->unqueue();
      
      if ($foreward !== null)
        $this->_forward($foreward['action'], 
                        $foreward['controller'],
                        $foreward['module'],
                        $foreward['params']);
    }
  }
  
  
}

?>