<?php

class Chof_Util_XML
{
  /**
   * The main function for converting to an XML document.
   * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
   *
   * @param array $data
   * @param string $rootNodeName - what you want the root node to be - defaultsto data.
   * @param SimpleXMLElement $xml - should only be used recursively
   * @return string XML
   */
  public static function encode($array, $root = 'data', $xml=null)
  #*****************************************************************************
  {
    if ($xml == null)
    {
      $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$root />");
    }
    
    foreach($array as $key => $value)
    {
      if (is_numeric($key))
      {
        // make string key...
        $key = "unknownNode_". (string) $key;
      }
       
      $key = preg_replace('/[^A-Za-z0-9]/i', '', $key);
      // if there is another array found recrusively call this function
      if (is_array($value))
      {
        $node = $xml->addChild($key);
        ArrayToXML::toXml($value, $rootNodeName, $node);
      }
      else
      {
        // add single node.
        $value = htmlentities($value);
        $xml->addChild($key,$value);
      }
    }
    // pass back as string. or simple xml object if you want!
    return $xml->asXML();
  }
}
?>