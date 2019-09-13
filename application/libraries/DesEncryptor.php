<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DesEncryptor
{

    protected $_key;
    protected $_iv;
    protected $_blocksize = 8;
    protected $_encrypt;
    protected $_cipher;

    /**
     * Creates a symmetric Data Encryption Standard (DES) encryptor object
     * with the specified key and initialization vector.
     *
     * @param $key
     * @param $iv
     * @param bool $encrypt
     */
    public function __construct($key, $iv, $encrypt = true)
    {
        $this->_key = $key;
        $this->_iv = $iv;
        $this->_encrypt = $encrypt;

        //$this->_cipher = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_CBC, '');
		$this->_cipher = "DES-CBC";
        //mcrypt_generic_init($this->_cipher, $this->_key, $this->_iv);
    }

    public function __destruct()
    {
        //mcrypt_generic_deinit($this->_cipher);
       // mcrypt_module_close($this->_cipher);
    }

    /**
     * Transforms the specified region of the specified byte array using PCKS7 padding.
     * @param $text
     * @return string
     */
    public function transformFinalBlock($text){
       if ($this->_encrypt){
            $padding = $this->_blocksize - strlen($text) % $this->_blocksize;
            $text .= str_repeat(pack('C', $padding), $padding);
        }

        $text = $this->transformBlock($text);
        
        if (!$this->_encrypt){
			$aPadding = array_values(unpack('C', substr($text, -1)));
			$padding = $aPadding[0];
            $text = substr($text, 0, strlen($text) - $padding);
        }
        
        return $text;
    }

    /**
     * Transforms the specified region of the specified byte array.
     * @param $text
     * @return string
     */
    public function transformBlock($text)
    {
        if ($this->_encrypt)
        {
            //Pad $text with zero's to bring it to a multiple of 8 bytes
			
			$enc = openssl_encrypt($text, $this->_cipher, $this->_key,  OPENSSL_NO_PADDING , $this->_iv);
			//return mcrypt_generic($this->_cipher, $text);
            
			//Trim last 8 bytes. Unsure why they are added
			//$enc = substr($enc, 0, -8);
			
			return $enc;
        }
        else
        {
            return openssl_decrypt($text, $this->_cipher, $this->_key, OPENSSL_NO_PADDING, $this->_iv);
        }
    }
}