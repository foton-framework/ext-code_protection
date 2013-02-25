<?php


class EXT_Code_Protection
{
	//--------------------------------------------------------------------------
	
	public $salt = 'sd4#fSd@%';
	
	//--------------------------------------------------------------------------
	
	public function __construct()
	{
		!session_id() AND session_start();
	}
	
	//--------------------------------------------------------------------------
	
	public function __destruct()
	{
		
	}
	
	//--------------------------------------------------------------------------
	
	public function init()
	{
		$_SESSION['ext_cp_value'] = $this->_code();
		$_SESSION['ext_cp_field'] = $field = $this->_field_name();

		sys::$lib->form->set_field($field , 'input', 'Защита от спама', 'callback[ext.code_protection.validate]');
		$enc_field = $this->_str_to_utf($field);
		$var = '_' . substr(md5(rand(0,999)),0,rand(5,10));
		sys::$lib->template->add_head_content('<!-- EXT_Code_Protection --><script type="text/javascript">var '.$var.'=["'.$this->_str_to_utf( substr( md5( rand(0,9999) ), 0, rand(0,30))).'","\x3C\x69\x6E\x70\x75\x74\x20\x74\x79\x70\x65\x3D\x22\x68\x69\x64\x64\x65\x6e\x22\x20\x6E\x61\x6D\x65\x3D\x22'.$enc_field.'\x22\x20\x2F\x3E","\x61\x70\x70\x65\x6E\x64","\x66\x6F\x72\x6D","'.$this->_str_to_utf($this->_code()).'","\x76\x61\x6C","\x69\x6E\x70\x75\x74\x5B\x6E\x61\x6D\x65\x3D'.$enc_field.'\x5D","\x72\x65\x61\x64\x79"];$()['.$var.'[7]](function(){$('.$var.'[3])['.$var.'[2]]('.$var.'[1]);$('.$var.'[6])['.$var.'[5]]('.$var.'[4])})</script><!-- /EXT_Code_Protection -->');
	}
	
	//--------------------------------------------------------------------------
	
	public function validate($val)
	{
		if (sys::$ext->user->id) return TRUE;
		
		sys::$lib->form->set_error_message('callback[ext.code_protection.validate]', 'Вы не прошли защту от спама :-(');
		
		if (empty($_SESSION['ext_cp_field']) && empty($_SESSION['ext_cp_value'])) return FALSE;
		
		
		$field = $this->_field_name(FALSE);
		$code = $this->_code(FALSE);
		
		if (empty($_POST[$field])) return FALSE;
		
		return $_POST[$field] == $code;
	}
	
	//--------------------------------------------------------------------------
	
	private function _field_name($generate = TRUE)
	{
		static $field;
		
		if ( ! $field)
		{
			$field = empty($_SESSION['ext_cp_field']) ? substr(md5(microtime() . $this->salt), 0, 32) : $_SESSION['ext_cp_field'];
		}
		
		return $generate ? substr(md5($field . $this->salt), 0, 10) : $field;
	}
	
	//--------------------------------------------------------------------------
	
	private function _code($generate = TRUE)
	{
		static $code;
		
		if ( ! $code)
		{
			$code = empty($_SESSION['ext_cp_value']) ? md5(microtime() . $this->salt) : $_SESSION['ext_cp_value'];
		}
		return $generate ? md5($code . $this->salt) : $code;
	}
	
	//--------------------------------------------------------------------------
	
	private function _str_to_utf($string)
	{
	    $hex='';
	    for ($i=0; $i < strlen($string); $i++)
	    {
	        $hex .= '\\x' . dechex(ord($string[$i]));
	    }
	    return $hex;
	}
	
	//--------------------------------------------------------------------------
	
}