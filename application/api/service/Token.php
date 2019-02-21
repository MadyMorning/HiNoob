<?php
namespace app\api\service;
use exception\TokenException;
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

  /**
   * 获取指定Token
   * @param  string $value 要获取的Token
   * @return array         返回数组
   */
  public static function getTokenValue($value)
  {
    $token = \request()->header('token');
    if (!$token) {
      throw new TokenException('无效的Token');
    }
    
    $data = \cache($token);
    if (!$data) {
      throw new TokenException('无效的Token或Token已过期');
    }

    if (!\is_array($data)) {
      $data = \json_decode($data, true);
    }

    if (!\array_key_exists($value, $data)) {
      throw new TokenException('要获取的Token变量不存在');
    }

    return $data[$value];
  }

  /**
   * 获取Token中的UID
   * @return string      返回UID
   */
  public static function getTokenUID()
  {
    $uid = self::getTokenValue('uid');
    return $uid;
  }
}
