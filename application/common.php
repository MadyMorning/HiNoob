<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

//加盐处理函数
if (!function_exists('encryption')) {
  /**
   * 加盐处理函数
   * @param  string $password 要加盐的字符串
   * @return string           返回加盐后的字符串
   */
  function encryption($password)
  {
    $str = config('config.salt_str');
    return md5(md5($password . $str) . $str);
  }
}

//curl请求
if (!function_exists('curl_request')) {
  /**
   * 发送请求
   * @param  string  $url   要发送请求的地址
   * @param  boolean $post  是否是POST请求
   * @param  array   $param POST请求参数
   * @param  boolean $https 是否是HTTPS请求
   */
  function curl_request($url, $post=false, $param=[], $https = true)
  {
    //curl_init 初始化
    $ch = curl_init($url);
    //curl_setopt 设置一些请求选项
    if ($post) {
        //设置请求方式和请求参数
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
    }
    // https请求，默认会进行验证
    if ($https) {
        //禁止从服务器端 验证客户端的证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }
    //curl_exec 执行请求会话（发送请求）
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    //curl_close 关闭请求会话
    curl_close($ch);
    return $res;
  }
}

// 获取随机字符串
if (!function_exists('getRandChars')) {
  /**
   * 获取随机字符
   * @param  integer $count 要获取的字符串长度
   * @return string         返回拼接好的字符串
   */
  function getRandChars($count)
  {
    $str = '';
    $strf = 'QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm';
    $max = strlen($strf) - 1;

    for ($i=0; $i < $count; $i++) {
      $str .= $strf[rand(0, $max)];
    }

    return $str;
  }
}
