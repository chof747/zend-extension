<?php

class Chof_Util_Log extends Zend_Log
{
	public function exception(Exception $e)
	{
		$className = get_class($e);
		$code = $e->getCode();
		$msg = $e->getMessage();
		$trace = $e->getTrace();
		
		$this->log("$className ($code): $msg:", self::ERR);
		
		$traceMessage = array('Stack trace:');
		foreach($trace as $t)
		{
			$params = join(", ", $t[args]);
			$params = str_replace("\n", ' ', $params);
		
			$traceMessage[] = $t['file'].':'.$t['line'];
			$traceMessage[] = '  '
			                . ((!empty($t['class'])) ? $t['class'].'->' : '')
			                . $t['function']."($params)";
      //$traceMessage[] = '';
		}
		
		$this->log("  ".join("\n  ", $traceMessage), self::ERR);
	}
}

?>
