<?php
namespace mmjurov;

/**
 * ����������� �����, ������� ��������� ��� ����������� ��� ���������� ������
 * Class YandexInflectorCache
 */
abstract class YandexInflectorCache
{
	/**
	 * �����������. ���������� �� ���� ���������, ����������� ��� ������ ������ ����
	 * @param $options
	 */
	abstract function __construct($options);

	/**
	 * ����� �����������. ���� ��������� ����������� � ���� � �����, �� ����� ����������� ��� � ���� ������
	 * @return boolean
	 */
	abstract function connect();

	/**
	 * ����� ��������� ������ �� ���� �� �����
	 * @param string $key
	 * @return mixed
	 */
	abstract function get($key);

	/**
	 * ����� ��������� �������� � ��� �� �����
	 * @param string $key
	 * @param $value
	 * @return boolean
	 */
	abstract function set($key, $value);
}
