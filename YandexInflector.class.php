<?php

class YandexInflectorException {}

class YandexInflector 
{
	const BASE_URI = 'http://export.yandex.ru/';
	const TIMEOUT = 3;

	private $baseWord = '';
	private $inflections = array();

	function __construct( $word )
	{
		$isPrepared = $this->prepareWord($word);

		if ( $isPrepared )
		{
			$this->baseWord = $word;
			$this->get();
		}
		else
		{
			throw new YandexInflectorException('WORD_NOT_VALID');
		}
			
	}

	protected function prepareWord( &$word )
	{
		trim($word);
		return strlen($word);
	}

	protected function getInflectionPath()
	{
		return 'inflect.xml';
	}

	private function get()
	{
		if ($cacheVars = $this->getFromCache($this->baseWord))
		{
			return $cacheVars;
		}

		$url = self::BASE_URI 
			. $this->getInflectionPath()
			. '?' 
			. http_build_query( array('name' => $this->baseWord) );

		$context = stream_context_create(array(
			'http' => array('timeout' => self::TIMEOUT)
		));
		$xmlResponse = file_get_contents( $url, false, $context );

		$this->parseResponse($xmlResponse);
	}

	private function parseResponse($xml)
	{
		try
		{
			$obXml = new SimpleXmlElement( $xml );
		}
		catch( Exception $e )
		{
			throw new YandexInflectorException('INVALID_RESPONSE_FROM_SERVICE');
			return false;
		}
		
		if (!property_exists($obXml, 'inflection'))
		{
			throw new YandexInflectorException('UNEXPECTED_RESPONSE_FROM_SERVICE');
		}

		foreach ($obXml->inflection as $obInflection ) 
		{
			$this->inflections[] = (string)$obInflection;
		}

		$this->storeCache($this->baseWord, $this->inflections);
	}

	public function getOriginal()
	{
		return $this->baseWord;
	}

	public function getNominative()
	{
		return $this->getInflection(0);
	}

	public function getGenitive()
	{
		return $this->getInflection(1);
	}

	public function getDative()
	{
		return $this->getInflection(2);
	}

	public function getAccusative()
	{
		return $this->getInflection(3);
	}

	public function getInstrumental()
	{
		return $this->getInflection(4);
	}

	public function getPrepositional()
	{
		return $this->getInflection(5);
	}

	public function getInflections()
	{
		return $this->inflections;
	}

	public function getInflection( $code )
	{
		$code = strtolower($code);
		switch ($code) 
		{
			case 'nominative':
			case 'именительный':
			case 0:
				$inflectionNum = 0;
				break;
			
			case 'genitive':
			case 'родительный':
			case 1:
				$inflectionNum = 1;
				break;

			case 'dative':
			case 'дательный':
			case 2:
				$inflectionNum = 2;
				break;

			case 'accusative':
			case 'винительный':
			case 3:
				$inflectionNum = 3;
				break;

			case 'instrumental':
			case 'творительный':
			case 4:
				$inflectionNum = 4;
				break;

			case 'prepositional':
			case 'предложный':
			case 5:
				$inflectionNum = 5;
				break;

			default:
				$inflectionNum = 0;
				break;
		}

		return (!empty($this->inflections) && strlen($this->inflections[ $inflectionNum ]) > 0) ? 
			$this->inflections[ $inflectionNum ] :
			$this->baseWord;
	}

	protected function storeCache($key, $value)
	{
		if (!isset($_SESSION[__CLASS__][ $key ]))
		{
			$_SESSION[__CLASS__][ $key ] = $value;
		}
	}

	protected function getFromCache($key)
	{
		return isset($_SESSION[__CLASS__][ $key ]) ? $_SESSION[__CLASS__][ $key ] : null;
	}

}