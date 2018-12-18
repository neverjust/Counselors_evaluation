<?php

/**
 * 用于实现Uestc学生的信息门户认证、课表、一卡通、身份信息获取
 * @author: RioChen<283489710@qq.com>
 * @version:2018/12/14
 */
class UestcApi
{
    const AUTH_URL = "http://idas.uestc.edu.cn/authserver/login";
    const CLASS_TABLE_URL = "";
    const PERSONAL_INFORMATION_URL = "";
    private $userName;
    private $password;
    private $userInfoArray;
    function __construct(string $userName, string $password)
    {
        $this->userName = $userName;
        $this->password = $password;
        $this->userInfoArray = array(
            "username" => $userName,
            "password" => $password
        );
    }
    /**
     * 用于执行get请求
     * @author RioChen<283489710@qq.com>
     * @param $url是需要请求的url地址
     * @param $cookieString是需要携带的cookie
     */
    private function get(string $url, string $cookieString = '') : array
    {
        $curl = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 1,
            CURLINFO_HEADER_OUT => true
        );

        curl_setopt($curl, CURLOPT_COOKIE, $cookieString);
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $headerSize);     //获取header部分
        preg_match_all("/(?<=[Ss][Ee][Tt]-[Cc][Oo][Oo][Kk][Ii][Ee]: )[^\r\n]*/", $header, $matchResult);       //获取cookie部分
        $cookieString = "";
        foreach ($matchResult[0] as $value) {
            $cookieString = $cookieString . ";" . " " . preg_replace("/;.*/", "", $value);        //正则的作用是去除表示cookie属性的那部分字符串，如 path = / 这种，因为curl函数并不能正常解析这些属性，而是会把它们当成几个新的cookie处理
        }
        $cookieString = substr($cookieString, 2);     //去掉一开始添加的 ";" 和 " "
        $return = array(
            "status" => curl_getinfo($curl, CURLINFO_HTTP_CODE),
            "cookie_string" => $cookieString,
            "body" => substr($result, $headerSize)
        );
        curl_close($curl);
        return $return;
    }
    /**
     * 用于执行post请求
     * @author RioChen<283489710@qq.com>
     * @param $url是需要请求的地址
     * @param $postData是需要上传的数据，以关联数组的形式传入
     * @param $cookieString是需要携带的cookie
     */
    private function post(string $url, array $postData = array(), string $cookieString = "", bool $needAllCookies = false)
    {
        $curl = curl_init();
        $options = array(
            CURLOPT_POST => 1,
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_TIMEOUT => 20,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_AUTOREFERER,
            CURLOPT_FOLLOWLOCATION,
            CURLOPT_HEADER => 1,
            CURLOPT_COOKIE => $cookieString
        );
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $headerSize);     //获取header部分
        preg_match_all("/(?<=[Ss][Ee][Tt]-[Cc][Oo][Oo][Kk][Ii][Ee]: )[^\r\n]*/", $header, $matchResult);       //获取cookie部分
        $cookieString = "";
        foreach ($matchResult[0] as $value) {
            $cookieString = $cookieString . ";" . " " . preg_replace("/;.*/", "", $value);        //正则的作用是去除表示cookie属性的那部分字符串，如 path = / 这种，因为curl函数并不能正常解析这些属性，而是会把它们当成几个新的cookie处理
        }
        $cookieString = substr($cookieString, 2);     //去掉一开始添加的 ";" 和 " "
        $return = array(
            "status" => curl_getinfo($curl, CURLINFO_HTTP_CODE),
            "cookie_string" => $cookieString,
            "body" => substr($result, $headerSize)
        );
        curl_close($curl);
        return $return;
    }
    /**
     * 用于获取需要post的参数
     * @author RioChen<283489710@qq.com>
     * @param $response是自定义的get请求返回结果
     * @param $judgeCaptha代表需不需要获取验证码的相关信息
     */
    private function getArgs(array $response, bool $judgeCaptha = false) : array
    {
        $html = new simple_html_dom();
        $html->load($response['body']);
        $input = $html->find('input');
        $input = array_slice($input, 2);
        $argsArray = [];
        foreach ($input as $value) {
            $argsArray = array_merge($argsArray, array($value->name => $value->value));
        }
        if ($judgeCaptha == false)
            array_pop($argsArray);
        $html->clear();
        return $argsArray;
    }
    /**
     * 用于执行post请求，并且保存用于认证的用户标识
     * @author RioChen<283489710@qq.com>
     */
    public function getAuth()
    {
        $getResult = $this->get(UestcApi::AUTH_URL);
        $postData = array_merge($this->userInfoArray, $this->getArgs($getResult));
        $postResult = $this->post(UestcApi::AUTH_URL, $postData, $getResult['cookie_string']);
        $judge = $postResult['status'];
        $auth = $judge == 302 ? true : false;
        if ($judge == 302) {
            $auth = true;
            $errCode = 200;
            $errMsg = "认证成功";
        } else {
            $auth = false;
            $errCode = 11;
            $errMsg = "认证失败";
        }
        $return = array(
            "return" => msg($auth, $errCode, $errMsg),
            "cookieString" => $postResult["cookie_string"]
        );
        return $return;
    }
}

?>