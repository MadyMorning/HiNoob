<?php
namespace app\api\service;

/**
 * Token 基类
 */
class Token
{
  /**
   * 生成Token
   * @param $count 要获取的字符串长度，默认32
   * @return string 返回Token
   */
  public static function generateToken($count = 32)
  {
    return \encryption(\getRandChars($count));
  }
}
