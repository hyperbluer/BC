<?php
/**
 * Captcha.class.php HTML处理类
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-26
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Html
{

    /**
     * 获取html代码中标签内容
     *
     * @static
     * @param $content HTML代码
     * @param $tag 标签名
     * @param bool $endTag 是否含有结束标签， 有结束标签如<head></head>, 无如<input />
     * @return array
     */
    public static function getContent($content, $tag, $endTag = true)
	{
		$tagStartPosition = stripos($content, '<' . $tag);

		if($tagStartPosition !== false)
		{
			$content = substr($content, $tagStartPosition + strlen($tag) + 1);

            if ($endTag === true)
			    $tagEndPosition = stripos($content, '</' . $tag . '>');
            else
                $tagEndPosition = stripos($content, '>');

			if($tagEndPosition !== false)
			{
				$data = substr($content, 0, $tagEndPosition);

				if($content !== false)
				{
					return array_merge(array($data), (array) self::getContent($content, $tag));
				}
			}
		}

		return array();
	}

    /**
     * 获取html代码中无结束标签符的标签内容
     * 例如此类标签：<input />
     *
     * @static
     * @param $content
     * @param $tag
     * @return void
     */
    public static function getTags($content, $tag)
	{
		self::getContent($content, $tag, false);
    }

    /**
     * 获取标签属性
     *
     * @static
     * @param $content
     * @param $key
     * @return bool|string
     */
	public static function getAttribute($content, $key)
	{
		$position = stripos($content, $key);

		if($position !== false)
		{
			$content = substr($content, $position + strlen($key));

			$position = strpos($content, '=');

			if($position !== false)
			{
				$content   = ltrim(substr($content, $position + 1));
				$delimiter = $content[0];
				$value     = '';

				switch($delimiter)
				{
					case '"':
					case '\'':

						$content = substr($content, 1);

						$pos = strpos($content, $delimiter);

						if($pos !== false)
						{
							$value = substr($content, 0, $pos);
						}

						break;

					default:

						$i = 0;

						while($content[$i] != ' ')
						{
							$value.= $content[$i];

							$i++;
						}

						break;
				}

				return empty($value) ? false : $value;
			}
		}

		return false;
	}
}