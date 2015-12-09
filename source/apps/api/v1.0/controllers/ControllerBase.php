<?php
namespace Apiserver\v1\Controllers;

use Phalcon\Mvc\Controller,
    Apiserver\Utils\RulesParse,
    Apiserver\Utils\CheckSign;

class ControllerBase extends Controller
{
    protected $warnMsg = array();     // 校验错误提示信息，包括code和msg
    protected $warnMsgCode = false;     // 校验错误提示信息，仅包括code
    protected $warnMsgMsg = false;     // 校验错误提示信息，仅包括msg
    protected $validFlag = true;    // 校验结果标识  true - 通过   false - 拒绝
    public $_sanReq = array();   // 经处理过的参数
    protected $ctrlName;    // 当前访问控制器名
    protected $actName;    // 当前访问方法名

    const TIMEOUT = 10000;
    const ERROR_APPID = 10002;
    const ERROR_SIGN = 10001;
    const NO_OAUTH = 99999;

    public function beforeExecuteRoute($dispatcher)
    {
        $this->ctrlName = $this->dispatcher->getControllerName();
        $this->actName = $this->dispatcher->getActionName();

        // 获取校验规则
        $rulesFile = __DIR__ . '/../config/rules/' . $this->ctrlName . 'Rules.php';
        $rules = file_exists($rulesFile) ? include $rulesFile : false;
        $actionRules = $rules && isset($rules[$this->actName]) ? $rules[$this->actName] : false;
        if (!$rules || !$actionRules)
        {
            $this->_sanReq = $this->request->get();
            return true;
        }
        $utils = new RulesParse($actionRules, $this->di);
        $utils->parse();
        if (!$utils->resFlag)
        {
            $this->validFlag = false;
            $this->warnMsg = $utils->warnMsg;
            $this->warnMsgCode = $utils->warnMsgCode;
            $this->warnMsgMsg = $utils->warnMsgMsg;
            foreach ($this->warnMsg as $warn)
            {
                echo json_encode(array('flag' => $warn['msg'], 'msg' => $this->di['flagmsg'][$warn['msg']]));
                exit;
            }
        }
        else
        {
            $this->_sanReq = $utils->_sanReq;
            //验证签名
            $this->_validateSign($this->_sanReq);
        }
    }

    /**
     * [验证签名]
     * @param  [type] $reqs [description]
     * @return [type]       [description]
     */
    public function _validateSign($reqs)
    {
        //首先判断请求时间与签名时间是否超过规定时间
        if($_SERVER['REQUEST_TIME'] - $reqs['timestamp'] > $this->di['sysConfig']['timeout'])
            $this->_showMsg(self::TIMEOUT, $this->di['flagmsg'][self::TIMEOUT]);

        //获取商家密钥
        if(!isset($this->di['providerInfo'][$this->_sanReq['appid']]['secretKey']))
            $this->_showMsg(self::ERROR_APPID, $this->di['flagmsg'][self::ERROR_APPID]);
        else
            $reqs['secretKey'] = $this->di['providerInfo'][$this->_sanReq['appid']]['secretKey'];
        $sign = $reqs['sign'];
        unset($reqs['sign']);

        if(isset($reqs['_url']))
        {
            unset($reqs['_url']);
        }
        //签名错误时，返回错误信息
        if(!CheckSign::checkSign($reqs, $sign))
            $this->_showMsg(self::ERROR_SIGN, $this->di['flagmsg'][self::ERROR_SIGN]);
    }

    /**
     * 输出json格式的结果
     */
    protected function _returnResult($ArrayResult)
    {
        exit(json_encode($ArrayResult));
    }

    /**
     * 输出提示信息到接口
     */
    protected function _showMsg($flag, $msg = FALSE)
    {
        if($msg)
            return $this->_returnResult(array('flag' => (string)$flag, 'msg' => $msg));
        else 
            return $this->_returnResult(array('flag' => (string)$flag));
    }

    /**
     * [判断用户是否有操作权限]
     */
    protected function _oauthrity($uid, $babyId)
    {
        $relObj = new \Apiserver\Mdu\Modules\FamilyModule();
        $rel = $relObj->checkRelation($uid, $babyId);
        if($rel['family_relation'] == 1 || $rel['family_relation'] == 5)
            return $rel['family_relation'];
        else
            $this->_showMsg(self::NO_OAUTH, $this->di['flagmsg'][self::NO_OAUTH]);
    }
}