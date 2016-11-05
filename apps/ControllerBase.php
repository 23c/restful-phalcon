<?php
/**
 * @description :the log
 * @author      :23c<phpcool@163.com>
 * @datetime    :2016-11-05
 */

namespace Demo\Apps;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
	public $error_code = 10000;
	public $msg = 'success';
	public $data = [];
	public function json_out($data)
	{
		$this->response->setHeader('Content-type', 'application/json');
		$this->response->setJsonContent($data, JSON_UNESCAPED_UNICODE);
		$this->response->send();
	}

	public function __destruct(){
		$this->json_out(['error_code'=>$this->error_code, 'msg'=>$this->msg, 'data'=>$this->data]);
	}
}
