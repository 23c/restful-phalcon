<?php

/**
 * @description :controller demo
 * @author      :23c<phpcool@163.com>
 * @datetime    :2016-11-05
 */

namespace Demo\Apps\Demo\V1\Controllers;

use Demo\Apps\ControllerBase;

class IndexController extends ControllerBase
{
	public function message()
	{
        $suppliers = \Suppliers::instance()->getCompanyList();
		if ($suppliers) {
			$this->msg = 'Suppliers->getCompanyList: ' . json_encode($suppliers, JSON_UNESCAPED_UNICODE);
		} else {
			$this->msg = 'This is v1 getMessage';
		}
		$this->data = $this->request->getRawBody();
	}
	public function messageAdd()
	{
		$name = $this->request->get('name','trim','23c');
		$email = $this->request->get('email','trim','phpcool@163.com');
		$this->msg = 'This is v1 postMessage ' . $name . ' ' . $email;
		$this->data = $this->request->getRawBody();
	}

	public function messageUpdate()
	{
		$this->msg = 'This is v1 getMessage';
		$this->data = $this->request->getRawBody();
	}

}
