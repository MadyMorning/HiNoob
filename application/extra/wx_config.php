<?php

// 微信相关配置文件

return [
  'appid' => 'You APPID',
  'appsecret' => 'You AppSecret',
  // 登录API
  'login_url' => 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code',
  // 支付回调函数
  'pay_callback_url' => 'https://applets.hinoob.cn/api/v1/pay/notify'
];
