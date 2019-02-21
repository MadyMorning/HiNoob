<?php
namespace exception;
use think\exception\Handle;
// use think\Exception;
use think\Request;
use think\Log;

/**
 * 自定义异常处理类
 */
class ExceptionHandle extends Handle
{
  // 返回状态码、错误信息、自定义错误码、请求路径
  private $code;
  private $message;
  private $error_code;
  private $url;

  /**
   * 异常处理（重写 Handle 类 render 方法）
   */
  public function render(\Exception $e)
  {
    $request = Request::instance();
    // 判断 $e 是否是实例化的 BaseException 类
    if ($e instanceof BaseException) {
      $this->code = $e->code;
      $this->message = $e->message;
      $this->error_code = $e->error_code;
      $this->url = $request->url();
    } else {
      // 若是内部错误，且 app_debug 为 true，则使用tp5内置异常处理机制
      if (\config('app_debug')) {
        return parent::render($e);
      } else {
        $this->code = 500;
        $this->message = '未知错误';
        $this->error_code = 999;
        $this->url = $request->url();
      }

      // 服务器错误，记录日志
      $this->recordErrorLog($e);
    }

    // 返回的错误信息
    $result = [
      'message' => $this->message,
      'error_code' => $this->error_code,
      'url' => $this->url
    ];

    return \json($result, $this->code);
  }

  /**
   * 日志处理函数
   * @param  object $e 错误信息
   */
  public function recordErrorLog(\Exception $e)
  {
    // 日志处理初始化
    Log::init([
      'type' => 'File',
      'path' => LOG_PATH,
      'level' => ['error']
    ]);

    // 将错误信息写入日志
    \trace($this->message, 'error');
  }
}
